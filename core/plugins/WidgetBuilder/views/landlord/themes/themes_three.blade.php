<section class="relative bg-center bg-cover bg-no-repeat py-[140px] mt-36"
    @if(!empty($bgImage)) style="background-image: url('{{ $bgImage }}')" @endif>

    <div class="absolute inset-0 bg-black/80 z-0"></div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        <div class="container mx-auto px-4 flex items-center-center justify-between head relative z-10">
            <div class="max-w-lg">
                <span class="inline-block rounded-lg py-1 px-3 border bg-[#E8C8FF]/15 text-base-100 border-borderCS">
                    {{ $badgeText }}
                </span>
                <h3 class="font-urbanist font-bold text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl mt-4 lg:mt-6 leading-7 lg:leading-[3.25rem]">
                    {{ $title }}
                </h3>
            </div>
            <div class="controls hidden lg:flex">
                <span id="prevBtn" class="nav-btn bg-white/20 backdrop-blur-md group w-14 h-14 flex items-center justify-center rounded-full hover:bg-white/30 transition-all duration-300 border border-white/30 shadow-lg" aria-label="Prev">
                    <i class="icon-base ti tabler-chevron-left text-white group-hover:text-white transition-colors"></i>
                </span>
                <span id="nextBtn" class="nav-btn bg-white/20 backdrop-blur-md group w-14 h-14 flex items-center justify-center rounded-full hover:bg-white/30 transition-all duration-300 border border-white/30 shadow-lg" aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right text-white group-hover:text-white transition-colors"></i>
                </span>
            </div>
        </div>

    </div>
    <div class=" px-4 ">
        <div class="relative max-w-7xl mx-auto">
            <!-- 3D Slider Container -->
            <div class="slider-3d-container relative h-[600px] lg:h-[700px] perspective-1000">
                <div class="slider-3d-wrapper relative w-full h-full flex items-center justify-center">
                    @foreach($slides as $index => $slide)
                        <div class="slider-3d-slide absolute transition-all duration-700 ease-out" data-index="{{ $index }}">
                            <!-- Main Preview Image -->
                            <div class="relative group">
                                <!-- Glow Effect -->
                                <div class="absolute inset-0 bg-gradient-to-r from-purple-600/20 to-pink-600/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-500"></div>

                                <!-- Main Image Container -->
                                <div class="relative bg-gradient-to-br from-gray-900/50 to-gray-800/50 backdrop-blur-sm rounded-2xl p-1 border border-white/20 shadow-2xl transform hover:scale-105 transition-all duration-500">
                                    <div class="relative overflow-hidden rounded-xl">
                                        <img class="w-full h-[400px] lg:h-[500px] object-cover transform transition-all duration-700 group-hover:scale-110"
                                             src="{{ $slide['preview'] }}"
                                             alt="{{ $slide['title'] ?? 'Slide ' . ($index + 1) }}">

                                        <!-- Overlay Gradient -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-60"></div>

                                        <!-- Thumbnail Mini Preview -->
                                        <div class="absolute bottom-6 right-6 w-40 h-40 lg:w-40 lg:h-40 rounded-lg overflow-hidden border-2 border-white/30 shadow-lg transform rotate-12 hover:rotate-0 transition-all duration-300">
                                            <img class="w-full h-full object-cover" src="{{ $slide['thumbnail'] }}" alt="">
                                        </div>

                                        <!-- Slide Number -->
                                        <div class="absolute top-6 left-6 w-12 h-12 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30">
                                            <span class="text-white font-bold text-lg">{{ $index + 1 }}</span>
                                        </div>

                                        <!-- Animated Elements -->
                                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                            <div class="w-32 h-32 rounded-full border-2 border-white/20 animate-pulse"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 3D Navigation Dots -->
                <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-3 z-20 mt-5">
                    @foreach($slides as $index => $slide)
                        <button class="slider-3d-dot w-3 h-3 rounded-full bg-white/30 hover:bg-white/60 transition-all duration-300 {{ $index === 0 ? 'bg-white w-8' : '' }}"
                                data-slide="{{ $index }}"></button>
                    @endforeach
                </div>

                <!-- Progress Bar -->
                <!-- <div class="absolute bottom-0 left-0 right-0 h-1 bg-white/10 rounded-full overflow-hidden">
                    <div class="slider-3d-progress h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full transition-all duration-700" style="width: 0%"></div>
                </div> -->
            </div>
        </div>
    </div>
