<?php

namespace Acme\TrainingBundle\Services;

use JMS\Serializer\Serializer;

/**
 * Class CustomerRoleHandler
 * @package Acme\TrainingBundle\Services
 */
class CustomerRoleHandler
{

    /** @var  Serializer */
    protected $serializer;

    /**
     * @param \JMS\Serializer\Serializer $serializer
     */
    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @return \JMS\Serializer\Serializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Deserialize JSON data into CustomerRole objects.
     *
     * @param $customerRoleData
     *   JSON data.
     * @return mixed
     *   Returns an array of CustomerRole objects.
     */
    public function deserializeData($customerRoleData)
    {
        $data = $this->serializer->deserialize(
          $customerRoleData,
          'array<Acme\TrainingBundle\Entity\CustomerRole>',
          'json'
        );

        return $data;
    }

    /**
     * Serializes data to the specified format.
     *
     * @param $customerRoleData
     *   Data to serialize.
     * @param string $type
     *   The format to serialize the data into. Defaults to 'json'.
     */
    public function serializeData($customerRoleData, $type = 'json')
    {
        return $this->serializer->serialize($customerRoleData, $type);
    }
}