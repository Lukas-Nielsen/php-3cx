<?php
namespace ln\threecx;

use GuzzleHttp\Client as gClient;
use OTPHP\TOTP;
use Psr\Http\Message\ResponseInterface;


class Client
{
	protected Config $config;
	protected gCLient $rest;
	protected bool $tokenAuth = false;

	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->rest = new gClient(
			[
				"base_uri" => "https://{$this->config->fqdn}:{$this->config->port}/xapi/v1",
				"debug" => $this->config->debug
			]
		);

		if (
			strlen($this->config->token->tokenType) == 0 ||
			strlen($this->config->token->accessToken) == 0 ||
			strlen($this->config->token->refreshToken) == 0 ||
			$this->config->token->expires == 0
		) {

			if (strlen($this->config->user) == 0) {
				throw new MissingUser();

			}

			if (strlen($this->config->password) == 0) {
				throw new MissingPassword();

			}
		} else {
			$this->tokenAuth = true;
		}


		$mfa = strlen($this->config->mfa) > 0 ? TOTP::create($this->config->mfa)->now() : "";

		if (!$this->tokenAuth) {
			$authReponse = $this->rest->post("https://{$this->config->fqdn}:{$this->config->port}/webclient/api/Login/GetAccessToken", [
				"json" => [
					"Username" => $this->config->user,
					"Password" => $this->config->password,
					"SecurityCode" => $mfa,
				]
			]);

			if ($authReponse->getStatusCode() !== 200) {
				throw new Generic("error during login");

			}
			$data = json_decode($authReponse->getBody()->__tostring(), true)["Token"];

			$token = new Token();
			$token->accessToken = $data["access_token"];
			$token->refreshToken = $data["refresh_token"];
			$token->tokenType = $data["token_type"];
			$token->expires = time() + $data["expires_in"] * 60 * 1000;
			$this->config->token = $token;
		}

		if ($this->config->token->expires < time()) {
			$authReponse = $this->rest->post("https://{$this->config->fqdn}:{$this->config->port}/connect/token", [
				"form_params" => [
					"client_id" => "php-3cx",
					"grant_type" => "refresh_token",
					"refresh_token" => $this->config->token->refreshToken
				]
			]);

			if ($authReponse->getStatusCode() !== 200) {
				throw new Generic("error during token refresh");

			}

			$data = json_decode($authReponse->getBody()->__tostring(), true);

			$token = new Token();
			$token->accessToken = $data["access_token"];
			$token->refreshToken = $data["refresh_token"];
			$token->tokenType = $data["token_type"];
			$token->expires = time() + $data["expires_in"] * 60 * 1000;
			$this->config->token = $token;
		}
	}

	public function getToken(): Token
	{
		return $this->config->token;
	}

	public function get(string $uri, array $query = []): ResponseInterface
	{
		return $this->rest->get($uri, [
			"query" => $query,
		]);
	}

	public function delete(string $uri, array $query = []): ResponseInterface
	{
		return $this->rest->delete($uri, [
			"query" => $query,
		]);
	}

	public function post(string $uri, array $payload, array $query = []): ResponseInterface
	{
		return $this->rest->post($uri, [
			"query" => $query,
			"json" => $payload,
		]);
	}

	public function put(string $uri, array $payload, array $query = []): ResponseInterface
	{
		return $this->rest->put($uri, [
			"query" => $query,
			"json" => $payload,
		]);
	}

	public function patch(string $uri, array $payload, array $query = []): ResponseInterface
	{
		return $this->rest->patch($uri, [
			"query" => $query,
			"json" => $payload,
		]);
	}
}
