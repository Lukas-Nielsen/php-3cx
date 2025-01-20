<?php
namespace ln\threecx;

use GuzzleHttp\Client as GuzzleClient;
use OTPHP\TOTP;
use Psr\Http\Message\ResponseInterface;


class Client
{
	protected Host $host;
	protected User $user;
	protected Token $token;
	protected Rest $rest;
	protected GuzzleClient $client;

	public function __construct(Host $host)
	{
		$this->host = $host;
	}

	public function setUser(User $user)
	{
		$this->user = $user;
		$this->setup();

		$mfa = strlen($this->user->mfa) > 0 ? TOTP::create($this->user->mfa)->now() : "";

		$authReponse = $this->client->post("/webclient/api/Login/GetAccessToken", [
			"json" => [
				"Username" => $this->user->username,
				"Password" => $this->user->password,
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

	public function setRest(Rest $rest)
	{
		$this->rest = $rest;
		$this->setup();

		$authReponse = $this->client->post("/connect/token", [
			"form_params" => [
				"client_id" => $this->rest->clientId,
				"client_secret" => $this->rest->clientSecret,
				"grant_type" => "client_credentials",
			]
		]);

		if ($authReponse->getStatusCode() !== 200) {
			throw new Generic("error during client_credentials login");
		}

		$data = json_decode($authReponse->getBody()->__tostring(), true);

		$this->token = new Token(
			$data["token_type"],
			time() + $data["expires_in"] * 60 * 1000,
			$data["access_token"],
			$data["refresh_token"]
		);
	}

	public function setToken(Token $token)
	{
		$this->token = $token;
		$this->setup();

		if ($this->token->expires < time()) {
			$authReponse = $this->client->post("/connect/token", [
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

	private function setup()
	{
		$this->client = new GuzzleClient(
			[
				"base_uri" => "https://{$this->host->fqdn}:{$this->host->port}",
				"debug" => $this->host->debug,
				"headers" => [
					"User-Agent" => "php-threecx"
				]
			]
		);
	}

	public function getToken(): Token
	{
		return $this->token;
	}

	public function get(string $uri, array $query = []): ResponseInterface
	{
		return $this->client->get($uri, [
			"headers" => [
				"Authorization" => "{$this->token->tokenType} {$this->token->accessToken}"
			],
			"query" => $query,
		]);
	}

	public function delete(string $uri, array $query = []): ResponseInterface
	{
		return $this->client->delete($uri, [
			"headers" => [
				"Authorization" => "{$this->token->tokenType} {$this->token->accessToken}"
			],
			"query" => $query,
		]);
	}

	public function post(string $uri, array $payload, array $query = []): ResponseInterface
	{
		return $this->client->post($uri, [
			"headers" => [
				"Authorization" => "{$this->token->tokenType} {$this->token->accessToken}"
			],
			"query" => $query,
			"json" => $payload,
		]);
	}

	public function put(string $uri, array $payload, array $query = []): ResponseInterface
	{
		return $this->client->put($uri, [
			"headers" => [
				"Authorization" => "{$this->token->tokenType} {$this->token->accessToken}"
			],
			"query" => $query,
			"json" => $payload,
		]);
	}

	public function patch(string $uri, array $payload, array $query = []): ResponseInterface
	{
		return $this->client->patch($uri, [
			"headers" => [
				"Authorization" => "{$this->token->tokenType} {$this->token->accessToken}"
			],
			"query" => $query,
			"json" => $payload,
		]);
	}
}
