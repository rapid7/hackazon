<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.09.2014
 * Time: 20:24
 */


namespace AmfphpModule\Services;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Model\Cart;
use App\Model\Coupon;
use App\Model\User;
use App\Pixie;

/**
 * Coupon service. Provides method for operating coupons
 * @package AmfphpModule\Services
 */
class CouponService
{
    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * Set discount on cart if correct coupon code is entered
     * @param string $couponCode
     * @throws \App\Exception\NotFoundException
     * @throws \App\Exception\HttpException
     * @return array Coupon code and respective discount
     */
    public function useCoupon($couponCode) {
        if (!trim($couponCode)) {
            throw new HttpException('Please provide coupon code');
        }

        /** @var Cart $cart */
        $cart = $this->pixie->orm->get('cart');
        $cart = $cart->getCart();

        /** @var Coupon $coupon */
        $coupon = $this->pixie->orm->get('coupon')->where('coupon', $couponCode)->find();
        if (!$coupon->loaded() || $coupon->coupon != $couponCode) {
            throw new NotFoundException('Wrong coupon.');
        }

        $cart->useCoupon($coupon->id());

        $result = array_intersect_key($coupon->as_array(true), array_flip(['coupon', 'discount']));
        /** @var User|null $user */
        $user = $this->pixie->auth->user();
        $result['username'] = $user ? $user->username : '';

        return $result;
    }

    /**
     * @return void
     */
    public function unsetCoupon()
    {
        /** @var Cart $cart */
        $cart = $this->pixie->orm->get('cart');
        $cart = $cart->getCart();
        $cart->unsetCoupon();
    }

    function getPixie()
    {
        return $this->pixie;
    }

    function setPixie(Pixie $pixie = null)
    {
        $this->pixie = $pixie;
    }
} 