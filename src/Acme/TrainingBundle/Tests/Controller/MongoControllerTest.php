<?php

namespace Acme\TrainingBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MongoControllerTest extends WebTestCase
{
    /** @var  Client */
    protected $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public static function tearDownAfterClass()
    {
        // Delete all items form db.
        $kernel = static::createKernel();
        $kernel->boot();
        $mongoManager = $kernel->getContainer()->get('doctrine_mongodb');
        $repo = $mongoManager->getRepository(
          'AcmeTrainingBundle:Product'
        );

        $products = $repo->findAll();
        foreach ($products as $product) {
            $mongoManager->getManager()->remove($product);
            $mongoManager->getManager()->flush();
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
          '/product/mongo/create',
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
        $this->client->request('GET', '/product/mongo/load-by-name/created_item');
        $response = json_decode($this->client->getResponse()->getContent());
        $product = array_pop($response);
        $this->assertEquals('created_item', $product->name);

        // Create one more product.
        $product = json_encode(array('name' => 'created_item', 'price' => 45));
        $this->client->request(
          'GET',
          '/product/mongo/create',
          array(),
          array(),
          array('CONTENT_TYPE' => 'application/json'),
          $product
        );

        $this->client->request('GET', '/product/mongo/load-by-name/created_item');
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertCount(2, $response);

        // Check if the right message is returned.
        $this->client->request('GET', '/product/mongo/load-by-name/nono');
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
          '/product/mongo/create',
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
          '/product/mongo/load-by-id/' . $expected_value->id
        );
        $actual_value = json_decode($this->client->getResponse()->getContent());

        $this->assertEquals($expected_value, $actual_value);

        $this->client->request('GET', '/product/mongo/load-by-id/nono');
        $response_code = json_decode(
          $this->client->getResponse()->getStatusCode()
        );
        $this->assertEquals(404, $response_code);
    }

}
