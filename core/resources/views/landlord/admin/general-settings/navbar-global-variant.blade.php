@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Navbar Global Variant Settings')}} @endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-grip-horizontal text-info text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Navbar Global Variant')}}</h3>
            <p class="text-xs text-muted">{{__('Choose a navbar style for your site')}}</p>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <form action="{{route(route_prefix().'admin.general.global.navbar.settings')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" class="form-control" id="global_navbar_variant" value="{{ get_static_option('global_navbar_variant') }}" name="global_navbar_variant">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                @for($i = 1; $i < 7; $i++)
                    <div class="img-select cursor-pointer rounded-xl border-2 border-main overflow-hidden transition hover:border-sidebar-hover hover:shadow-sm">
                        <div class="img-wrap">
                            <img src="{{global_asset('assets/tenant/frontend/navbar-variant/navbar-0'.$i.'.png')}}" data-home_id="0{{$i}}" alt="Navbar {{$i}}" class="w-full block">
                        </div>
                    </div>
                @endfor
            </div>

            <div class="pt-4 border-t border-main">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Update Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                var imgSelect = $('.img-select');
                var id = $('#global_navbar_variant').val();
                imgSelect.removeClass('selected').css({'border-color': ''});
                var activeEl = $('img[data-home_id="'+id+'"]').parent().parent();
                activeEl.addClass('selected').css({'border-color': 'var(--color-primary)'});

                $(document).on('click','.img-select img',function (e) {
                    e.preventDefault();
                    imgSelect.removeClass('selected').css({'border-color': ''});
                    $(this).parent().parent().addClass('selected').css({'border-color': 'var(--color-primary)'});
                    $('#global_navbar_variant').val($(this).data('home_id'));
                });
            });
        }(jQuery));
    </script>
@endsection
