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

define( 'BOFA_CLIENT_ID', '2527c2969cf7a97ea446ca0e3357fdd40bc8afd8812fccbe6f79b2a76803150c' );
define( 'BOFA_CLIENT_SECRET', 'c955a36efca2beebf4e1e1193aa24b82e8788b71cb443e5eb3c190a1e500e515' );

require_once( __DIR__ . '/lib/DashBoard.php' );
require_once( __DIR__ . '/lib/Admin.php' );
require_once( __DIR__ . '/lib/FreeeClient.php' );

// DashBoard
use Ippey\BalanceOfFreeAccount\Lib\DashBoard;

$dashboard = new DashBoard();
add_action( 'wp_dashboard_setup', [ $dashboard, 'setUp' ] );

// Setting
use Ippey\BalanceOfFreeAccount\Lib\Admin;

$admin = new Admin();
add_action( 'admin_menu', [ $admin, 'setUp' ] );

