<?php

namespace App\MyHealthcare\Helpers;

/**
 * Class for logging errors to flock groups via webhooks
 */
class FlockLogs
{
    /**
     * Logs system wide errors
     * @param type $errorMessage
     */
    public static function logError($message)
    {
        if (config('services.flock_logs.enabled') == 1 && !empty(config('services.flock_logs.uri'))) {
            $isObject = is_object($message);
            if ($isObject && $message instanceof \Exception) {
                if ($message instanceof \Illuminate\Validation\ValidationException) {
                    return;
                }
                $file = method_exists($message, 'getFile') ? $message->getFile() : '';
                $line = method_exists($message, 'getLine') ? $message->getLine() : '';
//                $trace = method_exists($message, 'getTraceAsString') ? $message->getTraceAsString() : '';
                $message = method_exists($message, 'getMessage') ? $message->getMessage() : '';
            } elseif ($isObject) {
                $message = null;
            } elseif (!empty($message) && is_array($message)) {
                $message = json_encode($message);
            }
            
            if (!empty($message)) {
                $appUrl = env('APP_URL');
                $requestUrl = !empty(request()->getUri()) ? request()->getUri() : '';
                $internalIp = @getHostByName(getHostName());
                $exceptionMessage = "~~Error generated@" . \Carbon\Carbon::now('Asia/Kolkata')->format('dS M, Y \a\t h:i:s A') . "~~";
                $exceptionMessage .= "\nAppUrl: " . $appUrl;
                if (!empty($requestUrl)) {
                    $exceptionMessage .= "\nRequestUrl: " . $requestUrl;
                }
                if (!empty($internalIp)) {
                    $exceptionMessage .= "\nInternalip: " . $internalIp;
                }
                $exceptionMessage .= "\nError: " . $message;
                if (!empty($file)) {
                    $exceptionMessage .= "\n" . "File: " . $file;
                }
                if (!empty($line)) {
                    $exceptionMessage .= "\n" . "Line: " . $line;
                }
                static::log($exceptionMessage);
            }
        }
    }

    /**
     * 
     * Method to log messages to flock
     * 
     * @param String $errorMessage
     * @return null
     */
    private static function log($errorMessage)
    {
        try
        {
            static::post_async(config('services.flock_logs.uri'), ['text' => $errorMessage]);
        } catch (\Exception $ex) { }
    }

    /**
     * 
     * Method to post curl request without waiting for response from endpoint
     * 
     * @param String $url
     * @param Array $params
     */
    private static function post_async($url, $params)
    {
        $post_string = json_encode($params);
        $parts = parse_url($url);
        $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, config('services.flock_logs.timeout'));
        $out = "POST " . $parts['path'] . " HTTP/1.1\r\n";
        $out.= "Host: " . $parts['host'] . "\r\n";
        $out.= "Content-Type: application/json\r\n";
        $out.= "Content-Length: " . strlen($post_string) . "\r\n";
        $out.= "Connection: Close\r\n\r\n";
        if (isset($post_string))
            $out.= $post_string;
        fwrite($fp, $out);
        fclose($fp);
    }

}