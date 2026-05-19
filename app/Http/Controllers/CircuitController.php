<?php

namespace App\Http\Controllers;

use App\Models\Circuit;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Visite;
use App\Models\Mouvement;
use App\Models\Lit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CircuitController extends Controller
{
    public function create(Request $request)
    {
        $services = Service::where('is_active', true)->orderBy('nom_service')->get();
        $patient = null;
        $visiteEnCours = null;
        $activeTab = $request->input('tab', 'passage'); // Récupérer l'onglet actif

        if ($request->filled('patient_id')) {
            $patient = Patient::findOrFail($request->patient_id);
            $visiteEnCours = $patient->getVisiteEnCours();
        }

        return view('circuits.create', compact('services', 'patient', 'visiteEnCours', 'activeTab'));
    }

    public function getLitsDisponibles(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $lits = Lit::whereHas('salle', function ($q) use ($request) {
            $q->where('service_id', $request->service_id);
        })->where('statut', 'libre')->with('salle')->get();

        return response()->json($lits);
    }

    private function getOrCreateVisite(int $patientId): Visite
    {
        $dernierMouvement = Mouvement::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$dernierMouvement) {
            // Premier mouvement du patient → créer première visite
            return Visite::create([
                'patient_id' => $patientId,
                'numero_visite' => 1,
                'date_debut' => now(),
                'statut' => 'en_cours',
            ]);
        }

        // Vérifier si la visite en cours est terminée (sortie validée)
        $visiteEnCours = Visite::where('patient_id', $patientId)
            ->where('statut', 'en_cours')
            ->latest()
            ->first();

        if (!$visiteEnCours || $visiteEnCours->statut === 'terminee') {
            // Plus de visite en cours → créer une nouvelle visite
            $numeroVisite = Visite::where('patient_id', $patientId)->count() + 1;
            return Visite::create([
                'patient_id' => $patientId,
                'numero_visite' => $numeroVisite,
                'date_debut' => now(),
                'statut' => 'en_cours',
            ]);
        }

        return $visiteEnCours;
    }

    public function storePassage(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'service_id' => 'required|exists:services,id',
            'note' => 'nullable|string|max:500',
        ]);

        $visite = $this->getOrCreateVisite($request->patient_id);

        Mouvement::create([
            'visite_id' => $visite->id,
            'patient_id' => $request->patient_id,
            'service_id' => $request->service_id,
            'type' => 'passage',
            'heure_arrivee' => now(),
            'agent_id' => Auth::id(),
            'note' => $request->note,
        ]);

        return redirect()->route('patients.show', $request->patient_id)
            ->with('success', 'Passage enregistré avec succès.');
    }

    public function storeAdmission(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'service_id' => 'required|exists:services,id',
            'lit_id' => 'required|exists:lits,id',
            'note' => 'nullable|string|max:500',
        ]);

        $lit = Lit::findOrFail($request->lit_id);

        if (!$lit->isFree()) {
            return back()->withErrors(['lit_id' => 'Ce lit est déjà occupé. Veuillez en choisir un autre.'])
                ->withInput();
        }

        $visite = $this->getOrCreateVisite($request->patient_id);

        // Occuper le lit
        $lit->occupy($request->patient_id);

        Mouvement::create([
            'visite_id' => $visite->id,
            'patient_id' => $request->patient_id,
            'service_id' => $request->service_id,
            'salle_id' => $lit->salle_id,
            'lit_id' => $lit->id,
            'type' => 'entree',
            'heure_arrivee' => now(),
            'agent_id' => Auth::id(),
            'note' => $request->note,
        ]);

        return redirect()->route('patients.show', $request->patient_id)
            ->with('success', "Admission enregistrée. Lit N°{$lit->numero} attribué.");
    }

    public function storeTransfert(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visite_id' => 'required|exists:visites,id',
            'nouveau_service_id' => 'required|exists:services,id',
            'nouveau_lit_id' => 'required|exists:lits,id',
            'note' => 'nullable|string|max:500',
        ]);

        $nouveauLit = Lit::findOrFail($request->nouveau_lit_id);

        if (!$nouveauLit->isFree()) {
            return back()->withErrors(['nouveau_lit_id' => 'Ce lit est déjà occupé. Veuillez en choisir un autre.'])
                ->withInput();
        }

        $visite = Visite::findOrFail($request->visite_id);

        if ($visite->statut !== 'en_cours') {
            return back()->withErrors(['visite_id' => 'Cette visite est déjà terminée. Impossible d\'effectuer un transfert.'])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Libérer l'ancien lit s'il existe
            $ancienMouvement = Mouvement::where('visite_id', $visite->id)
                ->whereNotNull('lit_id')
                ->latest('heure_arrivee')
                ->first();

            if ($ancienMouvement && $ancienMouvement->lit) {
                $ancienMouvement->lit->release();
            }

            // Occuper le nouveau lit
            $nouveauLit->occupy($request->patient_id);

            // Créer le mouvement de transfert
            Mouvement::create([
                'visite_id' => $visite->id,
                'patient_id' => $request->patient_id,
                'service_id' => $request->nouveau_service_id,
                'salle_id' => $nouveauLit->salle_id,
                'lit_id' => $nouveauLit->id,
                'type' => 'transfert',
                'heure_arrivee' => now(),
                'agent_id' => Auth::id(),
                'note' => $request->note,
            ]);

            DB::commit();

            $nouveauService = Service::findOrFail($request->nouveau_service_id);

            return redirect()->route('patients.show', $request->patient_id)
                ->with('success', "Transfert effectué. Le patient est maintenant en {$nouveauService->nom_service}, lit N°{$nouveauLit->numero}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors du transfert : ' . $e->getMessage());
        }
    }

    public function storeSortie(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visite_id' => 'required|exists:visites,id',
            'note' => 'nullable|string|max:500',
        ]);

        $visite = Visite::findOrFail($request->visite_id);

        if ($visite->statut !== 'en_cours') {
            return back()->withErrors(['visite_id' => 'Cette visite est déjà terminée.'])
                ->withInput();
        }

        // Récupérer le dernier service connu du patient
        $dernierMouvement = Mouvement::where('visite_id', $visite->id)
            ->latest('heure_arrivee')
            ->first();

        $dernierServiceId = $dernierMouvement ? $dernierMouvement->service_id : null;

        // Libérer le lit si le patient en avait un
        $litOccupe = $visite->getLitActuel();
        if ($litOccupe) {
            $litOccupe->release();
        }

        // Créer le mouvement de sortie
        Mouvement::create([
            'visite_id' => $visite->id,
            'patient_id' => $request->patient_id,
            'service_id' => $dernierServiceId,
            'type' => 'sortie',
            'heure_arrivee' => now(),
            'agent_id' => Auth::id(),
            'note' => $request->note,
        ]);

        // Clôturer la visite
        $visite->close(Auth::id());

        return redirect()->route('patients.show', $request->patient_id)
            ->with('success', "Sortie validée. La visite N°{$visite->numero_visite} est clôturée.");
    }

    public function edit(Circuit $circuit)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $services = Service::orderBy('nom_service')->get();
        $patient = $circuit->patient;

        // Récupérer tous les circuits du patient par ordre chronologique
        $circuits = Circuit::where('patient_id', $patient->id)
                        ->orderBy('created_at', 'asc')
                        ->get();

        $index = 0;
        foreach ($circuits as $key => $c) {
            if ($c->id == $circuit->id) {
                $index = $key;
                break;
            }
        }
        $isLastCircuit = ($index == $circuits->count() - 1);

        // Vérifier si le circuit suivant a is_entry = true
        $nextCircuit = null;
        if (!$isLastCircuit) {
            $nextCircuit = $circuits[$index + 1] ?? null;
        }

        return view('circuits.edit', compact('circuit', 'services', 'patient', 'isLastCircuit', 'nextCircuit'));
    }

    public function update(Request $request, Circuit $circuit)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $circuit->update([
            'service_id' => $request->service_id,
            'is_entry' => $request->boolean('is_entry'),
            'is_exit' => $request->boolean('is_exit'),
        ]);

        return redirect()->route('patients.show', $circuit->patient_id)
            ->with('success', 'Passage modifié avec succès.');
    }

    public function destroy(Circuit $circuit)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $patientId = $circuit->patient_id;
        $circuit->delete();

        return response()->json(['success' => true]);
    }
}
