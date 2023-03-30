<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        '_token', 'tipo', 'codigoSaleforce', 'montoTitular', 'montoBeneficiario', 'estado', 'default'
    ];
}
