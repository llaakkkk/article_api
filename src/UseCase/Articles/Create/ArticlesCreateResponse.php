<?php

namespace App\UseCase\Articles\Create;

use App\Entity\Article;
use App\Service\UseCase\UseCaseResponse;

class ArticlesCreateResponse extends UseCaseResponse
{
    /**
     * @var Article
     */
    public $article;
}