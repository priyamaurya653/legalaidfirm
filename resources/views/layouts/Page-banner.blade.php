<section class="page-banner">
    <div class="pb-bg" style="background-image:url('{{ $image ?? 'https://images.unsplash.com/photo-1589994965851-a8f479c573a9?w=1800&q=80' }}')"></div>
    <div class="pb-overlay"></div>
    <div class="container pb-inner">
        <h1 data-aos="fade-up">{!! $title !!}</h1>
        <ul class="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <li><a href="{{ route('home') }}">Home</a></li>
            @isset($parent)
                <li><a href="{{ $parent[1] }}">{{ $parent[0] }}</a></li>
            @endisset
            <li>{{ strip_tags($title) }}</li>
        </ul>
    </div>
</section>