<?php
/**
 * Plugin Name:  Balance of Freee Account
 * Plugin URI:   https://developer.wordpress.org/plugin/balance-of-freee-account/
 * Description:  Show your balance of freee account on dashboard.
 * Version:      1
 * Author:       Ippei Sumida
 * Author URI:   https://unplat.info
 * License:      GPL-2.0-or-later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Ippey\BalanceOfFreeAccount;

// DashBoard
require_once(__DIR__ . '/lib/DashBoard.php');
use Ippey\BalanceOfFreeAccount\Lib\DashBoard;
$dashboard = new DashBoard();
add_action('wp_dashboard_setup', [$dashboard, 'setUp']);

// Setting
require_once(__DIR__ . '/lib/Admin.php');
use Ippey\BalanceOfFreeAccount\Lib\Admin;
$admin = new Admin();
