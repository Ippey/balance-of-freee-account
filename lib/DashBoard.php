<?php

namespace Ippey\BalanceOfFreeAccount\Lib;


class DashBoard {
	public function setUp() {
		wp_add_dashboard_widget( 'balance_of_freee_account_widget', __( 'Freee 残高' ), array( $this, 'show' ) );
	}

	public function show() {
		$freee = new FreeeClient( BOFA_CLIENT_ID, BOFA_CLIENT_SECRET );
		if ( $freee->validAccessToken( get_option( 'bofa_access_token' ), get_option( 'bofa_expire' ) ) == false ) {
			$result = $freee->refreshToken( get_option( 'bofa_refresh_token' ) );
			update_option( 'bofa_access_token', $result->access_token );
			update_option( 'bofa_expire', time() + $result->expires_in );
		}
		$accessToken = get_option( 'bofa_access_token' );
		$user        = $freee->getUser( $accessToken );
		$companies   = $user->companies;
		$output      = '<div class="widget">';
		foreach ( $companies as $company ) {
			$output  .= '<h3>' . $company->display_name . '</h3><table class="links-table"><tr><th>口座</th><th>残高</th></tr>';
			$wallets = $freee->getWalletable( $accessToken, $company->id );
			foreach ( $wallets as $wallet ) {
				$output .= '<tr><td>' . $wallet->name . '</td><td class="right">' . number_format( $wallet->walletable_balance ) . '円</td></tr>';
			}
			$output .= '</table>';
		}
		$output .= '</div>';
		echo $output;
	}
}
