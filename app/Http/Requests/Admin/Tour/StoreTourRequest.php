<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Tour;

use App\Enums\General\FileType;
use App\Models\General\File;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title'          => $this->normalizeString('title'),
            'description'    => $this->normalizeString('description'),
            'start_location' => $this->normalizeString('start_location'),
            'end_location'   => $this->normalizeString('end_location'),
        ]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title'                         => ['required', 'string', 'max:180'],
            'description'                   => ['required', 'string'],
            'start_location'                => ['required', 'string', 'max:255'],
            'end_location'                  => ['required', 'string', 'max:255'],
            'distance_km'                   => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'duration_comfortable_days'     => ['required', 'integer', 'min:1', 'max:65535'],
            'duration_recommended_days'     => ['required', 'integer', 'min:1', 'max:65535'],
            'daily_distance_comfortable_km' => ['required', 'numeric', 'gt:0', 'max:999999.99'],
            'daily_distance_recommended_km' => ['required', 'numeric', 'gt:0', 'max:999999.99'],
            'elevation_gain_m'              => ['required', 'integer', 'min:0'],
            'difficulty_id'                 => ['required', 'integer', Rule::exists('difficulties', 'id')],
            'price_comfortable'             => ['required', 'integer', 'min:0'],
            'price_recommended'             => ['required', 'integer', 'min:0'],
            'average_daily_spend'           => ['required', 'integer', 'min:0'],
            'currency_id'                   => ['required', 'integer', Rule::exists('currencies', 'id')],
            'best_month_ids'                => ['required', 'array', 'min:1'],
            'best_month_ids.*'              => ['required', 'integer', 'distinct', Rule::exists('months', 'id')],
            'route_file_id'                 => [
                'required',
                'string',
                'ulid',
                Rule::exists('files', 'public_id')
                    ->where('type', FileType::ROUTE->value)
                    ->whereNull('deleted_at'),
            ],
            'cover_image_id' => [
                'required',
                'string',
                'ulid',
                Rule::exists('files', 'public_id')
                    ->where('type', FileType::IMAGE->value)
                    ->whereNull('deleted_at'),
            ],
            'publishing_status_id' => ['required', 'integer', Rule::exists('publishing_statuses', 'id')],
        ];
    }

    /** @return array<string, mixed> */
    public function validatedTour(): array
    {
        $data = $this->safe()->only([
            'title',
            'description',
            'start_location',
            'end_location',
            'distance_km',
            'duration_comfortable_days',
            'duration_recommended_days',
            'daily_distance_comfortable_km',
            'daily_distance_recommended_km',
            'elevation_gain_m',
            'difficulty_id',
            'price_comfortable',
            'price_recommended',
            'average_daily_spend',
            'currency_id',
            'publishing_status_id',
        ]);

        $data['route_file_id']  = $this->resolveFileId((string) $this->validated('route_file_id'));
        $data['cover_image_id'] = $this->resolveFileId((string) $this->validated('cover_image_id'));

        return $data;
    }

    /** @return list<int> */
    public function validatedBestMonthIds(): array
    {
        /** @var list<int> $monthIds */
        $monthIds = $this->validated('best_month_ids');

        return $monthIds;
    }

    private function normalizeString(string $key): mixed
    {
        $value = $this->input($key);

        return is_string($value) ? trim($value) : $value;
    }

    private function resolveFileId(string $publicId): int
    {
        return (int) File::query()
            ->where('public_id', $publicId)
            ->valueOrFail('id');
    }
}
