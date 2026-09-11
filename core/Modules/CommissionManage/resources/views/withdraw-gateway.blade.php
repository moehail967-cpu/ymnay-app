@extends(route_prefix().'admin.admin-master')
@section('title')
    {{__('Withdraw Gateway Setting')}}
@endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<form action="{{ route('admin.withdraw.gateways.store') }}" method="POST">
    @csrf

    <div class="bg-surface rounded-xl shadow-main border border-main mb-6" id="withdrawGatewayWrapper">
        {{-- Card Header --}}
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="las la-wallet text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Withdraw Payment Gateways')}}</h3>
                    <p class="text-xs text-muted">{{__('Configure withdraw methods for tenants')}}</p>
                </div>
            </div>
            <button type="button" onclick="addWithdrawGateway()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                <i class="las la-plus text-base"></i> {{__('Add Gateway')}}
            </button>
        </div>

        {{-- Gateway Container --}}
        <div class="px-4 sm:px-6 py-5" id="withdrawGatewaysContainer">
            {{-- Dynamic gateways will be added here --}}
        </div>

        <input type="hidden" name="withdraw_gateways" id="withdraw_gateways_data" value="">
    </div>

    {{-- Submit --}}
    <div class="flex">
        <button type="submit"
                class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition" id="submitBtn">
            <i class="las la-save"></i>
            <span id="btnText">{{__('Save Settings')}}</span>
        </button>
    </div>
</form>

@endsection

