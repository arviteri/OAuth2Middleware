# OAuth2Middleware-Facebook
Laravel package for securing API's with Facebook's OAuth2 service.<br />

OAuth2Middleware-Facebook works by verifying that each request sent to your server has an authorization token in the request `Authentication` header. After verifying that there is an authorization token available, it validates it against Facebook's Graph API. This validation has two steps; verifying that the token given belongs to Facebook, and verifying that the token belongs to YOUR Facebook application.

You will need to setup an application in Facebook as well as retrieve a non-expiring OAuth2 application token. (See configuration)

## Installation

Install with composer: `composer require oauth2middleware/facebook`

## Configuration 
The middleware requires two environment variables. (`FB_APP_TOKEN` and  `FB_APP_ID`)<br />
The `FB_APP_TOKEN` environment variable refers to a non-expiring application token which belongs to YOUR Facebook application.
##### Follow the instructions <a href="https://developers.facebook.com/docs/facebook-login/access-tokens/#apptokens">here</a> to retrieve a Facebook app token.

The `FB_APP_ID` is your Facebook application's app Id. This can easily be found in the Facebook developer console.

## Usage 

##### The middleware alias is 'fb.auth'.
To use, use the `middleware` function on the routes that you want to protect.<br />

For example...

    Route::middleware('fb.auth')->get('/secure_route', function (Request $request) {
        // Your code...
        
        return { your_response_variable };
    });
