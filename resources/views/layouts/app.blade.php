{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestão Financeira')</title>

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="theme-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            {{-- Logo / Nome do sistema --}}
            <a class="navbar-brand" href="{{ route('orcamentos.index') }}">
                <i class="bi bi-wallet2"></i>
                Gestão Financeira
            </a>

            {{-- Botão hamburguer mobile --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    {{-- Link Orçamentos --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('orcamentos.*') ? 'active' : '' }}"
                           href="{{ route('orcamentos.index') }}">
                            <i class="bi bi-calendar3"></i>
                            Orçamentos
                        </a>
                    </li>

                    {{-- Link Usuários (somente admin) --}}
                    @auth
                        @if(method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                                   href="{{ route('users.index') }}">
                                    <i class="bi bi-people"></i>
                                    Usuários
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav ms-auto align-items-center">
                    {{-- Toggle de tema light/dark --}}
                    <li class="nav-item mx-2">
                        <button id="themeToggleBtn"
                                class="btn btn-outline-light btn-sm"
                                type="button"
                                title="Alternar tema claro/escuro">
                            <i class="bi bi-moon-stars" id="themeToggleIcon"></i>
                        </button>
                    </li>

                    {{-- Usuário logado / login --}}
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center"
                               href="#"
                               role="button"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>
                                <span>{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right"></i>
                                            Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}"
                               href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Entrar
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- Conteúdo principal --}}
    <main class="py-4">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-light text-center py-3 mt-5 footer-theme">
        <p class="mb-0 text-muted">
            Desenvolvido por <strong>Matheus Teixeira 🤩</strong>
        </p>
    </footer>

    {{-- Scripts adicionais das views --}}
    @stack('scripts')

    {{-- SweetAlert para mensagens flash (opcional, mas útil) --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso',
                        text: @json(session('success')),
                        timer: 2500,
                        showConfirmButton: false
                    });
                }
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: @json(session('error')),
                    });
                }
            });
        </script>
    @endif
</body>
</html>