<?php


class Admin {

	/** @var Freee_Client */
	private $freee_client;

	/**
	 * Set freee client
	 *
	 * @param $freee_client
	 */
	public function set_freee_client( $freee_client ) {
		$this->freee_client = $freee_client;
	}

	/**
	 * Setup
	 */
	public function set_up() {
		add_menu_page( 'Balance of Freee Account', 'Balance of Freee Account', 'manage_options', 'balance_of_freee', array(
			$this,
			'show'
		) );
	}

	/**
	 * Show setting form
	 */
	public function show() {
		if ( ! empty( $_GET['code'] ) ) {
			$this->update_token( $_GET );

			return;
		}
		$access_token  = get_option( 'bofa_access_token' );
		$refresh_token = get_option( 'bofa_refresh_token' );
		$str           = 'freee連携';
		if ( $access_token && $refresh_token ) {
			$str = 'freee再連携';
		}
		$callback_url = menu_page_url( 'balance_of_freee', false );
		$url          = $this->freee_client->get_authorization_url( $callback_url );
		$output       = <<< EOT
<div class="form-wrap">
<h2>freee 連携設定</h2>
<div>
<a href="{$url}" class="button button-primary">{$str}</a>
</div>
</div>
EOT;
		echo $output;
	}

	/**
	 * Update Access/Refresh token
	 *
	 * @param $get
	 */
	public function update_token( $get ) {
		$callback_url = menu_page_url( 'balance_of_freee', false );
		try {
			$result = $this->freee_client->get_access_token( $get['code'], $callback_url );
			update_option( 'bofa_access_token', $result->access_token );
			update_option( 'bofa_refresh_token', $result->refresh_token );
			update_option( 'bofa_expire', time() + $result->expires_in );
			$url    = get_admin_url();
			$str    = __( 'ダッシュボードへ' );
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
