<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Teo
 * Date: 4/4/14
 * Time: 2:54 PM
 * To change this template use File | Settings | File Templates.
 */

namespace HelloTest\HelloBundle\Entity;


class CartResponse
{

    /** @var */
    protected $total;

    /** @var */
    protected $status;

    /**
     * @param  $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'total' => $this->total,
            'status' => $this->status,
        );
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

}