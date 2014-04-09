<?php

namespace Acme\TrainingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MysqlController
 * @package Acme\TrainingBundle\Controller
 */
class MysqlController extends Controller
{

    /**
     * Adds a product to mysql database.
     * @return Response
     */
    public function createAction()
    {
        $rawProduct = $this->getPostData();
        $rawProduct = json_decode($rawProduct, true);

        $persister = $this->get('acme_training.mysql_persister');
        $product = $persister->createProduct($rawProduct);

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
        $persister = $this->get('acme_training.mysql_persister');
        try {
            $product = $persister->loadProductById($id);
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
        $persister = $this->get('acme_training.mysql_persister');

        try {
            $products = $persister->loadProductByName($name);
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
     * @return mixed
     */
    public function getPostData()
    {
        return $this->get('request')->getContent();
    }

}