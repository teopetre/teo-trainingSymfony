<?php

namespace Acme\StoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package Acme\StoreBundle\Controller
 */
class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render(
          'AcmeStoreBundle:Default:index.html.twig',
          array('name' => $name)
        );
    }

    /**
     * Adds a product to MongoDB.
     * @return Response
     */
    public function createAction()
    {
        $rawProduct = $this->getPostData();
        $mongoPersister = $this->get('acme_store.mongo_persister');
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
        $mongoPersister = $this->get('acme_store.mongo_persister');
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
        $mongoPersister = $this->get('acme_store.mongo_persister');

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
     * @return mixed
     */
    public function getPostData()
    {
        return $this->get('request')->getContent();
    }

}