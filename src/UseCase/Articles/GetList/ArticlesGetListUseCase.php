<?php

namespace App\UseCase\Articles\GetList;

use App\Repository\ArticleRepository;
use App\Service\UseCase\GenericErrorsTrait;
use Particle\Validator\Validator;

class ArticlesGetListUseCase
{
    use GenericErrorsTrait;

    const SORT_NEWEST = 'newest';

    /**
     * @var ArticleRepository
     */
    private $articleRepo;

    public function __construct(
        ArticleRepository $articleRepo
    ) {
        $this->articleRepo = $articleRepo;
    }

    /**
     * @param ArticlesGetListRequest $request
     *
     * @return ArticlesGetListResponse
     */
    public function getArticles(ArticlesGetListRequest $request): ArticlesGetListResponse
    {
        $response = new ArticlesGetListResponse();

        $this->validateRequest($request, $response);

        if ($response->isError()) {
            return $response;
        }

        $limit  = (int)$request->limit;
        $offset = (int)$request->offset;

        $articlesParams     =
            [
                'limit'     => $limit,
                'offset'    => $offset,
                'sort'      => $request->sort,
                'fetchTags' => !empty($request->fields['tags'])
            ];
        $response->articles = $this->articleRepo->fetchByQueryParams($articlesParams);

        $response->limit  = $limit;
        $response->offset = $offset;
        $response->totalCount = $this->articleRepo->countByQueryParams();


        return $response;
    }

    /**
     * @param ArticlesGetListRequest  $request
     * @param ArticlesGetListResponse $response
     */
    private function validateRequest(
        ArticlesGetListRequest $request,
        ArticlesGetListResponse $response
    ): void {

        $v = new Validator();

        $v->optional('sort')
          ->inArray([self::SORT_NEWEST]);
        $v->required('limit')
          ->digits()
          ->greaterThan(0)
          ->lessThan(101);
        $v->required('offset')
          ->digits()
          ->greaterThan(-1);

        $result = $v->validate((array)$request);

        if (!$result->isValid()) {
            $this->setValidationError($response, $result->getMessages());

            return;
        }
    }

}