@extends('layouts.app')
@section('title', 'Dossier — ' . $patient->prenom . ' ' . $patient->nom)
@section('content')

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex gap-3 align-items-center">
                <div style="width:52px;height:52px;border-radius:14px;background:var(--blue-l);color:var(--blue);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;flex-shrink:0">
                    {{ strtoupper(substr($patient->prenom,0,1).substr($patient->nom,0,1)) }}
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="font-size:1.1rem">{{ $patient->prenom }} {{ $patient->nom }}</h4>
                    <div class="d-flex flex-wrap gap-3" style="font-size:.82rem;color:var(--muted)">
                        <span><i class="bi bi-upc me-1"></i><span class="code-badge">{{ $patient->code_unique }}</span></span>
                        @if($patient->telephone)<span><i class="bi bi-telephone me-1"></i>{{ $patient->telephone }}</span>@endif
                        @if($patient->npi)<span><i class="bi bi-person-badge me-1"></i>NPI: {{ $patient->npi }}</span>@endif
                        @if($patient->date_naissance)<span><i class="bi bi-calendar3 me-1"></i>{{ $patient->date_naissance->format('d/m/Y') }}</span>@endif
                        @if($patient->adresse)<span><i class="bi bi-geo-alt me-1"></i>{{ $patient->adresse }}</span>@endif
                    </div>
                    <div style="font-size:.75rem;color:#bbb;margin-top:3px">
                        Enregistré par {{ $patient->user->prenom ?? '' }} {{ $patient->user->nom ?? '' }} le {{ $patient->created_at->format('d/m/Y à H:i') }}
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('patients.edit',$patient) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Modifier</a>
                <a href="{{ route('factures.create',['patient_id'=>$patient->id]) }}" class="btn btn-sm btn-success"><i class="bi bi-receipt me-1"></i>Ajouter facture</a>
                <a href="{{ route('circuits.create',['patient_id'=>$patient->id]) }}" class="btn btn-sm btn-primary"><i class="bi bi-diagram-3 me-1"></i>Ajouter passage</a>
                <form method="POST" action="{{ route('patients.destroy',$patient) }}"
                      onsubmit="return confirm('Supprimer définitivement ce patient et tout son historique ?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash me-1"></i>Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="stat-card" style="animation-delay:.05s">
            <div class="stat-icon" style="background:var(--blue-l);color:var(--blue)"><i class="bi bi-diagram-3-fill"></i></div>
            <div>
                <div class="stat-num" style="color:var(--blue)">{{ $patient->circuits->count() }}</div>
                <div class="stat-lbl">Passages</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="stat-card" style="animation-delay:.1s">
            <div class="stat-icon" style="background:var(--green-l);color:var(--green)"><i class="bi bi-receipt-cutoff"></i></div>
            <div>
                <div class="stat-num" style="color:var(--green)">{{ $patient->factures->count() }}</div>
                <div class="stat-lbl">Factures</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="stat-card" style="animation-delay:.15s">
            <div class="stat-icon" style="background:#F3F0FF;color:#6D28D9"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="stat-num" style="color:#6D28D9;font-size:1.1rem">{{ number_format($patient->factures->sum('montant'),0,',',' ') }}</div>
                <div class="stat-lbl">FCFA facturé</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-clock-history" style="color:var(--blue)"></i>
        Historique complet
        <span class="badge ms-auto" style="background:var(--blue-l);color:var(--blue)">{{ $historique->count() }} événement(s)</span>
    </div>
    <div class="card-body p-3">
        @forelse($historique as $item)
        <div class="d-flex gap-3 hist-item {{ $item['type']==='circuit' ? 'hist-circuit' : 'hist-facture' }}" style="margin-bottom:1rem;border-bottom:1px solid #f0f0f0;padding-bottom:1rem;">
            <div class="hist-icon" style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;{{ $item['type']==='circuit' ? 'background:var(--blue-l);color:var(--blue)' : 'background:var(--green-l);color:var(--green)' }}">
                <i class="bi {{ $item['type']==='circuit' ? 'bi-diagram-3-fill' : 'bi-receipt-cutoff' }}" style="font-size:1.2rem;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <span class="fw-semibold">{{ $item['service'] }}</span>
                        <span class="badge ms-1" style="{{ $item['type']==='circuit' ? 'background:var(--blue-l);color:var(--blue)' : 'background:var(--green-l);color:var(--green)' }}">
                            {{ $item['type']==='circuit' ? 'Passage' : 'Facture' }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <small style="color:var(--muted)">{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y H:i') }}</small>
                        @if($item['type']==='facture')
                            <a href="{{ route('factures.preview', $item['id']) }}"
                               class="btn btn-sm btn-outline-info py-0 px-1"
                               target="_blank"
                               title="Aperçu">
                                <i class="bi bi-eye" style="font-size:.75rem"></i>
                            </a>
                            <a href="{{ route('factures.download', $item['id']) }}"
                               class="btn btn-sm btn-outline-primary py-0 px-1"
                               title="Télécharger PDF">
                                <i class="bi bi-download" style="font-size:.75rem"></i>
                            </a>
                        @endif
                        @if(Auth::user()->role==='admin')
                            @if($item['type']==='circuit')
                            <form method="POST" action="{{ route('circuits.destroy',$item['id']) }}"
                                  onsubmit="return confirm('Supprimer ce passage ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger py-0 px-1"><i class="bi bi-trash" style="font-size:.75rem"></i></button>
                            </form>
                            @else
                            <a href="{{ route('factures.edit',$item['id']) }}" class="btn btn-sm btn-outline-secondary py-0 px-1"><i class="bi bi-pencil" style="font-size:.75rem"></i></a>
                            <form method="POST" action="{{ route('factures.destroy',$item['id']) }}"
                                  onsubmit="return confirm('Supprimer cette facture ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger py-0 px-1"><i class="bi bi-trash" style="font-size:.75rem"></i></button>
                            </form>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Affichage détaillé selon le type --}}
                @if($item['type']==='circuit')
                    <div style="font-size:.84rem;color:var(--text);margin-top:6px">
                        {{ $item['detail'] }}
                    </div>
                @else
                    {{-- Affichage détaillé de la facture --}}
                    @php $facture = $item['data']; @endphp
                    <div style="margin-top:8px;">
                        <div style="background:#F9FAFB;border-radius:8px;padding:10px;font-size:.8rem;">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div><span style="color:var(--muted)">N° reçu:</span> <strong>{{ $facture->numero_facture }}</strong></div>
                                    <div><span style="color:var(--muted)">Date facture:</span> {{ $facture->date_facture->format('d/m/Y') }}</div>
                                    <div><span style="color:var(--muted)">Service:</span> {{ $facture->service->nom_service ?? '—' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div><span style="color:var(--muted)">Montant total:</span> <strong>{{ number_format($facture->montant, 0, ',', ' ') }} FCFA</strong></div>
                                    @if($facture->has_p_e_c)
                                        <div><span style="color:var(--green)">Prise en charge:</span>
                                            <strong>{{ number_format($facture->pec_montant, 0, ',', ' ') }} FCFA</strong>
                                            <span style="font-size:.72rem;color:var(--muted)">({{ $facture->pec_organisme }})</span>
                                        </div>
                                        <div><span style="color:var(--blue)">Solde patient:</span>
                                            <strong style="color:var(--blue)">{{ number_format($facture->montant_patient, 0, ',', ' ') }} FCFA</strong>
                                        </div>
                                    @else
                                        <div><span style="color:var(--green)">À charge patient:</span>
                                            <strong style="color:var(--green)">{{ number_format($facture->montant, 0, ',', ' ') }} FCFA</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div style="margin-top:6px;font-size:.72rem;color:var(--muted);border-top:1px solid #e5e7eb;padding-top:6px">
                                <i class="bi bi-person me-1"></i>Enregistré par: {{ $facture->user->prenom ?? '' }} {{ $facture->user->nom ?? '' }}
                            </div>
                        </div>
                    </div>
                @endif

                <div style="font-size:.76rem;color:#bbb;margin-top:4px">
                    <i class="bi bi-person me-1"></i>{{ $item['agent'] }}
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5" style="color:var(--muted)">
            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
            Aucun historique. <a href="{{ route('circuits.create',['patient_id'=>$patient->id]) }}">Enregistrer le premier passage</a>
        </div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script>
// Ajouter un peu d'interactivité si besoin
document.addEventListener('DOMContentLoaded', function() {
    // Animation légère au survol
    const histItems = document.querySelectorAll('.hist-item');
    histItems.forEach(item => {
        item.style.transition = 'all 0.2s ease';
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(4px)';
        });
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
});
</script>
@endsection
