<?php

namespace App\UseCase\Articles\Edit;

use App\Entity\Article;
use App\Entity\Tag;
use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use App\Service\UseCase\GenericErrorsTrait;
use App\Service\UseCase\UseCaseError;
use Particle\Validator\Validator;

class ArticlesEditUseCase
{
    use GenericErrorsTrait;

    /**
     * @var ArticleRepository
     */
    private $articleRepo;

    /**
     * @var TagRepository
     */
    private $tagRepo;

    /**
     * ArticlesEditUseCase constructor.
     *
     * @param ArticleRepository $articleRepo
     * @param TagRepository     $tagRepo
     */
    public function __construct(
        ArticleRepository $articleRepo,
        TagRepository $tagRepo
    ) {
        $this->articleRepo = $articleRepo;
        $this->tagRepo     = $tagRepo;
    }

    /**
     * @param ArticlesEditRequest $request
     *
     * @return ArticlesEditResponse
     */
    public function putArticle(ArticlesEditRequest $request): ArticlesEditResponse
    {
        $response = new ArticlesEditResponse();

        $this->validateRequest($request, $response);

        if ($response->isError()) {
            return $response;
        }

        $article = $this->editArticle($request);

        $this->articleRepo->store($article, true);
        $response->article = $article;

        return $response;
    }

    /**
     * @param ArticlesEditRequest $request
     *
     * @return Article
     */
    private function editArticle(ArticlesEditRequest $request): Article
    {
        /** @var Article $article */
        $article = $this->articleRepo->find($request->id);

        $article->setTitle($request->title);
        $article->setDescription($request->description);
        $article->setAuthorName($request->authorName);

        foreach ($request->tags as $tagItem) {

            $tagExist = $this->tagRepo->findTagByName($tagItem['name'], $article->getId());
            if ($tagExist === null) {
                $tag = new Tag();
                $tag->setName($tagItem['name']);
                $this->tagRepo->store($tag, true);

                $article->addTag($tag);
            }
        }

        return $article;
    }

    /**
     * @param ArticlesEditRequest  $request
     * @param ArticlesEditResponse $response
     */
    private function validateRequest(ArticlesEditRequest $request, ArticlesEditResponse $response): void
    {
        $v = new Validator();

        $v->required('id')
          ->digits()
          ->greaterThan(0);

        $v->required('title')
          ->string();

        $v->required('authorName')
          ->string();

        $v->required('description')
          ->lengthBetween(Article::DESCRIPTION_MIN_LENGTH, Article::DESCRIPTION_MAX_LENGTH);

        $v->required('tags')
          ->isArray()
          ->each(
              function (Validator $validator) {
                  $validator->required('name')->string()
                            ->lengthBetween(0, 100);
              }
          );

        $result = $v->validate((array)$request);

        if (!$result->isValid()) {
            $this->setValidationError($response, $result->getMessages());

            return;
        }

        /** @var Article $article */
        $article = $this->articleRepo->find($request->id);

        if ($article === null) {
            $this->setResponseError(
                $response,
                UseCaseError::CODE_NOT_FOUND,
                UseCaseError::$messages[UseCaseError::CODE_NOT_FOUND],

                [
                    'entity' => [
                        'Entity::NOT_FOUND' => UseCaseError::$messages[UseCaseError::CODE_NOT_FOUND]
                    ]
                ]

            );

            return;
        }
    }

}