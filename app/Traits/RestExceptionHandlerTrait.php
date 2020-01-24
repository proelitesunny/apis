<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

trait RestExceptionHandlerTrait
{

    /**
     * Creates a new JSON response based on exception type.
     *
     * @param Request $request
     * @param Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getJsonResponseForException(Request $request, Exception $e)
    {
        switch (true) {
            case $this->isModelNotFoundException($e):
                $retval = $this->modelNotFound();
                break;
            case $this->isValidationException($e):
                $message = collect($e->validator->errors()->toArray())->transform(function($error, $key) {
                    return [
                        'message' => implode(',', $error),
                        'source' => $key
                    ];    
                })->values();

                $retval = $this->validation($message);
                break;
            default:
                $statusCode = null; $headers = null;
                if(method_exists($e, 'getStatusCode')) {
                    $statusCode = $e->getStatusCode();
                }

                if (method_exists($e, 'getHeaders')) {
                    $headers = $e->getHeaders();
                }
                
                $source = isset($headers['source']) ? $headers['source'] : '';

                $retval = $this->badRequest($e->getMessage(), $statusCode, $source);
        }
        return $retval;
    }

    /**
     * Returns json response for generic bad request.
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badRequest($message = '', $statusCode = 500, $source = '')
    {
        $statusCode = empty($statusCode) ? 500 : $statusCode;
        $message = empty($message) || $statusCode == 500 ? 'Bad request' : $message;
        return $this->jsonResponse(['errors' => [['message' => $message, 'source' => $source]]], $statusCode);
    }

    /**
     * Returns json response for Eloquent model not found exception.
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function modelNotFound($message = 'Record not found', $statusCode = 404)
    {
        return $this->jsonResponse(['errors' => [['message' => $message, 'source' => '']]], $statusCode);
    }

    /**
     * Returns json response for Eloquent model not found exception.
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validation($messages = [], $statusCode = 400)
    {
        return $this->jsonResponse(['errors' => $messages], $statusCode);
    }

    /**
     * Returns json response.
     *
     * @param array|null $payload
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse(array $payload = null, $statusCode = 404)
    {
        $payload = $payload ? : [];
        return response()->json($payload, $statusCode);
    }

    /**
     * Determines if the given exception is an Eloquent model not found.
     *
     * @param Exception $e
     * @return bool
     */
    protected function isModelNotFoundException(Exception $e)
    {
        return $e instanceof ModelNotFoundException;
    }

    /**
     * Determines if the given exception is an Eloquent model not found.
     *
     * @param Exception $e
     * @return bool
     */
    protected function isValidationException(Exception $e)
    {
        return $e instanceof ValidationException;
    }

}