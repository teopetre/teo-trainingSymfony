<?php

namespace Acme\TrainingBundle\Services;

use Acme\TrainingBundle\Exception\FiltersException;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;

class Persister
{
    /** @var ObjectManager */
    protected $objectManager;

    /**
     * @param $objectManager
     */
    public function setObjectManager($objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return ObjectManager
     */
    public function getEntityManager()
    {
        return $this->objectManager;
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

        // Get the right class name of object managed by the repository
        // depending on database used.
        $productClassName = $this->getEntityManager()->getRepository(
          'AcmeTrainingBundle:Product'
        )->getClassName();

        // Create the product object.
        $product = new $productClassName();
        $product->setName($productArray['name']);
        $product->setPrice($productArray['price']);

        // Persist product to MongoDB.
        $this->objectManager->persist($product);
        $this->objectManager->flush();

        return $product;
    }

    /**
     * Loads products by name.
     *
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function loadProductByName($name)
    {
        $repository = $this->objectManager->getRepository(
          'AcmeTrainingBundle:Product'
        );
        $products = $repository->findByName($name);

        if (!$products) {
            throw new Exception('No product found for name "' . $name . '"');
        }

        return $products;
    }

    /**
     * Loads product by id.
     *
     * @throws Exception
     */
    public function loadProductById($id)
    {
        $repository = $this->objectManager
          ->getRepository('AcmeTrainingBundle:Product');
        $product = $repository->find($id);

        if (!$product) {
            throw new Exception('No product found for id ' . $id);
        }

        return $product;
    }

    /**
     * Returns a list of products that match a criteria.
     *
     * @param $filters
     *   The filters array which should have the format
     *   'filter_name' => 'filter_value'
     *
     * @return \Acme\TrainingBundle\Document\Product[]|array
     * @throws FiltersException
     * @throws Exception
     */
    public function filter($filters)
    {
        $repository = $this->objectManager->getRepository(
          'AcmeTrainingBundle:Product'
        );
        if (!$filters) {
            return $repository->findAll();
        }

        // Check if the right filters was sent.
        if (!is_array($filters) || array_diff(
            array_keys($filters),
            array('name', 'price')
          )
        ) {
            throw new FiltersException('Wrong filters sent.');
        }

        // Check if filters are empty.
        if ((isset($filters['name']) && trim($filters['name']) == '') ||
          (isset($filters['name']) && trim($filters['name']) == '')
        ) {
            throw new FiltersException('Filters are empty.');
        }

        $products = $repository->findBy($filters);
        if (empty($products)) {
            throw new Exception('No products found.');
        }

        return $products;
    }
}