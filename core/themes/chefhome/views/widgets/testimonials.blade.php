<section class="ch-testimonials-widget" style="padding-top:{{ $padding_top }}px; padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="text-center mb-5">
            <div class="ch-sec-title justify-content-center">{{ $title }}</div>
            @if(!empty($subtitle))
                <p class="ch-testimonials-sub">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="row g-4">
            @foreach($testimonials as $t)
                @php
                    $name    = e($t['name']   ?? '');
                    $role    = e($t['role']   ?? '');
                    $rating  = (int) ($t['rating'] ?? 5);
                    $review  = e($t['review'] ?? '');
                    $initial = mb_strtoupper(mb_substr($name, 0, 1));
                    $avatar  = null;
                    if (!empty($t['avatar'])) {
                        $d = get_attachment_image_by_id($t['avatar']);
                        $avatar = $d['img_url'] ?? null;
                    }
                @endphp
                <div class="col-md-6 col-lg-3">
                    <div class="ch-testimonial-card">
                        <div class="ch-testimonial-stars">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $rating ? '★' : '☆' }}
                            @endfor
                        </div>
                        <p class="ch-testimonial-text">"{{ $review }}"</p>
                        <div class="d-flex align-items-center gap-3">
                            <div class="ch-testimonial-avatar">
                                @if($avatar)
                                    <img src="{{ $avatar }}" alt="{{ $name }}">
                                @else
                                    {{ $initial }}
                                @endif
                            </div>
                            <div>
                                <div class="ch-testimonial-name">{{ $name }}</div>
                                <div class="ch-testimonial-role">{{ $role }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<style>
.ch-testimonials-sub { color:var(--ch-muted); margin-top:10px; font-size:15px; }
</style>
