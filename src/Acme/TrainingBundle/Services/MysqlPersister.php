<?php

namespace Acme\TrainingBundle\Services;


use Acme\TrainingBundle\Entity\Product;
use Doctrine\Common\Persistence\ObjectManager;

class MysqlPersister
{

    /** @var ObjectManager */
    protected $entityManager;

    /**
     * @param $entityManager ObjectManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return ObjectManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;

    }

    /**
     * Adds a new product to database.
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

        $em = $this->entityManager;
        $em->persist($product);
        $em->flush();

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
        $repository = $this->entityManager
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
        $repository = $this->entityManager
          ->getRepository('AcmeTrainingBundle:Product');
        $product = $repository->find($id);

        if (!$product) {
            throw new \Exception('No product found for id ' . $id);
        }

        return $product;
    }
}