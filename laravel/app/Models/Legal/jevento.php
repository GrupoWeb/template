<?php

namespace App\Models\Legal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jevento extends Model
{
    use HasFactory;

    protected $connection = 'mysql_diaco';

    protected $table = 'jevento';
}
