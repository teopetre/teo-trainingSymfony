<?php
/**
 * Created by PhpStorm.
 * User: Teo
 * Date: 4/11/14
 * Time: 4:18 PM
 */

namespace Acme\TrainingBundle\Tests\Controller;


use Guzzle\Http\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 * @package Acme\TrainingBundle\Tests\Controller
 */
class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        // Check if received initial data is the same after deserializing and
        // serializing it back in indexAction().
        $client = new Client('http://svsjbshc1.stg.allegiantair.com:8580');
        $request = $client->get(
          'otares/v2/api/lookups/CustomerRole'
        );

        $response = $request->send();
        $initialResult = $response->getBody(true);

        $client = static::createClient();
        $client->request(
          'GET',
          'customer-role',
          array(),
          array(),
          array('CONTENT_TYPE' => 'application/json')
        );

        $processedResult = $client->getResponse()->getContent();

        $this->assertJsonStringEqualsJsonString(
          $initialResult,
          $processedResult
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

} 