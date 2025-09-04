<?php

namespace App\Services\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class UaePassProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopes = ['profile', 'urn:uae:digitalid:profile:general'];
    protected string $authorizeUrl;
    protected string $tokenUrl;
    protected string $userinfoUrl;

    public function __construct($request, $clientId, $clientSecret, $redirectUrl)
    {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl);
        $cfg = config('services.uaepass');
        $this->authorizeUrl = $cfg['authorize_url'];
        $this->tokenUrl     = $cfg['token_url'];
        $this->userinfoUrl  = $cfg['userinfo_url'];
    }

    protected function getAuthUrl($state){ return $this->buildAuthUrlFromBase($this->authorizeUrl, $state); }
    protected function getTokenUrl(){ return $this->tokenUrl; }

    protected function getUserByToken($token)
    {
        $resp = $this->getHttpClient()->get($this->userinfoUrl, [
            'headers' => ['Authorization' => 'Bearer '.$token]
        ]);
        return json_decode((string) $resp->getBody(), true);
    }

    protected function mapUserToObject(array $u)
    {
        // Map fields per UAE PASS userinfo response
        $email = $u['email'] ?? ($u['emailAddress'] ?? null);
        $name  = trim(($u['firstnameEN'] ?? '').' '.($u['lastnameEN'] ?? '')) ?: ($u['fullnameEN'] ?? 'UAE PASS User');
        return (new User())->setRaw($u)->map([
            'id'       => $u['sub'] ?? ($u['uuid'] ?? null),
            'nickname' => null,
            'name'     => $name,
            'email'    => $email,
            'avatar'   => null,
        ]);
    }

    protected function getCodeFields($state = null)
    {
        return array_merge(parent::getCodeFields($state), [
           
        ]);
    }
}
