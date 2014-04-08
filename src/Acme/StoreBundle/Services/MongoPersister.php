<?php

namespace Acme\StoreBundle\Services;

use Acme\StoreBundle\Document\Product;
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
     * @return \Acme\StoreBundle\Document\Product
     */
    public function createProduct(array $productArray)
    {
        // Create the product object.
        $product = new Product();
        if (isset($productArray['name'])) {
            $product->setName($productArray['name']);
        }
        if (isset($productArray['price'])) {
            $product->setPrice($productArray['price']);
        }

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
          ->getRepository('AcmeStoreBundle:Product');
        $products = $repository->findByName($name);

        if (!$products) {
            throw new \Exception('No product found for name ' . $name);
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
          ->getRepository('AcmeStoreBundle:Product');
        $product = $repository->find($id);

        if (!$product) {
            throw new \Exception('No product found for id ' . $id);
        }

        return $product;
    }
}