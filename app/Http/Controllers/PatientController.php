<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class PatientController extends Controller
{
    // ── Liste + Recherche + Filtre par période ────────────────────────────────
    public function index(Request $request)
    {
        $query = Patient::with('user')->latest();

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

        if ($request->filled('date_start')) {
            $dateStart = $request->date_start . ' 00:00:00';
            $query->where('created_at', '>=', $dateStart);
        }

        if ($request->filled('date_end')) {
            $dateEnd = $request->date_end . ' 23:59:59';
            $query->where('created_at', '<=', $dateEnd);
        }

        $patients = $query->paginate(15)->withQueryString();
        $totalPatients = $query->count();

        return view('patients.index', compact('patients', 'totalPatients'));
    }

    public function create()
    {
        return view('patients.create');
    }

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

        // Vérification du téléphone même si NPI différent
        if ($request->filled('telephone')) {
            $existingPatient = Patient::where('telephone', $request->telephone)->first();
            if ($existingPatient) {
                return back()->withErrors(['telephone' => 'Ce numéro de téléphone est déjà utilisé par un autre patient.'])
                             ->withInput();
            }
        }

        // Vérification du NPI
        if ($request->filled('npi')) {
            $existingPatient = Patient::where('npi', $request->npi)->first();
            if ($existingPatient) {
                return back()->withErrors(['npi' => 'Ce NPI est déjà utilisé par un autre patient.'])
                             ->withInput();
            }
        }

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

    public function show(Patient $patient)
    {
        $patient->load(['user']);

        $visites = $patient->visites()
            ->with(['mouvements.service', 'mouvements.salle', 'mouvements.lit', 'mouvements.agent', 'validatedBy'])
            ->orderByDesc('date_debut')
            ->get();

        $visiteEnCours = $patient->getVisiteEnCours();

        return view('patients.show', compact('patient', 'visites', 'visiteEnCours'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

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

        // Vérification du téléphone pour modification (exclure le patient actuel)
        if ($request->filled('telephone') && $request->telephone !== $patient->telephone) {
            $existingPatient = Patient::where('telephone', $request->telephone)->where('id', '!=', $patient->id)->first();
            if ($existingPatient) {
                return back()->withErrors(['telephone' => 'Ce numéro de téléphone est déjà utilisé par un autre patient.'])
                             ->withInput();
            }
        }

        // Vérification du NPI pour modification
        if ($request->filled('npi') && $request->npi !== $patient->npi) {
            $existingPatient = Patient::where('npi', $request->npi)->where('id', '!=', $patient->id)->first();
            if ($existingPatient) {
                return back()->withErrors(['npi' => 'Ce NPI est déjà utilisé par un autre patient.'])
                             ->withInput();
            }
        }

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
            'npi'            => $request->npi            ?: nil,
        ]);

        return redirect()->route('patients.show',$patient)
                         ->with('success','Patient mis à jour.'.($nouveauCode!==$ancienCode?' Nouveau code : '.$nouveauCode:''));
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('success','Patient supprimé.');
    }

    public function apiShow($id)
    {
        $patient = Patient::with(['visites' => function($q) {
            $q->with(['mouvements.service', 'mouvements.lit', 'mouvements.salle']);
        }])->findOrFail($id);

        $visiteEnCours = $patient->getVisiteEnCours();
        $litActuel = $visiteEnCours ? $visiteEnCours->getLitActuel() : null;

        return response()->json([
            'id' => $patient->id,
            'nom' => $patient->nom,
            'prenom' => $patient->prenom,
            'code_unique' => $patient->code_unique,
            'visite_en_cours' => $visiteEnCours ? [
                'id' => $visiteEnCours->id,
                'numero_visite' => $visiteEnCours->numero_visite,
                'date_debut' => $visiteEnCours->date_debut,
                'duree' => $visiteEnCours->getDuree()
            ] : null,
            'lit_actuel' => $litActuel ? [
                'id' => $litActuel->id,
                'numero' => $litActuel->numero
            ] : null,
            'service_actuel' => $visiteEnCours && $visiteEnCours->mouvements->last() ?
                $visiteEnCours->mouvements->last()->service->nom_service : null
        ]);
    }

    private function genererCodeUnique(?string $npi, ?string $telephone): string
    {
        if ($npi && preg_match('/^[0-9]{10}$/', $npi))             return $npi;
        if ($telephone && preg_match('/^[0-9]{10}$/', $telephone)) return $telephone;
        return $this->prochainNumeroOrdre();
    }

    private function genererCodeUniqueMaj(?string $npi, ?string $telephone, string $ancien): string
    {
        if ($npi && preg_match('/^[0-9]{10}$/', $npi))             return $npi;
        if ($telephone && preg_match('/^[0-9]{10}$/', $telephone)) return $telephone;
        return $ancien;
    }

    private function prochainNumeroOrdre(): string
    {
        $dernier = Patient::where('code_unique','REGEXP','^0{4}[0-9]{6}$')
                          ->orderBy('code_unique','desc')
                          ->value('code_unique');
        return str_pad($dernier ? (intval($dernier)+1) : 1, 10, '0', STR_PAD_LEFT);
    }
}
