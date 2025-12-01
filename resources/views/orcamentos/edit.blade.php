{{-- resources/views/orcamentos/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Orçamento')

@section('content')
<div class="container py-4">
    {{-- Cabeçalho --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-pencil-square"></i>
                Editar Orçamento Mensal
            </h1>
            <p class="text-muted mb-0">
                Ajuste o salário, dízimo ou a distribuição por categorias para este mês.
            </p>
        </div>

        <a href="{{ route('orcamentos.show', $orcamento->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Voltar para detalhes
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

    <form action="{{ route('orcamentos.update', $orcamento->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3 g-md-4">
            {{-- Coluna esquerda: dados gerais --}}
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-info-circle"></i>
                            Informações Gerais
                        </h5>

                        {{-- Mês de referência (normalmente não se altera; se quiser travar, coloque disabled) --}}
                        <div class="mb-3">
                            <label for="mes_referencia" class="form-label">Mês de Referência</label>
                            <input type="month"
                                   name="mes_referencia"
                                   id="mes_referencia"
                                   class="form-control"
                                   value="{{ old('mes_referencia', $orcamento->mes_referencia) }}"
                                   required>
                            <small class="text-muted">
                                Ex.: 2025-01 para Janeiro/2025.
                            </small>
                        </div>

                        {{-- Salário bruto --}}
                        <div class="mb-3">
                            <label for="salario_bruto" class="form-label">Salário Bruto</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number"
                                       name="salario_bruto"
                                       id="salario_bruto"
                                       class="form-control"
                                       step="0.01"
                                       min="0"
                                       value="{{ old('salario_bruto', $orcamento->salario_bruto) }}"
                                       required>
                            </div>
                        </div>

                        {{-- Dízimo --}}
                        <div class="mb-3">
                            <label for="dizimo" class="form-label">Dízimo (opcional)</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number"
                                       name="dizimo"
                                       id="dizimo"
                                       class="form-control"
                                       step="0.01"
                                       min="0"
                                       value="{{ old('dizimo', $orcamento->dizimo) }}">
                            </div>
                            <small class="text-muted">
                                Se deixar em branco, assume zero.
                            </small>
                        </div>

                        {{-- Salário líquido calculado (preview) --}}
                        <div class="mb-0">
                            <label class="form-label">Salário líquido (estimado)</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text"
                                       id="salario_liquido_preview"
                                       class="form-control"
                                       value="0,00"
                                       readonly>
                            </div>
                            <small class="text-muted">
                                Calculado como <code>salário bruto - dízimo</code>. 
                                O valor real salvo é recalculado no backend.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Coluna direita: percentuais por categoria --}}
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-pie-chart"></i>
                                Distribuição por Categorias
                            </h5>
                        </div>
                        <p class="text-muted mb-3">
                            Ajuste quantos % do salário líquido vão para cada categoria. A soma deve ser <strong>100%</strong>.
                        </p>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="percentual_investimentos" class="form-label">Investimentos</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-graph-up-arrow"></i></span>
                                    <input type="number"
                                           name="percentual_investimentos"
                                           id="percentual_investimentos"
                                           class="form-control percentual"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           value="{{ old('percentual_investimentos', $orcamento->percentual_investimentos) }}"
                                           required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="percentual_custos_fixos" class="form-label">Custos Fixos</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-house-door"></i></span>
                                    <input type="number"
                                           name="percentual_custos_fixos"
                                           id="percentual_custos_fixos"
                                           class="form-control percentual"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           value="{{ old('percentual_custos_fixos', $orcamento->percentual_custos_fixos) }}"
                                           required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="percentual_conforto" class="form-label">Conforto</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-couch"></i></span>
                                    <input type="number"
                                           name="percentual_conforto"
                                           id="percentual_conforto"
                                           class="form-control percentual"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           value="{{ old('percentual_conforto', $orcamento->percentual_conforto) }}"
                                           required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="percentual_metas" class="form-label">Metas</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-bullseye"></i></span>
                                    <input type="number"
                                           name="percentual_metas"
                                           id="percentual_metas"
                                           class="form-control percentual"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           value="{{ old('percentual_metas', $orcamento->percentual_metas) }}"
                                           required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="percentual_prazeres" class="form-label">Prazeres</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-emoji-smile"></i></span>
                                    <input type="number"
                                           name="percentual_prazeres"
                                           id="percentual_prazeres"
                                           class="form-control percentual"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           value="{{ old('percentual_prazeres', $orcamento->percentual_prazeres) }}"
                                           required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="percentual_conhecimento" class="form-label">Conhecimento</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-book"></i></span>
                                    <input type="number"
                                           name="percentual_conhecimento"
                                           id="percentual_conhecimento"
                                           class="form-control percentual"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           value="{{ old('percentual_conhecimento', $orcamento->percentual_conhecimento) }}"
                                           required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- Soma dos percentuais --}}
                        @php
                            $somaInicial = 
                                (float) old('percentual_investimentos', $orcamento->percentual_investimentos) +
                                (float) old('percentual_custos_fixos', $orcamento->percentual_custos_fixos) +
                                (float) old('percentual_conforto', $orcamento->percentual_conforto) +
                                (float) old('percentual_metas', $orcamento->percentual_metas) +
                                (float) old('percentual_prazeres', $orcamento->percentual_prazeres) +
                                (float) old('percentual_conhecimento', $orcamento->percentual_conhecimento);
                        @endphp

                        <div class="mb-3">
                            <label class="form-label">Soma dos percentuais</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-percent"></i></span>
                                <input type="text"
                                       id="soma_percentuais"
                                       class="form-control"
                                       value="{{ number_format($somaInicial, 2, ',', '.') }}%"
                                       readonly>
                            </div>
                            <small class="text-muted">
                                A soma deve ser exatamente <strong>100%</strong> para salvar o orçamento.
                            </small>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i>
                                Atualizar Orçamento
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function formatCurrencyBR(value) {
        const num = isNaN(value) ? 0 : value;
        return num.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function atualizarSalarioLiquidoPreview() {
        const bruto = parseFloat(document.getElementById('salario_bruto').value || 0);
        const dizimo = parseFloat(document.getElementById('dizimo').value || 0);

        const liquido = bruto - dizimo;
        const preview = document.getElementById('salario_liquido_preview');
        preview.value = formatCurrencyBR(liquido);
    }

    function atualizarSomaPercentuais() {
        let soma = 0;
        document.querySelectorAll('.percentual').forEach(function (input) {
            soma += parseFloat(input.value || 0);
        });

        const campoSoma = document.getElementById('soma_percentuais');
        campoSoma.value = soma.toFixed(2) + '%';

        if (Math.abs(soma - 100) > 0.01) {
            campoSoma.classList.add('is-invalid');
        } else {
            campoSoma.classList.remove('is-invalid');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Eventos para recalcular salário líquido
        document.getElementById('salario_bruto').addEventListener('input', atualizarSalarioLiquidoPreview);
        document.getElementById('dizimo').addEventListener('input', atualizarSalarioLiquidoPreview);

        // Eventos para recalcular soma dos percentuais
        document.querySelectorAll('.percentual').forEach(function (input) {
            input.addEventListener('input', atualizarSomaPercentuais);
        });

        // Atualiza valores iniciais com base no que veio do backend/old()
        atualizarSalarioLiquidoPreview();
        atualizarSomaPercentuais();
    });
</script>
@endpush