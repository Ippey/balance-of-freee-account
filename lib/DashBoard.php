<?php
/**
 * Created by PhpStorm.
 * User: ippei
 * Date: 2018/07/10
 * Time: 10:36
 */

namespace Ippey\BalanceOfFreeAccount\Lib;

require_once(__DIR__ . '/Balance.php');
use Ippey\BalanceOfFreeAccount\Lib\Balance;

class DashBoard
{
    public function setUp()
    {
        wp_add_dashboard_widget('balance_of_freee_account_widget', __('Freee 残高'), [$this, 'show']);
    }

    public function show()
    {
        $balance = new Balance();
        echo ($balance->get());
    }
}