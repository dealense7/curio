<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Category;

use App\Contracts\Services\Category\CategoryServiceContract;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Category\IndexCategoryRequest;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CategoryController extends ApiController
{
    public function index(IndexCategoryRequest $request, CategoryServiceContract $service): JsonResponse
    {
        $filters = $this->getInputFilters($request);
        $page    = $this->getInputPage($request);
        $perPage = $this->getInputPerPage($request);
        $sort    = $this->getInputSort($request);
        $items   = $service->getItemsWithPagination($filters, ['parent'], $page, $perPage, $sort);

        return $this->resource($items, CategoryResource::class, ['parent']);
    }

    public function store(StoreCategoryRequest $request, CategoryServiceContract $service): JsonResponse
    {
        $data = $request->validated();

        if (! empty($data['slug']) && $service->slugExists((string) $data['slug'])) {
            $this->throwCustomValidationError('slug', __('category.slug_already_exists'));
        }

        if (array_key_exists('parent_id', $data) && $data['parent_id'] !== null) {
            $parent = $service->findByPublicId((string) $data['parent_id'], checkPermission: false);

            if ($parent === null) {
                $this->throwCustomValidationError('parent_id', __('category.parent_not_found'));
            }

            $data['parent_id'] = $parent->getId();
        }

        $item = $service->create($data);

        return $this->resource($item, CategoryResource::class)
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(string $categoryPublicId, CategoryServiceContract $service): JsonResponse
    {
        $item = $service->findByPublicId($categoryPublicId, ['parent', 'children']);

        if ($item === null) {
            return $this->resourceNotFound();
        }

        return $this->resource($item, CategoryResource::class, ['parent', 'children']);
    }

    public function update(
        string $categoryPublicId,
        UpdateCategoryRequest $request,
        CategoryServiceContract $service,
    ): JsonResponse {
        $item = $service->findByPublicId($categoryPublicId);

        if ($item === null) {
            return $this->resourceNotFound();
        }

        $data = $request->validated();

        if (! empty($data['slug']) && $service->slugExists((string) $data['slug'], $categoryPublicId)) {
            $this->throwCustomValidationError('slug', __('category.slug_already_exists'));
        }

        if (array_key_exists('parent_id', $data) && $data['parent_id'] !== null) {
            if ((string) $data['parent_id'] === $categoryPublicId) {
                $this->throwCustomValidationError('parent_id', __('category.parent_invalid'));
            }

            $parent = $service->findByPublicId((string) $data['parent_id'], checkPermission: false);

            if ($parent === null) {
                $this->throwCustomValidationError('parent_id', __('category.parent_not_found'));
            }

            $data['parent_id'] = $parent->getId();
        }

        $item = $service->update($item, $data);

        return $this->resource($item, CategoryResource::class);
    }

    public function destroy(string $categoryPublicId, CategoryServiceContract $service): JsonResponse
    {
        $item = $service->findByPublicId($categoryPublicId);

        if ($item === null) {
            return $this->resourceNotFound();
        }

        $service->delete($item);

        return $this->success(__('category.deleted_successfully'));
    }
}
