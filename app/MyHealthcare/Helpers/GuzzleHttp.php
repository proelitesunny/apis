<?php

namespace App\MyHealthcare\Helpers;

/**
** Class to send request to diffrent HIS.
*/
class GuzzleHttp
{
	
	/**
	** 
	** @param method GET/POST
	** @param uri API URL
	** @param additonal extra get or post parameter
	** @return response array
	*/
	public static function sendRequest($method, $uri, $additional = array())
	{
		$options = [
            'headers' => ['Authorization' => config('api.his.api_key')], 
            'timeout' => config('api.his.timeout')
        ];

        $options = $options + $additional;

        // Create a client with a base URI
		$client = new \GuzzleHttp\Client(['base_uri' => config('api.his.base_uri')]);
            
		// Prepare request object
		$request = new \GuzzleHttp\Psr7\Request($method, $uri);

		$result = [];

		try {

			// Send a request to his
            $response = $client->send($request, $options);
           
            // Get Data 
            $result['code'] = $response->getStatusCode();
            $result['headers'] = $response->getHeaders();
            $result['response'] = $response->getBody()->getContents();

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new \App\Exceptions\GuzzleHttpException($e->getMessage(), null, null, '');
            // throw new \App\Exceptions\GuzzleHttpException(trans('errors.GUZZLE_101'), null, null, '');
        }

        return $result;
	}
}