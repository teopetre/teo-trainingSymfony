<?php
/**
 * Created by PhpStorm.
 * User: Teo
 * Date: 4/11/14
 * Time: 1:39 PM
 */

namespace Acme\TrainingBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Class CustomerRole
 * @package Acme\TrainingBundle\Entity
 */
class CustomerRole
{

    /** @JMS\Type("integer") */
    protected $id;

    /** @JMS\Type("string") */
    protected $name;

    /**
     * @JMS\Type("array<Acme\TrainingBundle\Entity\CustomerPermission>")
     * @JMS\SerializedName("customerPermission")
     */
    protected $customerPermission;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $customerPermission
     */
    public function setCustomerPermission($customerPermission)
    {
        $this->customerPermission = $customerPermission;
    }

    /**
     * @return mixed
     */
    public function getCustomerPermission()
    {
        return $this->customerPermission;
    }


}