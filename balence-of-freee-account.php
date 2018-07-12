<?php
/**
Plugin Name:  Balance of freee account
Plugin URI:   https://developer.wordpress.org/plugin/balance-of-freee-account/
Description:  Show your balance of freee account on dashboard.
Version:      0.1
Author:       Ippei Sumida
Author URI:   https://unplat.info
License:      GPL-2.0-or-later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html

"Balance of freee account" is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

"Balance of freee account" is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with "Balance of freee account". If not, see https://unplat.info/.
 */

namespace Ippey\BalanceOfFreeAccount;

define( 'BOFA_CLIENT_ID', '2527c2969cf7a97ea446ca0e3357fdd40bc8afd8812fccbe6f79b2a76803150c' );
define( 'BOFA_CLIENT_SECRET', 'c955a36efca2beebf4e1e1193aa24b82e8788b71cb443e5eb3c190a1e500e515' );

require_once( __DIR__ . '/lib/DashBoard.php' );
require_once( __DIR__ . '/lib/Admin.php' );
require_once( __DIR__ . '/lib/FreeeClient.php' );

// FreeeClient
use Ippey\BalanceOfFreeAccount\Lib\FreeeClient;

$freeeClient = new FreeeClient( BOFA_CLIENT_ID, BOFA_CLIENT_SECRET );

// DashBoard
use Ippey\BalanceOfFreeAccount\Lib\DashBoard;

$dashboard = new DashBoard();
$dashboard->setFreeeClient( $freeeClient );
add_action( 'wp_dashboard_setup', array( $dashboard, 'setUp' ) );

// Setting
use Ippey\BalanceOfFreeAccount\Lib\Admin;

$admin = new Admin();
$admin->setFreeeClient( $freeeClient );
add_action( 'admin_menu', array( $admin, 'setUp' ) );

