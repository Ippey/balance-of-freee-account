<?php

namespace Ippey\BalanceOfFreeAccount\Lib;


class FreeeClient {
	private $domain = 'https://api.freee.co.jp';
	private $clientId;
	private $clientSecret;

	public function __construct( $clientId, $clientSecret ) {
		$this->clientId     = $clientId;
		$this->clientSecret = $clientSecret;
	}

	public function getHeaders( $accessToken, $params = [] ) {
		$headers = array_merge( [
			'Authorization' => 'Bearer ' . $accessToken
		], $params );

		return $headers;
	}

	public function getAuthorizationUrl( $callbackUrl ) {
		$url = 'https://secure.freee.co.jp/oauth/authorize?client_id=' . urlencode( $this->clientId ) . '&redirect_uri=' . urlencode( $callbackUrl ) . '&response_type=code';

		return $url;
	}

	public function getAccessToken( $code, $callbackUrl ) {
		$url      = $this->domain . '/oauth/token';
		$params   = [
			'grant_type'    => 'authorization_code',
			'client_id'     => $this->clientId,
			'client_secret' => $this->clientSecret,
			'code'          => $code,
			'redirect_uri'  => $callbackUrl,
		];
		$response = wp_remote_post( $url, [
			'body' => $params,
		] );

		if ( $response instanceof \WP_Error ) {
			return false;
		}
		$json = json_decode( $response['body'] );

		return $json;
	}

	public function validAccessToken( $accessToken, $expire ) {
		$now = time();
		if ( $now < $expire ) {
			return true;
		}

		return false;
	}

	public function refreshToken( $refreshToken ) {
		$url    = $this->domain . '/oauth/token';
		$params = [
			'grant_type'    => 'refresh_token',
			'client_id'     => $this->clientId,
			'client_secret' => $this->clientSecret,
			'refresh_token' => $refreshToken,
		];

		$response = wp_remote_post( $url, [
			'body' => $params,
		] );

		if ( $response instanceof \WP_Error || $response['response']['code'] != 200 ) {
			return false;
		}
		$json = json_decode( $response['body'] );

		return $json;
	}

	public function getUser( $accessToken ) {
		$url      = $this->domain . '/api/1/users/me?companies=true';
		$headers  = $this->getHeaders( $accessToken, [ 'Content-Type' => 'application/json' ] );
		$response = wp_remote_get( $url, [
			'headers' => $headers,
		] );

		if ( $response instanceof \WP_Error || $response['response']['code'] != 200 ) {
			return false;
		}
		$json = json_decode( $response['body'] );

		return $json->user;
	}

	public function getWalletable( $accessToken, $companyId ) {
		$url         = $this->domain . '/api/1/walletables';
		$headers     = $this->getHeaders( $accessToken, [ 'Content-Type' => 'application/json' ] );
		$params      = [
			'company_id'   => $companyId,
			'with_balance' => 'true'
		];
		$queryString = http_build_query( $params );
		$response    = wp_remote_get( $url . '?' . $queryString, [
			'headers' => $headers,
		] );
		if ( $response instanceof \WP_Error || $response['response']['code'] != 200 ) {
			return false;
		}
		$json = json_decode( $response['body'] );

		return $json->walletables;
	}
}
