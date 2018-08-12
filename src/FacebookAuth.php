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
        $fbUri = 'https://graph.facebook.com/debug_token';
        $fbUserAccessToken = $request->bearerToken();
        $fbAppToken = getenv('FB_APP_TOKEN');
        $fbQueryData = array(
            'query' => array(
                'input_token'   => $fbUserAccessToken,
                'access_token'  => $fbAppToken
            )
        );

        // Check if token was provided in request.
        if (!fbUserAccessToken) {
            throw new Exception("No access token provided.");  
        }

        try {
            $response = $guzzleClient->request('GET', $fbUri, $fbQueryData);
            $responseCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);

            //Guzzle should handle the condition below, but using for precaution.
            if ($responseCode != 200) {
                throw new Exception('Invalid token');
            }

            $appId = getenv('FB_APP_ID');
            $responseAppId = $responseData['data']['app_id'];

            //Verify the token against application registered with Facebook.
            if (strcmp($appId, $responseAppId) !== 0) {
                throw new Exception('Invalid token.');
            }


        } catch (Exception $e) {
            $responseData = json_encode(['error' => 'an error occured during authentication.']);
            $responseJSON = new Response($responseData, 401);
            $responseJSON->header('WWW-Authenticate', $e->getMessage());
            $responseJSON->header('Content-type', 'application/json');
            return $responseJSON;
        }

        return $next($request);
    }
}