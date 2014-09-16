<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.09.2014
 * Time: 20:24
 */


namespace AmfphpModule\Services;

/**
 * Voucher service. Provides method for operating vouchers
 * @package AmfphpModule\Services
 */
class VoucherService 
{
    public function registerVoucher($date, $dayOfWeek) {
        $daysOfWeek = [1 => 'Mondey', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $discounts = [
            1 => 10,
            2 => 20,
            3 => 20,
            4 => 20,
            5 => 30,
            6 => 30,
            7 => 50
        ];
        return [
            'date' => date('Y/m/d H:i:s', strftime($date)),
            'dayOfWeek' => $daysOfWeek[$dayOfWeek],
            'discount' => $discounts[$dayOfWeek] . '%'
        ];
    }
} 