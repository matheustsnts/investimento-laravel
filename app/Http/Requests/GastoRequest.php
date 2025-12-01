<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GastoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categoria'   => 'required|in:investimentos,custos_fixos,conforto,metas,prazeres,conhecimento',
            'descricao'   => 'required|string|max:255',
            'valor'       => 'required|numeric|min:0.01',
            'data_gasto'  => 'required|date',
            'observacao'  => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'categoria.required' => 'A categoria é obrigatória.',
            'categoria.in'       => 'Categoria inválida.',
            'descricao.required' => 'A descrição é obrigatória.',
            'valor.required'     => 'O valor é obrigatório.',
            'valor.numeric'      => 'O valor deve ser numérico.',
            'valor.min'          => 'O valor deve ser maior que zero.',
            'data_gasto.required'=> 'A data do gasto é obrigatória.',
            'data_gasto.date'    => 'A data do gasto é inválida.',
        ];
    }
}