@if(session('success'))
    <div class="alert alert-success" data-aos="fade-down">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-error" data-aos="fade-down">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <ul>
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif