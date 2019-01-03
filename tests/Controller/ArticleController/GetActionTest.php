<?php

namespace App\Tests\Controller\ArticleController;

use App\Tests\ApiControllerTestCase;

/**
 * @group getArticle
 */
class GetActionTest extends ApiControllerTestCase
{
    public function testGetArticleListSuccess(): void
    {

        $tag     = $this->entityFactory->getTag();
        $article = $this->entityFactory->getArticle();
        $article->addTag($tag);

        $this->em->persist($tag);
        $this->em->persist($article);
        $this->em->flush();

        $this->requestApi(
            'GET',
            '/articles/' . $article->getId() . '?_fields=id,title,description,createdAt,authorName,tags',
            []
        );
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertJsonValue($article->getId(), $response->getContent(), 'id');
        $this->assertJsonValue($article->getTitle(), $response->getContent(), 'title');
        $this->assertJsonValue($article->getDescription(), $response->getContent(), 'description');
        $this->assertJsonValue($article->getAuthorName(), $response->getContent(), 'authorName');
        $this->assertJsonValue($article->getCreatedAt()->format('c'), $response->getContent(), 'createdAt');
        $this->assertJsonValue($tag->getId(), $response->getContent(), 'tags/0/id');
        $this->assertJsonValue($tag->getName(), $response->getContent(), 'tags/0/name');

    }

    public function testGetArticleNotExistFail(): void
    {
        $article = $this->entityFactory->getArticle();
        $this->em->persist($article);
        $this->em->flush();

        $articleId = $article->getId() + 5;
        $this->requestApi('GET', '/articles/' . $articleId, []);
        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJsonValue('not_found', $response->getContent(), 'code');
        $this->assertJsonKey($response->getContent(), 'data/article/Entity::NOT_FOUND');
    }
}
