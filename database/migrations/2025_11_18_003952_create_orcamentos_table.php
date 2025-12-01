<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orcamentos', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('mes_referencia')->unique();
            $table->decimal('salario_bruto', 10, 2);
            $table->decimal('dizimo', 10, 2)->default(0);
            $table->decimal('salario_liquido', 10, 2);

            // Percentuais
            $table->decimal('perc_investimentos', 5, 2)->default(25.00);
            $table->decimal('perc_custos_fixos', 5, 2)->default(30.00);
            $table->decimal('perc_conforto', 5, 2)->default(15.00);
            $table->decimal('perc_metas', 5, 2)->default(15.00);
            $table->decimal('perc_prazeres', 5, 2)->default(10.00);
            $table->decimal('perc_conhecimento', 5, 2)->default(5.00);

            // Valores calculados
            $table->decimal('valor_investimentos', 10, 2);
            $table->decimal('valor_custos_fixos', 10, 2);
            $table->decimal('valor_conforto', 10, 2);
            $table->decimal('valor_metas', 10, 2);
            $table->decimal('valor_prazeres', 10, 2);
            $table->decimal('valor_conhecimento', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orcamentos');
    }
};