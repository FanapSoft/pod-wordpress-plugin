<?php

use GuzzleHttp\Client;

require_once("CustomException.php");

class ApiRequestHandler
{
    const SERVER_CONNECTION_ERROR = 800;

    public static function Request($baseUri, $method, $relativeUri, $options, $restFull = false)
    {
        echo 'fisrt';
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $baseUri,
            // You can set any number of default request options.
            'timeout'  => 30,
        ]);
        try {
            $response = $client->request($method, $relativeUri, $options);
        } catch (ClientException $e) {
            // echo Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $response = $e->getResponse();

                $code = $response->getStatusCode();
                $message = $response->getBody()->getContents();
            } else {
                $code = self::SERVER_CONNECTION_ERROR;
                $message  = "Connection Interrupt! please try again later.";
            }
            throw new Exception($message, $code);
        }

        $result = json_decode($response->getBody()->getContents(), true);
        // var_dump($result);
        //        $code = $response->getStatusCode();


        if (!$restFull) {
            if (isset($result["hasError"]) && $result["hasError"]) {
                throw new CustomException($result["message"], $result["errorCode"], null, $result);
            }
        }

        return $result;
    }
}
