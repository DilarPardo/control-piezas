<?php

namespace App\Models;

use App\Models\Bloques;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registros extends Model
{
    use HasFactory;

    protected $table = 'registros';
    protected $primaryKey = 'id_registro';
    public $incrementing = true;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pieza',
        'pieza',
        'peso_teorico',
        'peso_real',
        'estado',
        'id_bloque',
        'fecha_registro',
        'registrado_por'
    ];

    public function bloque()
    {
        return $this->belongsTo(Bloques::class, 'id_bloque', 'id_bloque');
    }

    public function pieza()
    {
        return $this->belongsTo(Piezas::class, 'id_pieza', 'id_pieza');
    }

}
