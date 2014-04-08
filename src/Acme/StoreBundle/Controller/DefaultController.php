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
        $mongoPersister = $this->get('acme_store.mongo_persister');
        $fakeInfo = array('name' => 'nuca', 'price' => 99);
        $product = $mongoPersister->createProduct($fakeInfo);

        return new Response('Created product id ' . $product->getId());
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
            return new Response($ex->getMessage());
        }

        return new Response("Product with id $id: " . $product->toString());
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
            return new Response($ex->getMessage());
        }

        $output = 'Products found: <ul>';
        foreach ($products as $product) {
            $output .= '<li>' . $product->toString() . '</li>';
        }
        $output .= '</ul>';

        return new Response($output);
    }

}