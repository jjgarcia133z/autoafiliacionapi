<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planes extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $fillable = [
        'tipo', 'codigoSalesforce', 'montoTitular', 'montoBeneficiario', 'estado', 'default'
    ];
}
