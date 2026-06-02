<?php

namespace App\Http\Controllers;

use App\Models\Lit;
use App\Models\Salle;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LitController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)
            ->with(['salles.lits.patient'])
            ->orderBy('nom_service')
            ->get();

        $stats = [
            'total' => Lit::count(),
            'libres' => Lit::where('statut', 'libre')->count(),
            'occupes' => Lit::where('statut', 'occupe')->count(),
            'maintenance' => Lit::where('statut', 'maintenance')->count(),
            'hors_service' => Lit::where('statut', 'hors_service')->count(),
        ];

        return view('lits.index', compact('services', 'stats'));
    }

    public function transfertForm()
    {
        // Suppression de la vérification admin - accessible à tous
        $litsLibres = Lit::where('statut', 'libre')->with(['salle.service'])->get();
        $salles = Salle::with('service')->orderBy('nom')->get();

        return view('lits.transfert', compact('litsLibres', 'salles'));
    }

    public function transfertLit(Request $request)
    {
        // Suppression de la vérification admin - accessible à tous
        $request->validate([
            'lit_id' => 'required|exists:lits,id',
            'salle_id' => 'required|exists:salles,id',
        ]);

        $lit = Lit::findOrFail($request->lit_id);
        $nouvelleSalle = Salle::findOrFail($request->salle_id);

        // Vérifier si le lit est libre
        if ($lit->statut !== 'libre') {
            return back()->with('error', 'Seul un lit libre peut être transféré.');
        }

        // Vérifier la capacité de la salle
        $litsCount = Lit::where('salle_id', $request->salle_id)->count();
        if ($litsCount >= $nouvelleSalle->capacite) {
            return back()->with('error', "Salle saturée ! Cette salle a une capacité maximale de {$nouvelleSalle->capacite} lit(s). Impossible d'y ajouter un nouveau lit.");
        }

        // Vérifier si le numéro existe déjà dans la nouvelle salle
        $exists = Lit::where('salle_id', $request->salle_id)
            ->where('numero', $lit->numero)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Un lit avec le numéro ' . $lit->numero . ' existe déjà dans cette salle.');
        }

        $ancienneSalle = $lit->salle->nom;
        $lit->update(['salle_id' => $request->salle_id]);

        return redirect()->route('lits.index')
            ->with('success', "Lit N°{$lit->numero} transféré de '{$ancienneSalle}' vers '{$nouvelleSalle->nom}' avec succès.");
    }

    public function create()
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $salles = Salle::with('service')->orderBy('nom')->get();

        return view('lits.create', compact('salles'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'salle_id' => 'required|exists:salles,id',
            'numero' => 'required|string|max:20',
            'statut' => 'required|in:libre,maintenance,hors_service',
        ]);

        $salle = Salle::findOrFail($request->salle_id);

        // Vérifier la capacité de la salle
        $litsCount = Lit::where('salle_id', $request->salle_id)->count();
        if ($litsCount >= $salle->capacite) {
            return back()->withErrors(['salle_id' => "Salle saturée ! Cette salle a une capacité maximale de {$salle->capacite} lit(s). Impossible d'ajouter un nouveau lit."])
                ->withInput();
        }

        // Vérifier l'unicité du numéro dans la même salle
        $exists = Lit::where('salle_id', $request->salle_id)
            ->where('numero', $request->numero)
            ->exists();

        if ($exists) {
            return back()->withErrors(['numero' => 'Ce numéro de lit existe déjà dans cette salle.'])
                ->withInput();
        }

        Lit::create([
            'salle_id' => $request->salle_id,
            'numero' => $request->numero,
            'statut' => $request->statut,
            'patient_id' => null,
        ]);

        return redirect()->route('lits.index')
            ->with('success', "Lit N°{$request->numero} ajouté avec succès.");
    }

    public function edit(Lit $lit)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $salles = Salle::with('service')->orderBy('nom')->get();

        return view('lits.edit', compact('lit', 'salles'));
    }

    public function update(Request $request, Lit $lit)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'salle_id' => 'required|exists:salles,id',
            'numero' => 'required|string|max:20',
            'statut' => 'required|in:libre,maintenance,hors_service',
        ]);

        if ($request->statut === 'occupe') {
            return back()->withErrors(['statut' => 'Le statut "occupé" ne peut pas être défini manuellement.'])
                ->withInput();
        }

        // Si la salle change, vérifier la capacité
        if ($lit->salle_id != $request->salle_id) {
            $nouvelleSalle = Salle::findOrFail($request->salle_id);
            $litsCount = Lit::where('salle_id', $request->salle_id)->count();

            // Si on modifie le lit dans la même salle, on ne compte pas le lit actuel
            if ($lit->salle_id == $request->salle_id) {
                $litsCount = Lit::where('salle_id', $request->salle_id)->count();
            } else {
                $litsCount = Lit::where('salle_id', $request->salle_id)->count();
            }

            if ($litsCount >= $nouvelleSalle->capacite) {
                return back()->withErrors(['salle_id' => "Salle saturée ! Cette salle a une capacité maximale de {$nouvelleSalle->capacite} lit(s)."])
                    ->withInput();
            }
        }

        $exists = Lit::where('salle_id', $request->salle_id)
            ->where('numero', $request->numero)
            ->where('id', '!=', $lit->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['numero' => 'Ce numéro de lit existe déjà dans cette salle.'])
                ->withInput();
        }

        if ($lit->statut === 'occupe') {
            $lit->update([
                'salle_id' => $request->salle_id,
                'numero' => $request->numero,
            ]);
            $message = "Lit N°{$lit->numero} modifié avec succès (statut inchangé car occupé).";
        } else {
            $lit->update([
                'salle_id' => $request->salle_id,
                'numero' => $request->numero,
                'statut' => $request->statut,
            ]);
            $message = "Lit N°{$lit->numero} modifié avec succès.";
        }

        return redirect()->route('lits.index')->with('success', $message);
    }

    public function destroy(Lit $lit)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        if ($lit->statut === 'occupe') {
            return redirect()->route('lits.index')
                ->with('error', 'Impossible de supprimer un lit occupé. Validez d\'abord la sortie du patient.');
        }

        $numero = $lit->numero;
        $lit->delete();

        return redirect()->route('lits.index')
            ->with('success', "Lit N°{$numero} supprimé.");
    }

    public function changerStatut(Request $request, Lit $lit)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'statut' => 'required|in:libre,maintenance,hors_service',
        ]);

        if ($lit->statut === 'occupe') {
            return redirect()->route('lits.index')
                ->with('error', 'Ce lit est occupé par un patient. Validez la sortie avant de changer le statut.');
        }

        $lit->update(['statut' => $request->statut]);

        return redirect()->route('lits.index')
            ->with('success', "Statut du lit N°{$lit->numero} mis à jour.");
    }
    public function delete(Lit $lit)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        return view('lits.delete', compact('lit'));
    }
}
