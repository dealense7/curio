<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Contracts\Services\General\Category\CategoryServiceContract;
use App\Contracts\Services\Product\ProductServiceContract;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Product\IndexProductRequest;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Http\Resources\Product\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductController extends ApiController
{
    public function index(IndexProductRequest $request, ProductServiceContract $service): JsonResponse
    {
        $filters = $this->getInputFilters($request);
        $page    = $this->getInputPage($request);
        $perPage = $this->getInputPerPage($request);
        $sort    = $this->getInputSort($request);
        $items   = $service->getItemsWithPagination($filters, [], $page, $perPage, $sort);

        return $this->resource($items, ProductResource::class);
    }

    public function store(
        StoreProductRequest $request,
        ProductServiceContract $service,
        CategoryServiceContract $categoryService,
    ): JsonResponse {
        $data = $request->validated();

        if (array_key_exists('category_id', $data) && $data['category_id'] !== null) {
            $category = $categoryService->findByPublicId((string) $data['category_id'], checkPermission: false);
            if ($category === null) {
                $this->throwCustomValidationError('category_id', __('product.category_not_found'));
            }
            $data['category_id'] = $category->getId();
        }

        $item = $service->create($data);

        return $this->resource($item, ProductResource::class)->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(string $productPublicId, ProductServiceContract $service): JsonResponse
    {
        $item = $service->findByPublicId($productPublicId, ['category']);
        if ($item === null) {
            return $this->resourceNotFound();
        }

        return $this->resource($item, ProductResource::class);
    }

    public function update(
        string $productPublicId,
        UpdateProductRequest $request,
        ProductServiceContract $service,
        CategoryServiceContract $categoryService,
    ): JsonResponse {
        $data = $request->validated();
        $item = $service->findByPublicId($productPublicId);

        if ($item === null) {
            return $this->resourceNotFound();
        }

        if (array_key_exists('category_id', $data) && $data['category_id'] !== null) {
            $category = $categoryService->findByPublicId((string) $data['category_id'], checkPermission: false);
            if ($category === null) {
                $this->throwCustomValidationError('category_id', __('product.category_not_found'));
            }
            $data['category_id'] = $category->getId();
        }

        return $this->resource($service->update($item, $data), ProductResource::class);
    }

    public function destroy(string $productPublicId, ProductServiceContract $service): JsonResponse
    {
        $item = $service->findByPublicId($productPublicId);
        if ($item === null) {
            return $this->resourceNotFound();
        }
        $service->delete($item);

        return $this->success(__('product.deleted_successfully'));
    }
}
