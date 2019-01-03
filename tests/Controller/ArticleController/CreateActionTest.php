<?php

namespace App\Tests\Controller\ArticleController;

use App\Entity\Article;
use App\Tests\ApiControllerTestCase;

/**
 * @group createArticle
 */
class CreateActionTest extends ApiControllerTestCase
{

    public function testArticleCreateSuccess(): void
    {
        $this->requestApi(
            'POST',
            '/articles',
            [
                'title'       => 'Some Title',
                'description' => 'Some description',
                'authorName'  => 'Some Name',
                'tags'        => [
                    ['name' => 'some'],
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

    public function testNotValidCreateArticleDataFailed(): void
    {
        $this->requestApi(
            'POST',
            '/articles',
            [
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
        $this->assertJsonKey($response->getContent(), 'data/title/IsString::NOT_A_STRING');
        $this->assertJsonKey($response->getContent(), 'data/authorName/NotEmpty::EMPTY_VALUE');
        $this->assertJsonKey($response->getContent(), 'data/description/LengthBetween::TOO_SHORT');
        $this->assertJsonKey($response->getContent(), 'data/tags.0.name/Required::NON_EXISTENT_KEY');
        $this->assertJsonKey($response->getContent(), 'data/tags.1.name/IsString::NOT_A_STRING');

    }
}
