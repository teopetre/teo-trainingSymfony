<?php

namespace Acme\TrainingBundle\Tests\Services;

use Acme\TrainingBundle\Services\Persister;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class PersisterTest
 * @package Acme\TrainingBundle\Tests\Services
 */
class MongoPersisterTest extends WebTestCase
{
    /** @var  Persister */
    protected $persister;

    /** @var  ObjectManager */
    protected static $objectManager;

    /** @var  ObjectRepository */
    protected static $repo;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        self::$objectManager = $kernel->getContainer()->get('doctrine_mongodb')->getManager();
        self::$repo = self::$objectManager->getRepository(
          'AcmeTrainingBundle:Product'
        );

        $this->persister = new Persister();
        $this->persister->setObjectManager(self::$objectManager);
    }

    public static function tearDownAfterClass()
    {
        // Delete all items form db.
        $products = self::$repo->findAll();
        foreach ($products as $product) {
            self::$objectManager->remove($product);
            self::$objectManager->flush();
        }
    }

    /**
     * Validates the method that saves an object into db.
     */
    public function testCreate()
    {
        $product = $this->persister->createProduct(
          array('name' => 'item1', 'price' => 54)
        );
        $this->assertEquals(54, $product->getPrice());
        $this->assertEquals('item1', $product->getName());
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testCreateException()
    {
        $this->persister->createProduct(
          array('name' => '', 'price' => 54)
        );
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testCreateException2()
    {
        $this->persister->createProduct(
          array('name' => '', 54)
        );
    }

    /**
     * Validates the method that loads and object from db, by id.
     *
     * @expectedException \Exception
     */
    public function testLoadById()
    {
        $product = $this->persister->createProduct(
          array('name' => 'item1', 'price' => 65)
        );
        $this->assertEquals(
          $product,
          $this->persister->loadProductById($product->getId())
        );

        // This should throw Exception.
        $this->persister->loadProductById('1teo234');
    }

    /**
     * Validates the method that loads a list of objects from db, by name.
     *
     * @expectedException \Exception
     */
    public function testLoadByName()
    {
        // Check if there are 2 items "item1" created in previous tests.
        $this->assertCount(
          2,
          $this->persister->loadProductByName('item1')
        );

        // Create new product and search for it.
        $product = $this->persister->createProduct(
          array('name' => 'item2', 'price' => 100)
        );
        $products = $this->persister->loadProductByName('item2');
        $actual_product = array_pop($products);
        $this->assertEquals($product, $actual_product);

        // Search for an non-existent item.
        $this->persister->loadProductByName('item3');
    }

}
 