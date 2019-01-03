<?php

namespace App\UseCase\Articles\Edit;

use App\Service\UseCase\UseCaseRequest;

class ArticlesEditRequest extends UseCaseRequest
{

    /**
     * @var int
     */
    public $id;

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