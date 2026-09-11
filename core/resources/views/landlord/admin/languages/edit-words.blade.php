@php
    $route_name = is_null(tenant()) ? 'landlord' : 'tenant';
//    $totalWords = !is_array($all_word) ? count((array) $all_word) : count($all_word);
    $all_word = is_array($all_word) ? $all_word : (array) ($all_word ?? []);
    $totalWords = count($all_word);
    $translatedWords = 0;
    foreach($all_word as $k => $v) { if($k !== $v && !empty($v)) $translatedWords++; }
    $progressPct = $totalWords > 0 ? round(($translatedWords / $totalWords) * 100) : 0;
@endphp
@extends($route_name.'.admin.admin-master')

@section('title')
    {{__('Edit Words Settings')}}
@endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/new-landlord/admin/css/components/languages.css')}}">
@endsection

@section('content')

    <x-landlord-flash-msg/>
    <x-landlord-error-msg/>

    {{-- Header Card --}}
    <div class="bg-surface rounded-xl shadow-main border border-main mb-5">
        <div class="px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-translate text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__("Translate Words")}}</h3>
                    <p class="text-xs text-muted">{{__('Translating to')}} <strong class="text-primary">{{$language->name}}</strong></p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-dark bg-secondary border border-main hover:border-hover transition" href="{{ route(route_prefix().'admin.languages')}}">
                    <i class="mdi mdi-arrow-left text-sm"></i> {{__('Back')}}
                </a>
                <button type="button" id="regenerate_source_text_btn" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-white transition hover:opacity-90" style="background: var(--color-warning);">
                    <i class="mdi mdi-refresh text-sm"></i> {{__('Regenerate')}}
                </button>
                <button type="button" class="add_new_string_btn inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-white transition hover:opacity-90" style="background: var(--color-info);">
                    <i class="mdi mdi-plus text-sm"></i> {{__('Add String')}}
                </button>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="px-4 sm:px-6 pb-4">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] font-bold tracking-widest text-muted uppercase">{{__('Translation Progress')}}</span>
                <span class="text-[11px] font-semibold text-dark">{{$translatedWords}} / {{$totalWords}} <span class="text-muted font-normal">({{$progressPct}}%)</span></span>
            </div>
            <div class="w-full h-1.5 rounded-full bg-secondary overflow-hidden">
                <div class="h-full rounded-full transition-all" style="width: {{$progressPct}}%; background: var(--color-primary);"></div>
            </div>
        </div>
    </div>

    {{-- Main Content: Side-by-side on desktop --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        {{-- Word List (left) --}}
        <div class="lg:col-span-3 bg-surface rounded-xl shadow-main border border-main overflow-hidden">

            {{-- Search --}}
            <div class="lang-search">
                <i class="mdi mdi-magnify"></i>
                <input type="text" name="word_search" id="word_search" placeholder="{{__('Search source text...')}}">
            </div>

            {{-- Column Header --}}
            <div class="lang-cols">
                <div class="lang-col-label">{{__('Source Text')}}</div>
                <div class="lang-col-label">{{__('Translation')}}</div>
            </div>

            {{-- Word List --}}
            <div class="lang-list" style="max-height: 560px;">
                @foreach($all_word as $key => $value)
                    <div class="lang-row">
                        <div class="lang-source" data-key="{{$key}}">{{$key}}</div>
                        <div class="lang-trans" data-trans="{{$value}}">{{$key === $value ? '' : $value}}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Translate Form (right, sticky) --}}
        <div class="lg:col-span-2 lg:sticky lg:top-5 h-fit">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
                <div class="px-4 py-3.5 border-b border-main flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-pencil-outline text-primary text-sm"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-dark font-urbanist">{{__('Edit Translation')}}</h4>
                        <p class="text-[10px] text-muted">{{__('Select a word from the list')}}</p>
                    </div>
                </div>

                <div class="p-4 space-y-4">
                    {{-- Selected source --}}
                    <div id="selected_source_text" class="px-4 py-3 rounded-xl bg-secondary border border-main min-h-[3.5rem]">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Source Text')}}</span>
                        <strong class="text text-sm font-semibold text-dark block mt-1">—</strong>
                    </div>

                    {{-- Form --}}
                    <form action="javascript:void(0)" method="POST" id="langauge_translate_form" enctype="multipart/form-data">
                        <input type="hidden" name="type" value="{{$type}}">
                        <input type="hidden" name="string_key">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Translate To')}} <span class="text-primary">{{$language->name}}</span></label>
                        <textarea name="translate_word" rows="4" class="lang-textarea" placeholder="{{__('Enter your translation...')}}"></textarea>
                        <button type="submit" class="mt-3 w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90" style="background: var(--color-primary);">
                            <i class="mdi mdi-check text-base"></i> {{__('Update Translation')}}
                        </button>
                    </form>
                </div>

                {{-- Hint --}}
                <div class="px-4 py-3 border-t border-main" style="background: var(--color-bg-secondary);">
                    <p class="text-[11px] text-muted"><i class="mdi mdi-lightbulb-outline mr-1 text-primary"></i>{{__('Click any source text on the left to translate it.')}}</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Add New String Modal --}}
    <div id="add_new_string_modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm lang-modal-overlay"></div>
        <div class="relative bg-surface rounded-2xl border border-main w-full max-w-md overflow-hidden" style="box-shadow: 0 24px 48px rgba(0,0,0,0.15);">
            <div class="px-5 py-4 border-b border-main flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--color-info-bg);">
                        <i class="mdi mdi-plus-circle-outline text-sm" style="color: var(--color-info);"></i>
                    </div>
                    <h5 class="text-sm font-bold text-dark font-urbanist">{{__('Add New String')}}</h5>
                </div>
                <button type="button" class="lang-modal-close w-7 h-7 rounded-lg flex items-center justify-center hover:bg-secondary transition">
                    <i class="mdi mdi-close text-muted"></i>
                </button>
            </div>
            <form action="{{route(route_prefix().'admin.languages.add.string')}}" id="add_new_string_modal_form" method="post">
                <div class="p-5 space-y-4">
                    @csrf
                    <input type="hidden" name="slug" value="{{$lang_slug}}">
                    <input type="hidden" name="type" value="{{$type}}">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('String')}}</label>
                        <input type="text" class="lang-input" name="string" placeholder="{{__('Original string')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Translated String')}}</label>
                        <input type="text" class="lang-input" name="translate_string" placeholder="{{__('Translated string')}}">
                    </div>
                    <p class="text-[11px] text-muted"><i class="mdi mdi-lightbulb-outline mr-1 text-primary"></i>{{__('If the translation does not work, try inputting the same text in lowercase')}}</p>
                </div>
                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-main" style="background: var(--color-bg-secondary);">
                    <button type="button" class="lang-modal-close px-4 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">{{__('Cancel')}}</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background: var(--color-primary);">{{__('Add String')}}</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        (function($){
            "use strict";

            $(document).ready(function (){

                // Click source text to select
                $(document).on('click','.lang-list .lang-row .lang-source',function (e){
                    e.preventDefault();
                    var langKey = $(this).data('key');
                    var langValue = $(this).next().data('trans');

                    // Highlight active row
                    $('.lang-row').removeClass('active');
                    $(this).parent().addClass('active');

                    var formContainer = $('#langauge_translate_form');
                    $('#selected_source_text .text').text(langKey);
                    formContainer.find('input[name="string_key"]').val(langKey);
                    formContainer.find('textarea[name="translate_word"]').val(langValue);
                });

                // Search
                $(document).on('keyup','#word_search',function (e){
                    e.preventDefault();
                    var searchText = $(this).val();
                    var allSourceText = $('.lang-list .lang-row .lang-source');
                    $.each(allSourceText,function (index,value){
                        var text = $(this).text();
                        var found = text.toLowerCase().match(searchText.toLowerCase().trim());
                        if (!found){
                            $(this).parent().hide();
                        }else{
                            $(this).parent().show();
                        }
                    });
                });

                // Regenerate
                $(document).on('click','#regenerate_source_text_btn',function (e){
                    e.preventDefault();
                    Swal.fire({
                        title: '{{__("Are you sure?")}}',
                        text: '{{__("It will delete current source texts, you will lose your current translated data!")}}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1a5c4e',
                        cancelButtonColor: '#ef4444',
                        confirmButtonText: "{{__('Yes, Generate!')}}"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: 'POST',
                                url: "{{route(route_prefix().'admin.languages.regenerate.source.texts')}}",
                                data: {
                                    _token : "{{csrf_token()}}",
                                    slug : "{{$lang_slug}}",
                                    type : "{{$type}}",
                                },
                                success : function (){
                                    toastr.success("{{__('source text generate success')}}")
                                    location.reload();
                                }
                            });
                        }
                    });
                });

                // Update translation
                $(document).on('submit', '#langauge_translate_form', function (event){
                    event.preventDefault();

                    var form = $(this);
                    var type = form.find('input[name=type]').val();
                    var string_key = form.find('input[name=string_key]').val();
                    var translate_word = form.find('textarea[name=translate_word]').val();

                    $.ajax({
                        type: 'POST',
                        url: '{{route(route_prefix().'admin.languages.words.update', $lang_slug)}}',
                        data: {
                            _token: '{{csrf_token()}}',
                            type: type,
                            string_key: string_key,
                            translate_word: translate_word
                        },
                        success: function (data){
                            if(data.type === 'success'){
                                toastr.success(data.msg);
                                var translatedString = $('.lang-row .lang-source[data-key="'+string_key+'"]').next();
                                translatedString.text(translate_word);
                                translatedString.attr("data-trans",translate_word);
                            }
                        },
                        error: function (data) {
                            var response = JSON.parse(data.responseText);
                            $.each(response.errors, function(key, value) {
                                toastr.error(value);
                            });
                        }
                    });
                });

                // Open add string modal
                $(document).on('click', '.add_new_string_btn', function(){
                    $('#add_new_string_modal').removeClass('hidden');
                });

                // Close modals
                $(document).on('click', '.lang-modal-close, .lang-modal-overlay', function () {
                    $(this).closest('.fixed').addClass('hidden');
                });
            });
        })(jQuery);
    </script>
@endsection
