<?php

namespace App\Service\UseCase;

trait GenericErrorsTrait
{
    /**
     * @param UseCaseResponse $response
     * @param                 $data
     */
    protected function setValidationError(UseCaseResponse $response, $data)
    {
        $this->setResponseError(
            $response,
            UseCaseError::CODE_VALIDATION_FAILED,
            UseCaseError::$messages[UseCaseError::CODE_VALIDATION_FAILED],
            $data
        );
    }

    /**
     * @param UseCaseResponse $response
     * @param                 $data
     */
    protected function setNotFoundError(UseCaseResponse $response, $data)
    {
        $this->setResponseError(
            $response,
            UseCaseError::CODE_NOT_FOUND,
            UseCaseError::$messages[UseCaseError::CODE_NOT_FOUND],
            $data
        );
    }

    /**
     * @param UseCaseResponse $response
     */
    protected function setUnauthenticatedError(UseCaseResponse $response)
    {
        $this->setResponseError(
            $response,
            UseCaseError::CODE_UNATHENTICATED,
            UseCaseError::$messages[UseCaseError::CODE_UNATHENTICATED]
        );
    }

    /**
     * @param UseCaseResponse $response
     */
    protected function setUnauthorizedError(UseCaseResponse $response)
    {
        $this->setResponseError(
            $response,
            UseCaseError::CODE_UNAUTHORIZED,
            UseCaseError::$messages[UseCaseError::CODE_UNAUTHORIZED]
        );
    }

    /**
     * @param UseCaseResponse $response
     */
    protected function setServerError(UseCaseResponse $response)
    {
        $this->setResponseError(
            $response,
            UseCaseError::CODE_SERVER_ERROR,
            UseCaseError::$messages[UseCaseError::CODE_SERVER_ERROR]
        );
    }

    /**
     * @param UseCaseResponse $response
     */
    protected function setRateLimitedError(UseCaseResponse $response)
    {
        $this->setResponseError(
            $response,
            UseCaseError::CODE_RATE_LIMITED,
            UseCaseError::$messages[UseCaseError::CODE_RATE_LIMITED]
        );
    }

    /**
     * @param UseCaseResponse $response
     * @param string          $code
     * @param string          $message
     * @param array|null      $data
     */
    protected function setResponseError(UseCaseResponse $response, string $code, string $message, array $data = null)
    {
        $error = new UseCaseError($code, $message);
        $error->setData($data);
        $response->setError($error);
    }
}
