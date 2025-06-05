<?php

namespace App\Http\Controllers;

use App\Models\Bloques;
use App\Models\Proyectos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class BloquesController extends Controller
{
    public function index()
    {
        $bloques = Bloques::with('proyecto')->get();

        return Inertia::render('BloqueComponent', [
            'bloques' => $bloques,
            'proyectos' => Proyectos::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_bloque' => 'required|string|max:10',
            'id_proyecto' => 'required|string|exists:proyectos,id_proyecto',
        ]);

        $idBloque = strtoupper(Str::random(10));
        while (Bloques::where('id_bloque', $idBloque)->exists()) {
            $idBloque = strtoupper(Str::random(10));
        }

        Bloques::create([
            'id_bloque' => $idBloque,
            'nombre_bloque' => $request->input('nombre_bloque'),
            'id_proyecto' => $request->input('id_proyecto'),
        ]);

        return redirect()->route('bloques.index')->with('success', 'Bloque creado correctamente.');
    }

    public function update(Request $request, Bloques $bloque)
    {
        $request->validate([
            'nombre_bloque' => 'required|string|max:10',
            'id_proyecto' => 'required|string|exists:proyectos,id_proyecto',
        ]);

        $bloque->update([
            'nombre_bloque' => $request->input('nombre_bloque'),
            'id_proyecto' => $request->input('id_proyecto'),
        ]);

        return redirect()->route('bloques.index')->with('success', 'Bloque actualizado correctamente.');
    }

    public function destroy(Bloques $bloque)
    {
        $bloque->delete();

        return redirect()->route('bloques.index')->with('success', 'Bloque eliminado correctamente.');
    }
}
