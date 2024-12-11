<?php

namespace App\Services\Discord;

use Illuminate\Support\Facades\Http;

class Discord
{
    protected string $apiEndpoint;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->apiEndpoint = 'https://discord.com/api/v10';
        $this->clientId = config('services.discord.client_id');
        $this->clientSecret = config('services.discord.client_secret');
    }

    /**
     * Obtenir un access token depuis l'API Discord
     * @return string
     * @throws \Exception
     */
    protected function getAccessToken(bool $botToken = false): string
    {
        if($botToken) {
            if(\Session::has('discord_bot_access_token')) {
                return \Session::get('discord_bot_access_token');
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post("{$this->apiEndpoint}/oauth2/token", [
                    "grant_type" => "client_credentials",
                    "scope" => "identify connections guilds email guilds.members.read bot"
                ]);

            if($response->failed()) {
                throw new \Exception('Erreur lors de l\'obtention du token : ' . $response->body());
            }

            $data = $response->json();
            $accessToken = $data['access_token'];

            \Session::put('discord', $accessToken);
        } else {
            if (\Session::has('discord_access_token')) {
                return \Session::get("discord_access_token");
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post("{$this->apiEndpoint}/oauth2/token", [
                    "grant_type" => "client_credentials",
                    "scope" => "identify connections guilds email guilds.members.read"
                ]);

            if($response->failed()) {
                throw new \Exception('Erreur lors de l\'obtention du token : ' . $response->body());
            }

            $data = $response->json();
            $accessToken = $data['access_token'];

            \Session::put('discord_bot_access_token', $accessToken);
        }
        return $accessToken;

    }

    /**
     * Effectuer une requête à l'API Discord
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->{strtolower($method)}("{$this->apiEndpoint}{$endpoint}", $data);

        if ($response->failed()) {
            throw new \Exception('Erreur lors de la requête API : ' . $response->body());
        }

        return $response->json();
    }

    public function makeRequestBot(string $method, string $endpoint, array $data = []): array
    {
        $accessToken = $this->getAccessToken(true);

        $response = Http::withHeaders([
            'Authorization' => "Bot {$accessToken}",
        ])
            ->{strtolower($method)}("{$this->apiEndpoint}{$endpoint}", $data);

        if ($response->failed()) {
            throw new \Exception('Erreur lors de la requête API : ' . $response->body());
        }

        return $response->json();
    }

    public function getAvatarLink()
    {
        try {
            $info = $this->makeRequest('GET', '/users/@me');
        }catch (\Exception $exception) {
            return $exception->getMessage();
        }

        return "https://cdn.discordapp.com/avatars/{$info['id']}/{$info['avatar']}.png";
    }

    public function getUserGuilds()
    {
        try {
            $call = $this->makeRequest('GET', '/users/@me/guilds');
            return collect($call)->where('id', config('services.discord.server_id'))->first();
        }  catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
