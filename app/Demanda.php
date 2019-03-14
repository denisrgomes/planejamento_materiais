<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
\Carbon\Carbon::setToStringFormat('d-m-Y');


class Demanda extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'codigo_material', 'descricao_material', 'quantidade_material', 'centro_logistico',
        'regional', 'data_programacao'
    ];

	protected $dates = [
        'data_programacao',
    ];
}
