<?php

namespace App\Models\Legal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class evjuridico extends Model
{
    use HasFactory;

    protected $connection = 'mysql_diaco';

    protected $table = 'evjuridico';

    public $timestamps = false;

    protected $fillable = [
        'expediente',
        'fdia',
        'fmes',
        'fanio',
        'fecha',
        'id_jevento',
        'id_regional',
        'observaciones',
        'usuario',
        'ncomercial',
        'id_juridicos'
    ];
}
