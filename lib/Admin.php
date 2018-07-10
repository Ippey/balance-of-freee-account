<?php
/**
 * Created by PhpStorm.
 * User: ippei
 * Date: 2018/07/10
 * Time: 10:52
 */

namespace Ippey\BalanceOfFreeAccount\Lib;


class Admin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'setUp']);
    }

    public function setUp()
    {
        add_menu_page( 'Balance of Freee Account', 'Balance of Freee Account', 'manage_options', 'balance_of_freee', [$this, 'show']);
    }

    public function show()
    {
        echo('hoge');
    }
}