@extends('layouts.app')
@section('title', 'Services hospitaliers')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <span style="color:var(--muted);font-size:.88rem">{{ $services->count() }} service(s)</span>
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Nouveau service</a>
</div>
<div class="card">
    <div class="card-body p-0">
        @if($services->isEmpty())
        <div class="text-center py-5" style="color:var(--muted)"><i class="bi bi-building fs-1 d-block mb-2 opacity-25"></i>Aucun service. <a href="{{ route('admin.services.create') }}">Ajouter</a></div>
        @else
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Service</th><th>Description</th><th class="text-center">Circuits</th><th class="text-center">Factures</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @foreach($services as $s)
            <tr>
                <td class="fw-semibold">{{ $s->nom_service }}</td>
                <td style="color:var(--muted);font-size:.85rem">{{ $s->description ?? '—' }}</td>
                <td class="text-center"><span class="badge" style="background:var(--blue-l);color:var(--blue)">{{ $s->circuits_count }}</span></td>
                <td class="text-center"><span class="badge" style="background:var(--green-l);color:var(--green)">{{ $s->factures_count }}</span></td>
                <td class="text-end">
                    <a href="{{ route('admin.services.edit',$s) }}" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('admin.services.destroy',$s) }}" class="d-inline"
                          onsubmit="return confirm('Supprimer le service {{ $s->nom_service }} ?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" {{ ($s->circuits_count>0||$s->factures_count>0)?'disabled title=Service utilisé':'' }}><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
</div>
@endsection
