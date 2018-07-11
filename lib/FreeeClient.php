<?php

namespace Ippey\BalanceOfFreeAccount\Lib;


class FreeeClient {
	private $domain = 'https://api.freee.co.jp';
	private $clientId;
	private $clientSecret;

	/**
	 * FreeeClient constructor.
	 *
	 * @param $clientId
	 * @param $clientSecret
	 */
	public function __construct( $clientId, $clientSecret ) {
		$this->clientId     = $clientId;
		$this->clientSecret = $clientSecret;
	}


	/**
	 * create HTTP Headers
	 *
	 * @param $accessToken
	 * @param array $params
	 *
	 * @return array
	 */
	public function createHeaders( $accessToken, $params = array() ) {
		$headers = array_merge( array(
			'Authorization' => 'Bearer ' . $accessToken
		), $params );

		return $headers;
	}

	/**
	 * get oauth2 authorization url
	 *
	 * @param $callbackUrl
	 *
	 * @return string
	 */
	public function getAuthorizationUrl( $callbackUrl ) {
		$url = 'https://secure.freee.co.jp/oauth/authorize?client_id=' . urlencode( $this->clientId ) . '&redirect_uri=' . urlencode( $callbackUrl ) . '&response_type=code';

		return $url;
	}

	/**
	 * get access token
	 *
	 * @param $code
	 * @param $callbackUrl
	 *
	 * @return array|mixed|object
	 */
	public function getAccessToken( $code, $callbackUrl ) {
		$url      = $this->domain . '/oauth/token';
		$params   = array(
			'grant_type'    => 'authorization_code',
			'client_id'     => $this->clientId,
			'client_secret' => $this->clientSecret,
			'code'          => $code,
			'redirect_uri'  => $callbackUrl,
		);
		$response = wp_remote_post( $url, array(
			'body' => $params,
		) );

		$this->checkResponse( $response );
		$json = json_decode( $response['body'] );

		return $json;
	}

	/**
	 * validate access token
	 *
	 * @param $accessToken
	 * @param $expire
	 *
	 * @return bool
	 */
	public function validAccessToken( $accessToken, $expire ) {
		if ( empty( $accessToken ) ) {
			return false;
		}
		$now = time();
		if ( $now < $expire ) {
			return true;
		}

		return false;
	}

	/**
	 * refresh token
	 *
	 * @param $refreshToken
	 *
	 * @return array|mixed|object
	 */
	public function refreshToken( $refreshToken ) {
		$url    = $this->domain . '/oauth/token';
		$params = array(
			'grant_type'    => 'refresh_token',
			'client_id'     => $this->clientId,
			'client_secret' => $this->clientSecret,
			'refresh_token' => $refreshToken,
		);

		$response = wp_remote_post( $url, array(
			'body' => $params,
		) );

		$this->checkResponse( $response );
		$json = json_decode( $response['body'] );

		return $json;
	}

	/**
	 * get user
	 *
	 * @param $accessToken
	 *
	 * @return mixed
	 */
	public function getUser( $accessToken ) {
		$url      = $this->domain . '/api/1/users/me?companies=true';
		$headers  = $this->createHeaders( $accessToken, array( 'Content-Type' => 'application/json' ) );
		$response = wp_remote_get( $url, array(
			'headers' => $headers,
		) );

		$this->checkResponse( $response );
		$json = json_decode( $response['body'] );

		return $json->user;
	}

	/**
	 * get walletable
	 *
	 * @param $accessToken
	 * @param $companyId
	 *
	 * @return mixed
	 */
	public function getWalletable( $accessToken, $companyId ) {
		$url         = $this->domain . '/api/1/walletables';
		$headers     = $this->createHeaders( $accessToken, array( 'Content-Type' => 'application/json' ) );
		$params      = array(
			'company_id'   => $companyId,
			'with_balance' => 'true'
		);
		$queryString = http_build_query( $params );
		$response    = wp_remote_get( $url . '?' . $queryString, array(
			'headers' => $headers,
		) );
		$this->checkResponse( $response );
		$json = json_decode( $response['body'] );

		return $json->walletables;
	}

	/**
	 * @param $response
	 *
	 * @return bool
	 */
	protected function checkResponse( $response ) {
		if ( $response instanceof \WP_Error ) {
			throw new \RuntimeException( $response->get_error_message() );
		} else if ( $response['response']['code'] != 200 ) {
			throw new \RuntimeException( $response['response']['body'], $response['response']['code'] );
		}

		return true;
	}
}
