<?php

namespace Acme\TrainingBundle\Tests\Services;

use Acme\TrainingBundle\Services\MysqlPersister;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MysqlPersisterTest
 * @package Acme\TrainingBundle\Tests\Services
 */
class MysqlPersisterTest extends WebTestCase
{
    /** @var MysqlPersister */
    private $mysqlPersister;

    /** @var ObjectManager */
    protected static $entityManager;

    /** @var  EntityRepository */
    protected static $repo;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        self::$entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        self::$repo = self::$entityManager->getRepository(
          'AcmeTrainingBundle:Product'
        );

        $this->mysqlPersister= new MysqlPersister();
        $this->mysqlPersister->setEntityManager(self::$entityManager);
    }

    public static function tearDownAfterClass()
    {
        // Delete all items form db.
        $products = self::$repo->findAll();
        foreach ($products as $product) {
            self::$entityManager->remove($product);
            self::$entityManager->flush();
        }
    }

    /**
     * Validates the method that saves a product in Mysql db.
     */
    public function testCreate()
    {
        $product = $this->mysqlPersister->createProduct(
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
        $this->mysqlPersister->createProduct(
          array('name' => '', 'price' => 54)
        );
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testCreateException2()
    {
        $this->mysqlPersister->createProduct(
          array('name' => 'teo', 54)
        );
    }

    /**
     * Validates the method that loads and object from Mysql db, by id.
     *
     * @expectedException Exception
     */
    public function testLoadById()
    {
        $product = $this->mysqlPersister->createProduct(
          array('name' => 'item1', 'price' => 65)
        );
        $this->assertEquals(
          $product,
          $this->mysqlPersister->loadProductById($product->getId())
        );

        // This should throw Exception.
        $this->mysqlPersister->loadProductById('1teo234');
    }

    /**
     * Validates the method that loads a list of objects from a Mysql db, by
     * name.
     *
     * @expectedException Exception
     */
    public function testLoadByName()
    {
        // Check if there are 2 items "item1" created in previous tests.
        $this->assertCount(
          2,
          $this->mysqlPersister->loadProductByName('item1')
        );

        // Create new product and search for it.
        $product = $this->mysqlPersister->createProduct(
          array('name' => 'item2', 'price' => 100)
        );
        $products = $this->mysqlPersister->loadProductByName('item2');
        $actual_product = array_pop($products);
        $this->assertEquals($product, $actual_product);

        // Search for an non-existent item.
        $this->mysqlPersister->loadProductByName('item3');
    }

}
 