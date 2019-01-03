<?php

namespace App\Service\UseCase;

class UseCaseResponse
{
    /**
     * @var UseCaseError
     */
    private $error;

    /**
     * @return UseCaseError
     */
    public function getError(): ?UseCaseError
    {
        return $this->error;
    }

    /**
     * @param UseCaseError $error
     */
    public function setError(UseCaseError $error)
    {
        $this->error = $error;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->error !== null;
    }
}
