<?php

namespace Acme\TrainingBundle\Tests\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MongoControllerTest extends WebTestCase
{
    /** @var  Client */
    protected $client;

    /** @var  string */
    protected $route;

    /** @var  ObjectRepository */
    protected static $repo;

    /** @var  ObjectManager */
    protected static $objectManager;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->route = '/product/mongo';

        $kernel = static::createKernel();
        $kernel->boot();
        self::$objectManager = $kernel->getContainer()
          ->get('doctrine_mongodb')
          ->getManager();
        self::$repo = self::$objectManager->getRepository(
          'AcmeTrainingBundle:Product'
        );
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
     * Functional test for Create action.
     */
    public function testCreate()
    {
        $product = json_encode(array('name' => 'created_item', 'price' => 85));
        $this->client->request(
          'GET',
          "$this->route/create",
          array(),
          array(),
          array('CONTENT_TYPE' => 'application/json'),
          $product
        );

        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals('created_item', $response->name);

        // Assert that the "Content-Type" header is "application/json"
        $this->assertTrue(
          $this->client->getResponse()->headers->contains(
            'Content-Type',
            'application/json'
          )
        );
    }

    /**
     * Functional test for Load by name action.
     */
    public function testLoadByName()
    {
        $this->client->request(
          'GET',
          "$this->route/load-by-name/created_item"
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $product = array_pop($response);
        $this->assertEquals('created_item', $product->name);

        // Create one more product.
        $product = json_encode(array('name' => 'created_item', 'price' => 45));
        $this->client->request(
          'GET',
          "$this->route/create",
          array(),
          array(),
          array('CONTENT_TYPE' => 'application/json'),
          $product
        );

        $this->client->request(
          'GET',
          "$this->route/load-by-name/created_item"
        );
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertCount(2, $response);

        // Check if the right message is returned.
        $this->client->request('GET', "$this->route/load-by-name/nono");
        $response_code = json_decode(
          $this->client->getResponse()->getStatusCode()
        );
        $this->assertEquals(404, $response_code);
    }

    /**
     * Functional test for Load by id action.
     */
    public function testLoadById()
    {
        $product = json_encode(array('name' => 'test1234584', 'price' => 85));
        $this->client->request(
          'GET',
          "$this->route/create",
          array(),
          array(),
          array('CONTENT_TYPE' => 'application/json'),
          $product
        );

        $expected_value = json_decode(
          $this->client->getResponse()->getContent()
        );

        $this->client->request(
          'GET',
          "$this->route/load-by-id/$expected_value->id"
        );
        $actual_value = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals($expected_value, $actual_value);

        $this->client->request('GET', "$this->route/load-by-id/nono");
        $response_code = json_decode(
          $this->client->getResponse()->getStatusCode()
        );
        $this->assertEquals(404, $response_code);
    }

    /**
     * Functional test for Filter method.
     */
    public function testFilter()
    {
        // Check empty filters response.
        $filters = json_encode(array('name' => '', 'price' => 56));
        $this->client->request(
          'POST',
          "$this->route/filter",
          array(),
          array(),
          array('CONTENT_TYPE' => 'application/json'),
          $filters
        );

        $response_code = json_decode(
          $this->client->getResponse()->getStatusCode()
        );
        $this->assertEquals(400, $response_code);

        // Check wrong filters response.
        $filters = json_encode(array('another_filter' => 50));
        $this->client->request(
          'POST',
          "$this->route/filter",
          array(),
          array(),
          array('CONTENT_TYPE' => 'application/json'),
          $filters
        );

        $response = json_decode(
          $this->client->getResponse()->getContent()
        );
        $response_code = json_decode(
          $this->client->getResponse()->getStatusCode()
        );
        $this->assertEquals(400, $response_code);
        $this->assertEquals('Wrong filters sent.', $response);

        // Filter by price.
        $filters = json_encode(array('price' => 85));
        $this->client->request(
          'POST',
          "$this->route/filter",
          array(),
          array(),
          array('CONTENT_TYPE' => 'application/json'),
          $filters
        );

        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertCount(2, $response);

        // Filter by price and name.
        $filters = json_encode(array('name' => 'created_item', 'price' => 85));
        $this->client->request(
          'POST',
          "$this->route/filter",
          array(),
          array(),
          array('CONTENT_TYPE' => 'application/json'),
          $filters
        );

        $response = json_decode($this->client->getResponse()->getContent());
        $product = array_pop($response);
        $this->assertEquals('created_item', $product->name);

        // Filter by criteria that certainly won't match.
        $filters = json_encode(array('price' => 0));
        $this->client->request(
          'POST',
          "$this->route/filter",
          array(),
          array(),
          array('CONTENT_TYPE' => 'application/json'),
          $filters
        );

        $response = json_decode($this->client->getResponse()->getContent());
        $response_code = json_decode(
          $this->client->getResponse()->getStatusCode()
        );
        $this->assertEquals('No products found.', $response);
        $this->assertEquals(404, $response_code);

        // No filters - should return entire list of products.
        $this->client->request(
          'POST',
          "$this->route/filter",
          array(),
          array(),
          array('CONTENT_TYPE' => 'application/json')
        );

        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertCount(count(self::$repo->findAll()), $response);
    }
}
