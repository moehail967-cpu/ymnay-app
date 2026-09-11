<details class="cm-filter-panel mb-5">
    <summary class="cm-filter-header">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="las la-filter text-primary text-sm"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-dark">{{__('Search & Filter')}}</h4>
                <p class="text-xs text-muted">{{__('Filter commission records')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('landlord.admin.commission.history') }}"
               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-muted border border-main hover:bg-muted transition">
                <i class="las la-redo-alt text-sm"></i> {{__('Reset')}}
            </a>
            <i class="las la-angle-down text-muted cm-filter-toggle-icon"></i>
        </div>
    </summary>

    <div class="cm-filter-body">
        <form method="GET" action="{{ route('landlord.admin.commission.history') }}">
            <input type="hidden" name="duration" id="duration-input" value="{{ request('duration') ?? '30' }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="lnd-label">{{__('Payment Status')}}</label>
                    <select name="payment_status" class="lnd-input">
                        <option value="">{{__('All Status')}}</option>
                        <option value="success" {{ request('payment_status') == 'success' ? 'selected' : '' }}>{{__('Success')}}</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>{{__('Pending')}}</option>
                        <option value="cancel" {{ request('payment_status') == 'cancel' ? 'selected' : '' }}>{{__('Cancel')}}</option>
                    </select>
                </div>
                <div>
                    <label class="lnd-label">{{__('Status')}}</label>
                    <select name="status" class="lnd-input">
                        <option value="">{{__('All')}}</option>
                        <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>{{__('Complete')}}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{__('Pending')}}</option>
                    </select>
                </div>
                <div>
                    <label class="lnd-label">{{__('Tenant ID')}}</label>
                    <input name="tenant_id" type="text" class="lnd-input" value="{{ request('tenant_id') }}" placeholder="{{__('Search by Tenant ID')}}">
                </div>
                <div>
                    <label class="lnd-label">{{__('Order UID')}}</label>
                    <input name="order_id" type="text" class="lnd-input" value="{{ request('order_id') }}" placeholder="{{__('Search by Order ID')}}">
                </div>
                <div>
                    <label class="lnd-label">{{__('Date From')}}</label>
                    <input type="date" name="date_from" class="lnd-input" value="{{ request('date_from') }}">
                </div>
                <div>
                    <label class="lnd-label">{{__('Date To')}}</label>
                    <input type="date" name="date_to" class="lnd-input" value="{{ request('date_to') }}">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-search text-sm"></i> {{__('Apply Filters')}}
                </button>
            </div>
        </form>
    </div>
</details>
