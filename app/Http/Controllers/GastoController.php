<?php

namespace App\Http\Controllers;

use App\Http\Requests\GastoRequest;
use App\Models\Gasto;
use App\Models\Orcamento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GastoController extends Controller
{
    public function create(Orcamento $orcamento)
    {
        $categorias = Gasto::categorias();
        return view('gastos.create', compact('orcamento', 'categorias'));
    }

    public function store(GastoRequest $request, Orcamento $orcamento)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $data['orcamento_id'] = $orcamento->id;
            Gasto::create($data);

            DB::commit();

            return redirect()
                ->route('orcamentos.show', $orcamento)
                ->with('success', 'Gasto registrado com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao criar gasto', [
                'orcamento_id' => $orcamento->id,
                'error'        => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocorreu um erro ao registrar o gasto. Tente novamente.');
        }
    }

    public function edit(Orcamento $orcamento, Gasto $gasto)
    {
        $categorias = Gasto::categorias();
        return view('gastos.edit', compact('orcamento', 'gasto', 'categorias'));
    }

    public function update(GastoRequest $request, Orcamento $orcamento, Gasto $gasto)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $gasto->update($data);

            DB::commit();

            return redirect()
                ->route('orcamentos.show', $orcamento)
                ->with('success', 'Gasto atualizado com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar gasto', [
                'gasto_id'     => $gasto->id,
                'orcamento_id' => $orcamento->id,
                'error'        => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocorreu um erro ao atualizar o gasto. Tente novamente.');
        }
    }

    public function destroy(Orcamento $orcamento, Gasto $gasto)
    {
        DB::beginTransaction();

        try {
            $gasto->delete();

            DB::commit();

            return redirect()
                ->route('orcamentos.show', $orcamento)
                ->with('success', 'Gasto excluído com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao excluir gasto', [
                'gasto_id'     => $gasto->id,
                'orcamento_id' => $orcamento->id,
                'error'        => $e->getMessage(),
            ]);

            return redirect()
                ->route('orcamentos.show', $orcamento)
                ->with('error', 'Não foi possível excluir o gasto. Tente novamente.');
        }
    }
}