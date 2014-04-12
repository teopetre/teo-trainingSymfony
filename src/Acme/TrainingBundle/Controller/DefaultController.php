<?php

namespace Acme\TrainingBundle\Controller;

use Guzzle\Service\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package Acme\TrainingBundle\Controller
 */
class DefaultController extends Controller
{

    /**
     * @return Response
     */
    public function indexAction()
    {
        $client = new Client();
        $request = $client->get(
          'http://svsjbshc1.stg.allegiantair.com:8580/otares/v2/api/lookups/CustomerRole'
        );

        try {
            $response = $request->send();
        } catch (\Exception $e) {
            $response = new Response($e->getMessage(), 500);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $result = $response->getBody(true);

        $customerRoleHandler = $this->get(
          'acme_training.customer_role_handler'
        );

        // Deserialize data received.
        $customerRoles = $customerRoleHandler->deserializeData($result);

        // Serialize back to return it as response.
        $serializedCustomers = $customerRoleHandler->serializeData(
          $customerRoles
        );

        $response = new Response($serializedCustomers);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

} 