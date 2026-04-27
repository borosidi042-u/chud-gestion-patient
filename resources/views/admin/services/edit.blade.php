@extends('layouts.app')
@section('title','Modifier le service')
@section('content')
<div class="row justify-content-center"><div class="col-lg-5">
<div class="card">
    <div class="card-header"><i class="bi bi-pencil me-2" style="color:var(--amber)"></i>Modifier le service</div>
    <div class="card-body p-4">
        @if($errors->any())
        <div class="alert alert-danger mb-3"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <form method="POST" action="{{ route('admin.services.update',$service) }}" id="formSvcE" novalidate>
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nom du service <span class="text-danger">*</span></label>
                <input type="text" name="nom_service" id="nomSvcE" value="{{ old('nom_service',$service->nom_service) }}"
                       class="form-control @error('nom_service') is-invalid @enderror" required>
                <div class="invalid-feedback" id="nomSvcE-err">{{ $errors->first('nom_service') ?: 'Nom invalide.' }}</div>
            </div>
            <div class="mb-4">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description',$service->description) }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning"><i class="bi bi-check-circle me-1"></i>Enregistrer</button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
</div></div>
@endsection
@section('scripts')
<script>
document.getElementById('formSvcE').addEventListener('submit',function(e){
    const n=document.getElementById('nomSvcE');
    if(!n.value.trim()){n.classList.add('is-invalid');e.preventDefault();}
});
</script>
@endsection
