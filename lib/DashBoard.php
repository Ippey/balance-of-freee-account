<?php

namespace Ippey\BalanceOfFreeAccount\Lib;


class DashBoard {

	/** @var FreeeClient */
	private $freeeClient;

	public function setFreeeClient( $freeeClient ) {
		$this->freeeClient = $freeeClient;
	}

	public function setUp() {
		wp_add_dashboard_widget( 'balance_of_freee_account_widget', __( 'freee 残高' ), array( $this, 'show' ) );
		wp_register_style( 'bofa_style', plugins_url( '../css/bofa.css', __FILE__ ) );
		wp_enqueue_style( 'bofa_style' );
	}

	public function show() {
		$accessToken  = get_option( 'bofa_access_token' );
		$refreshToken = get_option( 'bofa_refresh_token' );
		if ( empty( $accessToken ) ) {
			$this->showNotAvailable();

			return;
		}
		try {
			if ( $this->freeeClient->validAccessToken( get_option( 'bofa_access_token' ), get_option( 'bofa_expire' ) ) == false ) {
				$result = $this->freeeClient->refreshToken( $refreshToken );
				update_option( 'bofa_access_token', $result->access_token );
				update_option( 'bofa_expire', time() + $result->expires_in );
			}
			$user      = $this->freeeClient->getUser( $accessToken );
			$companies = $user->companies;
			$output    = '<div class="widget bofa_widget">';
			foreach ( $companies as $company ) {
				$output  .= '<h3>' . $company->display_name . '</h3><table><tr><th>口座</th><th>残高</th></tr>';
				$wallets = $this->freeeClient->getWalletable( $accessToken, $company->id );
				foreach ( $wallets as $wallet ) {
					if($wallet->walletable_balance < 0) {
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
		$settingUrl = menu_page_url( 'balance_of_freee', false );;
		$output = <<<EOT
<div class="widget bofa_widget">
<div class="error-div">
<p class="error-message">freeeの設定がされていません。</p>
<a href="{$settingUrl}" class="button button-primary">freee設定画面へ</a>
</div>
</div>
EOT;
		echo $output;
	}
}
