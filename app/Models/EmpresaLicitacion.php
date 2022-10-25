<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaLicitacion extends Model
{
    use HasFactory;
    protected $table = 'empresa_licitacion';
    public $timestamps = false;
}
