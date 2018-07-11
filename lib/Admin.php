<?php

namespace Ippey\BalanceOfFreeAccount\Lib;

class Admin {
	public function __construct() {
	}

	public function setUp() {
		add_menu_page( 'Balance of Freee Account', 'Balance of Freee Account', 'manage_options', 'balance_of_freee', [
			$this,
			'show'
		] );
	}

	public function show() {
		if ( ! empty( $_GET['code'] ) ) {
			$this->setToken( $_GET );

			return;
		}
		$freee        = new FreeeClient( BOFA_CLIENT_ID, BOFA_CLIENT_SECRET );
		$accessToken  = get_option( 'bofa_access_token' );
		$refreshToken = get_option( 'bofa_refresh_token' );
		$linkStr      = 'Freee連携';
		if ( $accessToken && $refreshToken ) {
			$linkStr = 'Freee再連携';
		}
		$callbackUrl = menu_page_url( 'balance_of_freee', false );
		$url         = $freee->getAuthorizationUrl( $callbackUrl );
		$output      = <<< EOT
<div class="wrap">
<a href="{$url}">{$linkStr}</a>
</div>
EOT;
		echo $output;
	}

	public function setToken( $get ) {
		$freee       = new FreeeClient( BOFA_CLIENT_ID, BOFA_CLIENT_SECRET );
		$callbackUrl = menu_page_url( 'balance_of_freee', false );
		$result      = $freee->getAccessToken( $get['code'], $callbackUrl );
		update_option( 'bofa_access_token', $result->access_token );
		update_option( 'bofa_refresh_token', $result->refresh_token );
		update_option( 'bofa_expire', time() + $result->expires_in );

		$output = <<< "EOT"
<div class="wrap">
Freeeとの連携が完了しました！
</div>
EOT;
		echo $output;
	}
}
