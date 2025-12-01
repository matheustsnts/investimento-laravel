@extends('layouts.app')

@section('title', 'Novo Usuário')

@section('content')
<div class="container mt-4">
    <h1 class="mb-3">
        <i class="bi bi-person-plus"></i>
        Novo Usuário
    </h1>

    <a href="{{ route('users.index') }}" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>

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

    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nome completo</label>
                    <input type="text"
                           name="name"
                           id="name"
                           class="form-control"
                           value="{{ old('name') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email"
                           name="email"
                           id="email"
                           class="form-control"
                           value="{{ old('email') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control"
                           required>
                    <small class="text-muted">Mínimo 6 caracteres</small>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar senha</label>
                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox"
                           name="is_admin"
                           id="is_admin"
                           class="form-check-input"
                           value="1"
                           {{ old('is_admin') ? 'checked' : '' }}>
                    <label for="is_admin" class="form-check-label">
                        <i class="bi bi-shield-check text-danger"></i>
                        Conceder privilégios de administrador
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i>
                    Criar Usuário
                </button>
            </form>
        </div>
    </div>
</div>
@endsection