<?php
// app/Models/Gasto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Gasto extends Model
{
    protected $table = 'gastos';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'orcamento_id',
        'categoria',
        'descricao',
        'valor',
        'data_gasto',
        'observacao',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_gasto' => 'date',
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

    public function orcamento(): BelongsTo
    {
        return $this->belongsTo(Orcamento::class);
    }

    public static function categorias(): array
    {
        return [
            'investimentos' => 'Investimentos (Liberdade Financeira)',
            'custos_fixos'  => 'Custos Fixos',
            'conforto'      => 'Conforto',
            'metas'         => 'Metas',
            'prazeres'      => 'Prazeres',
            'conhecimento'  => 'Conhecimento',
        ];
    }
}