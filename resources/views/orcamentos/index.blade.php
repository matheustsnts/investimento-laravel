{{-- resources/views/orcamentos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Orçamentos')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-calendar3"></i>
                Orçamentos Mensais
            </h1>
            <p class="text-muted mb-0">Acompanhe seus orçamentos por mês e veja rapidamente o status geral.</p>
        </div>

        <a href="{{ route('orcamentos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Novo Orçamento
        </a>
    </div>

    {{-- Alerts via SweetAlert já estão no layout, mas se quiser manter os alerts do Bootstrap também: --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($orcamentos->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <h5 class="card-title mb-3">Nenhum orçamento cadastrado ainda</h5>
                <p class="text-muted mb-4">
                    Comece criando um orçamento para o mês atual e organize sua vida financeira.
                </p>
                <a href="{{ route('orcamentos.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    Criar primeiro orçamento
                </a>
            </div>
        </div>
    @else
        {{-- Grid responsivo de cards --}}
        <div class="row g-3 g-md-4">
            @foreach ($orcamentos as $orcamento)
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 orcamento-card">
                        <div class="card-body d-flex flex-column p-3 p-md-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <i class="bi bi-calendar3"></i>
                                        {{ \Carbon\Carbon::parse($orcamento->mes_referencia . '-01')->translatedFormat('F \d\e Y') }}
                                    </h5>
                                    <small class="text-muted">
                                        Salário líquido: 
                                        <strong>R$ {{ number_format($orcamento->salario_liquido, 2, ',', '.') }}</strong>
                                    </small>
                                </div>

                                <span class="badge bg-secondary text-uppercase small">
                                    {{ $orcamento->mes_referencia }}
                                </span>
                            </div>

                            {{-- Resumo rápido (exemplo: total de gastos x orçado) --}}
                            @php
                                $totalOrcado = ($orcamento->valor_investimentos ?? 0)
                                            + ($orcamento->valor_custos_fixos ?? 0)
                                            + ($orcamento->valor_conforto ?? 0)
                                            + ($orcamento->valor_metas ?? 0)
                                            + ($orcamento->valor_prazeres ?? 0)
                                            + ($orcamento->valor_conhecimento ?? 0);

                                $totalGasto = $orcamento->gastos()->sum('valor');
                                $percentGasto = $totalOrcado > 0 ? ($totalGasto / $totalOrcado) * 100 : 0;
                                $percentGasto = min(100, round($percentGasto, 1));
                            @endphp

                            <div class="mb-2">
                                <small class="text-muted">
                                    Total orçado:
                                    <strong>R$ {{ number_format($totalOrcado, 2, ',', '.') }}</strong>
                                </small>
                                <br>
                                <small class="text-muted">
                                    Total gasto:
                                    <strong>R$ {{ number_format($totalGasto, 2, ',', '.') }}</strong>
                                </small>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Consumo do orçamento</span>
                                    <span class="fw-semibold {{ $percentGasto >= 90 ? 'text-danger' : ($percentGasto >= 70 ? 'text-warning' : 'text-success') }}">
                                        {{ $percentGasto }}%
                                    </span>
                                </div>
                                <div class="progress" style="height: 8px;">
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
                            </div>

                            <div class="mt-auto d-flex justify-content-between gap-2">
                                <a href="{{ route('orcamentos.show', $orcamento->id) }}" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="bi bi-eye"></i>
                                    Detalhes
                                </a>
                                <a href="{{ route('orcamentos.edit', $orcamento->id) }}" class="btn btn-sm btn-outline-warning w-100">
                                    <i class="bi bi-pencil"></i>
                                    Editar
                                </a>
                            </div>

                            {{-- Formulário de exclusão com SweetAlert --}}
                            <form action="{{ route('orcamentos.destroy', $orcamento->id) }}"
                                  method="POST"
                                  class="mt-2 form-delete-confirm"
                                  data-message="Tem certeza que deseja excluir o orçamento de {{ \Carbon\Carbon::parse($orcamento->mes_referencia . '-01')->translatedFormat('F \d\e Y') }}? Todos os gastos vinculados também serão excluídos. Esta ação não pode ser desfeita.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="bi bi-trash"></i>
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection