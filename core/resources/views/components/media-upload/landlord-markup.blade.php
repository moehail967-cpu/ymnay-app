@php
    $user_type      = $userType ?? 'admin';
    $upload_route   = route('landlord.admin.upload.media.file');
    $all_route      = route('landlord.admin.upload.media.file.all');
    $loadmore_route = route('landlord.admin.upload.media.file.loadmore');
@endphp

{{-- Backdrop --}}
<div id="tw_media_modal_backdrop"
     class="hidden fixed inset-0 bg-black/50 z-[900]"></div>

{{-- Modal --}}
<div id="tw_media_modal"
     class="hidden fixed inset-0 z-[901] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 id="tw_media_modal_title" class="text-base font-semibold text-gray-800">{{ __('Media Uploads') }}</h3>
            <button type="button" id="tw_media_modal_close"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                <i class="ti tabler-x text-lg"></i>
            </button>
        </div>

        {{-- Tabs --}}
        <div class="flex border-b border-gray-100 px-6">
            <button type="button" id="tw_tab_upload"
                    class="tw-tab-btn py-3 px-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition"
                    data-tab="tw_upload_panel">
                {{ __('Upload Files') }}
            </button>
            <button type="button" id="tw_tab_library"
                    class="tw-tab-btn py-3 px-4 text-sm font-medium border-b-2 border-sectionC text-sectionC"
                    data-tab="tw_library_panel">
                {{ __('Media Library') }}
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto">

            {{-- Upload Panel --}}
            <div id="tw_upload_panel" class="hidden p-6">
                <label for="tw_file_input"
                       class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-sectionC hover:bg-gray-50 transition">
                    <i class="ti tabler-cloud-upload text-4xl text-gray-400 mb-2"></i>
                    <span class="text-sm text-gray-500">{{ __('Click or drag an image here to upload') }}</span>
                    <span class="text-xs text-gray-400 mt-1">PNG, JPG, GIF · max 10MB</span>
                    <input id="tw_file_input" type="file" accept="image/*" class="hidden" />
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
            <div id="tw_library_panel" class="p-6">
                {{-- Preloader --}}
                <div id="tw_library_preloader" class="flex items-center justify-center h-32">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sectionC"></div>
                </div>

                {{-- Image grid --}}
                <ul id="tw_media_image_list"
                    class="hidden grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 mb-4">
                </ul>

                {{-- Load more --}}
                <div id="tw_loadmore_wrap" class="hidden text-center mt-2">
                    <button type="button" id="tw_loadmore_btn"
                            class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        {{ __('Load More') }}
                    </button>
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
                    class="hidden px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:opacity-90 transition">
                {{ __('Set Image') }}
            </button>
        </div>
    </div>
</div>

<script>
window._twMediaConfig = {
    uploadRoute  : "{{ $upload_route }}",
    allRoute     : "{{ $all_route }}",
    loadmoreRoute: "{{ $loadmore_route }}",
    userType     : "{{ $user_type }}",
    csrfToken    : "{{ csrf_token() }}"
};
</script>
