<?php

namespace App\Tests\Controller\ArticleController;

use App\Tests\ApiControllerTestCase;

/**
 * @group getArticleList
 */
class GetListActionTest extends ApiControllerTestCase
{
    public function testGetArticleListSuccess(): void
    {

        $tag     = $this->entityFactory->getTag();
        $article = $this->entityFactory->getArticle();
        $article->addTag($tag);

        $this->em->persist($tag);
        $this->em->persist($article);
        $this->em->flush();

        $this->requestApi('GET', '/articles?_fields=id,title,description,createdAt,authorName,tags', []);
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertJsonValue($article->getId(), $response->getContent(), '0/id');
        $this->assertJsonValue($article->getTitle(), $response->getContent(), '0/title');
        $this->assertJsonValue($article->getDescription(), $response->getContent(), '0/description');
        $this->assertJsonValue($article->getAuthorName(), $response->getContent(), '0/authorName');
        $this->assertJsonValue($article->getCreatedAt()->format('c'), $response->getContent(), '0/createdAt');
        $this->assertJsonValue($tag->getId(), $response->getContent(), '0/tags/0/id');
        $this->assertJsonValue($tag->getName(), $response->getContent(), '0/tags/0/name');

    }

    public function testGetArticleListWrongParamsValuesFail(): void
    {
        $tag     = $this->entityFactory->getTag();
        $article = $this->entityFactory->getArticle();
        $article->addTag($tag);

        $this->em->persist($tag);
        $this->em->persist($article);
        $this->em->flush();

        $this->requestApi('GET', '/articles?limit=abd&offset=rut&sort=nvknvn', []);
        $response = $this->client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJsonValue('validation_failed', $response->getContent(), 'code');
        $this->assertJsonKey($response->getContent(), 'data/sort/InArray::NOT_IN_ARRAY');
        $this->assertJsonKey($response->getContent(), 'data/limit/Digits::NOT_DIGITS');
        $this->assertJsonKey($response->getContent(), 'data/limit/GreaterThan::NOT_GREATER_THAN');
        $this->assertJsonKey($response->getContent(), 'data/offset/Digits::NOT_DIGITS');
    }
}
