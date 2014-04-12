<?php
/**
 * Created by PhpStorm.
 * User: Teo
 * Date: 4/11/14
 * Time: 1:36 PM
 */

namespace Acme\TrainingBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Class CustomerPermission
 * @package Acme\TrainingBundle\Entity
 */
class CustomerPermission {

    /** @JMS\Type("integer") */
    protected $id;

    /** @JMS\Type("string") */
    protected $name;

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

}