{{-- Usage: @include('layouts.explore-card', ['slug' => $slug, 'a' => $a, 'i' => $loop->index]) --}}
<a href="{{ route('practice.show', $slug) }}" class="ex-card tilt" data-aos="fade-up" data-aos-delay="{{ ($i % 4) * 100 }}">
    <div class="ex-inner">
        <img src="{{ $a['image'] }}" alt="{{ $a['title'] }}" loading="lazy">
        <div class="ex-shade"></div>
        <span class="ex-shine"></span>

        <span class="ex-icon"><i class="{{ $a['icon'] }}"></i></span>

        <div class="ex-body">
            <h3>{{ $a['title'] }}</h3>
            <p>{{ $a['short'] }}</p>
            <span class="ex-more">View Details <i class="fa-solid fa-arrow-right"></i></span>
        </div>
    </div>
</a>