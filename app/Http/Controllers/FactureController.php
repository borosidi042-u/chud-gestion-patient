<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Patient;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class FactureController extends Controller
{
    // ── Liste ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Facture::with(['patient','service','user'])->latest();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('numero_facture','LIKE','%'.$s.'%')
                  ->orWhereHas('patient', fn($p) =>
                      $p->whereRaw('LOWER(nom) LIKE ?',   ['%'.strtolower($s).'%'])
                        ->orWhereRaw('LOWER(prenom) LIKE ?',['%'.strtolower($s).'%'])
                        ->orWhere('code_unique','LIKE','%'.$s.'%'));
            });
        }
        $factures     = $query->paginate(20)->withQueryString();
        $totalMontant = Facture::sum('montant');          // total brut
        $totalPEC     = Facture::sum('pec_montant');      // total prise en charge
        $totalPatient = $totalMontant - $totalPEC;        // charge réelle patients

        return view('factures.index', compact('factures','totalMontant','totalPEC','totalPatient'));
    }

    // ── Formulaire création ───────────────────────────────────────────────
    public function create(Request $request)
    {
        $services = Service::orderBy('nom_service')->get();
        $patient  = $request->filled('patient_id')
                    ? Patient::findOrFail($request->patient_id) : null;
        return view('factures.create', compact('services','patient'));
    }

    // ── Enregistrement ────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'     => ['required','exists:patients,id'],
            'numero_facture' => ['required','string','max:50','regex:/^[A-Za-z0-9\-\/]+$/'],
            'montant'        => ['required','numeric','min:1','max:99999999'],
            'date_facture'   => ['required','date','before_or_equal:today'],
            'service_id'     => ['required','exists:services,id'],
            // Prise en charge — facultative
            'pec_organisme'  => ['nullable','string','max:150'],
            'pec_montant'    => ['nullable','numeric','min:0','max:99999999'],
        ],[
            'patient_id.required'         => 'Veuillez sélectionner un patient.',
            'numero_facture.required'      => 'Le numéro de reçu est obligatoire.',
            'numero_facture.regex'         => 'N° reçu : lettres, chiffres, tirets ou slashes.',
            'montant.required'             => 'Le montant total est obligatoire.',
            'montant.min'                  => 'Le montant doit être supérieur à 0.',
            'date_facture.before_or_equal' => 'La date ne peut pas être dans le futur.',
            'service_id.required'          => 'Veuillez sélectionner un service.',
            'pec_montant.min'              => 'Le montant de prise en charge ne peut pas être négatif.',
        ]);

        // Vérification : pec_montant ne doit pas dépasser le montant total
        $pec = $request->pec_montant ? floatval($request->pec_montant) : null;
        if ($pec && $pec > floatval($request->montant)) {
            return back()->withErrors(['pec_montant'=>'La prise en charge ne peut pas dépasser le montant total.'])
                         ->withInput();
        }

        Facture::create([
            'patient_id'     => $request->patient_id,
            'numero_facture' => strtoupper(trim($request->numero_facture)),
            'montant'        => $request->montant,
            'date_facture'   => $request->date_facture,
            'service_id'     => $request->service_id,
            'user_id'        => Auth::id(),
            'pec_organisme'  => $request->pec_organisme ? trim($request->pec_organisme) : null,
            'pec_montant'    => $pec,
        ]);

        return redirect()->route('patients.show', $request->patient_id)
                         ->with('success','Facture enregistrée avec succès.');
    }

    // ── Modification ──────────────────────────────────────────────────────
    public function edit(Facture $facture)
    {
        $this->checkAdmin();
        $services = Service::orderBy('nom_service')->get();
        return view('factures.edit', compact('facture','services'));
    }

    public function update(Request $request, Facture $facture)
    {
        $this->checkAdmin();
        $request->validate([
            'numero_facture' => ['required','string','max:50','regex:/^[A-Za-z0-9\-\/]+$/'],
            'montant'        => ['required','numeric','min:1','max:99999999'],
            'date_facture'   => ['required','date','before_or_equal:today'],
            'service_id'     => ['required','exists:services,id'],
            'pec_organisme'  => ['nullable','string','max:150'],
            'pec_montant'    => ['nullable','numeric','min:0','max:99999999'],
        ],[
            'numero_facture.regex'         => 'Format N° reçu invalide.',
            'montant.min'                  => 'Montant doit être > 0.',
            'date_facture.before_or_equal' => 'La date ne peut pas être dans le futur.',
            'pec_montant.min'              => 'Montant PEC ne peut pas être négatif.',
        ]);

        $pec = $request->pec_montant ? floatval($request->pec_montant) : null;
        if ($pec && $pec > floatval($request->montant)) {
            return back()->withErrors(['pec_montant'=>'PEC ne peut pas dépasser le montant total.'])->withInput();
        }

        $facture->update([
            'numero_facture' => strtoupper(trim($request->numero_facture)),
            'montant'        => $request->montant,
            'date_facture'   => $request->date_facture,
            'service_id'     => $request->service_id,
            'pec_organisme'  => $request->pec_organisme ? trim($request->pec_organisme) : null,
            'pec_montant'    => $pec,
        ]);

        return redirect()->route('patients.show',$facture->patient_id)
                         ->with('success','Facture modifiée avec succès.');
    }

    // ── Suppression ───────────────────────────────────────────────────────
    public function destroy(Facture $facture)
    {
        $this->checkAdmin();
        $pid = $facture->patient_id;
        $facture->delete();
        return redirect()->route('patients.show',$pid)->with('success','Facture supprimée.');
    }

    // ── Aperçu PDF ───────────────────────────────────────────────────────
    public function preview(Facture $facture)
    {
        $facture->load(['patient', 'service', 'user']);
        return view('factures.preview', compact('facture'));
    }

    // ── Télécharger PDF ───────────────────────────────────────────────────
    public function download(Facture $facture)
    {
        $facture->load(['patient', 'service', 'user']);
        $pdf = Pdf::loadView('factures.pdf', compact('facture'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('facture_' . $facture->numero_facture . '_' . date('Ymd') . '.pdf');
    }

    private function checkAdmin()
    {
        if (Auth::user()->role !== 'admin') abort(403,'Réservé à l\'administrateur.');
    }
}
