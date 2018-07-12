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
		$linkStr      = 'freee連携';
		if ( $accessToken && $refreshToken ) {
			$linkStr = 'freee再連携';
		}
		$callbackUrl = menu_page_url( 'balance_of_freee', false );
		$url         = $this->freeeClient->getAuthorizationUrl( $callbackUrl );
		$output      = <<< EOT
<div class="form-wrap">
<h2>freee 連携設定</h2>
<div>
<a href="{$url}" class="button button-primary">{$linkStr}</a>
</div>
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
			$url = get_admin_url();
			$str = __('ダッシュボードへ');
			$output = <<< "EOT"
<div class="wrap">
<div class="message">freeeとの連携が完了しました！</div>
<a href="{$url}" class="button button-primary">{$str}</a>
</div>
EOT;
		} catch ( \RuntimeException $e ) {
			$output = '<div class="wrap"><p class="error-message">freeeとの連携に失敗しました。再度おためしください。</p></div>';
		}
		echo $output;
	}
}
