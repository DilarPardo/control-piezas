<?php

namespace App\Http\Controllers;

use App\Models\Registros;
use App\Models\Bloques;
use App\Models\Piezas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\PiezasPendientesExport;
use Maatwebsite\Excel\Facades\Excel;

class RegistrosController extends Controller
{
    public function index()
    {
        $usuarioNombre = Auth::user()->name; 
        $registros = Registros::with('bloque')->get();
        $bloques = Bloques::all();
        $piezas = Piezas::all();

        return Inertia::render('RegistrosComponent', [
            'registros' => $registros,
            'bloques' => $bloques,
            'usuarioNombre' => $usuarioNombre,
            'piezas' => $piezas
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pieza' => 'required|string|max:10',
            'peso_teorico' => 'required|numeric',
            'peso_real' => 'nullable|numeric',
            'estado' => 'required|in:Fabricado,Pendiente',
            'id_bloque' => 'required|string|exists:bloques,id_bloque',
            'fecha_registro' => 'required|date',
            'registrado_por' => 'nullable|string|max:50',
        ]);

        Registros::create([
            'id_pieza' => $request->input('id_pieza'),
            'pieza' => $request->input('pieza'),
            'peso_teorico' => $request->input('peso_teorico'),
            'peso_real' => $request->input('peso_real'),
            'estado' => $request->input('estado'),
            'id_bloque' => $request->input('id_bloque'),
            'fecha_registro' => $request->input('fecha_registro'),
            'registrado_por' => $request->input('registrado_por'),
        ]);

        return redirect()->route('registros.index')->with('success', 'Registro creado correctamente.');
    }

    public function update(Request $request, Registros $registro)
    {
        $request->validate([
            'pieza' => 'required|string|max:10',
            'peso_teorico' => 'required|numeric',
            'peso_real' => 'nullable|numeric',
            'estado' => 'required|in:Fabricado,Pendiente',
            'id_bloque' => 'required|string|exists:bloques,id_bloque',
            'fecha_registro' => 'required|date',
            'registrado_por' => 'nullable|string|max:50',
        ]);

        $registro->update([
            'pieza' => $request->input('pieza'),
            'peso_teorico' => $request->input('peso_teorico'),
            'peso_real' => $request->input('peso_real'),
            'estado' => $request->input('estado'),
            'id_bloque' => $request->input('id_bloque'),
            'fecha_registro' => $request->input('fecha_registro'),
            'registrado_por' => $request->input('registrado_por'),
        ]);

        return redirect()->route('registros.index')->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy($id)
    {
        $registro = Registros::findOrFail($id);
        $registro->delete();

        return redirect()->route('registros.index')->with('success', 'Registro eliminado correctamente.');
    }

    public function dashboard(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = DB::table('registros')
            ->join('bloques', 'registros.id_bloque', '=', 'bloques.id_bloque')
            ->join('proyectos', 'bloques.id_proyecto', '=', 'proyectos.id_proyecto')
            ->select(
                'proyectos.nombre as proyecto',
                DB::raw("SUM(CASE WHEN registros.estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes"),
                DB::raw("SUM(CASE WHEN registros.estado = 'Fabricado' THEN 1 ELSE 0 END) as fabricados")
            );

        if ($fechaInicio && $fechaFin) {
            // Convertir '2025-06' => '2025-06-01', '2025-07' => '2025-07-31'
            $fechaInicio .= '-01';
            $fechaFin .= '-31';

            $query->whereBetween('registros.fecha_registro', [$fechaInicio, $fechaFin]);
        }

        $datos = $query->groupBy('proyectos.nombre')->get();

        return Inertia::render('Dashboard', [
            'datosGraficos' => $datos,
            'filtros' => [
                'fecha_inicio' => $request->input('fecha_inicio'),
                'fecha_fin' => $request->input('fecha_fin'),
            ]
        ]);
    }

    public function exportarPendientes(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        return Excel::download(new PiezasPendientesExport($fechaInicio, $fechaFin), 'piezas_pendientes.xlsx');
    }

}
