@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Commission & Withdraw Management')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Page Header with Tabs + Duration --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-5">
    <div class="px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">
        {{-- Tabs --}}
        <nav class="cm-tab-nav">
            <a href="{{ route('landlord.admin.commission.history', ['tab' => 'commission']) }}"
               class="cm-tab-link {{ $tab === 'commission' ? 'active' : '' }}">
                <i class="las la-coins"></i> {{__('Commission History')}}
            </a>
            <a href="{{ route('landlord.admin.commission.history', ['tab' => 'withdraw']) }}"
               class="cm-tab-link {{ $tab === 'withdraw' ? 'active' : '' }}">
                <i class="las la-money-bill-wave"></i> {{__('Withdraw Requests')}}
            </a>
        </nav>

        {{-- Duration Filter --}}
        <div class="flex items-center gap-2">
            <label for="duration-filter" class="text-xs font-semibold text-muted whitespace-nowrap">{{__('Duration:')}}</label>
            <select id="duration-filter" class="cm-duration-select" onchange="filterByDuration(this.value)">
                <option value="30" {{ request('duration') == '30' || !request('duration') ? 'selected' : '' }}>{{__('Last 30 Days')}}</option>
                <option value="60" {{ request('duration') == '60' ? 'selected' : '' }}>{{__('Last 60 Days')}}</option>
                <option value="90" {{ request('duration') == '90' ? 'selected' : '' }}>{{__('Last 90 Days')}}</option>
                <option value="120" {{ request('duration') == '120' ? 'selected' : '' }}>{{__('Last 120 Days')}}</option>
            </select>
        </div>
    </div>
</div>

{{-- Tab Content: Stats + Filters + Table --}}
@if($tab === 'commission')
    @include('commissionmanage::partials.commission-stats')
    @include('commissionmanage::partials.commission-filters')
    @include('commissionmanage::partials.commission-table')
@else
    @include('commissionmanage::partials.withdraw-stats')
    @include('commissionmanage::partials.withdraw-filters')
    @include('commissionmanage::partials.withdraw-table')
@endif

{{-- Commission Details Modal --}}
<div class="cm-modal-backdrop" id="detailsBackdrop"></div>
<div class="cm-modal" id="detailsModal">
    <div class="cm-modal-dialog">
        <div class="cm-modal-header">
            <h5 class="cm-modal-title">
                <i class="las la-info-circle text-primary"></i> {{__('Commission Details')}}
            </h5>
            <button type="button" class="cm-modal-close" onclick="closeModal('details')">
                <i class="las la-times"></i>
            </button>
        </div>
        <div class="cm-modal-body" id="detailsContent">
            <div class="flex items-center justify-center py-8">
                <div class="cm-spinner"></div>
            </div>
        </div>
    </div>
</div>

{{-- Withdraw Details Modal --}}
<div class="cm-modal-backdrop" id="withdrawDetailsBackdrop"></div>
<div class="cm-modal" id="withdrawDetailsModal">
    <div class="cm-modal-dialog">
        <div class="cm-modal-header">
            <h5 class="cm-modal-title">
                <i class="las la-info-circle text-primary"></i> {{__('Withdraw Request Details')}}
            </h5>
            <button type="button" class="cm-modal-close" onclick="closeModal('withdrawDetails')">
                <i class="las la-times"></i>
            </button>
        </div>
        <div class="cm-modal-body" id="withdrawDetailsContent">
            <div class="flex items-center justify-center py-8">
                <div class="cm-spinner"></div>
            </div>
        </div>
    </div>
</div>

