<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Category;

class UpdateCategoryRequest extends StoreCategoryRequest
{
    public function rules(): array
    {
        return [
            'parent_id' => ['sometimes', 'nullable', 'string', 'ulid'],
            'name'      => ['sometimes', 'string', 'max:160'],
            'slug'      => ['sometimes', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ];
    }
}
