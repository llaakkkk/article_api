<?php

namespace App\UseCase\Articles\Get;

use App\Entity\Article;
use App\Service\UseCase\UseCaseResponse;

class ArticlesGetResponse extends UseCaseResponse
{

    /**
     * @var Article
     */
    public $article;

}