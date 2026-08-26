<div class="qa-section" id="qa-section">

    <div class="qa-header">
        <i class="mdi mdi-comment-question-outline qa-header-icon"></i>
        <span class="qa-header-title">{{ __('Customer Questions & Answers') }}</span>
        <span class="qa-count">{{ count($questions) }}</span>
    </div>

    @if(count($questions) > 0)
    <div class="qa-list">
        @foreach($questions as $q)
        <div class="qa-item">
            <div class="qa-question-row">
                <i class="mdi mdi-help-circle qa-q-icon"></i>
                <div class="qa-question-text">{{ $q->question }}</div>
            </div>
            <div class="qa-answer-row">
                <i class="mdi mdi-check-circle qa-a-icon"></i>
                <div class="qa-answer-text">{{ $q->answer }}</div>
            </div>
            <div class="qa-meta-row">
                <span class="qa-asker">{{ __('Asked by') }} {{ $q->name }}</span>
                <button type="button" class="qa-helpful-btn" data-id="{{ $q->id }}" data-url="{{ $helpfulUrl }}">
                    <i class="mdi mdi-thumb-up-outline"></i>
                    <span class="qa-helpful-count">{{ $q->helpful_count }}</span>
                    {{ __('Helpful') }}
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Ask a question form --}}
    @if(! $requireLogin || $isLoggedIn)
    <div class="qa-ask-wrap">
        <div class="qa-ask-title">{{ __('Ask a Question') }}</div>
        <div id="qa-submit-msg" class="qa-msg"></div>
        <div class="qa-form" id="qa-form">
            <input type="hidden" id="qa-product-id" value="{{ $product->id }}">
            @if($isLoggedIn && $user)
            <input type="hidden" id="qa-name"  value="{{ $user->name }}">
            <input type="hidden" id="qa-email" value="{{ $user->email }}">
            @else
            <div class="qa-field-row">
                <input type="text"  id="qa-name"  placeholder="{{ __('Your name') }}"  class="qa-input" required>
                <input type="email" id="qa-email" placeholder="{{ __('Your email') }}" class="qa-input" required>
            </div>
            @endif
            <textarea id="qa-question" rows="3" placeholder="{{ __('What would you like to know about this product?') }}" class="qa-textarea" maxlength="1000"></textarea>
            <button type="button" id="qa-submit-btn" class="qa-submit-btn">
                <i class="mdi mdi-send-outline"></i> {{ __('Submit Question') }}
            </button>
        </div>
    </div>
    @else
    <div class="qa-login-prompt">
        <i class="mdi mdi-account-lock-outline"></i>
        {{ __('Please log in to ask a question.') }}
    </div>
    @endif

</div>