</section>

<style>
/* 3D Slider Styles */
.perspective-1000 {
    perspective: 1000px;
}

.slider-3d-container {
    position: relative;
    overflow: hidden;
}

.slider-3d-wrapper {
    transform-style: preserve-3d;
    transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}

.slider-3d-slide {
    width: 100%;
    max-width: 800px;
    opacity: 0;
    transform: translateX(100%) scale(0.8) rotateY(-25deg);
    pointer-events: none;
    z-index: 1;
}

.slider-3d-slide.active {
    opacity: 1;
    transform: translateX(0) scale(1) rotateY(0deg);
    pointer-events: auto;
    z-index: 10;
}

.slider-3d-slide.prev {
    opacity: 0.7;
    transform: translateX(-50%) scale(0.8) rotateY(25deg);
    pointer-events: auto;
    z-index: 5;
}

.slider-3d-slide.next {
    opacity: 0.7;
    transform: translateX(50%) scale(0.8) rotateY(-25deg);
    pointer-events: auto;
    z-index: 5;
}

.slider-3d-slide.far-prev {
    opacity: 0;
    transform: translateX(-100%) scale(0.6) rotateY(45deg);
    pointer-events: none;
    z-index: 1;
}

.slider-3d-slide.far-next {
    opacity: 0;
    transform: translateX(100%) scale(0.6) rotateY(-45deg);
    pointer-events: none;
    z-index: 1;
}

.slider-3d-dot {
    transition: all 0.3s ease;
}

.slider-3d-dot:hover {
    transform: scale(1.2);
}

.slider-3d-dot.active {
    background: white;
    transform: scale(1.5);
}

/* Mobile Responsive */
@media (max-width: 1023px) {
    .slider-3d-container {
        height: 500px !important;
    }

    .slider-3d-slide {
        max-width: 90%;
    }

    .slider-3d-slide.active {
        transform: translateX(0) scale(1) rotateY(0deg);
    }

    .slider-3d-slide.prev,
    .slider-3d-slide.next,
    .slider-3d-slide.far-prev,
    .slider-3d-slide.far-next {
        opacity: 0;
        transform: translateX(100%) scale(0.8);
        pointer-events: none;
    }

    .slider-3d-slide.prev {
        transform: translateX(-100%) scale(0.8);
    }
}

/* Touch Swipe Support */
.slider-3d-container {
    touch-action: pan-y;
}

.slider-3d-wrapper {
    cursor: grab;
}

.slider-3d-wrapper.dragging {
    cursor: grabbing;
}

/* Loading Animation */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(100%) scale(0.8) rotateY(-25deg);
    }
    to {
        opacity: 1;
        transform: translateX(0) scale(1) rotateY(0deg);
    }
}

.slider-3d-slide.active {
    animation: slideIn 0.7s ease-out;
}

/* Hover Effects */
.slider-3d-slide:hover .group-hover\:blur-2xl {
    filter: blur(1.5rem);
}

.slider-3d-slide:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Progress Bar Animation */
@keyframes progress {
    from { width: 0%; }
    to { width: 100%; }
}

