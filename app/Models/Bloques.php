<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bloques extends Model
{
    protected $table = 'bloques';
    protected $primaryKey = 'id_bloque';
    public $incrementing = false; // porque la PK es string
    protected $keyType = 'string';

    protected $fillable = [
        'id_bloque',
        'nombre_bloque',
        'id_proyecto',
    ];

    // Relación con Proyecto
    public function proyecto()
    {
        return $this->belongsTo(Proyectos::class, 'id_proyecto', 'id_proyecto');
    }
}
