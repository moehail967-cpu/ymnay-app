@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Send Mail To All Newsletter Subscriber')}} @endsection

@section('style')
    <x-summernote.css/>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<form action="{{route(route_prefix().'admin.newsletter.mail')}}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Main Content (Left) --}}
        <div class="lg:col-span-9 space-y-6">

            {{-- Compose Mail Card --}}
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-email-fast-outline text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Send Mail To All Subscribers')}}</h3>
                        <p class="text-xs text-muted">{{__('Compose and broadcast an email to all newsletter subscribers')}}</p>
                    </div>
                    <div class="ml-auto">
                        <a href="{{route(route_prefix().'admin.newsletter')}}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                            <i class="mdi mdi-arrow-left text-base"></i> {{__('All Subscribers')}}
                        </a>
                    </div>
                </div>

                <div class="p-4 sm:p-6 space-y-5">
                    {{-- Subject --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Subject')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-text-short text-lg text-primary"></i>
                            <input type="text" name="subject" value="{{old('subject')}}"
                                   placeholder="{{__('Enter email subject')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    {{-- Message --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Message')}}</label>
                        <input type="hidden" name="message">
                        <div class="summernote"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar (Right) --}}
        <div class="lg:col-span-3">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4">
                <div class="px-4 py-4 border-b border-main">
                    <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Send')}}</h4>
                </div>

                <div class="p-4 space-y-5">

                    {{-- Info --}}
                    <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                        <i class="mdi mdi-information-outline text-info text-lg mt-0.5 shrink-0"></i>
                        <span class="text-[11px] text-dark leading-relaxed">{{__('This email will be sent to all verified newsletter subscribers.')}}</span>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-send text-base"></i> {{__('Send Mail')}}
                    </button>
                </div>
            </div>
        </div>

    </div>

</form>

@endsection

@section('scripts')
    <x-summernote.js/>
    <script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            $('.summernote').summernote({
                height: 300,
                codemirror: {
                    theme: 'monokai'
                },
                callbacks: {
                    onChange: function (contents, $editable) {
                        $(this).prev('input').val(contents);
                    }
                }
            });
        });
    })(jQuery);
    </script>
@endsection
