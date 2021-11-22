<?php

namespace App\Models\Legal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class regional extends Model
{
    use HasFactory;

    protected $connection = 'mysql_diaco';

    protected $table = 'regional';


    public $timestamps = false;
}
