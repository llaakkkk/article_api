<?php

namespace App\UseCase\Articles\GetList;

use App\Service\UseCase\UseCaseRequest;

class ArticlesGetListRequest extends UseCaseRequest
{
    /**
     * @var string
     */
    public $sort;

    /**
     * @var int|string
     */
    public $limit;

    /**
     * @var int|string
     */
    public $offset = 0;

    /**
     * @var array
     */
    public $fields;
}
