<?php

namespace Acme\TrainingBundle\Tests\Services;

use Acme\TrainingBundle\Services\MysqlPersister;
use Acme\TrainingBundle\Services\Persister;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MysqlPersisterTest
 * @package Acme\TrainingBundle\Tests\Services
 */
class MysqlPersisterTest extends MongoPersisterTest
{
    /**
     * Override setUp() method in order to use Mysql.
     */
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        self::$objectManager = $kernel->getContainer()->get('doctrine')
          ->getManager();
        self::$repo = self::$objectManager->getRepository(
          'AcmeTrainingBundle:Product'
        );

        $this->persister = new Persister();
        $this->persister->setObjectManager(self::$objectManager);
    }

}
 