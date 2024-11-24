<?php

namespace App\Http\Request;

use App\Enums\DirectionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateRouteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'directions' => 'array|min:1|max:2',
            'directions.*.direction' => new Enum(DirectionType::class),
            'directions.*.stops' => 'array',
            'directions.*.stops.*' => 'exists:stops,id'
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Название маршрута должно быть строкой',
            'name.max' => 'Название маршрута не должно превышать 255 символов',
            'directions.array' => 'Направления должны быть представлены в виде массива',
            'directions.min' => 'Должно быть указано минимум одно направление',
            'directions.max' => 'Можно указать максимум два направления',
            'directions.*.direction.in' => 'Тип направления может быть только ПРЯМОЕ или ОБРАТНОЕ',
            'directions.*.stops.array' => 'Остановки должны быть представлены в виде массива',
            'directions.*.stops.*.exists' => 'Указанная остановка не существует',
        ];
    }


}
