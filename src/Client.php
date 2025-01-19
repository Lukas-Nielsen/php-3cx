<?php
namespace ln\threecx;

use GuzzleHttp\Client as gClient;
use OTPHP\TOTP;
use Psr\Http\Message\ResponseInterface;


class Client
{
	protected Config $config;
	protected Token $token;
	protected gCLient $rest;
	protected bool $tokenAuth = false;

	public function __construct()
	{
		$this->config = new Config("", "", "");
		$this->token = new Token("", 0, "", "");
	}

	public function setConfig(Config $config)
	{
		$this->config = $config;
		$this->setup();
	}

	public function setToken(Token $token)
	{
		$this->token = $token;
		$this->setup();
	}

	private function setup()
	{
		$this->rest = new gClient(
			[
				"base_uri" => "https://{$this->config->fqdn}:{$this->config->port}/xapi/v1",
				"debug" => $this->config->debug,
				"headers" => [
					"User-Agent" => "php-threecx"
				]
			]
		);

		if (
			strlen($this->token->tokenType) == 0 ||
			strlen($this->token->accessToken) == 0 ||
			strlen($this->token->refreshToken) == 0 ||
			$this->token->expires == 0
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

			$this->token = new Token(
				$data["token_type"],
				time() + $data["expires_in"] * 60 * 1000,
				$data["access_token"],
				$data["refresh_token"]
			);
		}

		if ($this->token->expires < time()) {
			$authReponse = $this->rest->post("https://{$this->config->fqdn}:{$this->config->port}/connect/token", [
				"form_params" => [
					"client_id" => "php-3cx",
					"grant_type" => "refresh_token",
					"refresh_token" => $this->token->refreshToken
				]
			]);

			if ($authReponse->getStatusCode() !== 200) {
				throw new Generic("error during token refresh");

			}

			$data = json_decode($authReponse->getBody()->__tostring(), true);

			$this->token = new Token(
				$data["token_type"],
				time() + $data["expires_in"] * 60 * 1000,
				$data["access_token"],
				$data["refresh_token"]
			);
		}
	}

	public function getToken(): Token
	{
		return $this->token;
	}

	public function get(string $uri, array $query = []): ResponseInterface
	{
		return $this->rest->get($uri, [
			"headers" => [
				"Authorization" => "{$this->token->tokenType} {$this->token->accessToken}"
			],
			"query" => $query,
		]);
	}

	public function delete(string $uri, array $query = []): ResponseInterface
	{
		return $this->rest->delete($uri, [
			"headers" => [
				"Authorization" => "{$this->token->tokenType} {$this->token->accessToken}"
			],
			"query" => $query,
		]);
	}

	public function post(string $uri, array $payload, array $query = []): ResponseInterface
	{
		return $this->rest->post($uri, [
			"headers" => [
				"Authorization" => "{$this->token->tokenType} {$this->token->accessToken}"
			],
			"query" => $query,
			"json" => $payload,
		]);
	}

	public function put(string $uri, array $payload, array $query = []): ResponseInterface
	{
		return $this->rest->put($uri, [
			"headers" => [
				"Authorization" => "{$this->token->tokenType} {$this->token->accessToken}"
			],
			"query" => $query,
			"json" => $payload,
		]);
	}

	public function patch(string $uri, array $payload, array $query = []): ResponseInterface
	{
		return $this->rest->patch($uri, [
			"headers" => [
				"Authorization" => "{$this->token->tokenType} {$this->token->accessToken}"
			],
			"query" => $query,
			"json" => $payload,
		]);
	}
}
