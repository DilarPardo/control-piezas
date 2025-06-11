<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class PiezasPendientesExport implements FromQuery, WithHeadings
{
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($fechaInicio = null, $fechaFin = null)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function query()
    {
        $query = DB::table('registros')
            ->join('bloques', 'registros.id_bloque', '=', 'bloques.id_bloque')
            ->join('proyectos', 'bloques.id_proyecto', '=', 'proyectos.id_proyecto')
            ->select(
                'proyectos.nombre as Proyecto',
                DB::raw("SUM(CASE WHEN registros.estado = 'Pendiente' THEN 1 ELSE 0 END) as Piezas_Pendientes")
            )
            ->where('registros.estado', 'Pendiente');

        if ($this->fechaInicio && $this->fechaFin) {
            $query->whereBetween(DB::raw('DATE(registros.fecha_registro)'), [
                $this->fechaInicio . '-01',
                $this->fechaFin . '-31'
            ]);
        }

        return $query
            ->orderBy('proyectos.nombre') // <- Esta línea resuelve el error
            ->groupBy('proyectos.nombre');
    }

    public function headings(): array
    {
        return ['Proyecto', 'Piezas Pendientes'];
    }
}
