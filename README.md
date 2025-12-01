# Sistema de Gestão Financeira Pessoal

Aplicação web para controle financeiro pessoal baseada no método do “Investidor Sardinha” , construída em **Laravel 11**, **PostgreSQL**, **Docker**, **Vite**, **Bootstrap 5** e **jQuery**.  
Permite cadastrar orçamentos mensais, distribuir o salário líquido em categorias percentuais que somam 100% e registrar gastos vinculados a cada orçamento.

[Vídeo do projeto](https://seulink.net/EducacaoFinanceira)

---

### 🧱 Tecnologias Utilizadas

- **Backend**
  - PHP 8.x
  - Laravel 11
  - PostgreSQL
- **Frontend**
  - Bootstrap 5 (layout responsivo + tema claro/escuro)
  - jQuery
  - Vite (build dos assets)
- **Infraestrutura / Dev**
  - Docker + Docker Compose
  - Node + NPM (dentro de container ou local)
- **Extras**
  - Autenticação Laravel (login/logout)
  - SweetAlert2 (confirmações e mensagens amigáveis)
  - UUIDs como chave primária nas principais tabelas

---

### 📂 Estrutura Geral da Aplicação

Principais módulos:

1.  **Autenticação**
    -   Registro de usuário, login, logout.
    -   Menu com nome do usuário logado.
    -   (Opcional) Flag `is_admin` para gestão de usuários.

2.  **Orçamentos**
    -   Cada orçamento é mensal (`mes_referencia` no formato `YYYY-MM`).
    -   Campos principais:
        -   `mes_referencia` (ex: `2025-01`)
        -   `salario_bruto`
        -   `dizimo` (opcional)
        -   `salario_liquido` (calculado: bruto – dízimo)
        -   Percentuais:
            -   `percentual_investimentos`
            -   `percentual_custos_fixos`
            -   `percentual_conforto`
            -   `percentual_metas`
            -   `percentual_prazeres`
            -   `percentual_conhecimento`
        -   Valores calculados:
            -   `valor_investimentos`
            -   `valor_custos_fixos`
            -   `valor_conforto`
            -   `valor_metas`
            -   `valor_prazeres`
            -   `valor_conhecimento`

    -   **Regra importante:** a soma dos percentuais deve ser **exatamente 100%**.

3.  **Gastos**
    -   Cada gasto pertence a um orçamento.
    -   Campos principais:
        -   `orcamento_id` (UUID)
        -   `categoria` (investimentos, custos fixos, conforto, metas, prazeres, conhecimento)
        -   `data_gasto`
        -   `descricao`
        -   `valor`
        -   `observacao` (opcional)

---

### 🧮 Lógica de Distribuição do Salário

A aplicação permite que o usuário defina quantos **% do salário líquido** vão para cada categoria.  
Uma sugestão (ponto de partida) é:

-   Investimentos: **30%**
-   Custos fixos: **40%**
-   Conforto: **10%**
-   Metas: **10%**
-   Prazeres: **5%**
-   Conhecimento: **5%**

Total:

$$30 + 40 + 10 + 10 + 5 + 5 = 100\%$$

A partir do salário líquido, a aplicação calcula:

-   `valor_investimentos   = salario_liquido * (percentual_investimentos   / 100)`
-   `valor_custos_fixos    = salario_liquido * (percentual_custos_fixos   / 100)`
-   `valor_conforto        = salario_liquido * (percentual_conforto       / 100)`
-   `valor_metas           = salario_liquido * (percentual_metas          / 100)`
-   `valor_prazeres        = salario_liquido * (percentual_prazeres       / 100)`
-   `valor_conhecimento    = salario_liquido * (percentual_conhecimento   / 100)`

---

### ✅ Validações Importantes

A `OrcamentoRequest` garante:

-   `mes_referencia` obrigatório no formato `Y-m`.
-   `salario_bruto` numérico e não negativo.
-   `dizimo` numérico e não negativo (opcional).
-   Todos os percentuais são obrigatórios, numéricos, entre 0 e 100.
-   **Regra de soma:**  

    $$\text{percentual_investimentos} + \text{percentual_custos_fixos} + \text{percentual_conforto} + \text{percentual_metas} + \text{percentual_prazeres} + \text{percentual_conhecimento} = 100\%$$

Se a soma for diferente de 100%, a request adiciona um erro específico:

> A soma dos percentuais deve ser exatamente 100%.

---

### 🎨 Interface e Responsividade

-   **Layout principal:** `layouts/app.blade.php`
    -   Navbar com:
        -   Logo / nome do sistema.
        -   Link para Orçamentos.
        -   (Opcional) Link para Usuários (somente admin).
        -   Botão de alternância de tema (claro/escuro).
        -   Menu de usuário logado e logout.
    -   `@yield('content')` para conteúdo das páginas.
    -   Footer simples responsável, mantendo contraste no tema claro/escuro.

-   **Tema Claro/Escuro**
    -   Controlado por botão na navbar (`#themeToggleBtn`).
    -   Classes `body.theme-light` e `body.theme-dark`.
    -   Escolha do usuário salva em `localStorage`.
    -   Ajuste de cores para textos, cards e footer em ambos os temas.

-   **Páginas principais**
    -   `orcamentos.index`:
        -   Lista cards de orçamentos mensais.
        -   Cada card mostra:
            -   Mês de referência formatado (ex: “Janeiro de 2025”).
            -   Salário líquido.
            -   Total orçado e total gasto.
            -   Barra de progresso (% de consumo do orçamento) com cores:
                -   Verde: até ~70%
                -   Amarelo: ~70–90%
                -   Vermelho: acima de 90%
            -   Botões: Detalhes, Editar, Excluir (com SweetAlert2).
    -   `orcamentos.create` e `orcamentos.edit`:
        -   Formulário dividido em seções:
            -   Dados básicos (mês, salário, dízimo).
            -   Distribuição de percentuais (6 campos) com somador dinâmico (JS).
            -   Exibição da soma em um campo de leitura (ex: “100%`).
        -   Uso de `old()` para preservar dados em caso de erro.
        -   Classes `is-invalid` e mensagens de erro diretamente abaixo dos inputs.
    -   `orcamentos.show`:
        -   Resumo do orçamento (salário, total orçado, total gasto, saldo).
        -   Tabelas e cards de gastos por categoria.
        -   Acesso rápido para adicionar novos gastos.
    -   `gastos.create` e `gastos.edit`:
        -   Formulário amigável com:
            -   Categoria (select).
            -   Data do gasto.
            -   Descrição.
            -   Valor.
            -   Observação opcional.
        -   Card lateral com contexto do orçamento (salário, orçado, gasto, saldo, barra de consumo).
    -   Todos os layouts usam **Bootstrap grid** (`row`, `col-12`, `col-md-6`, `col-lg-4`) para ficar bem em celular, tablet e desktop.

---

### 🔔 SweetAlert2 – Confirmações e Mensagens

-   **Confirmação de exclusão**:
    -   Formularios de delete usam a classe `.form-delete-confirm` e o atributo `data-message`.
    -   Exemplo:

    ```blade
    <form action="{{ route('orcamentos.destroy', $orcamento->id) }}"
          method="POST"
          class="form-delete-confirm"
          data-message="Tem certeza que deseja excluir este orçamento? Todos os gastos vinculados também serão excluídos.">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-trash"></i> Excluir
        </button>
    </form>
    ```

    -   No `app.js`, um listener intercepta o `submit`, mostra o SweetAlert, e só submete de fato se o usuário confirmar.

-   **Mensagens de sucesso/erro (flash)**:
    -   No `layouts/app.blade.php`, se existir `session('success')` ou `session('error')`, é disparado um SweetAlert automático após o carregamento da página.

---

### 🐳 Ambiente com Docker

A aplicação foi pensada para rodar em containers, com algo semelhante a:

-   **Serviços**:
    -   `app`: container PHP-FPM/Laravel.
    -   `postgres`: PostgreSQL.
    -   `webserver`: servidor web http.
    -   `npm`: container para rodar `npm install`, `npm run dev`, etc.

Passos gerais (podem variar conforme seu `docker-compose.yml` e `Dockerfile`):

1.  Subir os containers:

    ```bash
    docker-compose up -d
    ```

2.  Instalar dependências PHP:

    ```bash
    docker-compose run --rm composer install
    ```

3.  Instalar dependências Node:

    ```bash
    docker-compose run --rm npm install
    ```

4.  Rodar migrations e seeders (se houver):

    ```bash
    docker-compose run --rm artisan migrate
    # docker-compose run --rm artisan db:seed
    ```

5.  Gerar a key da aplicação:

    ```bash
    docker-compose run --rm artisan key:generate
    ```

6.  Build dos assets para produção:

    ```bash
    docker-compose run --rm npm run build
    ```

    Ou, em desenvolvimento:

    ```bash
    docker-compose run --rm npm run dev
    ```

---

### ⚙️ Configuração do `.env`

Os principais pontos do `.env`:

```env
APP_NAME="Gestao Financeira"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=gestao_financeira
DB_USERNAME=postgres
DB_PASSWORD=postgres

# Vite
VITE_APP_NAME="${APP_NAME}"
