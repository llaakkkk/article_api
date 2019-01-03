<?php

namespace App\Tests\Controller\ArticleController;

use App\Entity\Article;
use App\Tests\ApiControllerTestCase;

/**
 * @group editArticle
 */
class EditActionTest extends ApiControllerTestCase
{

    public function testArticleEditSuccess(): void
    {
        $tag     = $this->entityFactory->getTag();
        $article = $this->entityFactory->getArticle();
        $article->addTag($tag);

        $this->em->persist($tag);
        $this->em->persist($article);
        $this->em->flush();

        $this->requestApi(
            'PUT',
            '/articles/' . $article->getId(),
            [
                'title'       => 'Some Title',
                'description' => 'Some description',
                'authorName'  => 'Some Name',
                'tags'        => [
                    ['name' => 'Article Tag'],
                    ['name' => 'title'],
                    ['name' => 'tag'],
                ]
            ]
        );

        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonValue('Some Title', $response->getContent(), 'title');
        $this->assertJsonValue('Some description', $response->getContent(), 'description');
        $this->assertJsonValue('Some Name', $response->getContent(), 'authorName');
        $this->assertJsonValue(3, $response->getContent(), 'tagsCount');

        $articles = $this->em->getRepository(Article::class)->findAll();
        $this->assertCount(1, $articles);
    }

    public function testEditArticleNotValidFailed(): void
    {
        $this->requestApi(
            'PUT',
            '/articles/fhfh',
            [
                'id'          => 'iddr',
                'title'       => 123,
                'description' => 1,
                'tags'        => [
                    [2],
                    ['name' => 3],
                ]
            ]
        );

        $response = $this->client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJsonValue('validation_failed', $response->getContent(), 'code');
        $this->assertJsonKey($response->getContent(), 'data/id/Digits::NOT_DIGITS');
        $this->assertJsonKey($response->getContent(), 'data/title/IsString::NOT_A_STRING');
        $this->assertJsonKey($response->getContent(), 'data/authorName/NotEmpty::EMPTY_VALUE');
        $this->assertJsonKey($response->getContent(), 'data/description/LengthBetween::TOO_SHORT');
        $this->assertJsonKey($response->getContent(), 'data/tags.0.name/Required::NON_EXISTENT_KEY');
        $this->assertJsonKey($response->getContent(), 'data/tags.1.name/IsString::NOT_A_STRING');

    }

    public function testEditArticleNotExistFail(): void
    {
        $article = $this->entityFactory->getArticle();
        $this->em->persist($article);
        $this->em->flush();

        $articleId = $article->getId() + 5;
        $this->requestApi(
            'PUT',
            '/articles/' . $articleId,
            [
                'title'       => 'Some Title',
                'description' => 'Some description',
                'authorName'  => 'Some Name',
                'tags'        => [
                    ['name' => 'Article Tag'],
                    ['name' => 'title'],
                    ['name' => 'tag'],
                ]
            ]
        );

        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJsonValue('not_found', $response->getContent(), 'code');
        $this->assertJsonKey($response->getContent(), 'data/article/Entity::NOT_FOUND');
    }
}
