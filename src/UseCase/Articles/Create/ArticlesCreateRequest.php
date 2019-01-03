<?php

namespace App\UseCase\Articles\Create;

use App\Service\UseCase\UseCaseRequest;

class ArticlesCreateRequest extends UseCaseRequest
{

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $authorName;

    /**
     * @var array
     */
    public $tags;
}