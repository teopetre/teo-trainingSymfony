<?php

namespace HelloTest\HelloBundle\Services;

use HelloTest\HelloBundle\Entity\Cart;

/**
 * Class CartBuilder
 * @package HelloTest\HelloBundle\Services
 */
class CartBuilder
{

    public function populateCart(array $cartArray)
    {
        $cart = new Cart();
        if (isset($cartArray['item1'])) {
            $cart->setItem1($cartArray['item1']);
        }
        if (isset($cartArray['item2'])) {
            $cart->setItem2($cartArray['item2']);
        }
        if (isset($cartArray['item3'])) {
            $cart->setItem3($cartArray['item3']);
        }
        if (isset($cartArray['item4'])) {
            $cart->setItem4($cartArray['item4']);
        }

        return $cart;
    }

}