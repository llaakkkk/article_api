<?php

namespace App\UseCase\Articles\Edit;

use App\Entity\Article;
use App\Service\UseCase\UseCaseResponse;

class ArticlesEditResponse extends UseCaseResponse
{

    /**
     * @var Article
     */
    public $article;
}