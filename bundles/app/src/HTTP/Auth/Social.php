<?php

namespace Project\App\HTTP\Auth;

use PHPixie\App\ORM\User;
use PHPixie\AuthSocial\Providers\OAuth as OAuthProvider;
use PHPixie\HTTP\Request;
use Project\App\ORM\User\UserRepository;
use Project\App\HTTP\Processor;
use PHPixie\Social\OAuth\User as SocialUser;

/**
 * Handles social login
 */
class Social extends Processor
{
    /**
     * Redirect the user to the appropriate external login page,
     * e.g. Twitter or Facebook
     *
     * @param Request $request HTTP request
     * @return mixed
     */
    public function defaultAction($request)
    {
        $provider = $request->attributes()->get('provider');

        // if no provider is specified redirect to the login page
        if(empty($provider)) {
            return $this->redirect('app.processor', ['processor' => 'auth']);
        }

        // Generate the login url and redirect the user
        $callbackUrl = $this->buildCallbackUrl($provider);

        $url = $this->oauthProvider()->loginUrl($provider, $callbackUrl);
        return $this->responses()->redirect($url);
    }

    /**
     * Process the callback from the external login provider.
     *
     * @param Request $request HTTP request
     * @return mixed
     */
    public function callbackAction($request)
    {
        $provider = $request->attributes()->getRequired('provider');
        $callbackUrl = $this->buildCallbackUrl($provider);
        $query = $request->query()->get();

        // Handle authorization callback
        // This will also automatically login the user if he exists in the database already
        $userData = $this->oauthProvider()->handleCallback($provider, $callbackUrl, $query);

        // If user denied the request redirect to login page
        if($userData === null) {
            return $this->redirect('app.processor', ['processor' => 'auth']);
        }

        // If the user was not automatically logged in
        // it means it is a new user and we must register him
        if($this->user() === null) {
            $user = $this->registerNewUser($userData);

            // After registering log him in
            $this->oauthProvider()->setUser($user);
        }

        return $this->redirect('app.frontpage');
    }

    /**
     * Register new user in our database from his social login.
     *
     * @param SocialUser $socialUser
     * @return mixed
     */
    protected function registerNewUser($socialUser)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->components()->orm()->repository('user');

        // extract users profile name from his social data
        $profileName =  $this->getProfileName($socialUser);

        // get the name of the field to save his social id in, e.g. twitterId, or facebookId
        $socialIdField = $userRepository->socialIdField($socialUser->providerName());

        // Create ad save the user
        $user = $userRepository->create([
            'name'         => $profileName,
            $socialIdField => $socialUser->id()
        ]);

        $user->save();
        return $user;
    }

    /**
     * Get users' profile name from his social data.
     *
     * @param SocialUser $socialUser
     * @return mixed
     * @throws \Exception If an unknown provider is used
     */
    protected function getProfileName($socialUser)
    {
        return $socialUser->loginData()->name;
    }

    /**
     * Build a callback url that the external service (e.g. Facebook)
     * will redirect the user to.
     *
     * @param $provider
     * @return string
     */
    protected function buildCallbackUrl($provider)
    {
        return $this->frameworkHttp()->generateUri('app.socialAuthCallback', [
            'provider' => $provider
        ])->__toString();
    }

    /**
     * Get the OAuth authentication provider
     *
     * @return OAuthProvider
     */
    protected function oauthProvider()
    {
        $domain = $this->components()->auth()->domain();
        return $domain->provider('social');
    }
}