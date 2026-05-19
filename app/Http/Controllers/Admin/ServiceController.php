<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Salle;
use App\Models\Lit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Réservé à l\'administrateur.');
        }

        $services = Service::with(['salles.lits'])->withCount(['salles as salles_count'])->orderBy('nom_service')->get();

        // Calculer le nombre total de lits par service
        foreach ($services as $service) {
            $service->lits_count = $service->salles->sum(function($salle) {
                return $salle->lits->count();
            });
            $service->lits_libres = $service->salles->sum(function($salle) {
                return $salle->lits->where('statut', 'libre')->count();
            });
            $service->lits_occupes = $service->salles->sum(function($salle) {
                return $salle->lits->where('statut', 'occupe')->count();
            });
        }

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Réservé à l\'administrateur.');
        }

        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Réservé à l\'administrateur.');
        }

        $request->validate([
            'nom_service' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\p{L}0-9\s\-\']+$/u',
                'unique:services,nom_service',
                function ($attribute, $value, $fail) {
                    if (preg_match('/^\d+$/', trim($value))) {
                        $fail('Le nom du service ne peut pas être composé uniquement de chiffres.');
                    }
                    if (!preg_match('/[\p{L}]/u', trim($value))) {
                        $fail('Le nom du service doit contenir au moins une lettre.');
                    }
                },
            ],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'nom_service.required' => 'Le nom du service est obligatoire.',
            'nom_service.regex'    => 'Le nom ne doit contenir que des lettres, chiffres, espaces ou tirets.',
            'nom_service.unique'   => 'Ce service existe déjà.',
            'description.max'      => 'La description ne peut pas dépasser 500 caractères.',
        ]);

        Service::create([
            'nom_service' => trim($request->nom_service),
            'description' => trim($request->description),
            'is_active' => true,
        ]);

        return redirect()->route('admin.services.index')
                         ->with('success', 'Service "' . $request->nom_service . '" ajouté.');
    }

    public function edit(Service $service)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Réservé à l\'administrateur.');
        }

        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Réservé à l\'administrateur.');
        }

        $request->validate([
            'nom_service' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\p{L}0-9\s\-\']+$/u',
                'unique:services,nom_service,' . $service->id,
                function ($attribute, $value, $fail) {
                    if (preg_match('/^\d+$/', trim($value))) {
                        $fail('Le nom du service ne peut pas être composé uniquement de chiffres.');
                    }
                    if (!preg_match('/[\p{L}]/u', trim($value))) {
                        $fail('Le nom du service doit contenir au moins une lettre.');
                    }
                },
            ],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'nom_service.regex'  => 'Nom invalide (lettres, chiffres, espaces ou tirets uniquement).',
            'nom_service.unique' => 'Ce nom est déjà utilisé.',
            'description.max'    => 'La description ne peut pas dépasser 500 caractères.',
        ]);

        $service->update([
            'nom_service' => trim($request->nom_service),
            'description' => trim($request->description),
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Service mis à jour.');
    }

    public function destroy(Service $service)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Réservé à l\'administrateur.');
        }

        if ($service->salles()->count() > 0) {
            return redirect()->route('admin.services.index')
                             ->with('error', 'Impossible : ce service contient des salles. Supprimez d\'abord les salles.');
        }

        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service supprimé.');
    }
}
