<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class FindBusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from' => 'required|exists:stops,id',
            'to' => 'required|exists:stops,id|different:from',
        ];
    }

    public function messages(): array
    {
        return [
            'from.required' => 'Начальная остановка обязательна',
            'from.exists' => 'Выбранная начальная остановка не существует',
            'to.required' => 'Конечная остановка обязательна',
            'to.exists' => 'Выбранная конечная остановка не существует',
            'to.different' => 'Конечная остановка должна отличаться от начальной'
        ];
    }

}
