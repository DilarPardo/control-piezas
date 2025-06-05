<?php

namespace App\Http\Controllers;

use App\Models\Proyectos;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ProyectosController extends Controller
{
    public function index()
    {
        $proyectos = Proyectos::all();

        return Inertia::render('ProyectoComponent', [
            'proyectos' => $proyectos,
            'flash' => session('message')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $idProyecto = strtoupper(Str::random(10));
        while (Proyectos::where('id_proyecto', $idProyecto)->exists()) {
            $idProyecto = strtoupper(Str::random(10));
        }

        Proyectos::create([
            'id_proyecto' => $idProyecto,
            'nombre' => $request->input('nombre'),
        ]);

        return Redirect::back()->with('message', 'Proyecto creado correctamente.');
    }

    public function update(Request $request, Proyectos $proyecto)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $proyecto->update([
            'nombre' => $request->input('nombre'),
        ]);

        return Redirect::back()->with('message', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Proyectos $proyecto)
    {
        $proyecto->delete();

        return Redirect::back()->with('message', 'Proyecto eliminado correctamente.');
    }
}
