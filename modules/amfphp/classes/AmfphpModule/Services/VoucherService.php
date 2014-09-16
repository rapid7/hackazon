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
        return [
            'date' => date('Y/m/d H:i:s', strftime($date)),
            'dayOfWeek' => $daysOfWeek[$dayOfWeek],
            'discount' => (10 * $dayOfWeek) . '%'
        ];
    }
} 