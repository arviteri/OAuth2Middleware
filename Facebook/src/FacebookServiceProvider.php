<?php

namespace OAuth2Middleware\Facebook;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class FacebookServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        //
        $router->aliasMiddleware('fb.auth', 'OAuth2Middleware\Facebook\FacebookAuth::class');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