{{-- Approve Withdraw Modal --}}
<div class="cm-modal-backdrop" id="approveBackdrop"></div>
<div class="cm-modal" id="approveModal">
    <div class="cm-modal-dialog">
        <div class="cm-modal-header">
            <h5 class="cm-modal-title">
                <i class="las la-check-circle text-success"></i> {{__('Approve Withdraw Request')}}
            </h5>
            <button type="button" class="cm-modal-close" onclick="closeModal('approve')">
                <i class="las la-times"></i>
            </button>
        </div>
        <form id="approveForm">
            <div class="cm-modal-body">
                <div class="mb-4">
                    <label class="lnd-label">{{__('Transaction ID')}}</label>
                    <input type="text" name="transaction_id" class="lnd-input" placeholder="{{__('Enter transaction ID')}}">
                </div>
                <div>
                    <label class="lnd-label">{{__('Note (Optional)')}}</label>
                    <textarea name="note" class="lnd-input" rows="3" placeholder="{{__('Add any notes')}}"></textarea>
                </div>
            </div>
            <div class="cm-modal-footer">
                <button type="button" onclick="closeModal('approve')"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-muted border border-main hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-success hover:opacity-90 transition">
                    {{__('Approve')}}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Withdraw Modal --}}
<div class="cm-modal-backdrop" id="rejectBackdrop"></div>
<div class="cm-modal" id="rejectModal">
    <div class="cm-modal-dialog">
        <div class="cm-modal-header">
            <h5 class="cm-modal-title">
                <i class="las la-times-circle text-danger"></i> {{__('Reject Withdraw Request')}}
            </h5>
            <button type="button" class="cm-modal-close" onclick="closeModal('reject')">
                <i class="las la-times"></i>
            </button>
        </div>
        <form id="rejectForm">
            <div class="cm-modal-body">
                <div>
                    <label class="lnd-label">{{__('Reason')}} <span class="text-danger">*</span></label>
                    <textarea name="note" class="lnd-input" rows="3" placeholder="{{__('Enter reason for rejection')}}" required></textarea>
                </div>
            </div>
            <div class="cm-modal-footer">
                <button type="button" onclick="closeModal('reject')"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-muted border border-main hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-danger hover:opacity-90 transition">
                    {{__('Reject')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <script>
        const baseUrl = "{{ route(route_prefix().'admin.commission.history') }}";
        const currentTab = "{{ $tab }}";
        let currentWithdrawId = null;

        /* ── Modal helpers ──────────────────────────────────── */
        function openModal(name) {
            document.getElementById(name + 'Backdrop').classList.add('active');
            document.getElementById(name + 'Modal').classList.add('active');
        }

        function closeModal(name) {
            document.getElementById(name + 'Backdrop').classList.remove('active');
            document.getElementById(name + 'Modal').classList.remove('active');
        }

        // Close modal on backdrop click
        document.querySelectorAll('.cm-modal-backdrop').forEach(function(backdrop) {
            backdrop.addEventListener('click', function() {
                var id = this.id.replace('Backdrop', '');
                closeModal(id);
            });
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.cm-modal.active').forEach(function(modal) {
                    var id = modal.id.replace('Modal', '');
                    closeModal(id);
                });
            }
        });

        $(document).ready(function(){
            @if(session('success'))
            toastr.success("{{ session('success') }}");
            @endif

            @if(session('error'))
            toastr.error("{{ session('error') }}");
            @endif

            // Approve Form Submit
            $('#approveForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                approveWithdraw(currentWithdrawId, formData);
            });

            // Reject Form Submit
            $('#rejectForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                rejectWithdraw(currentWithdrawId, formData);
            });
        });

        function filterByDuration(days) {
            const url = new URL(window.location.href);
            url.searchParams.set('duration', days);
            url.searchParams.set('tab', currentTab);
            window.location.href = url.toString();
        }

        // Commission Functions
        function viewDetails(id) {
            document.getElementById('detailsContent').innerHTML = '<div class="flex items-center justify-center py-8"><div class="cm-spinner"></div></div>';
            openModal('details');

            axios.get(`${baseUrl}/${id}`)
                .then(response => {
                    document.getElementById('detailsContent').innerHTML = response.data.html;
                })
                .catch(error => {
                    document.getElementById('detailsContent').innerHTML =
                        '<div class="text-center py-6 text-danger text-sm"><i class="las la-exclamation-triangle text-xl block mb-1"></i>Failed to load details</div>';
                });
        }

        function markAsPaid(id) {
            Swal.fire({
                title: 'Mark as Paid?',
                text: "This will update the payment status to paid",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#dc2626',
                confirmButtonText: 'Yes, mark as paid!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(`${baseUrl}/${id}/mark-paid`, {
                        _token: '{{ csrf_token() }}'
                    })
                        .then(response => {
                            Swal.fire('Updated!', 'Commission marked as paid.', 'success');
                            setTimeout(() => location.reload(), 1500);
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'Failed to update status.', 'error');
                        });
                }
            });
        }

        // Withdraw Functions
        function viewWithdrawDetails(id) {
            document.getElementById('withdrawDetailsContent').innerHTML = '<div class="flex items-center justify-center py-8"><div class="cm-spinner"></div></div>';
            openModal('withdrawDetails');

            axios.get(`${baseUrl}/withdraw/show/${id}`)
                .then(response => {
                    document.getElementById('withdrawDetailsContent').innerHTML = response.data.html;
                })
                .catch(error => {
                    document.getElementById('withdrawDetailsContent').innerHTML =
                        '<div class="text-center py-6 text-danger text-sm"><i class="las la-exclamation-triangle text-xl block mb-1"></i>Failed to load details</div>';
                });
        }

        function showApproveModal(id) {
            currentWithdrawId = id;
            document.getElementById('approveForm').reset();
            openModal('approve');
        }

        function showRejectModal(id) {
            currentWithdrawId = id;
            document.getElementById('rejectForm').reset();
            openModal('reject');
        }

        function approveWithdraw(id, formData) {
            const data = {
                _token: '{{ csrf_token() }}',
                transaction_id: formData.get('transaction_id'),
                note: formData.get('note')
            };

            axios.post(`${baseUrl}/withdraw/approve/${id}`, data)
                .then(response => {
                    closeModal('approve');
                    Swal.fire('Approved!', response.data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                })
                .catch(error => {
                    Swal.fire('Error!', error.response?.data?.message || 'Failed to approve', 'error');
                });
        }

        function rejectWithdraw(id, formData) {
            const data = {
                _token: '{{ csrf_token() }}',
                note: formData.get('note')
            };

            axios.post(`${baseUrl}/withdraw/${id}/reject`, data)
                .then(response => {
                    closeModal('reject');
                    Swal.fire('Rejected!', response.data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                })
                .catch(error => {
                    Swal.fire('Error!', error.response?.data?.message || 'Failed to reject', 'error');
                });
        }

        function exportData() {
            const filters = new URLSearchParams(window.location.search);
            if (currentTab === 'withdraw') {
                window.location.href = `${baseUrl}/withdraw/export?${filters.toString()}`;
            } else {
                window.location.href = `${baseUrl}/export?${filters.toString()}`;
            }
        }

        function deleteCommission(id) {
            Swal.fire({
                title: 'Delete Commission Record?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#dc2626',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`${baseUrl}/${id}/delete`, {
                        data: { _token: '{{ csrf_token() }}' }
                    })
                        .then(response => {
                            Swal.fire('Deleted!', "{{ __('Commission record deleted.') }}", 'success');
                            setTimeout(() => location.reload(), 1500);
                        })
                        .catch(error => {
                            Swal.fire('Error!', "{{__('Failed to delete record.')}}", 'error');
                        });
                }
            });
        }

        function deleteWithdraw(id) {
            Swal.fire({
                title: 'Delete Withdraw Request?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#dc2626',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`${baseUrl}/withdraw/${id}/delete`, {
                        data: { _token: '{{ csrf_token() }}' }
                    })
                        .then(response => {
                            Swal.fire('Deleted!', "{{__('Withdraw request deleted.')}}", 'success');
                            setTimeout(() => location.reload(), 1500);
                        })
                        .catch(error => {
                            Swal.fire('Error!', "{{__('Failed to delete request.')}}", 'error');
                        });
                }
            });
        }
    </script>
@endsection
