<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.09.2014
 * Time: 20:24
 */


namespace AmfphpModule\Services;


use AmfphpModule\Service;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Model\Coupon;
use App\Model\User;
use VulnModule\Config\Annotations as Vuln;

/**
 * Coupon service. Provides method for operating coupons.
 * @package AmfphpModule\Services
 * @Vuln\Description("Service used to operate coupons.")
 */
class CouponService  extends Service
{
    /**
     * Set discount on cart if correct coupon code is entered
     * @param string $couponCode
     * @throws \App\Exception\NotFoundException
     * @throws \App\Exception\HttpException
     * @return array Coupon code and respective discount
     * @Vuln\Description("Sets the coupon for current cart.")
     */
    public function useCoupon($couponCode) {
        if (!trim($couponCode)) {
            throw new HttpException('Please provide coupon code');
        }

        $couponCode = $this->wrap('couponCode', $couponCode);

        /** @var Coupon $coupon */
        $coupon = $this->pixie->orm->get('coupon')->where('coupon', $couponCode)->find();
        if (!$coupon->loaded() || $coupon->coupon != $couponCode->getFilteredValue()) {
            throw new NotFoundException('Wrong coupon.');
        }

        $this->pixie->cart->useCoupon($coupon->id());

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
        $this->pixie->cart->unsetCoupon();
    }
} 