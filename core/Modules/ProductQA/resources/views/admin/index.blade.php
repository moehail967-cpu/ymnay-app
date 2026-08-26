@extends('tenant.admin.admin-master')

@section('title') {{ __('Product Q&A') }} @endsection
@section('page-title') {{ __('Product Q&A') }} @endsection

@section('content')
<div class="col-12">

    <x-msg.flash />
    <x-msg.error />

    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-base font-bold text-dark">{{ __('Customer Questions') }}</h2>
            <p class="text-xs text-muted mt-0.5">{{ __('Review, answer, or reject questions submitted on product pages.') }}</p>
        </div>
        <div class="flex items-center gap-2">
            @if($pendingCount > 0)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                    {{ $pendingCount }} {{ __('pending') }}
                </span>
            @endif
            <a href="{{ route('tenant.admin.product-qa.settings') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                <i class="mdi mdi-cog-outline text-base"></i>
                {{ __('Settings') }}
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-comment-question-outline text-amber-500 text-base"></i>
            </div>
            <h3 class="text-sm font-bold text-dark">{{ __('All Questions') }}</h3>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($questions as $q)
                @php
                    $badge = match($q->status) {
                        'answered' => 'text-green-700 bg-green-100',
                        'pending'  => 'text-amber-700 bg-amber-100',
                        'rejected' => 'text-gray-600 bg-gray-100',
                        default    => 'text-gray-600 bg-gray-100',
                    };
                @endphp
                <div class="px-5 py-4" id="q-{{ $q->id }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="text-xs font-semibold text-gray-500">{{ $q->name }}</span>
                                <span class="text-gray-300 text-xs">·</span>
                                <span class="text-xs text-gray-400">{{ $productNames[$q->product_id] ?? '—' }}</span>
                                <span class="text-gray-300 text-xs">·</span>
                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($q->created_at)->format('M d, Y') }}</span>
                            </div>
                            <p class="text-sm font-medium text-dark mb-2">{{ $q->question }}</p>

                            @if($q->status === 'answered' && $q->answer)
                                <div class="bg-green-50 border border-green-100 rounded-xl px-4 py-3 mb-2">
                                    <div class="text-xs font-bold text-green-700 mb-1">{{ __('Answer') }}</div>
                                    <p class="text-sm text-gray-700">{{ $q->answer }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $badge }}">
                                {{ ucfirst($q->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-3">
                        @if($q->status === 'pending')
                            <button type="button"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 text-xs font-semibold hover:bg-green-100 transition qa-answer-btn"
                                    data-id="{{ $q->id }}"
                                    data-question="{{ $q->question }}">
                                <i class="mdi mdi-reply-outline text-sm"></i>
                                {{ __('Answer') }}
                            </button>
                            <button type="button"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 text-xs font-semibold hover:bg-gray-200 transition qa-action-btn"
                                    data-route="{{ route('tenant.admin.product-qa.reject', $q->id) }}"
                                    data-confirm="{{ __('Reject this question?') }}">
                                <i class="mdi mdi-close text-sm"></i>
                                {{ __('Reject') }}
                            </button>
                        @elseif($q->status === 'answered')
                            <button type="button"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold hover:bg-blue-100 transition qa-answer-btn"
                                    data-id="{{ $q->id }}"
                                    data-question="{{ $q->question }}"
                                    data-answer="{{ $q->answer }}">
                                <i class="mdi mdi-pencil-outline text-sm"></i>
                                {{ __('Edit Answer') }}
                            </button>
                        @endif
                        <button type="button"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-semibold hover:bg-red-100 transition qa-action-btn"
                                data-route="{{ route('tenant.admin.product-qa.destroy', $q->id) }}"
                                data-confirm="{{ __('Permanently delete this question?') }}"
                                data-reload="true">
                            <i class="mdi mdi-trash-can-outline text-sm"></i>
                            {{ __('Delete') }}
                        </button>
                        @if($q->helpful_count > 0)
                            <span class="text-xs text-gray-400 ml-2">
                                <i class="mdi mdi-thumb-up-outline text-xs"></i> {{ $q->helpful_count }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-gray-400">
                    <i class="mdi mdi-comment-question-outline text-3xl block mb-2 text-gray-300"></i>
                    {{ __('No questions yet.') }}
                </div>
            @endforelse
        </div>

        @if($questions->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $questions->links() }}
        </div>
        @endif
    </div>

</div>

{{-- Answer Modal --}}
<div id="qa-answer-modal" class="fixed inset-0 z-[800] hidden items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-dark">{{ __('Answer Question') }}</h3>
            <button type="button" id="qa-modal-close" class="text-gray-400 hover:text-gray-600">
                <i class="mdi mdi-close text-xl"></i>
            </button>
        </div>
        <p class="text-sm text-gray-600 bg-gray-50 rounded-xl px-4 py-3 mb-4" id="qa-modal-question"></p>
        <textarea id="qa-modal-answer" rows="4" placeholder="{{ __('Type your answer here…') }}"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
        <div class="flex justify-end gap-2 mt-4">
            <button type="button" id="qa-modal-close2"
                    class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200 transition">
                {{ __('Cancel') }}
            </button>
            <button type="button" id="qa-modal-submit"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition">
                <i class="mdi mdi-send-outline"></i>
                {{ __('Publish Answer') }}
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var modal     = document.getElementById('qa-answer-modal');
    var qText     = document.getElementById('qa-modal-question');
    var aText     = document.getElementById('qa-modal-answer');
    var submitBtn = document.getElementById('qa-modal-submit');
    var currentId = null;

    function openModal(id, question, answer) {
        currentId = id;
        qText.textContent = question;
        aText.value = answer || '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentId = null;
    }

    document.querySelectorAll('.qa-answer-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openModal(this.dataset.id, this.dataset.question, this.dataset.answer || '');
        });
    });

    [document.getElementById('qa-modal-close'), document.getElementById('qa-modal-close2')].forEach(function (el) {
        el.addEventListener('click', closeModal);
    });

    submitBtn.addEventListener('click', function () {
        var answer = aText.value.trim();
        if (! answer) { toastr.warning('{{ __("Please enter an answer.") }}'); return; }

        $.post('{{ url(route_prefix("admin") . "/product-qa/") }}/' + currentId + '/answer', {
            _token: '{{ csrf_token() }}',
            answer: answer
        }, function (res) {
            if (res.success) {
                toastr.success('{{ __("Answer published.") }}');
                closeModal();
                setTimeout(function () { location.reload(); }, 800);
            }
        }).fail(function () {
            toastr.error('{{ __("Something went wrong.") }}');
        });
    });

    document.querySelectorAll('.qa-action-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var route   = this.dataset.route;
            var confirm = this.dataset.confirm;
            var reload  = this.dataset.reload;

            Swal.fire({
                title: confirm,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '{{ __("Yes") }}',
                cancelButtonText: '{{ __("Cancel") }}'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.post(route, {_token: '{{ csrf_token() }}'}, function () {
                        if (reload) { location.reload(); }
                        else { location.reload(); }
                    });
                }
            });
        });
    });
}());
</script>
@endsection
