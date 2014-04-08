<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Teo
 * Date: 4/4/14
 * Time: 3:20 PM
 * To change this template use File | Settings | File Templates.
 */

namespace HelloTest\HelloBundle\Services;

use HelloTest\HelloBundle\Entity\Cart;
use HelloTest\HelloBundle\Entity\CartResponse;

class CartHandler
{
    /** @var int */
    protected $minCartTotal;

    /**
     * @param int $minCartTotal
     */
    public function setMinCartTotal($minCartTotal)
    {
        $this->minCartTotal = $minCartTotal;
    }

    /**
     * @return int
     */
    public function getMinCartTotal()
    {
        return $this->minCartTotal;
    }

    /**
     * @param Cart $cart
     * @return CartResponse
     */
    public function buildCartResponse(Cart $cart)
    {
        $cartResponse = new CartResponse();
        $cartResponse->setTotal($cart->computeTotal());
        if ($cartResponse->getTotal() > $this->minCartTotal) {
            $cartResponse->setStatus('OK');
        } else {
            $cartResponse->setStatus('NOT OK');
        }

        return $cartResponse;
    }

}