<?php

namespace App\Service\UseCase;

class UseCaseError
{
    const CODE_VALIDATION_FAILED = 'validation_failed';

    const CODE_NOT_FOUND = 'not_found';

    const CODE_UNATHENTICATED = 'unauthenticated';

    const CODE_UNAUTHORIZED = 'unauthorized';

    const CODE_SERVER_ERROR = 'server_error';

    const CODE_RATE_LIMITED = 'rate_limited';

    /**
     * @var array
     */
    public static $messages = [
        self::CODE_NOT_FOUND         => 'Resource not found',
        self::CODE_SERVER_ERROR      => 'Server Error',
        self::CODE_UNATHENTICATED    => 'User is not authenticated',
        self::CODE_UNAUTHORIZED      => 'User is not authorized to perform action',
        self::CODE_VALIDATION_FAILED => 'Request data validation failed',
        self::CODE_RATE_LIMITED      => 'Too Many Requests'
    ];

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $data;

    /**
     * UseCaseError constructor.
     *
     * @param string     $code
     * @param string     $message
     * @param array|null $data
     */
    public function __construct(string $code, string $message, array $data = null)
    {
        $this->code    = $code;
        $this->message = $message;
        $this->data    = $data;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array|null $data
     */
    public function setData(?array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
