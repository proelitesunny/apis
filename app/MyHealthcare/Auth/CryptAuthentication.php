<?php

namespace App\MyHealthcare\Auth;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

/**
* 
* Authentication based on crypted token
* 
*/
class CryptAuthentication
{
    public function validateAccessToken($accessToken, $aggregatorType, $secret_key = null)
    {
        if (empty($secret_key)) {
            $secret_key = config('source.api_secret_keys')[$aggregatorType];
    	}
        
        $accessToken = trim($accessToken);
        if (empty($accessToken)) {
            abort(400, trans('errors.PATIENT_116'));
        }

        $decryptedAccessToken = static::decryptAccessToken($accessToken);
        if (empty($decryptedAccessToken)) {
            abort(400, trans('errors.PATIENT_117'));
        }

        try
        {
            $secret_key = Crypt::decrypt($secret_key);
        } catch (DecryptException $e)
        {
            $secret_key = null;
        }

        if ($secret_key != $decryptedAccessToken) {
            abort(400, trans('errors.PATIENT_117'));
        }

        return true;
    }

    private function decryptAccessToken($accessToken)
    {
        try
        {
            $decryptedAccessToken = Crypt::decrypt($accessToken);
        } catch (DecryptException $e)
        {
            $decryptedAccessToken = null;
        }

        return $decryptedAccessToken;
    }
}