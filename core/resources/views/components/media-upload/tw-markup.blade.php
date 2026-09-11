@php
    $user_type      = $userType ?? 'admin';
    $route_prefix   = is_null(tenant()) ? 'landlord.admin.upload.media.file' : 'tenant.admin.upload.media.file';
    $upload_route   = route($route_prefix);
    $all_route      = route($route_prefix . '.all');
    $loadmore_route = route($route_prefix . '.loadmore');
    $delete_route   = route($route_prefix . '.delete');
    $alt_route      = route($route_prefix . '.alt.change');
@endphp

{{-- Backdrop --}}
<div id="tw_media_modal_backdrop"
     class="hidden fixed inset-0 bg-black/50 z-[10000]"></div>

{{-- Modal --}}
<div id="tw_media_modal"
     class="hidden fixed inset-0 z-[10001] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="ti tabler-photo text-amber-500 text-base"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-800">{{ __('Upload Image') }}</h3>
            </div>
            <button type="button" id="tw_media_modal_close"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                <i class="ti tabler-x text-lg"></i>
            </button>
        </div>

        {{-- Tabs --}}
        <div class="flex border-b border-gray-100 px-6">
            <button type="button" id="tw_tab_upload"
                    class="tw-tab-btn py-3 px-1 mr-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition flex items-center gap-2"
                    data-tab="tw_upload_panel">
                <i class="ti tabler-cloud-upload text-base"></i>
                {{ __('Upload Files') }}
            </button>
            <button type="button" id="tw_tab_library"
                    class="tw-tab-btn py-3 px-1 mr-6 text-sm font-medium border-b-2 border-sectionC text-sectionC flex items-center gap-2"
                    data-tab="tw_library_panel">
                <i class="ti tabler-layout-grid text-base"></i>
                {{ __('Media Library') }}
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 min-h-0 flex flex-col">

            {{-- Upload Panel --}}
            <div id="tw_upload_panel" class="hidden flex-1 overflow-y-auto p-6">
                <label id="tw_dropzone" for="tw_file_input"
                       class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-sectionC hover:bg-gray-50 transition">
                    <i class="ti tabler-cloud-upload text-5xl text-gray-300 mb-3"></i>
                    <span class="text-sm font-medium text-gray-600">{{ __('Click or drag files to upload') }}</span>
                    <span class="text-xs text-gray-400 mt-1">PNG, JPG, GIF, WEBP · max 10MB</span>
                    <input id="tw_file_input" type="file" accept="image/*" multiple class="hidden" />
                </label>
                <div id="tw_upload_progress" class="hidden mt-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div id="tw_progress_bar" class="bg-primary h-2 rounded-full transition-all" style="width:0%"></div>
                        </div>
                        <span id="tw_progress_text" class="text-xs text-gray-500">0%</span>
                    </div>
                    <p id="tw_upload_status" class="text-xs text-gray-400 mt-1"></p>
                </div>
            </div>

            {{-- Library Panel --}}
            <div id="tw_library_panel" class="flex flex-1 min-h-0">

                {{-- Left: image grid --}}
                <div class="flex-1 overflow-y-auto p-6">
                    {{-- Preloader --}}
                    <div id="tw_library_preloader" class="flex items-center justify-center h-32">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sectionC"></div>
                    </div>

                    {{-- Image grid --}}
                    <ul id="tw_media_image_list"
                        class="hidden grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 mb-4">
                    </ul>

                    {{-- Load more --}}
                    <div id="tw_loadmore_wrap" class="hidden text-center mt-2 pb-2">
                        <button type="button" id="tw_loadmore_btn"
                                class="px-5 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            {{ __('Load more') }}
                        </button>
                    </div>
                </div>

                {{-- Right: detail panel --}}
                <div id="tw_detail_panel" class="hidden w-64 shrink-0 border-l border-gray-100 overflow-y-auto flex flex-col bg-white">

                    {{-- Preview --}}
                    <div class="p-4">
                        <div class="rounded-lg overflow-hidden bg-gray-50 border border-gray-100" style="aspect-ratio:4/3">
                            <img id="tw_detail_img" src="" alt=""
                                 class="w-full h-full object-cover" />
                        </div>
                    </div>

                    {{-- Meta --}}
                    <div class="px-4 pb-3 space-y-1">
                        <p id="tw_detail_title" class="text-sm font-semibold text-gray-800 break-all leading-snug"></p>
                        <p id="tw_detail_date" class="text-xs text-gray-400"></p>
                        <p id="tw_detail_dimensions" class="text-xs text-gray-500"></p>
                        <p id="tw_detail_size" class="text-xs text-gray-500"></p>
                        <p id="tw_detail_url" class="text-xs text-gray-400 break-all line-clamp-2 cursor-text select-all"></p>
                    </div>

                    {{-- Alt text --}}
                    <div class="px-4 pb-4">
                        <div class="flex items-center gap-2">
                            <input type="text" id="tw_detail_alt"
                                   placeholder="{{ __('Alt text...') }}"
                                   class="flex-1 text-xs px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-sectionC transition" />
                            <button type="button" id="tw_detail_alt_save"
                                    class="w-8 h-8 shrink-0 flex items-center justify-center rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-600 transition">
                                <i class="ti tabler-check text-sm"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Delete --}}
                    <div class="px-4 pb-5 mt-auto">
                        <button type="button" id="tw_detail_delete"
                                class="w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-red-500 border border-red-200 rounded-lg hover:bg-red-50 transition">
                            <i class="ti tabler-trash text-base"></i>
                            {{ __('Delete') }}
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
            <button type="button" id="tw_media_modal_cancel"
                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                {{ __('Cancel') }}
            </button>
            <button type="button" id="tw_media_modal_submit"
                    class="hidden px-5 py-2 text-sm font-medium text-white bg-sectionC rounded-lg hover:opacity-90 transition">
                {{ __('Select Image') }}
            </button>
        </div>
    </div>
</div>

<style>
.tw-mu-swal.swal2-container { z-index: 99999 !important; }
</style>

<script>
window._twMediaConfig = {
    uploadRoute  : "{{ $upload_route }}",
    allRoute     : "{{ $all_route }}",
    loadmoreRoute: "{{ $loadmore_route }}",
    deleteRoute  : "{{ $delete_route }}",
    altRoute     : "{{ $alt_route }}",
    userType     : "{{ $user_type }}",
    csrfToken    : "{{ csrf_token() }}"
};
</script>
