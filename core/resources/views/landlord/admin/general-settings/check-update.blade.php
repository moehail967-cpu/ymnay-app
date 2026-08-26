@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Check Update')}} @endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-cloud-download-alt text-success text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Check Update')}}</h3>
            <p class="text-xs text-muted">{{__('Check for available system updates')}}</p>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <button type="button"
                class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition"
                id="click_for_check_update">
            <i class="las la-spinner la-spin hidden"></i>
            <i class="las la-sync-alt"></i>
            {{__('Click to Check For Update')}}
        </button>

        <div id="update_notice_wrapper" class="hidden mt-5"></div>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        (function($){
            "use strict";
            $(document).ready(function() {
                $("body").on("click","#update_download_and_run_update",function (e){
                    e.preventDefault();
                    var el = $(this);
                    el.find('.la-spinner').removeClass('hidden');
                    if(el.attr("disabled") != undefined && el.attr("disabled") === "disabled"){
                        return;
                    }
                    el.attr("disabled",true);
                    $.ajax({
                        url: el.attr("data-action"),
                        type: "POST",
                        data: {
                            _token : "{{csrf_token()}}",
                            version: el.attr("data-version")
                        },
                        success: function (data){
                            el.find('.la-spinner').addClass('hidden');
                            if(data.msg != undefined && data.msg != ""){
                                el.text(data.msg);
                            }
                        },
                        error: function (error) {
                            console.log(error);
                        }
                    });
                });

                $(document).on("click","#click_for_check_update",function (e){
                    e.preventDefault();
                    var el = $(this);
                    el.find('.la-spinner').removeClass('hidden');
                    el.attr("disabled",true);
                    $.ajax({
                        url: "{{route('landlord.admin.general.update.version.check')}}",
                        type: "GET",
                        success: function (data){
                            el.find('.la-spinner').addClass('hidden');
                            if(data.markup != ""){
                                $("#update_notice_wrapper").append(data.markup);
                            }else if(data.msg != ""){
                                $("#update_notice_wrapper").append("<div class='bg-secondary border border-main rounded-xl px-4 py-3 flex items-start gap-2'><i class='las la-info-circle text-info text-lg mt-0.5 flex-shrink-0'></i><p class='text-xs text-muted'>"+data.msg+"</p></div>");
                            }
                            $("#update_notice_wrapper").removeClass('hidden');
                            el.hide();
                        },
                        error: function (error) {
                            console.log(error);
                        }
                    });
                });
            });
        }(jQuery));
    </script>
@endsection
