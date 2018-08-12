<?php

namespace OAuth2Middleware\Facebook;

use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Closure;
use Exception;

class FacebookAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $guzzleClient = new Client();

        // Facebook data for verification.
        $fbAppId = getenv('FB_APP_ID');
        $fbAppToken = getenv('FB_APP_TOKEN');
        $fbUri = 'https://graph.facebook.com/debug_token';
        $fbUserAccessToken = $request->bearerToken();
        $fbQueryData = array(
            'query' => array(
                'input_token'   => $fbUserAccessToken,
                'access_token'  => $fbAppToken
            )
        );

        try {

            // Check for .env FB_APP_ID
            if (!$fbAppId) {
                throw new Exception('no app id provided.');
            }

            // Check for .env FB_APP_TOKEN
            if (!$fbAppToken) {
                throw new Exception('no app token provided.');
            }

            // Check if token was provided in request.
            if (!$fbUserAccessToken) {
                throw new Exception("no access token provided.");  
            }

            $response = $guzzleClient->request('GET', $fbUri, $fbQueryData);
            $responseCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);
            $responseAppId = $responseData['data']['app_id'];

            //Guzzle should handle the condition below, but using for precaution.
            if ($responseCode != 200) {
                throw new Exception('invalid token');
            }

            //Verify the token against application registered with Facebook.
            if (strcmp($fbAppId, $responseAppId) !== 0) {
                throw new Exception('invalid token.');
            }


        } catch (Exception $e) {
            $responseData = json_encode(['error' => $e->getMessage()]);
            $responseJSON = new Response($responseData, 401);
            $responseJSON->header('WWW-Authenticate', 'error');
            $responseJSON->header('Content-type', 'application/json');
            return $responseJSON;
        }

        return $next($request);
    }
}