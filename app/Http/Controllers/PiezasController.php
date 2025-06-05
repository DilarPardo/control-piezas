<?php

namespace App\Http\Controllers;

use App\Models\Bloques;
use App\Models\Piezas;
use App\Models\Proyectos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;


class PiezasController extends Controller
{
    public function index()
    {
        $piezas = Piezas::with('bloque')->get();
        $proyectos = Proyectos::all();

        return Inertia::render('PiezasComponent', [
            'piezas' => $piezas,
            'bloques' => Bloques::all(),
            'proyectos' => $proyectos,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pieza' => 'required|string|max:10',    
            'nombre_pieza' => 'required|string|max:50',
            'peso_teorico' => 'nullable|numeric',
            'peso_real' => 'nullable|numeric',
            'estado' => 'nullable|string',
            'id_proyecto' => 'nullable|string|exists:proyectos,id_proyecto',
            'id_bloque' => 'required|string|exists:bloques,id_bloque',
            'fecha_registro' => 'nullable|date',
        ]);


        do {
            $idPieza = strtoupper(Str::random(10));
        } while (Piezas::where('id_pieza', $idPieza)->exists());

        Piezas::create([
            'id_pieza' => $idPieza,
            'pieza' => $request->pieza,
            'nombre_pieza' => $request->nombre_pieza,
            'peso_teorico' => $request->peso_teorico,
            'peso_real' => $request->peso_real,
            'estado' => $request->estado ?? 'Pendiente',
            'id_proyecto' => $request->id_proyecto,
            'id_bloque' => $request->id_bloque,
            'fecha_registro' => $request->fecha_registro,
            'registrado_por' => auth()->id() ?? null, 
        ]);

        return Redirect::route('piezas.index')->with('success', 'Pieza creada correctamente.');
    }

    public function update(Request $request, Piezas $pieza)
    {
        $request->validate([
            'pieza' => 'required|string|max:10',      
            'nombre_pieza' => 'required|string|max:50',
            'peso_teorico' => 'nullable|numeric',
            'peso_real' => 'nullable|numeric',
            'estado' => 'nullable|string',
            'id_proyecto' => 'nullable|string|exists:proyectos,id_proyecto',
            'id_bloque' => 'required|string|exists:bloques,id_bloque',
            'fecha_registro' => 'nullable|date',
        ]);


        $pieza->update([
            'id_pieza' => $idPieza,
            'pieza' => $request->pieza,
            'nombre_pieza' => $request->nombre_pieza,
            'peso_teorico' => $request->peso_teorico,
            'peso_real' => $request->peso_real,
            'estado' => $request->estado ?? 'Pendiente',
            'id_proyecto' => $request->id_proyecto,
            'id_bloque' => $request->id_bloque,
            'fecha_registro' => $request->fecha_registro,
            'registrado_por' => auth()->id() ?? null, 
        ]);

        return Redirect::route('piezas.index')->with('success', 'Pieza actualizada correctamente.');
    }

    public function destroy(Piezas $pieza)
    {
        $pieza->delete();

        return Redirect::route('piezas.index')->with('success', 'Pieza eliminada correctamente.');
    }
}
