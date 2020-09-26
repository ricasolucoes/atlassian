<?php

namespace Atlassian\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PaginatedRequest.
 *
 * @package Atlassian\Http\Requests
 */
class PaginatedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return true
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[][]
     *
     * @psalm-return array{page: array{0: string, 1: string, 2: string}, per_page: array{0: string, 1: string, 2: string, 3: string}}
     */
    public function rules(): array
    {
        return [
            'page' => ['filled', 'integer', 'min:1'],
            'per_page' => ['filled', 'integer', 'min:1', 'max:50'],
        ];
    }
}
