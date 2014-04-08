<?php

namespace HelloTest\HelloBundle\Entity;


class Cart
{
    /** @var int */
    protected $item1;

    /** @var int */
    protected $item2;

    /** @var int */
    protected $item3;

    /** @var int */
    protected $item4;

    /**
     * @param int $item1
     */
    public function setItem1($item1)
    {
        $this->item1 = $item1;
    }

    /**
     * @return int
     */
    public function getItem1()
    {
        return $this->item1;
    }

    /**
     * @param int $item2
     */
    public function setItem2($item2)
    {
        $this->item2 = $item2;
    }

    /**
     * @return int
     */
    public function getItem2()
    {
        return $this->item2;
    }

    /**
     * @param int $item3
     */
    public function setItem3($item3)
    {
        $this->item3 = $item3;
    }

    /**
     * @return int
     */
    public function getItem3()
    {
        return $this->item3;
    }

    /**
     * @param int $item4
     */
    public function setItem4($item4)
    {
        $this->item4 = $item4;
    }

    /**
     * @return int
     */
    public function getItem4()
    {
        return $this->item4;
    }

    /**
     * @return int
     */
    public function computeTotal()
    {
        return $this->item1 + $this->item2 + $this->item3 + $this->item4;
    }
}