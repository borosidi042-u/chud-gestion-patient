<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salle;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalleController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $salles = Salle::with(['service'])->withCount('lits')->orderBy('nom')->get();
        $services = Service::where('is_active', true)->orderBy('nom_service')->get();

        return view('admin.salles.index', compact('salles', 'services'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $services = Service::where('is_active', true)->orderBy('nom_service')->get();

        return view('admin.salles.create', compact('services'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'nom' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'capacite' => 'required|integer|min:1|max:200',
        ]);

        // Vérifier l'unicité du nom dans le même service
        $exists = Salle::where('service_id', $request->service_id)
            ->where('nom', $request->nom)
            ->exists();

        if ($exists) {
            return back()->withErrors(['nom' => 'Une salle avec ce nom existe déjà dans ce service.'])
                ->withInput();
        }

        Salle::create([
            'service_id' => $request->service_id,
            'nom' => $request->nom,
            'description' => $request->description,
            'capacite' => $request->capacite,
        ]);

        return redirect()->route('admin.salles.index')
            ->with('success', 'Salle ajoutée avec succès.');
    }

    public function edit(Salle $salle)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $services = Service::where('is_active', true)->orderBy('nom_service')->get();

        return view('admin.salles.edit', compact('salle', 'services'));
    }

    public function update(Request $request, Salle $salle)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'nom' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'capacite' => 'required|integer|min:1|max:200',
        ]);

        // Vérifier l'unicité du nom dans le même service (sauf elle-même)
        $exists = Salle::where('service_id', $request->service_id)
            ->where('nom', $request->nom)
            ->where('id', '!=', $salle->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['nom' => 'Une salle avec ce nom existe déjà dans ce service.'])
                ->withInput();
        }

        $salle->update([
            'service_id' => $request->service_id,
            'nom' => $request->nom,
            'description' => $request->description,
            'capacite' => $request->capacite,
        ]);

        return redirect()->route('admin.salles.index')
            ->with('success', 'Salle modifiée avec succès.');
    }

    public function destroy(Salle $salle)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        if ($salle->lits()->count() > 0) {
            return redirect()->route('admin.salles.index')
                ->with('error', 'Impossible de supprimer cette salle : elle contient des lits. Supprimez d\'abord les lits.');
        }

        $salle->delete();

        return redirect()->route('admin.salles.index')
            ->with('success', 'Salle supprimée avec succès.');
    }
}
