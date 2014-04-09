<?php

namespace Acme\StoreBundle\Tests\Services;

use Acme\StoreBundle\Entity\Product;
use Acme\StoreBundle\Services\MongoPersister;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

/**
 * Class MongoPersisterTest
 * @package Acme\StoreBundle\Tests\Services
 */
class MongoPersisterTest extends WebTestCase
{
    /** @var MongoPersister */
    private $mongoPersister;

    /** @var ManagerRegistry */
    protected static $mongoManager;

    /** @var  DocumentRepository */
    protected static $repo;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        self::$mongoManager = $kernel->getContainer()->get('doctrine_mongodb');
        self::$repo = self::$mongoManager->getRepository(
          'AcmeStoreBundle:Product'
        );

        $this->mongoPersister = new MongoPersister();
        $this->mongoPersister->setMongoManager(self::$mongoManager);
    }

    public static function tearDownAfterClass()
    {
        // Delete all items form db.
        $products = self::$repo->findAll();
        foreach ($products as $product) {
            self::$mongoManager->getManager()->remove($product);
            self::$mongoManager->getManager()->flush();
        }
    }

    /**
     * Validates the method that saves an object into a mongo document.
     */
    public function testCreate()
    {
        $product = $this->mongoPersister->createProduct(
          array('name' => 'item1', 'price' => 54)
        );
        $this->assertEquals(54, $product->getPrice());
        $this->assertEquals('item1', $product->getName());
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testCreateException()
    {
        $this->mongoPersister->createProduct(
          array('name' => '', 'price' => 54)
        );
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testCreateException2()
    {
        $this->mongoPersister->createProduct(
          array('name' => '', 54)
        );
    }

    /**
     * Validates the method that loads and object from a mongo document, by id.
     *
     * @expectedException Exception
     */
    public function testLoadById()
    {
        $product = $this->mongoPersister->createProduct(
          array('name' => 'item1', 'price' => 65)
        );
        $this->assertEquals(
          $product,
          $this->mongoPersister->loadProductById($product->getId())
        );

        // This should throw Exception.
        $this->mongoPersister->loadProductById('1teo234');
    }

    /**
     * Validates the method that loads a list of objects from a mongo db, by
     * name.
     *
     * @expectedException Exception
     */
    public function testLoadByName()
    {
        // Check if there are 2 items "item1" created in previous tests.
        $this->assertCount(
          2,
          $this->mongoPersister->loadProductByName('item1')
        );

        // Create new product and search for it.
        $product = $this->mongoPersister->createProduct(
          array('name' => 'item2', 'price' => 100)
        );
        $products = $this->mongoPersister->loadProductByName('item2');
        $actual_product = array_pop($products);
        $this->assertEquals($product, $actual_product);

        // Search for an non-existent item.
        $this->mongoPersister->loadProductByName('item3');
    }

}
 