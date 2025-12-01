<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrcamentoRequest;
use App\Models\Orcamento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrcamentoController extends Controller
{
    public function index()
    {
        $orcamentos = Orcamento::orderBy('mes_referencia', 'desc')->get();
        return view('orcamentos.index', compact('orcamentos'));
    }

    public function create()
    {
        return view('orcamentos.create');
    }

    public function store(OrcamentoRequest $request)
    {
        $data = $request->validated();
        $data['dizimo'] = $data['dizimo'] ?? 0;

        DB::beginTransaction();

        try {
            $orcamento = new Orcamento($data);
            $orcamento->calcularValores();
            $orcamento->save();

            DB::commit();

            return redirect()
                ->route('orcamentos.show', $orcamento)
                ->with('success', 'Orçamento criado com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao criar orçamento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocorreu um erro ao criar o orçamento. Tente novamente.');
        }
    }

    public function show(Orcamento $orcamento)
    {
        $orcamento->load('gastos');

        $saldos = [
            'investimentos' => $orcamento->getSaldoPorCategoria('investimentos'),
            'custos_fixos'  => $orcamento->getSaldoPorCategoria('custos_fixos'),
            'conforto'      => $orcamento->getSaldoPorCategoria('conforto'),
            'metas'         => $orcamento->getSaldoPorCategoria('metas'),
            'prazeres'      => $orcamento->getSaldoPorCategoria('prazeres'),
            'conhecimento'  => $orcamento->getSaldoPorCategoria('conhecimento'),
        ];

        return view('orcamentos.show', compact('orcamento', 'saldos'));
    }

    public function edit(Orcamento $orcamento)
    {
        return view('orcamentos.edit', compact('orcamento'));
    }

    public function update(OrcamentoRequest $request, Orcamento $orcamento)
    {
        $data = $request->validated();
        $data['dizimo'] = $data['dizimo'] ?? 0;

        DB::beginTransaction();

        try {
            $orcamento->fill($data);
            $orcamento->calcularValores();
            $orcamento->save();

            DB::commit();

            return redirect()
                ->route('orcamentos.show', $orcamento)
                ->with('success', 'Orçamento atualizado com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar orçamento', [
                'orcamento_id' => $orcamento->id,
                'error'        => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocorreu um erro ao atualizar o orçamento. Tente novamente.');
        }
    }

    public function destroy(Orcamento $orcamento)
    {
        DB::beginTransaction();

        try {
            $orcamento->delete();

            DB::commit();

            return redirect()
                ->route('orcamentos.index')
                ->with('success', 'Orçamento excluído com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao excluir orçamento', [
                'orcamento_id' => $orcamento->id,
                'error'        => $e->getMessage(),
            ]);

            return redirect()
                ->route('orcamentos.index')
                ->with('error', 'Não foi possível excluir o orçamento. Tente novamente.');
        }
    }
}