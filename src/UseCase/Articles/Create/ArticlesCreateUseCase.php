<?php

namespace App\UseCase\Articles\Create;

use App\Entity\Article;
use App\Entity\Tag;
use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use App\Service\UseCase\GenericErrorsTrait;
use Particle\Validator\Validator;

class ArticlesCreateUseCase
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
     * ArticlesCreateUseCase constructor.
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
     * @param ArticlesCreateRequest $request
     *
     * @return ArticlesCreateResponse
     */
    public function postArticle(ArticlesCreateRequest $request): ArticlesCreateResponse
    {
        $response = new ArticlesCreateResponse();

        $this->validateRequest($request, $response);

        if ($response->isError()) {
            return $response;
        }

        $article = $this->createArticle($request);

        $this->articleRepo->store($article, true);
        $response->article = $article;

        return $response;
    }

    /**
     * @param ArticlesCreateRequest $request
     *
     * @return Article
     */
    private function createArticle(ArticlesCreateRequest $request): Article
    {
        $article = new Article();
        $article->setTitle($request->title);
        $article->setDescription($request->description);
        $article->setAuthorName($request->authorName);
        $article->setCreatedAt(new \DateTime());
        foreach ($request->tags as $tagItem) {

            $tag = new Tag();
            $tag->setName($tagItem['name']);
            $this->tagRepo->store($tag, true);

            $article->addTag($tag);
        }

        return $article;
    }

    /**
     * @param ArticlesCreateRequest  $request
     * @param ArticlesCreateResponse $response
     */
    private function validateRequest(ArticlesCreateRequest $request, ArticlesCreateResponse $response): void
    {
        $v = new Validator();
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
    }

}