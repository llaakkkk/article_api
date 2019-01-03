<?php

namespace App\Controller;

use App\Api\Hydrator\ArticleHydrator;
use App\UseCase\Articles\Create\ArticlesCreateRequest;
use App\UseCase\Articles\Create\ArticlesCreateUseCase;
use App\UseCase\Articles\Delete\ArticlesDeleteRequest;
use App\UseCase\Articles\Delete\ArticlesDeleteUseCase;
use App\UseCase\Articles\Edit\ArticlesEditRequest;
use App\UseCase\Articles\Edit\ArticlesEditUseCase;
use App\UseCase\Articles\Get\ArticlesGetRequest;
use App\UseCase\Articles\Get\ArticlesGetUseCase;
use App\UseCase\Articles\GetList\ArticlesGetListRequest;
use App\UseCase\Articles\GetList\ArticlesGetListUseCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends ApiController
{

    /**
     * @Route("/articles/{id}", methods={"GET"})
     * @param Request            $httpRequest
     * @param ArticlesGetUseCase $useCase
     * @param ArticleHydrator    $hydrator
     *
     * @return JsonResponse
     */
    public function getAction(
        Request $httpRequest,
        ArticlesGetUseCase $useCase,
        ArticleHydrator $hydrator
    ) {
        $fields = $this->parseAndValidateFields($httpRequest->get('_fields'), $hydrator);
        if ($fields === false) {
            return $this->getBadFieldsErrorResponse();
        }

        $request         = new ArticlesGetRequest();
        $request->id     = $httpRequest->get('id');
        $request->fields = $fields;

        $response = $useCase->getArticle($request);
        if ($response->isError()) {
            return $this->getErrorHttpResponse($response->getError());
        }

        return new JsonResponse(
            $hydrator->hydrate(
                $response->article,
                $fields
            )
        );
    }

    /**
     * @Route("/articles", methods={"GET"})
     * @param Request                $httpRequest
     * @param ArticlesGetListUseCase $useCase
     * @param ArticleHydrator        $hydrator
     *
     * @return JsonResponse
     */
    public function getListAction(Request $httpRequest, ArticlesGetListUseCase $useCase, ArticleHydrator $hydrator)
    {
        $fields = $this->parseAndValidateFields($httpRequest->get('_fields'), $hydrator);
        if ($fields === false) {
            return $this->getBadFieldsErrorResponse();
        }

        $request         = new ArticlesGetListRequest();
        $request->sort   = $httpRequest->get('sort');
        $request->limit  = $httpRequest->get('limit', 10);
        $request->offset = $httpRequest->get('offset', 0);
        $request->fields = $fields;

        $response = $useCase->getArticles($request);

        if ($response->isError()) {
            return $this->getErrorHttpResponse($response->getError());
        }

        return new JsonResponse(
            $hydrator->hydrateCollection($response->articles, $fields),
            200,
            ['X-Total-Count' => $response->totalCount]
        );
    }

    /**
     * @Route("/articles/{id}", methods={"DELETE"})
     * @param Request               $httpRequest
     * @param ArticlesDeleteUseCase $useCase
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $httpRequest, ArticlesDeleteUseCase $useCase): JsonResponse
    {
        $request     = new ArticlesDeleteRequest();
        $request->id = $httpRequest->get('id');

        $response = $useCase->deleteArticle($request);

        if ($response->isError()) {
            return $this->getErrorHttpResponse($response->getError());
        }

        return new JsonResponse(['deleted' => true]);
    }

    /**
     * @Route("/articles", methods={"POST"})
     * @param Request               $httpRequest
     * @param ArticlesCreateUseCase $useCase
     * @param ArticleHydrator       $hydrator
     *
     * @return JsonResponse
     */
    public function createAction(
        Request $httpRequest,
        ArticlesCreateUseCase $useCase,
        ArticleHydrator $hydrator
    ): JsonResponse {
        $request = new ArticlesCreateRequest();

        $body = $this->getJsonBody($httpRequest);
        if ($body === false) {
            return $this->getNotJsonRequestErrorResponse();
        }

        $request->title       = $body['title'] ?? null;
        $request->description = $body['description'] ?? null;
        $request->authorName  = $body['authorName'] ?? null;
        $request->tags        = $body['tags'] ?? null;

        $response = $useCase->postArticle($request);

        if ($response->isError()) {
            return $this->getErrorHttpResponse($response->getError());
        }

        return new JsonResponse(
            $hydrator->hydrate($response->article, ArticleHydrator::CREATED_RESOURCE_FIELDS)
        );
    }

    /**
     * @Route("/articles/{id}", methods={"PUT"})
     * @param Request             $httpRequest
     * @param ArticlesEditUseCase $useCase
     * @param ArticleHydrator     $hydrator
     *
     * @return JsonResponse
     */
    public function editAction(
        Request $httpRequest,
        ArticlesEditUseCase $useCase,
        ArticleHydrator $hydrator
    ): JsonResponse {
        $request     = new ArticlesEditRequest();
        $request->id = $httpRequest->get('id');

        $body = $this->getJsonBody($httpRequest);
        if ($body === false) {
            return $this->getNotJsonRequestErrorResponse();
        }

        $request->title       = $body['title'] ?? null;
        $request->description = $body['description'] ?? null;
        $request->authorName  = $body['authorName'] ?? null;
        $request->tags        = $body['tags'] ?? null;

        $response = $useCase->putArticle($request);
        if ($response->isError()) {
            return $this->getErrorHttpResponse($response->getError());
        }

        return new JsonResponse(
            $hydrator->hydrate($response->article, ArticleHydrator::CREATED_RESOURCE_FIELDS)
        );
    }

}