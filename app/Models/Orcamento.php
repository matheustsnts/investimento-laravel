<?php
// app/Models/Orcamento.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Orcamento extends Model
{
    protected $table = 'orcamentos';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'mes_referencia',
        'salario_bruto',
        'dizimo',
        'salario_liquido',
        'perc_investimentos',
        'perc_custos_fixos',
        'perc_conforto',
        'perc_metas',
        'perc_prazeres',
        'perc_conhecimento',
        'valor_investimentos',
        'valor_custos_fixos',
        'valor_conforto',
        'valor_metas',
        'valor_prazeres',
        'valor_conhecimento',
    ];

    protected $casts = [
        'salario_bruto' => 'decimal:2',
        'dizimo' => 'decimal:2',
        'salario_liquido' => 'decimal:2',
        'perc_investimentos' => 'decimal:2',
        'perc_custos_fixos' => 'decimal:2',
        'perc_conforto' => 'decimal:2',
        'perc_metas' => 'decimal:2',
        'perc_prazeres' => 'decimal:2',
        'perc_conhecimento' => 'decimal:2',
        'valor_investimentos' => 'decimal:2',
        'valor_custos_fixos' => 'decimal:2',
        'valor_conforto' => 'decimal:2',
        'valor_metas' => 'decimal:2',
        'valor_prazeres' => 'decimal:2',
        'valor_conhecimento' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

    public function calcularValores(): void
    {
        $this->salario_liquido = $this->salario_bruto - $this->dizimo;

        $this->valor_investimentos = ($this->salario_liquido * $this->perc_investimentos) / 100;
        $this->valor_custos_fixos = ($this->salario_liquido * $this->perc_custos_fixos) / 100;
        $this->valor_conforto = ($this->salario_liquido * $this->perc_conforto) / 100;
        $this->valor_metas = ($this->salario_liquido * $this->perc_metas) / 100;
        $this->valor_prazeres = ($this->salario_liquido * $this->perc_prazeres) / 100;
        $this->valor_conhecimento = ($this->salario_liquido * $this->perc_conhecimento) / 100;
    }

    public function getSaldoPorCategoria(string $categoria): float
    {
        $campo_valor = "valor_{$categoria}";
        $total_categoria = $this->$campo_valor;

        $gasto_total = $this->gastos()
            ->where('categoria', $categoria)
            ->sum('valor');

        return $total_categoria - $gasto_total;
    }
}