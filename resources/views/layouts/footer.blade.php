@php $footAreas = config('practice', []); @endphp

<section class="cta-strip">
    <div class="container cta-inner" data-aos="zoom-in">
        <div>
            <h2>Need Legal Help <span class="gold-text">Right Now?</span></h2>
            <p>Talk to an experienced advocate today. First consultation is completely confidential.</p>
        </div>
        <div class="cta-actions">
            <a href="tel:+919999999999" class="btn btn-outline"><i class="fa-solid fa-phone"></i> Call Now</a>
            <a href="{{ route('contact') }}" class="btn btn-gold">Book Consultation</a>
        </div>
    </div>
</section>

<footer class="site-footer">
    <div class="footer-glow"></div>
    <div class="container footer-grid">
        <div class="footer-col" data-aos="fade-up">
           <a href="{{ route('home') }}" class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="LEXORA LEGAL Logo" class="logo-image">
        </a>
            <p class="footer-about">Advocates & legal consultants delivering honest advice, fearless representation and results-driven strategy since 2005.</p>
            <div class="socials">
                <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
            </div>
        </div>

        <div class="footer-col" data-aos="fade-up" data-aos-delay="100">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('about') }}">About Firm</a></li>
                <li><a href="{{ route('practice.index') }}">Practice Areas</a></li>
                <li><a href="{{ route('career') }}">Career</a></li>
                <li><a href="{{ route('contact') }}">Contact Us</a></li>
            </ul>
        </div>

        <div class="footer-col" data-aos="fade-up" data-aos-delay="200">
            <h4>Practice Areas</h4>
            <ul>
                @foreach(array_slice($footAreas, 0, 6, true) as $slug => $a)
                    <li><a href="{{ route('practice.show', $slug) }}">{{ $a['title'] }}</a></li>
                @endforeach
                <li><a href="{{ route('practice.index') }}" class="gold-link">View All Services</a></li>
            </ul>
        </div>

        <div class="footer-col" data-aos="fade-up" data-aos-delay="300">
            <h4>Get In Touch</h4>
            <ul class="footer-contact">
                <li><i class="fa-solid fa-location-dot"></i> Chamber 214, Supreme Tower, Connaught Place, New Delhi – 110001</li>
                <li><i class="fa-solid fa-phone"></i> +91 99999 99999</li>
                <li><i class="fa-solid fa-envelope"></i> info@lexoralegal.com</li>
                <li><i class="fa-regular fa-clock"></i> Mon – Sat : 10 AM – 7 PM</li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <p>© {{ date('Y') }} Lexora Legal. All Rights Reserved.</p>
            <p class="disclaimer">As per Bar Council of India rules, this website is for information only and is not a solicitation.</p>
        </div>
    </div>
</footer>