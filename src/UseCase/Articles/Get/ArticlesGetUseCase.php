<?php

namespace App\UseCase\Articles\Get;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\UseCase\GenericErrorsTrait;
use App\Service\UseCase\UseCaseError;
use Particle\Validator\Validator;

class ArticlesGetUseCase
{
    use GenericErrorsTrait;

    /**
     * @var ArticleRepository
     */
    private $articleRepo;

    /**
     * ArticlesGetUseCase constructor.
     *
     * @param ArticleRepository $articleRepo
     */
    public function __construct(
        ArticleRepository $articleRepo
    ) {
        $this->articleRepo = $articleRepo;
    }

    /**
     * @param ArticlesGetRequest $request
     *
     * @return ArticlesGetResponse
     */
    public function getArticle(ArticlesGetRequest $request): ArticlesGetResponse
    {
        $response = new ArticlesGetResponse();

        $this->validateRequest($request, $response);

        if ($response->isError()) {
            return $response;
        }

        $response->article = $this->articleRepo->find($request->id);

        if (!empty($request->fields['tags'])) {
            $this->articleRepo->fetchTags([$response->article]);
        }

        return $response;
    }

    /**
     * @param ArticlesGetRequest  $request
     * @param ArticlesGetResponse $response
     */
    private function validateRequest(
        ArticlesGetRequest $request,
        ArticlesGetResponse $response
    ): void {

        $v = new Validator();
        $v->required('id')
          ->digits()
          ->greaterThan(0);

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