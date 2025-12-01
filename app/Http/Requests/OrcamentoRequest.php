<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrcamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ajuste se tiver regras de autorização
        return true;
    }

    public function rules(): array
    {
        return [
            'mes_referencia' => ['required', 'date_format:Y-m'],
            'salario_bruto'  => ['required', 'numeric', 'min:0'],
            'dizimo'         => ['nullable', 'numeric', 'min:0'],

            'percentual_investimentos'   => ['required', 'numeric', 'min:0', 'max:100'],
            'percentual_custos_fixos'   => ['required', 'numeric', 'min:0', 'max:100'],
            'percentual_conforto'       => ['required', 'numeric', 'min:0', 'max:100'],
            'percentual_metas'          => ['required', 'numeric', 'min:0', 'max:100'],
            'percentual_prazeres'       => ['required', 'numeric', 'min:0', 'max:100'],
            'percentual_conhecimento'   => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function withValidator($validator)
    {
        // Garante que a soma dos percentuais é 100%
        $validator->after(function ($validator) {
            $data = $this->all();

            $soma =
                (float)($data['percentual_investimentos']   ?? 0) +
                (float)($data['percentual_custos_fixos']   ?? 0) +
                (float)($data['percentual_conforto']       ?? 0) +
                (float)($data['percentual_metas']          ?? 0) +
                (float)($data['percentual_prazeres']       ?? 0) +
                (float)($data['percentual_conhecimento']   ?? 0);

            if (abs($soma - 100) > 0.01) {
                $validator->errors()->add(
                    'percentuais',
                    'A soma dos percentuais deve ser exatamente 100%. Atualmente está em ' . number_format($soma, 2, ',', '.') . '%.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'mes_referencia.required' => 'O mês de referência é obrigatório.',
            'mes_referencia.date_format' => 'O mês de referência deve estar no formato ano-mês (ex.: 2025-01).',

            'salario_bruto.required' => 'O salário bruto é obrigatório.',
            'salario_bruto.numeric'  => 'O salário bruto deve ser um número.',
            'salario_bruto.min'      => 'O salário bruto não pode ser negativo.',

            'dizimo.numeric' => 'O dízimo deve ser um número.',
            'dizimo.min'     => 'O dízimo não pode ser negativo.',

            'percentual_investimentos.required' => 'O percentual de investimentos é obrigatório.',
            'percentual_investimentos.numeric'  => 'O percentual de investimentos deve ser um número.',
            'percentual_investimentos.min'      => 'O percentual de investimentos não pode ser negativo.',
            'percentual_investimentos.max'      => 'O percentual de investimentos não pode ser maior que 100%.',

            'percentual_custos_fixos.required' => 'O percentual de custos fixos é obrigatório.',
            'percentual_custos_fixos.numeric'  => 'O percentual de custos fixos deve ser um número.',
            'percentual_custos_fixos.min'      => 'O percentual de custos fixos não pode ser negativo.',
            'percentual_custos_fixos.max'      => 'O percentual de custos fixos não pode ser maior que 100%.',

            'percentual_conforto.required' => 'O percentual de conforto é obrigatório.',
            'percentual_conforto.numeric'  => 'O percentual de conforto deve ser um número.',
            'percentual_conforto.min'      => 'O percentual de conforto não pode ser negativo.',
            'percentual_conforto.max'      => 'O percentual de conforto não pode ser maior que 100%.',

            'percentual_metas.required' => 'O percentual de metas é obrigatório.',
            'percentual_metas.numeric'  => 'O percentual de metas deve ser um número.',
            'percentual_metas.min'      => 'O percentual de metas não pode ser negativo.',
            'percentual_metas.max'      => 'O percentual de metas não pode ser maior que 100%.',

            'percentual_prazeres.required' => 'O percentual de prazeres é obrigatório.',
            'percentual_prazeres.numeric'  => 'O percentual de prazeres deve ser um número.',
            'percentual_prazeres.min'      => 'O percentual de prazeres não pode ser negativo.',
            'percentual_prazeres.max'      => 'O percentual de prazeres não pode ser maior que 100%.',

            'percentual_conhecimento.required' => 'O percentual de conhecimento é obrigatório.',
            'percentual_conhecimento.numeric'  => 'O percentual de conhecimento deve ser um número.',
            'percentual_conhecimento.min'      => 'O percentual de conhecimento não pode ser negativo.',
            'percentual_conhecimento.max'      => 'O percentual de conhecimento não pode ser maior que 100%.',
        ];
    }
}