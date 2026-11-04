@if($errors->any())
    <div class="notice" role="alert" tabindex="-1" data-errors>
        <strong>Periksa kembali isian Anda.</strong>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
