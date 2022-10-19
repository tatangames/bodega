<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbicacionRepuesto extends Model
{
    use HasFactory;
    protected $table = 'ubicacion_repuesto';
    public $timestamps = false;
}
