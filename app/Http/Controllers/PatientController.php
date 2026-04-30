<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    // ── Liste + Recherche + Filtre par période ────────────────────────────────
    public function index(Request $request)
    {
        $query = Patient::with('user')->latest();

        // Filtre par recherche textuelle (nom, prénom, code, NPI, téléphone)
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereRaw('LOWER(nom) LIKE ?',    ['%'.strtolower($s).'%'])
                  ->orWhereRaw('LOWER(prenom) LIKE ?',['%'.strtolower($s).'%'])
                  ->orWhere('code_unique','LIKE','%'.$s.'%')
                  ->orWhere('telephone',  'LIKE','%'.$s.'%')
                  ->orWhere('npi',        'LIKE','%'.$s.'%');
            });
        }

        // Filtre par période (date_start et date_end)
        if ($request->filled('date_start')) {
            $dateStart = $request->date_start . ' 00:00:00';
            $query->where('created_at', '>=', $dateStart);
        }

        if ($request->filled('date_end')) {
            $dateEnd = $request->date_end . ' 23:59:59';
            $query->where('created_at', '<=', $dateEnd);
        }

        $patients = $query->paginate(15)->withQueryString();

        // Total des patients pour la période sélectionnée
        $totalPatients = $query->count();

        return view('patients.index', compact('patients', 'totalPatients'));
    }

    // ── Formulaire création ──────────────────────────────────────────────
    public function create()
    {
        return view('patients.create');
    }

    // ── Enregistrement ───────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'nom'            => ['required','string','max:100','regex:/^[\p{L}\s\-\']+$/u'],
            'prenom'         => ['required','string','max:100','regex:/^[\p{L}\s\-\']+$/u'],
            'date_naissance' => ['nullable','date','before:today'],
            'adresse'        => ['nullable','string','max:255','regex:/^[\p{L}0-9\s\,\.\-\']+$/u'],
            'telephone'      => ['nullable','regex:/^[\+]?[0-9]{8,15}$/'],
            'npi'            => ['nullable','regex:/^[0-9]{10}$/'],
        ], [
            'nom.required'         => 'Le nom est obligatoire.',
            'nom.regex'            => 'Le nom ne doit contenir que des lettres, espaces ou tirets.',
            'prenom.required'      => 'Le prénom est obligatoire.',
            'prenom.regex'         => 'Le prénom ne doit contenir que des lettres, espaces ou tirets.',
            'date_naissance.date'  => 'Veuillez entrer une date valide.',
            'date_naissance.before'=> 'La date de naissance doit être dans le passé.',
            'adresse.regex'        => 'L\'adresse contient des caractères non autorisés.',
            'telephone.regex'      => 'Le téléphone doit contenir uniquement des chiffres (8 à 15), avec éventuellement un + au début.',
            'npi.regex'            => 'Le NPI doit contenir exactement 10 chiffres.',
        ]);

        $codeUnique = $this->genererCodeUnique($request->npi, $request->telephone);

        if (Patient::where('code_unique', $codeUnique)->exists()) {
            return back()->withErrors(['code_unique' => 'Ce code existe déjà. Vérifiez le NPI ou le téléphone.'])->withInput();
        }

        Patient::create([
            'nom'            => strtoupper(trim($request->nom)),
            'prenom'         => ucfirst(strtolower(trim($request->prenom))),
            'code_unique'    => $codeUnique,
            'date_naissance' => $request->date_naissance ?: null,
            'adresse'        => $request->adresse        ?: null,
            'telephone'      => $request->telephone      ?: null,
            'npi'            => $request->npi            ?: null,
            'user_id'        => Auth::id(),
        ]);

        return redirect()->route('patients.index')
                         ->with('success', 'Patient enregistré. Code : '.$codeUnique);
    }

    // ── Détail / Historique ──────────────────────────────────────────────
    public function show(Patient $patient)
    {
        $patient->load(['user', 'circuits', 'factures']);

        // Historique combiné
        $historique = collect();

        // Ajouter les circuits
        foreach ($patient->circuits as $circuit) {
            $historique->push([
                'type' => 'circuit',
                'id' => $circuit->id,
                'service' => $circuit->service->nom_service ?? 'Service',
                'detail' => $circuit->description ?? 'Passage enregistré',
                'date' => $circuit->created_at,
                'agent' => ($circuit->user->prenom ?? '') . ' ' . ($circuit->user->nom ?? ''),
                'data' => $circuit
            ]);
        }

        // Ajouter les factures
        foreach ($patient->factures as $facture) {
            $detail = "Facture N° " . $facture->numero_facture . " - ";
            $detail .= "Montant: " . number_format($facture->montant, 0, ',', ' ') . " FCFA";

            if ($facture->has_p_e_c) {
                $detail .= " | Prise en charge: " . $facture->pec_organisme . " (" . number_format($facture->pec_montant, 0, ',', ' ') . " FCFA)";
                $detail .= " | Solde patient: " . number_format($facture->montant_patient, 0, ',', ' ') . " FCFA";
            }

            $historique->push([
                'type' => 'facture',
                'id' => $facture->id,
                'service' => $facture->service->nom_service ?? 'Service',
                'detail' => $detail,
                'date' => $facture->created_at,
                'agent' => ($facture->user->prenom ?? '') . ' ' . ($facture->user->nom ?? ''),
                'data' => $facture
            ]);
        }

        $historique = $historique->sortByDesc('date');

        return view('patients.show', compact('patient', 'historique'));
    }

    // ── Formulaire modification ──────────────────────────────────────────
    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    // ── Mise à jour ──────────────────────────────────────────────────────
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'nom'            => ['required','string','max:100','regex:/^[\p{L}\s\-\']+$/u'],
            'prenom'         => ['required','string','max:100','regex:/^[\p{L}\s\-\']+$/u'],
            'date_naissance' => ['nullable','date','before:today'],
            'adresse'        => ['nullable','string','max:255','regex:/^[\p{L}0-9\s\,\.\-\']+$/u'],
            'telephone'      => ['nullable','regex:/^[\+]?[0-9]{8,15}$/'],
            'npi'            => ['nullable','regex:/^[0-9]{10}$/'],
        ], [
            'nom.regex'            => 'Le nom ne doit contenir que des lettres.',
            'prenom.regex'         => 'Le prénom ne doit contenir que des lettres.',
            'date_naissance.date'  => 'Date invalide.',
            'date_naissance.before'=> 'La date de naissance doit être dans le passé.',
            'adresse.regex'        => 'L\'adresse contient des caractères non autorisés.',
            'telephone.regex'      => 'Le téléphone doit contenir uniquement des chiffres (8-15), + autorisé au début.',
            'npi.regex'            => 'Le NPI doit contenir exactement 10 chiffres.',
        ]);

        $ancienCode  = $patient->code_unique;
        $nouveauCode = $this->genererCodeUniqueMaj($request->npi, $request->telephone, $ancienCode);

        if ($nouveauCode !== $ancienCode &&
            Patient::where('code_unique',$nouveauCode)->where('id','!=',$patient->id)->exists()) {
            return back()->withErrors(['code_unique'=>'Le nouveau code est déjà utilisé.'])->withInput();
        }

        $patient->update([
            'nom'            => strtoupper(trim($request->nom)),
            'prenom'         => ucfirst(strtolower(trim($request->prenom))),
            'code_unique'    => $nouveauCode,
            'date_naissance' => $request->date_naissance ?: null,
            'adresse'        => $request->adresse        ?: null,
            'telephone'      => $request->telephone      ?: null,
            'npi'            => $request->npi            ?: null,
        ]);

        return redirect()->route('patients.show',$patient)
                         ->with('success','Patient mis à jour.'.($nouveauCode!==$ancienCode?' Nouveau code : '.$nouveauCode:''));
    }

    // ── Suppression ──────────────────────────────────────────────────────
    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('success','Patient supprimé.');
    }

    // ── Génération code (création) ───────────────────────────────────────
    private function genererCodeUnique(?string $npi, ?string $telephone): string
    {
        if ($npi && preg_match('/^[0-9]{10}$/', $npi))             return $npi;
        if ($telephone && preg_match('/^[0-9]{10}$/', $telephone)) return $telephone;
        return $this->prochainNumeroOrdre();
    }

    // ── Génération code (modification) ───────────────────────────────────
    private function genererCodeUniqueMaj(?string $npi, ?string $telephone, string $ancien): string
    {
        if ($npi && preg_match('/^[0-9]{10}$/', $npi))             return $npi;
        if ($telephone && preg_match('/^[0-9]{10}$/', $telephone)) return $telephone;
        return $ancien; // aucun identifiant → conserver l'ancien
    }

    // ── Prochain numéro d'ordre ──────────────────────────────────────────
    private function prochainNumeroOrdre(): string
    {
        $dernier = Patient::where('code_unique','REGEXP','^0{4}[0-9]{6}$')
                          ->orderBy('code_unique','desc')
                          ->value('code_unique');
        return str_pad($dernier ? (intval($dernier)+1) : 1, 10, '0', STR_PAD_LEFT);
    }
}
