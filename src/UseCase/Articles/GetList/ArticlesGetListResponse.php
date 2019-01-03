<?php

namespace App\UseCase\Articles\GetList;

use App\Service\UseCase\UseCaseResponse;

class ArticlesGetListResponse extends UseCaseResponse
{

    /**
     * @var array
     */
    public $articles;

    /**
     * @var int
     */
    public $limit;

    /**
     * @var int
     */
    public $offset;

    /**
     * @var int
     */
    public $totalCount;

}