<?php

namespace App\Http\Request;

use App\Enums\DirectionType;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRouteDirectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'direction' => new Enum(DirectionType::class),
            'stops' => 'array',
            'stops.*' => 'exists:stops,id'

        ];
    }

    public function messages(): array
    {
        return [
            'direction in' => 'Тип направления может быть только ПРЯМОЕ или ОБРАТНОЕ',
            'stops.array' => 'Остановки должны быть представлены в виде массива',
            'stops.*.exists' => 'Указанная остановка не существует'
        ];
    }

}