.slider-3d-progress.auto-sliding {
    animation: progress 5s linear;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider3D = {
        container: document.querySelector('.slider-3d-wrapper'),
        slides: document.querySelectorAll('.slider-3d-slide'),
        dots: document.querySelectorAll('.slider-3d-dot'),
        prevBtn: document.getElementById('prevBtn'),
        nextBtn: document.getElementById('nextBtn'),
        progressBar: document.querySelector('.slider-3d-progress'),
        currentIndex: 0,
        isAutoSliding: true,
        autoSlideInterval: null,
        isDragging: false,
        startX: 0,
        currentX: 0,

        init() {
            this.setupSlides();
            this.bindEvents();
            this.startAutoSlide();
            this.updateProgress();
        },

        setupSlides() {
            this.slides.forEach((slide, index) => {
                slide.classList.remove('active', 'prev', 'next', 'far-prev', 'far-next');

                if (index === this.currentIndex) {
                    slide.classList.add('active');
                } else if (index === this.currentIndex - 1 || (this.currentIndex === 0 && index === this.slides.length - 1)) {
                    slide.classList.add('prev');
                } else if (index === this.currentIndex + 1 || (this.currentIndex === this.slides.length - 1 && index === 0)) {
                    slide.classList.add('next');
                } else if (index < this.currentIndex) {
                    slide.classList.add('far-prev');
                } else {
                    slide.classList.add('far-next');
                }
            });

            this.updateDots();
        },

        updateDots() {
            this.dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === this.currentIndex);
            });
        },

        updateProgress() {
            const progress = ((this.currentIndex + 1) / this.slides.length) * 100;
            this.progressBar.style.width = `${progress}%`;
        },

        goToSlide(index) {
            this.currentIndex = index;
            this.setupSlides();
            this.updateProgress();
            this.restartAutoSlide();
        },

        next() {
            this.currentIndex = (this.currentIndex + 1) % this.slides.length;
            this.goToSlide(this.currentIndex);
        },

        prev() {
            this.currentIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
            this.goToSlide(this.currentIndex);
        },

        startAutoSlide() {
            if (this.isAutoSliding) {
                this.autoSlideInterval = setInterval(() => {
                    if (!this.isDragging) {
                        this.next();
                    }
                }, 5000);
            }
        },

        stopAutoSlide() {
            if (this.autoSlideInterval) {
                clearInterval(this.autoSlideInterval);
                this.autoSlideInterval = null;
            }
        },

        restartAutoSlide() {
            this.stopAutoSlide();
            this.startAutoSlide();
        },

        bindEvents() {
            // Navigation buttons
            this.prevBtn?.addEventListener('click', () => this.prev());
            this.nextBtn?.addEventListener('click', () => this.next());

            // Dot navigation
            this.dots.forEach((dot, index) => {
                dot.addEventListener('click', () => this.goToSlide(index));
            });

            // Touch events for mobile
            this.container.addEventListener('touchstart', (e) => this.handleTouchStart(e), { passive: true });
            this.container.addEventListener('touchmove', (e) => this.handleTouchMove(e), { passive: true });
            this.container.addEventListener('touchend', (e) => this.handleTouchEnd(e), { passive: true });

            // Mouse events for desktop
            this.container.addEventListener('mousedown', (e) => this.handleMouseDown(e));
            this.container.addEventListener('mousemove', (e) => this.handleMouseMove(e));
            this.container.addEventListener('mouseup', (e) => this.handleMouseUp(e));
            this.container.addEventListener('mouseleave', (e) => this.handleMouseUp(e));

            // Pause on hover
            this.container.addEventListener('mouseenter', () => this.stopAutoSlide());
            this.container.addEventListener('mouseleave', () => this.startAutoSlide());

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') this.prev();
                if (e.key === 'ArrowRight') this.next();
            });
        },

        handleTouchStart(e) {
            this.startX = e.touches[0].clientX;
            this.isDragging = true;
            this.container.classList.add('dragging');
        },

        handleTouchMove(e) {
            if (!this.isDragging) return;
            this.currentX = e.touches[0].clientX;
        },

        handleTouchEnd(e) {
            if (!this.isDragging) return;

            const diff = this.startX - this.currentX;
            const threshold = 50;

            if (Math.abs(diff) > threshold) {
                if (diff > 0) {
                    this.next();
                } else {
                    this.prev();
                }
            }

            this.isDragging = false;
            this.container.classList.remove('dragging');
        },

        handleMouseDown(e) {
            this.startX = e.clientX;
            this.isDragging = true;
            this.container.classList.add('dragging');
        },

        handleMouseMove(e) {
            if (!this.isDragging) return;
            this.currentX = e.clientX;
        },

        handleMouseUp(e) {
            if (!this.isDragging) return;

            const diff = this.startX - this.currentX;
            const threshold = 50;

            if (Math.abs(diff) > threshold) {
                if (diff > 0) {
                    this.next();
                } else {
                    this.prev();
                }
            }

            this.isDragging = false;
            this.container.classList.remove('dragging');
        }
    };

    // Initialize 3D slider
    if (document.querySelector('.slider-3d-container')) {
        slider3D.init();
    }
});
</script>
