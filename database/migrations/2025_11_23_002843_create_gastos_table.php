<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('orcamento_id')
                ->constrained('orcamentos')
                ->onDelete('cascade');

            $table->enum('categoria', [
                'investimentos',
                'custos_fixos',
                'conforto',
                'metas',
                'prazeres',
                'conhecimento'
            ]);
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->date('data_gasto');
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};