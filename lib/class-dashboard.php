<?php


class Dashboard {

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
		wp_add_dashboard_widget( 'balance_of_freee_account_widget', __( 'freee 残高' ), array( $this, 'show' ) );
		wp_register_style( 'bofa_style', plugins_url( '../css/bofa.css', __FILE__ ) );
		wp_enqueue_style( 'bofa_style' );
	}

	/**
	 * Show dashboard
	 */
	public function show() {
		$access_token  = get_option( 'bofa_access_token' );
		$refresh_token = get_option( 'bofa_refresh_token' );
		if ( empty( $access_token ) ) {
			$this->showNotAvailable();

			return;
		}
		try {
			if ( $this->freee_client->valid_access_token( get_option( 'bofa_access_token' ), get_option( 'bofa_expire' ) ) == false ) {
				$result = $this->freee_client->refresh_token( $refresh_token );
				update_option( 'bofa_access_token', $result->access_token );
				update_option( 'bofa_expire', time() + $result->expires_in );
			}
			$user      = $this->freee_client->get_user( $access_token );
			$companies = $user->companies;
			$output    = '<div class="widget bofa_widget">';
			foreach ( $companies as $company ) {
				$output  .= '<h3>' . $company->display_name . '</h3><table><tr><th>口座</th><th>残高</th></tr>';
				$wallets = $this->freee_client->get_walletable( $access_token, $company->id );
				foreach ( $wallets as $wallet ) {
					if ( $wallet->walletable_balance < 0 ) {
						$output .= '<tr><td>' . $wallet->name . '</td><td class="money minus">' . number_format( $wallet->walletable_balance ) . '円</td></tr>';
					} else {
						$output .= '<tr><td>' . $wallet->name . '</td><td class="money">' . number_format( $wallet->walletable_balance ) . '円</td></tr>';
					}
				}
				$output .= '</table>';
			}
			$output .= '</div>';
		} catch ( \RuntimeException $e ) {
			$output = '<div class="wrap"><p class="error-message">データの取得に失敗しました。</p></div>';
		}
		echo $output;
	}

	public function showNotAvailable() {
		$setting_url = menu_page_url( 'balance_of_freee', false );
		$output      = <<<EOT
<div class="widget bofa_widget">
<div class="error-div">
<p class="error-message">freeeの設定がされていません。</p>
<a href="{$setting_url}" class="button button-primary">freee設定画面へ</a>
</div>
</div>
EOT;
		echo $output;
	}
}
