<?php

namespace Ippey\BalanceOfFreeAccount\Lib;

class Admin {

	/** @var FreeeClient */
	private $freeeClient;

	public function setFreeeClient( $freeeClient ) {
		$this->freeeClient = $freeeClient;
	}

	public function setUp() {
		add_menu_page( 'Balance of Freee Account', 'Balance of Freee Account', 'manage_options', 'balance_of_freee', array(
			$this,
			'show'
		) );
	}

	public function show() {
		if ( ! empty( $_GET['code'] ) ) {
			$this->setToken( $_GET );

			return;
		}
		$accessToken  = get_option( 'bofa_access_token' );
		$refreshToken = get_option( 'bofa_refresh_token' );
		$linkStr      = 'Freee連携';
		if ( $accessToken && $refreshToken ) {
			$linkStr = 'Freee再連携';
		}
		$callbackUrl = menu_page_url( 'balance_of_freee', false );
		$url         = $this->freeeClient->getAuthorizationUrl( $callbackUrl );
		$output      = <<< EOT
<div class="form-wrap">
<a href="{$url}" class="link-text">{$linkStr}</a>
</div>
EOT;
		echo $output;
	}

	public function setToken( $get ) {
		$callbackUrl = menu_page_url( 'balance_of_freee', false );
		try {
			$result = $this->freeeClient->getAccessToken( $get['code'], $callbackUrl );
			update_option( 'bofa_access_token', $result->access_token );
			update_option( 'bofa_refresh_token', $result->refresh_token );
			update_option( 'bofa_expire', time() + $result->expires_in );

			$output = <<< "EOT"
<div class="wrap">
<p class="message">Freeeとの連携が完了しました！</p>
</div>
EOT;
		} catch ( \RuntimeException $e ) {
			$output = '<div class="wrap"><p class="error-message">Freeeとの連携に失敗しました。再度おためしください。</p></div>';
		}
		echo $output;
	}
}
