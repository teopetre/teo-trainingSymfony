<?php

namespace Acme\TrainingBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MysqlControllerTest extends MongoControllerTest
{

    public function setUp()
    {
        $this->client = static::createClient();
        $this->route = '/product/mysql';

        $kernel = static::createKernel();
        $kernel->boot();
        self::$objectManager = $kernel->getContainer()
          ->get('doctrine')
          ->getManager();
        self::$repo = self::$objectManager->getRepository(
          'AcmeTrainingBundle:Product'
        );
    }

}
