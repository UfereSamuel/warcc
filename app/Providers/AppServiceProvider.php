<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Socialite::extend('microsoft', function ($app) {
            $config = $app['config']['services.microsoft'];
            return new AzureADProvider(
                $app['request'],
                $config['client_id'],
                $config['client_secret'],
                $config['redirect'],
                $config['tenant']
            );
        });
    }
}

class AzureADProvider extends AbstractProvider implements ProviderInterface
{
    protected $tenant;

    public function __construct($request, $clientId, $clientSecret, $redirectUrl, $tenant, $guzzle = [])
    {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $guzzle);
        $this->tenant = $tenant;
    }

    protected function getAuthUrl($state)
    {
        return "https://login.microsoftonline.com/{$this->tenant}/oauth2/v2.0/authorize?" . http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => 'openid profile email',
            'response_type' => 'code',
            'state' => $state,
        ]);
    }

    protected function getTokenUrl()
    {
        return "https://login.microsoftonline.com/{$this->tenant}/oauth2/v2.0/token";
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://graph.microsoft.com/v1.0/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'name' => $user['displayName'],
            'email' => $user['mail'] ?? $user['userPrincipalName'],
        ]);
    }
}
