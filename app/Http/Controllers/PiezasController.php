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
use Illuminate\Support\Facades\Auth;


class PiezasController extends Controller
{
    public function index()
    {
        
        $proyecto = Proyectos::all();
        $usuarioNombre = Auth::user()->name; 

        return Inertia::render('PiezasComponent', [
           
            'piezas' => Piezas::with('proyecto', 'bloque')->get(),
            'proyectos' => Proyectos::all(),
            'bloques' => Bloques::all(),
            'usuarioNombre' => Auth::user()->name,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
        'pieza' => 'required|max:10',
        'peso_teorico' => 'required|numeric',
        'estado' => 'required|in:Fabricado,Pendiente',
        'id_proyecto' => 'required|exists:proyectos,id_proyecto',
        'id_bloque' => 'required|exists:bloques,id_bloque',
        ]);

        $request->merge([
            'id_pieza' => Str::uuid(), // o tu lógica de ID de 10 caracteres
        ]);

        Piezas::create($request->all());

        return Redirect::route('piezas.index')->with('success', 'Pieza creada correctamente.');
    }

    public function update(Request $request, Piezas $pieza)
    {
        $request->validate([
            'pieza' => 'required|max:10',
            'peso_teorico' => 'required|numeric',
            'estado' => 'required|in:Fabricado,Pendiente',
            'id_proyecto' => 'required|exists:proyectos,id_proyecto',
            'id_bloque' => 'required|exists:bloques,id_bloque',
        ]);

        $pieza->update($request->all());

        return Redirect::route('piezas.index')->with('success', 'Pieza actualizada correctamente.');
    }

    public function destroy(Piezas $pieza)
    {
        $pieza->delete();
        return Redirect::route('piezas.index')->with('success', 'Pieza eliminada correctamente.');
    }
}