<style>
.qa-section{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin:20px 0;font-family:inherit}
.qa-header{display:flex;align-items:center;gap:8px;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #f3f4f6}
.qa-header-icon{font-size:20px;color:#f59e0b}
.qa-header-title{font-size:15px;font-weight:700;color:#111827;flex:1}
.qa-count{background:#f3f4f6;color:#6b7280;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px}
.qa-list{display:flex;flex-direction:column;gap:0;margin-bottom:16px}
.qa-item{padding:14px 0;border-bottom:1px solid #f3f4f6}
.qa-item:last-child{border-bottom:none}
.qa-question-row{display:flex;gap:10px;margin-bottom:8px}
.qa-q-icon{font-size:16px;color:#f59e0b;flex-shrink:0;margin-top:2px}
.qa-question-text{font-size:14px;font-weight:600;color:#1f2937;line-height:1.5}
.qa-answer-row{display:flex;gap:10px;margin-bottom:6px;padding-left:2px}
.qa-a-icon{font-size:16px;color:#10b981;flex-shrink:0;margin-top:2px}
.qa-answer-text{font-size:13px;color:#374151;line-height:1.6}
.qa-meta-row{display:flex;align-items:center;gap:12px;margin-top:6px;padding-left:26px}
.qa-asker{font-size:11px;color:#9ca3af}
.qa-helpful-btn{display:inline-flex;align-items:center;gap:4px;font-size:11px;color:#6b7280;background:none;border:1px solid #e5e7eb;border-radius:20px;padding:2px 10px;cursor:pointer;transition:all .15s}
.qa-helpful-btn:hover{background:#f0fdf4;color:#16a34a;border-color:#bbf7d0}
.qa-helpful-btn.voted{background:#f0fdf4;color:#16a34a;border-color:#bbf7d0}
.qa-ask-wrap{background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px}
.qa-ask-title{font-size:13px;font-weight:700;color:#92400e;margin-bottom:10px}
.qa-field-row{display:flex;gap:8px;margin-bottom:8px}
.qa-input{flex:1;border:1px solid #d1d5db;border-radius:8px;padding:7px 10px;font-size:13px;color:#374151;outline:none}
.qa-input:focus{border-color:#f59e0b;box-shadow:0 0 0 2px rgba(245,158,11,.15)}
.qa-textarea{width:100%;border:1px solid #d1d5db;border-radius:8px;padding:7px 10px;font-size:13px;color:#374151;outline:none;resize:vertical;box-sizing:border-box;margin-bottom:8px}
.qa-textarea:focus{border-color:#f59e0b;box-shadow:0 0 0 2px rgba(245,158,11,.15)}
.qa-submit-btn{display:inline-flex;align-items:center;gap:6px;background:#f59e0b;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s}
.qa-submit-btn:hover{background:#d97706}
.qa-msg{font-size:12px;margin-bottom:8px;min-height:16px}
.qa-msg.success{color:#16a34a}
.qa-msg.error{color:#dc2626}
.qa-login-prompt{text-align:center;font-size:13px;color:#9ca3af;padding:12px 0}
</style>

<script>
(function () {
    var SUBMIT_URL  = '{{ $submitUrl }}';
    var HELPFUL_URL = '{{ $helpfulUrl }}';
    var CSRF        = '{{ csrf_token() }}';

    var submitBtn = document.getElementById('qa-submit-btn');
    var msg       = document.getElementById('qa-submit-msg');

    if (submitBtn) {
        submitBtn.addEventListener('click', function () {
            var productId = document.getElementById('qa-product-id').value;
            var name      = (document.getElementById('qa-name')  || {value: ''}).value.trim();
            var email     = (document.getElementById('qa-email') || {value: ''}).value.trim();
            var question  = document.getElementById('qa-question').value.trim();

            if (! name || ! email || ! question) {
                msg.textContent = '{{ __("Please fill in all fields.") }}';
                msg.className = 'qa-msg error';
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i>';

            fetch(SUBMIT_URL, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
                body: JSON.stringify({product_id: productId, name: name, email: email, question: question})
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.status === 'success') {
                    msg.textContent = data.msg;
                    msg.className = 'qa-msg success';
                    document.getElementById('qa-question').value = '';
                } else {
                    msg.textContent = data.msg || '{{ __("Error. Please try again.") }}';
                    msg.className = 'qa-msg error';
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="mdi mdi-send-outline"></i> {{ __("Submit Question") }}';
            })
            .catch(function () {
                msg.textContent = '{{ __("Something went wrong.") }}';
                msg.className = 'qa-msg error';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="mdi mdi-send-outline"></i> {{ __("Submit Question") }}';
            });
        });
    }

    document.querySelectorAll('.qa-helpful-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (btn.classList.contains('voted')) return;
            var id = btn.dataset.id;
            fetch(HELPFUL_URL, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
                body: JSON.stringify({id: id})
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.status === 'success') {
                    btn.querySelector('.qa-helpful-count').textContent = data.count;
                    btn.classList.add('voted');
                }
            });
        });
    });
}());
</script>
