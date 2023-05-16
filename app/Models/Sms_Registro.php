<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sms_Registro extends Model
{
      protected $table = 'sms_registro';
    public $incrementing = true;
    protected $primaryKey = 'id_sms';
    protected $fillable	=	[
        'telefono',
        'cedula',
        'sms',
        'validado',
        'fecha_creacion',
        'fecha_validacion',
        'ip',
        'usos'
      ];
      public $timestamps	=	false;



}