@section('scripts')
    <script>
        // Initialize with existing data from backend
        window.preselectedWithdrawGateways = @json($withdraw_gateways_json ?? '[]');

        let gatewayCounter = 0;
        let withdrawGatewaysData = [];

        document.addEventListener('DOMContentLoaded', function() {
            const existingData = typeof window.preselectedWithdrawGateways === 'string'
                ? JSON.parse(window.preselectedWithdrawGateways)
                : window.preselectedWithdrawGateways;

            if (Array.isArray(existingData) && existingData.length > 0) {
                withdrawGatewaysData = existingData;
                existingData.forEach(gateway => addWithdrawGateway(gateway));
            }

            updateEmptyState();
        });

        /* ── Empty State ──────────────────────────────────── */
        function updateEmptyState() {
            const container = document.getElementById('withdrawGatewaysContainer');
            const existingMsg = document.getElementById('emptyGatewaysMessage');

            if (container.querySelectorAll('.cm-gateway-card').length === 0) {
                if (!existingMsg) {
                    container.innerHTML = `
                    <div id="emptyGatewaysMessage" class="text-center py-8 text-muted">
                        <i class="las la-wallet text-3xl block mb-2 opacity-40"></i>
                        <span class="text-sm">{{ __('No withdraw gateways added yet.') }}</span><br>
                        <span class="text-xs">{{ __('Click') }} <strong>"{{ __('Add Gateway') }}"</strong> {{ __('to create one.') }}</span>
                    </div>`;
                }
            } else {
                if (existingMsg) existingMsg.remove();
            }
        }

        /* ── Add Gateway ──────────────────────────────────── */
        function addWithdrawGateway(existingData = null) {
            const emptyMsg = document.getElementById('emptyGatewaysMessage');
            if (emptyMsg) emptyMsg.remove();

            const gatewayId = existingData?.id || `gateway_${Date.now()}_${gatewayCounter++}`;
            const gatewayName = existingData?.name || '';
            const fields = existingData?.fields || [];

            const gatewayHTML = `
        <div class="cm-gateway-card" id="${gatewayId}" data-gateway-id="${gatewayId}">
            <div class="cm-gateway-header">
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <i class="las la-grip-vertical text-muted cursor-move"></i>
                    <input type="text" class="lnd-input gateway-name-input"
                           placeholder="{{ __('Gateway Name (e.g., bKash, Nagad, Bank)') }}"
                           value="${gatewayName}"
                           onchange="updateGatewayName('${gatewayId}', this.value)"
                           style="max-width: 300px;">
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <button type="button"
                            class="tw-btn-icon" style="color:#0d9488;background:#f0fdfa;border-color:#99f6e4;"
                            onclick="addCustomField('${gatewayId}')" title="{{ __('Add Field') }}">
                        <i class="las la-plus text-sm"></i>
                    </button>
                    <button type="button"
                            class="tw-btn-icon tw-btn-icon-danger"
                            onclick="removeGateway('${gatewayId}')" title="{{ __('Remove Gateway') }}">
                        <i class="las la-trash text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="cm-gateway-body">
                <div class="custom-fields-container" id="fields_${gatewayId}">
                    ${
                fields.length === 0
                    ? '<p class="text-center text-xs text-muted py-3"><i class="las la-info-circle mr-1"></i>{{ __('No custom fields added yet. Click "+" to create input fields.') }}</p>'
                    : ''
            }
                </div>
            </div>
        </div>`;

            document.getElementById('withdrawGatewaysContainer').insertAdjacentHTML('beforeend', gatewayHTML);

            fields.forEach(field => addCustomField(gatewayId, field));
            updateWithdrawGatewaysData();
        }

        /* ── Add Custom Field ─────────────────────────────── */
        function addCustomField(gatewayId, existingField = null) {
            const container = document.getElementById(`fields_${gatewayId}`);
            const noFieldsMsg = container.querySelector('p.text-muted');
            if (noFieldsMsg) noFieldsMsg.remove();

            const fieldId = existingField?.id || `field_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
            const fieldLabel = existingField?.label || '';
            const fieldType = existingField?.type || 'text';
            const fieldRequired = existingField?.required || false;
            const fieldPlaceholder = existingField?.placeholder || '';

            const fieldHTML = `
        <div class="cm-field-item" id="${fieldId}" data-field-id="${fieldId}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                <div class="lg:col-span-3">
                    <label class="text-xs font-semibold text-muted block mb-1">{{ __('Field Label') }}</label>
                    <input type="text" class="lnd-input text-sm"
                           value="${fieldLabel}"
                           placeholder="{{ __('e.g., Account Number') }}"
                           onchange="updateFieldData('${gatewayId}', '${fieldId}', 'label', this.value)">
                </div>
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold text-muted block mb-1">{{ __('Type') }}</label>
                    <select class="lnd-input text-sm"
                            onchange="updateFieldData('${gatewayId}', '${fieldId}', 'type', this.value)">
                        <option value="text" ${fieldType === 'text' ? 'selected' : ''}>Text</option>
                        <option value="email" ${fieldType === 'email' ? 'selected' : ''}>Email</option>
                        <option value="number" ${fieldType === 'number' ? 'selected' : ''}>Number</option>
                        <option value="date" ${fieldType === 'date' ? 'selected' : ''}>Date</option>
                        <option value="tel" ${fieldType === 'tel' ? 'selected' : ''}>Phone</option>
                        <option value="textarea" ${fieldType === 'textarea' ? 'selected' : ''}>Textarea</option>
                    </select>
                </div>
                <div class="lg:col-span-3">
                    <label class="text-xs font-semibold text-muted block mb-1">{{ __('Placeholder') }}</label>
                    <input type="text" class="lnd-input text-sm"
                           value="${fieldPlaceholder}"
                           placeholder="{{ __('e.g., Enter your account number') }}"
                           onchange="updateFieldData('${gatewayId}', '${fieldId}', 'placeholder', this.value)">
                </div>
                <div class="lg:col-span-2 flex items-center gap-2 pt-4 lg:pt-0">
                    <input type="checkbox" class="accent-[var(--color-primary)] w-4 h-4 cursor-pointer" ${fieldRequired ? 'checked' : ''}
                           onchange="updateFieldData('${gatewayId}', '${fieldId}', 'required', this.checked)">
                    <label class="text-xs font-semibold text-muted">{{ __('Required') }}</label>
                </div>
                <div class="lg:col-span-2 flex items-center justify-end gap-1.5">
                    <button type="button"
                            class="tw-btn-icon" style="color:#0d9488;background:#f0fdfa;border-color:#99f6e4;"
                            onclick="addCustomField('${gatewayId}')" title="{{ __('Add Field') }}">
                        <i class="las la-plus text-sm"></i>
                    </button>
                    <button type="button"
                            class="tw-btn-icon tw-btn-icon-danger"
                            onclick="removeField('${gatewayId}', '${fieldId}')" title="{{ __('Remove Field') }}">
                        <i class="las la-trash text-sm"></i>
                    </button>
                </div>
            </div>
        </div>`;

            container.insertAdjacentHTML('beforeend', fieldHTML);
            updateWithdrawGatewaysData();
        }

        /* ── Remove Field ─────────────────────────────────── */
        function removeField(gatewayId, fieldId) {
            Swal.fire({
                title: '{{ __('Remove this field?') }}',
                text: "{{ __('This field will be deleted permanently.') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#dc2626',
                confirmButtonText: '{{ __('Yes, remove') }}'
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById(fieldId).remove();

                    const container = document.getElementById(`fields_${gatewayId}`);
                    if (container.children.length === 0) {
                        container.innerHTML = `
                        <p class="text-center text-xs text-muted py-3">
                            <i class="las la-info-circle mr-1"></i>{{ __('No custom fields added yet. Click "+" to create input fields.') }}
                        </p>`;
                    }

                    updateWithdrawGatewaysData();
                    toastr.success("{{ __('Field removed successfully') }}");
                }
            });
        }

        /* ── Remove Gateway ───────────────────────────────── */
        function removeGateway(gatewayId) {
            Swal.fire({
                title: '{{ __('Remove this gateway?') }}',
                text: "{{ __('This will delete the gateway and all its fields.') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#dc2626',
                confirmButtonText: '{{ __('Yes, remove') }}'
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById(gatewayId).remove();
                    updateWithdrawGatewaysData();
                    updateEmptyState();
                    toastr.success("{{ __('Gateway removed successfully') }}");
                }
            });
        }

        function updateGatewayName() { updateWithdrawGatewaysData(); }
        function updateFieldData() { updateWithdrawGatewaysData(); }

        function updateWithdrawGatewaysData() {
            const gateways = [];
            const cards = document.querySelectorAll('.cm-gateway-card');

            cards.forEach(card => {
                const gatewayId = card.dataset.gatewayId;
                const gatewayName = card.querySelector('.gateway-name-input').value.trim();

                if (!gatewayName) return;

                const fields = [];
                const fieldItems = card.querySelectorAll('.cm-field-item');

                fieldItems.forEach(item => {
                    const inputs = item.querySelectorAll('input, select');

                    const field = {
                        id: item.dataset.fieldId,
                        label: inputs[0].value.trim(),
                        type: inputs[1].value,
                        placeholder: inputs[2].value.trim(),
                        required: inputs[3].checked
                    };

                    if (field.label) fields.push(field);
                });

                gateways.push({ id: gatewayId, name: gatewayName, fields });
            });

            withdrawGatewaysData = gateways;
            document.getElementById('withdraw_gateways_data').value = JSON.stringify(gateways);
        }

        /* ── Session Messages ─────────────────────────────── */
        @if(session('success'))
        toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
        toastr.error("{{ session('error') }}");
        @endif
    </script>
@endsection
