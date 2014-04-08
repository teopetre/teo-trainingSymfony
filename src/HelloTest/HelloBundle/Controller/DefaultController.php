<?php

namespace HelloTest\HelloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        phpinfo();
        die();
//        return new Response('<html><body>Hello ' . $name . '</body></html>');
    }

    /**
     * @return Response
     */
    public function cartAction()
    {
        $rawCart = $this->getPostData();
        $cartBuilder = $this->get('hello_test_hello.cart_builder');
        $rawCart = json_decode($rawCart, TRUE);
        $cart = $cartBuilder->populateCart($rawCart);
        $cartHandler = $this->get('hello_test_hello.cart_handler');
        $cartResponse = $cartHandler->buildCartResponse($cart);

        return new Response($cartResponse->toJson());
    }

    /**
     * @return mixed
     */
    public function getPostData()
    {
        return $this->get('request')->getContent();
    }
}
