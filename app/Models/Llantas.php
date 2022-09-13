<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Llantas extends Model
{
    use HasFactory;
    protected $table = 'llantas';
    public $timestamps = false;
}
