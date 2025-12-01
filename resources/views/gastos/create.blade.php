{{-- resources/views/gastos/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Registrar Gasto')

@section('content')
<div class="container py-4">
    {{-- Cabeçalho --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-receipt"></i>
                Registrar Gasto
            </h1>
            <p class="text-muted mb-0">
                Vinculando gasto ao orçamento de 
                <strong>
                    {{ \Carbon\Carbon::parse($orcamento->mes_referencia . '-01')->translatedFormat('F \d\e Y') }}
                </strong>.
            </p>
        </div>

        <a href="{{ route('orcamentos.show', $orcamento->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Voltar para o orçamento
        </a>
    </div>

    {{-- Alerts --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Verifique os erros abaixo:<br><br>
            <ul class="mb-0">
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('gastos.store', $orcamento->id) }}" method="POST">
        @csrf

        <div class="row g-3 g-md-4">
            {{-- Coluna esquerda: contexto do orçamento --}}
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-wallet2"></i>
                            Orçamento do Mês
                        </h5>

                        @php
                            $totalOrcado = ($orcamento->valor_investimentos ?? 0)
                                        + ($orcamento->valor_custos_fixos ?? 0)
                                        + ($orcamento->valor_conforto ?? 0)
                                        + ($orcamento->valor_metas ?? 0)
                                        + ($orcamento->valor_prazeres ?? 0)
                                        + ($orcamento->valor_conhecimento ?? 0);

                            $totalGasto = $orcamento->gastos()->sum('valor');
                            $saldoGeral = $totalOrcado - $totalGasto;
                            $percentGasto = $totalOrcado > 0 ? ($totalGasto / $totalOrcado) * 100 : 0;
                            $percentGasto = min(100, round($percentGasto, 1));
                        @endphp

                        <p class="mb-1">
                            <span class="text-muted">Salário líquido:</span><br>
                            <strong>R$ {{ number_format($orcamento->salario_liquido, 2, ',', '.') }}</strong>
                        </p>
                        <p class="mb-1">
                            <span class="text-muted">Total orçado:</span><br>
                            <strong>R$ {{ number_format($totalOrcado, 2, ',', '.') }}</strong>
                        </p>
                        <p class="mb-1">
                            <span class="text-muted">Total gasto:</span><br>
                            <strong>R$ {{ number_format($totalGasto, 2, ',', '.') }}</strong>
                        </p>
                        <p class="mb-0">
                            <span class="text-muted">Saldo geral:</span><br>
                            <strong class="{{ $saldoGeral < 0 ? 'text-danger' : 'text-success' }}">
                                R$ {{ number_format($saldoGeral, 2, ',', '.') }}
                            </strong>
                        </p>

                        <hr>

                        <div class="mb-1 small d-flex justify-content-between">
                            <span class="text-muted">Consumo do orçamento</span>
                            <span class="{{ $percentGasto >= 90 ? 'text-danger' : ($percentGasto >= 70 ? 'text-warning' : 'text-success') }}">
                                {{ $percentGasto }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div 
                                class="progress-bar
                                    @if($percentGasto >= 90) bg-danger
                                    @elseif($percentGasto >= 70) bg-warning
                                    @else bg-success @endif"
                                role="progressbar"
                                style="width: {{ $percentGasto }}%;"
                                aria-valuenow="{{ $percentGasto }}"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></div>
                        </div>

                        <small class="text-muted d-block mt-2">
                            Este gasto será somado ao total de {{ $orcamento->mes_referencia }}.
                        </small>
                    </div>
                </div>
            </div>

            {{-- Coluna direita: formulário do gasto --}}
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-receipt-cutoff"></i>
                            Dados do Gasto
                        </h5>

                        <div class="row g-3">
                            {{-- Categoria --}}
                            <div class="col-12 col-md-6">
                                <label for="categoria" class="form-label">Categoria</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-tags"></i>
                                    </span>
                                    <select name="categoria" id="categoria" class="form-select" required>
                                        @foreach ($categorias as $key => $label)
                                            <option value="{{ $key }}" {{ old('categoria') === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <small class="text-muted">
                                    Escolha a categoria na qual este gasto se encaixa.
                                </small>
                            </div>

                            {{-- Data do gasto --}}
                            <div class="col-12 col-md-6">
                                <label for="data_gasto" class="form-label">Data do Gasto</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-calendar-event"></i>
                                    </span>
                                    <input type="date"
                                           name="data_gasto"
                                           id="data_gasto"
                                           class="form-control"
                                           value="{{ old('data_gasto', now()->toDateString()) }}"
                                           required>
                                </div>
                            </div>

                            {{-- Descrição --}}
                            <div class="col-12">
                                <label for="descricao" class="form-label">Descrição</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-card-text"></i>
                                    </span>
                                    <input type="text"
                                           name="descricao"
                                           id="descricao"
                                           class="form-control"
                                           value="{{ old('descricao') }}"
                                           placeholder="Ex.: Supermercado, conta de luz, gasolina..."
                                           required>
                                </div>
                            </div>

                            {{-- Valor --}}
                            <div class="col-12 col-md-6">
                                <label for="valor" class="form-label">Valor</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number"
                                           name="valor"
                                           id="valor"
                                           class="form-control"
                                           step="0.01"
                                           min="0.01"
                                           value="{{ old('valor') }}"
                                           required>
                                </div>
                            </div>

                            {{-- Observação --}}
                            <div class="col-12 col-md-6">
                                <label for="observacao" class="form-label">Observação (opcional)</label>
                                <textarea name="observacao"
                                          id="observacao"
                                          class="form-control"
                                          rows="2"
                                          placeholder="Algum detalhe adicional sobre este gasto?">{{ old('observacao') }}</textarea>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('orcamentos.show', $orcamento->id) }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i>
                                Salvar Gasto
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection