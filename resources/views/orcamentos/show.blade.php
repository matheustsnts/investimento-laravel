{{-- resources/views/orcamentos/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalhes do Orçamento')

@section('content')
<div class="container py-4">
    {{-- Cabeçalho --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-calendar3"></i>
                Orçamento – 
                {{ \Carbon\Carbon::parse($orcamento->mes_referencia . '-01')->translatedFormat('F \d\e Y') }}
            </h1>
            <p class="text-muted mb-0">
                Visão detalhada das categorias, valores orçados, gastos e saldos.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('orcamentos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Voltar
            </a>
            <a href="{{ route('orcamentos.edit', $orcamento->id) }}" class="btn btn-outline-warning">
                <i class="bi bi-pencil"></i>
                Editar
            </a>
            <form action="{{ route('orcamentos.destroy', $orcamento->id) }}"
                  method="POST"
                  data-message="Tem certeza que deseja excluir este orçamento? Esta ação não pode ser desfeita.">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash"></i>
                    Excluir
                </button>
            </form>
        </div>
    </div>

    {{-- Alerts --}}
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

    {{-- Resumo geral --}}
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
        $saldoGeral = $totalOrcado - $totalGasto;
    @endphp

    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small mb-2">Salário</h6>
                    <p class="mb-1">
                        Salário bruto: 
                        <strong>R$ {{ number_format($orcamento->salario_bruto, 2, ',', '.') }}</strong>
                    </p>
                    <p class="mb-1">
                        Dízimo: 
                        <strong>R$ {{ number_format($orcamento->dizimo ?? 0, 2, ',', '.') }}</strong>
                    </p>
                    <p class="mb-0">
                        Salário líquido:
                        <strong>R$ {{ number_format($orcamento->salario_liquido, 2, ',', '.') }}</strong>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small mb-2">Orçamento</h6>
                    <p class="mb-1">
                        Total orçado:
                        <strong>R$ {{ number_format($totalOrcado, 2, ',', '.') }}</strong>
                    </p>
                    <p class="mb-1">
                        Total gasto:
                        <strong>R$ {{ number_format($totalGasto, 2, ',', '.') }}</strong>
                    </p>
                    <p class="mb-0">
                        Saldo geral:
                        <strong class="{{ $saldoGeral < 0 ? 'text-danger' : 'text-success' }}">
                            R$ {{ number_format($saldoGeral, 2, ',', '.') }}
                        </strong>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small mb-2">Consumo</h6>
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
                    <small class="text-muted d-block mt-2">
                        Status geral do mês com base no total orçado vs. gastos.
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Cards por categoria --}}
    @php
        // Ajuste esses nomes de acordo com seu model
        $categorias = [
            'investimentos' => [
                'label' => 'Investimentos',
                'percentual' => $orcamento->percentual_investimentos ?? 0,
                'valor' => $orcamento->valor_investimentos ?? 0,
                'icon' => 'bi-graph-up-arrow',
                'badge' => 'bg-success',
            ],
            'custos_fixos' => [
                'label' => 'Custos Fixos',
                'percentual' => $orcamento->percentual_custos_fixos ?? 0,
                'valor' => $orcamento->valor_custos_fixos ?? 0,
                'icon' => 'bi-house-door',
                'badge' => 'bg-primary',
            ],
            'conforto' => [
                'label' => 'Conforto',
                'percentual' => $orcamento->percentual_conforto ?? 0,
                'valor' => $orcamento->valor_conforto ?? 0,
                'icon' => 'bi-couch',
                'badge' => 'bg-info',
            ],
            'metas' => [
                'label' => 'Metas',
                'percentual' => $orcamento->percentual_metas ?? 0,
                'valor' => $orcamento->valor_metas ?? 0,
                'icon' => 'bi-bullseye',
                'badge' => 'bg-warning',
            ],
            'prazeres' => [
                'label' => 'Prazeres',
                'percentual' => $orcamento->percentual_prazeres ?? 0,
                'valor' => $orcamento->valor_prazeres ?? 0,
                'icon' => 'bi-emoji-smile',
                'badge' => 'bg-danger',
            ],
            'conhecimento' => [
                'label' => 'Conhecimento',
                'percentual' => $orcamento->percentual_conhecimento ?? 0,
                'valor' => $orcamento->valor_conhecimento ?? 0,
                'icon' => 'bi-book',
                'badge' => 'bg-secondary',
            ],
        ];
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-2 mt-2">
        <h2 class="h5 mb-0">Categorias</h2>
        <a href="{{ route('gastos.create', $orcamento->id) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i>
            Registrar Gasto
        </a>
    </div>
    <p class="text-muted mb-3">Veja o status de cada categoria e quanto já foi consumido.</p>

    <div class="row g-3 g-md-4 mb-4">
        @foreach ($categorias as $key => $cat)
            @php
                $gastoCategoria = $orcamento->gastos()->where('categoria', $key)->sum('valor');
                $saldoCategoria = $cat['valor'] - $gastoCategoria;
                $percentCat = $cat['valor'] > 0 ? ($gastoCategoria / $cat['valor']) * 100 : 0;
                $percentCat = min(100, round($percentCat, 1));
            @endphp

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 orcamento-card">
                    <div class="card-body d-flex flex-column p-3 p-md-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="card-title mb-1">
                                    <i class="bi {{ $cat['icon'] }}"></i>
                                    {{ $cat['label'] }}
                                </h5>
                                <small class="text-muted">
                                    {{ $cat['percentual'] }}% do salário líquido
                                </small>
                            </div>
                            <span class="badge {{ $cat['badge'] }}">
                                R$ {{ number_format($cat['valor'], 2, ',', '.') }}
                            </span>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted d-block">
                                Gasto:
                                <strong>R$ {{ number_format($gastoCategoria, 2, ',', '.') }}</strong>
                            </small>
                            <small class="text-muted d-block">
                                Saldo:
                                <strong class="{{ $saldoCategoria < 0 ? 'text-danger' : 'text-success' }}">
                                    R$ {{ number_format($saldoCategoria, 2, ',', '.') }}
                                </strong>
                            </small>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Consumo</span>
                                <span class="fw-semibold {{ $percentCat >= 90 ? 'text-danger' : ($percentCat >= 70 ? 'text-warning' : 'text-success') }}">
                                    {{ $percentCat }}%
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div
                                    class="progress-bar 
                                        @if($percentCat >= 90) bg-danger 
                                        @elseif($percentCat >= 70) bg-warning 
                                        @else bg-success @endif"
                                    role="progressbar"
                                    style="width: {{ $percentCat }}%;"
                                    aria-valuenow="{{ $percentCat }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100"
                                ></div>
                            </div>
                        </div>

                        <div class="mt-auto d-flex justify-content-between gap-2">
                            <a href="{{ route('gastos.create', $orcamento->id) }}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-plus-circle"></i>
                                Novo Gasto
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Histórico de gastos --}}
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">
                <i class="bi bi-list-ul"></i>
                Histórico de Gastos
            </h2>
            <span class="badge bg-secondary">
                {{ $orcamento->gastos()->count() }} registro(s)
            </span>
        </div>
        <div class="card-body p-0">
            @if ($orcamento->gastos()->count() === 0)
                <p class="text-center text-muted py-4 mb-0">
                    Nenhum gasto registrado ainda para este orçamento.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Categoria</th>
                                <th>Descrição</th>
                                <th class="text-end">Valor</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($orcamento->gastos()->orderBy('data_gasto', 'desc')->get() as $gasto)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($gasto->data_gasto)->format('d/m/Y') }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $gasto->categoria)) }}</td>
                                <td>{{ $gasto->descricao }}</td>
                                <td class="text-end">
                                    R$ {{ number_format($gasto->valor, 2, ',', '.') }}
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('gastos.edit', $gasto->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('gastos.destroy', $gasto->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Tem certeza que deseja excluir este gasto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection