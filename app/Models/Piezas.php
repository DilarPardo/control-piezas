<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piezas extends Model
{
    use HasFactory;

    protected $table = 'piezas';
    protected $primaryKey = 'id_pieza';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pieza',
        'pieza',
        'peso_teorico',
        'peso_real',
        'estado',
        'id_proyecto',
        'id_bloque',
        'fecha_registro',
        'registrado_por',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id_proyecto');
    }

    public function bloque()
    {
        return $this->belongsTo(Bloque::class, 'id_bloque', 'id_bloque');
    }
}
