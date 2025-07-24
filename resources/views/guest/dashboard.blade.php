{{-- filepath: resources/views/guest/dashboard.blade.php --}}
@extends('layouts.guest')

@section('content')
    @include('guest.partials.header')
    <section class="content-section">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Available Classes</h2>
            <a href="{{ route('guest.join-class') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Join a Class
            </a>
        </div>

        <div class="classes-carousel">
            <button class="carousel-nav prev" onclick="moveCarousel(-1)">&#10094;</button>
            <button class="carousel-nav next" onclick="moveCarousel(1)">&#10095;</button>
            <div class="carousel-inner" id="carouselInner">
                @forelse($classes as $class)
                    <div class="carousel-card">
                        <div class="class-thumbnail">
                            @if($class->thumbnail)
                                <img src="{{ asset('storage/' . $class->thumbnail) }}" alt="{{ $class->title }}">
                            @else
                                <i class="fas fa-graduation-cap" style="font-size:48px; color:#ccc;"></i>
                            @endif
                        </div>
                        <div class="class-info">
                            <h4>{{ $class->title }}</h4>
                            <p>{{ Str::limit($class->description, 200) }}</p>
                        </div>
                    </div>
                @empty
                    <p>No classes available at this time.</p>
                @endforelse
            </div>
            <div class="carousel-indicators" id="carouselIndicators"></div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.carousel-inner');
    const cards = Array.from(document.querySelectorAll('.carousel-card'));
    const prevBtn = document.querySelector('.carousel-nav.prev');
    const nextBtn = document.querySelector('.carousel-nav.next');
    let currentIndex = 1; // Start with the second card as center

    function updateCarousel() {
        cards.forEach((card, idx) => {
            card.classList.remove('active', 'side');
            card.style.display = 'none';
            if (idx === currentIndex) {
                card.classList.add('active');
                card.style.display = 'flex';
            } else if (idx === currentIndex - 1 || idx === currentIndex + 1) {
                card.classList.add('side');
                card.style.display = 'flex';
            }
        });
        // Adjust transform so the center card is always in the middle
        const cardWidth = 330; // width + margin
        carousel.style.transform = `translateX(-${(currentIndex - 1) * cardWidth}px)`;
    }

    nextBtn.addEventListener('click', () => {
        if (currentIndex < cards.length - 1) {
            currentIndex++;
            updateCarousel();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });

    // Auto-rotate every 5 seconds
    setInterval(() => {
        if (currentIndex < cards.length - 2) {
            currentIndex++;
        } else {
            currentIndex = 1;
        }
        updateCarousel();
    }, 5000);

    // Initial state
    updateCarousel();
});
    </script>
@endsection