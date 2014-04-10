<?php

namespace Acme\TrainingBundle\Controller;

use Acme\TrainingBundle\Exception\FiltersException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MongoController
 * @package Acme\TrainingBundle\Controller
 */
class MongoController extends Controller
{

    /**
     * Adds a product to MongoDB.
     * @return Response
     */
    public function createAction()
    {
        $rawProduct = $this->getPostData();
        $mongoPersister = $this->get('acme_training.mongo_persister');
        $rawProduct = json_decode($rawProduct, true);
        $product = $mongoPersister->createProduct($rawProduct);

        $response = new Response($product->toJson());
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Loads a product by id.
     *
     * @param $id
     * @return Response
     */
    public function loadByIdAction($id)
    {
        $mongoPersister = $this->get('acme_training.mongo_persister');
        try {
            $product = $mongoPersister->loadProductById($id);
        } catch (\Exception $ex) {
            $response = new Response(json_encode($ex->getMessage()), 404);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $response = new Response($product->toJson());
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Loads a list of products by a name.
     *
     * @param $name
     *   The name to search for.
     * @return Response
     */
    public function loadByNameAction($name)
    {
        $mongoPersister = $this->get('acme_training.mongo_persister');

        try {
            $products = $mongoPersister->loadProductByName($name);
        } catch (\Exception $ex) {
            $response = new Response(json_encode($ex->getMessage()), 404);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $productsArray = array();
        foreach ($products as $product) {
            $productsArray[] = $product->toArray();
        }

        $response = new Response(json_encode($productsArray));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Loads a list of products by some filters.
     *
     * @return Response
     */
    public function filterAction()
    {
        $filters = $this->getPostData();
        $mongoPersister = $this->get('acme_training.mongo_persister');
        $rawProduct = json_decode($filters, true);

        try {
            $products = $mongoPersister->filter($rawProduct);
        } catch (\Exception $e) {
            $status = ($e instanceof FiltersException) ? 400 : 404;
            $response = new Response(json_encode($e->getMessage()), $status);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $productsArray = array();
        foreach ($products as $product) {
            $productsArray[] = $product->toArray();
        }

        $response = new Response(json_encode($productsArray));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @return mixed
     */
    public function getPostData()
    {
        return $this->get('request')->getContent();
    }

}