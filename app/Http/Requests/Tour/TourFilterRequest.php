<?php

declare(strict_types=1);

namespace App\Http\Requests\Tour;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TourFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'month_id'                 => ['sometimes', 'integer', Rule::exists('months', 'id')],
            'difficulty_id'            => ['sometimes', 'integer', Rule::exists('difficulties', 'id')],
            'publishing_status_id'     => ['sometimes', 'integer', Rule::exists('publishing_statuses', 'id')],
        ];
    }

    /** @return array<string, mixed> */
    public function filters(bool $includePublishingStatus = false): array
    {
        $keys = ['month_id', 'difficulty_id'];

        if ($includePublishingStatus) {
            $keys[] = 'publishing_status_id';
        }

        return $this->safe()->only($keys);
    }
}
