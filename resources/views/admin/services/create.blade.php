@extends('layouts.app')
@section('title','Nouveau service')
@section('content')
<div class="row justify-content-center"><div class="col-lg-5">
<div class="card">
    <div class="card-header"><i class="bi bi-building-fill me-2" style="color:var(--blue)"></i>Ajouter un service</div>
    <div class="card-body p-4">
        @if($errors->any())
        <div class="alert alert-danger mb-3"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('admin.services.store') }}" id="formSvc" novalidate>
            @csrf
            <div class="mb-3">
                <label class="form-label">Nom du service <span class="text-danger">*</span></label>
                <input type="text" name="nom_service" id="nomSvc" value="{{ old('nom_service') }}"
                       class="form-control @error('nom_service') is-invalid @enderror"
                       placeholder="Ex: Radiologie, Urgences…" required>
                <div class="invalid-feedback" id="nomSvc-err">{{ $errors->first('nom_service') ?: 'Nom invalide.' }}</div>
                <div class="field-hint">Lettres, chiffres et tirets uniquement.</div>
            </div>
            <div class="mb-4">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"
                          placeholder="Description optionnelle">{{ old('description') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Ajouter</button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
</div></div>
@endsection
@section('scripts')
<script>
document.getElementById('formSvc').addEventListener('submit',function(e){
    const n=document.getElementById('nomSvc');
    if(!n.value.trim()){
        n.classList.add('is-invalid');
        document.getElementById('nomSvc-err').textContent='Le nom du service est obligatoire.';
        e.preventDefault();
    }
});
</script>
@endsection
