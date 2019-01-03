<?php

namespace App\Controller\Api\V1;

use App\Api\Hydrator\AbstractHydrator;
use App\Service\UseCase\UseCaseError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    const ERROR_CODE_BAD_REQUEST = 'bad_request';

    /**
     * @Route("/", methods={""})
     */
    public function base()
    {
    }

    /**
     * @param UseCaseError $error
     *
     * @return JsonResponse
     */
    protected function getErrorHttpResponse(UseCaseError $error): JsonResponse
    {
        return $this->getErrorHttpResponseFromData(
            $error->getCode(),
            $error->getMessage(),
            $error->getData(),
            $this->getStatus($error)
        );
    }

    /**
     * @param string     $code
     * @param string     $message
     * @param array|null $data
     * @param int        $status
     *
     * @return JsonResponse
     */
    protected function getErrorHttpResponseFromData(
        string $code,
        string $message,
        ?array $data,
        int $status
    ): JsonResponse {
        return new JsonResponse(
            [
                'code'    => $code,
                'message' => $message,
                'data'    => $data
            ], $status
        );
    }

    /**
     * @return JsonResponse
     */
    protected function getNotJsonRequestErrorResponse()
    {
        return $this->getErrorHttpResponseFromData(self::ERROR_CODE_BAD_REQUEST, 'Content-Type must be json', [], 400);
    }

    /**
     * @return JsonResponse
     */
    protected function getBadFieldsErrorResponse()
    {
        return $this->getErrorHttpResponseFromData(
            self::ERROR_CODE_BAD_REQUEST,
            '_fields format or structure is invalid',
            [],
            400
        );
    }

    /**
     * @param UseCaseError $error
     *
     * @return int
     */
    protected function getStatus(UseCaseError $error): int
    {
        switch ($error->getCode()) {
            case UseCaseError::CODE_NOT_FOUND:
                return 404;
            case UseCaseError::CODE_SERVER_ERROR:
                return 500;
            case UseCaseError::CODE_UNATHENTICATED:
                return 401;
            case UseCaseError::CODE_VALIDATION_FAILED:
                return 400;
            case UseCaseError::CODE_UNAUTHORIZED:
                return 403;
            case UseCaseError::CODE_RATE_LIMITED:
                return 429;
            default:
                return 400;
        }
    }

    /**
     * @param Request $request
     *
     * @return bool|mixed
     */
    protected function getJsonBody(Request $request)
    {
        if ($request->getContentType() != 'json') {
            return false;
        }

        try {
            $data = json_decode($request->getContent(), true);
        } catch (\Throwable $e) {
            return false;
        }

        if ($data === null) {
            return false;
        }

        return $data;
    }

    /**
     * @param                  $value
     * @param AbstractHydrator $hydrator
     *
     * @return array|bool
     */
    protected function parseAndValidateFields($value, AbstractHydrator $hydrator)
    {
        $fields = $this->parseFields($value);
        if ($fields === false) {
            return false;
        }

        if (!$hydrator->validateFields($fields)) {
            return false;
        }

        return $fields;
    }

    /**
     * @param $value
     *
     * @return array|bool
     */
    private function parseFields($value)
    {
        $value  = (string)$value;
        $fields = [];

        $level         = 0;
        $stack         = [];
        $stack[$level] = &$fields;

        $field = '';

        $len = strlen($value) - 1;
        for ($i = 0; $i <= $len; $i++) {
            $char = $value[$i];
            switch ($char) {
                case ',':
                    if ('' != $field) {
                        $stack[$level][$field] = true;
                        $field                 = '';
                    }
                    break;
                case '(':
                    $stack[$level][$field] = [];
                    $oldLevel              = $level;
                    $stack[++$level]       = &$stack[$oldLevel][$field];
                    $field                 = '';
                    break;
                case ')':
                    if ('' != $field) {
                        $stack[$level][$field] = true;
                    }
                    unset($stack[$level--]);
                    if ($level < 0) {
                        return false;
                    }
                    $field = '';
                    break;
                default:
                    $field .= $char;
                    if ($i == $len && '' !== $field) {
                        $stack[$level][$field] = true;
                        $field                 = '';
                    }
            }
        }
        if (count($stack) > 1) {
            return false;
        }

        return $fields;
    }
}
