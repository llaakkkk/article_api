<?php

namespace App\Tests\Controller\ArticleController;

use App\Entity\Article;
use App\Tests\ApiControllerTestCase;

/**
 * @group deleteArticle
 */
class DeleteActionTest extends ApiControllerTestCase
{

    public function testDeleteArticleSuccess(): void
    {

        $tag     = $this->entityFactory->getTag();
        $article = $this->entityFactory->getArticle();
        $article->addTag($tag);

        $this->em->persist($tag);
        $this->em->persist($article);
        $this->em->flush();

        $this->requestApi('DELETE', '/articles/' . $article->getId(), []);
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonValue(['deleted' => true], $response->getContent());
        $articles = $this->em->getRepository(Article::class)->findAll();
        $this->assertCount(0, $articles);

    }

    public function testDeleteArticleNotExistFail(): void
    {
        $article = $this->entityFactory->getArticle();
        $this->em->persist($article);
        $this->em->flush();

        $articleId = $article->getId() + 5;
        $this->requestApi('DELETE', '/articles/' . $articleId, []);
        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJsonValue('not_found', $response->getContent(), 'code');
        $this->assertJsonKey($response->getContent(), 'data/article/Entity::NOT_FOUND');
    }
}