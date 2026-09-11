@include('landlord.frontend.partials.header')
@include('landlord.frontend.partials.navbar')

@php $page_post = $page_post ?? null; @endphp
<section style="background: linear-gradient(to right, rgba(var(--main-color-one-rgb, 240, 72, 83), 0.05), rgba(var(--main-color-two-rgb, 255, 128, 93), 0.10));" class="mt-20 @if((in_array(request()->route()->getName(),['landlord.homepage','landlord.dynamic.page']) && isset($page_post) && $page_post->breadcrumb == 0 )) hidden @endif">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative z-10 py-8 flex flex-col items-center justify-center">
            <h4 class="font-urbanist font-semibold md:text-2xl lg:text-3xl text-secondary leading-16">@yield('page-title')</h4>
            <nav aria-label="Breadcrumb" class="max-w-full overflow-x-auto mt-5">
                <ol class="flex items-center gap-2 whitespace-nowrap">
                    <li>
                        <a href="{{ route('landlord.homepage') }}" class="text-[#374253] text-[18px] font-normal transition-colors">
                            {{ __('Home') }}
                        </a>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-[6px] h-[6px] rounded-full bg-[#374253]"></span>
                        <span class="text-[18px] text-[#374253] font-normal">@yield('page-title')</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</section>

@yield('content')
@include('landlord.frontend.partials.footer')
