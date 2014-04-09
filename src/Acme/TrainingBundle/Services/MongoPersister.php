<?php

namespace Acme\TrainingBundle\Services;

use Acme\TrainingBundle\Document\Product;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

class MongoPersister
{

    /** @var ManagerRegistry */
    protected $mongoManager;

    /**
     * @param \Doctrine\Bundle\MongoDBBundle\ManagerRegistry $mongoManager
     */
    public function setMongoManager($mongoManager)
    {
        $this->mongoManager = $mongoManager;
    }

    /**
     * @return \Doctrine\Bundle\MongoDBBundle\ManagerRegistry
     */
    public function getMongoManager()
    {
        return $this->mongoManager;
    }

    /**
     * Adds a new product to MongoDB.
     *
     * @param array $productArray
     *
     * @throws \UnexpectedValueException
     * @return \Acme\TrainingBundle\Document\Product
     */
    public function createProduct(array $productArray)
    {
        if (empty($productArray['name']) || empty($productArray['price'])) {
            throw new \UnexpectedValueException ('Both fields are required.');
        }

        // Create the product object.
        $product = new Product();
        $product->setName($productArray['name']);
        $product->setPrice($productArray['price']);

        // Persist product to MongoDB.
        $dm = $this->mongoManager->getManager();
        $dm->persist($product);
        $dm->flush();

        return $product;
    }

    /**
     * Loads products by name.
     *
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function loadProductByName($name)
    {
        $repository = $this->mongoManager
          ->getRepository('AcmeTrainingBundle:Product');
        $products = $repository->findByName($name);

        if (!$products) {
            throw new \Exception('No product found for name "' . $name . '"');
        }

        return $products;
    }

    /**
     * Loads product by id.
     *
     * @param $id
     * @return Product
     * @throws \Exception
     */
    public function loadProductById($id)
    {
        $repository = $this->mongoManager
          ->getRepository('AcmeTrainingBundle:Product');
        $product = $repository->find($id);

        if (!$product) {
            throw new \Exception('No product found for id ' . $id);
        }

        return $product;
    }
}