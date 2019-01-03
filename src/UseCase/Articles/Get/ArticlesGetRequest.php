<?php

namespace App\UseCase\Articles\Get;

use App\Service\UseCase\UseCaseRequest;

class ArticlesGetRequest extends UseCaseRequest
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var array
     */
    public $fields;
}
