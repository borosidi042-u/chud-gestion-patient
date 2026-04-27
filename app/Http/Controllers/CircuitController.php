<?php

namespace App\Http\Controllers;

use App\Models\Circuit;
use App\Models\Patient;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CircuitController extends Controller
{
    public function create(Request $request)
    {
        $services = Service::orderBy('nom_service')->get();
        $patient  = null;
        if ($request->filled('patient_id')) {
            $patient = Patient::findOrFail($request->patient_id);
        }
        return view('circuits.create', compact('services','patient'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => ['required','exists:patients,id'],
            'service_id' => ['required','exists:services,id'],
        ], [
            'patient_id.required' => 'Veuillez sélectionner un patient.',
            'patient_id.exists'   => 'Patient introuvable.',
            'service_id.required' => 'Veuillez sélectionner un service.',
            'service_id.exists'   => 'Service introuvable.',
        ]);

        Circuit::create([
            'patient_id' => $request->patient_id,
            'service_id' => $request->service_id,
            'user_id'    => Auth::id(),
        ]);

        return redirect()->route('patients.show',$request->patient_id)
                         ->with('success','Passage enregistré dans le circuit patient.');
    }

    public function destroy(Circuit $circuit)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $pid = $circuit->patient_id;
        $circuit->delete();
        return redirect()->route('patients.show',$pid)->with('success','Passage supprimé.');
    }
}
