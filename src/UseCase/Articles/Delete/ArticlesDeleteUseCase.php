<?php

namespace App\UseCase\Articles\Delete;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\UseCase\GenericErrorsTrait;
use App\Service\UseCase\UseCaseError;
use Doctrine\ORM\EntityManagerInterface;
use Particle\Validator\Validator;

class ArticlesDeleteUseCase
{
    use GenericErrorsTrait;

    /**
     * @var ArticleRepository
     */
    private $articleRepo;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ArticlesDeleteUseCase constructor.
     *
     * @param ArticleRepository      $articleRepo
     * @param EntityManagerInterface $em
     */
    public function __construct(
        ArticleRepository $articleRepo,
        EntityManagerInterface $em
    ) {
        $this->articleRepo = $articleRepo;
        $this->em          = $em;
    }

    /**
     * @param ArticlesDeleteRequest $request
     *
     * @return ArticlesDeleteResponse
     */
    public function deleteArticle($request): ArticlesDeleteResponse
    {
        $response = new ArticlesDeleteResponse();

        $this->validateRequest($request, $response);

        if ($response->isError()) {
            return $response;
        }

        $this->delete($request);

        return $response;
    }

    /**
     * @param ArticlesDeleteRequest $request
     */
    private function delete(ArticlesDeleteRequest $request): void
    {
        /** @var Article $article */
        $article = $this->articleRepo->find($request->id);

        $this->em->remove($article);
        $this->em->flush();
    }

    /**
     * @param ArticlesDeleteRequest  $request
     * @param ArticlesDeleteResponse $response
     */
    private function validateRequest(ArticlesDeleteRequest $request, ArticlesDeleteResponse $response): void
    {

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