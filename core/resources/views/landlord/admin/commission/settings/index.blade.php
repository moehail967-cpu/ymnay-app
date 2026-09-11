{{--@extends(route_prefix().'admin.admin-master')--}}
{{--@section('title') {{__('Commission Settings')}} @endsection--}}

{{--@section('content')--}}
{{--    <div class="container-fluid">--}}
{{--        <div class="row">--}}
{{--            <div class="col-12">--}}
{{--                <div class="card">--}}
{{--                    <div class="card-header">--}}
{{--                        <h4 class="card-title mb-0">Commission Settings</h4>--}}
{{--                        <p class="text-muted mb-0">Configure your commission type and payment gateway preferences</p>--}}
{{--                    </div>--}}
{{--                    <div class="card-body">--}}
{{--                        @if(session('success'))--}}
{{--                            <div class="alert alert-success alert-dismissible fade show" role="alert">--}}
{{--                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}--}}
{{--                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        @if(session('error'))--}}
{{--                            <div class="alert alert-danger alert-dismissible fade show" role="alert">--}}
{{--                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}--}}
{{--                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        @if(session('warning'))--}}
{{--                            <div class="alert alert-warning alert-dismissible fade show" role="alert">--}}
{{--                                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}--}}
{{--                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        <form action="{{ $setting ? route('landlord.commission.settings.update', $setting->id) : route('landlord.commission.settings.store') }}"--}}
{{--                              method="POST"--}}
{{--                              id="commissionSettingsForm">--}}
{{--                            @csrf--}}
{{--                            @if($setting)--}}
{{--                                @method('PUT')--}}
{{--                            @endif--}}

{{--                            <div class="row">--}}
{{--                                <!-- Commission Type -->--}}
{{--                                <div class="col-md-6 mb-3">--}}
{{--                                    <label for="commission_type" class="form-label">--}}
{{--                                        Commission Type <span class="text-danger">*</span>--}}
{{--                                    </label>--}}
{{--                                    <select name="commission_type"--}}
{{--                                            id="commission_type"--}}
{{--                                            class="form-select @error('commission_type') is-invalid @enderror">--}}
{{--                                        <option value="">-- Select Commission Type --</option>--}}
{{--                                        @foreach($commissionTypes as $key => $label)--}}
{{--                                            <option value="{{ $key }}"--}}
{{--                                                {{ old('commission_type', $setting?->commission_type) === $key ? 'selected' : '' }}>--}}
{{--                                                {{ $label }}--}}
{{--                                            </option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                    @error('commission_type')--}}
{{--                                    <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                                    @enderror--}}
{{--                                    <small class="form-text text-muted">--}}
{{--                                        <ul class="mb-0 mt-1 ps-3">--}}
{{--                                            <li><strong>Subscription Only:</strong> Charge subscription fees only</li>--}}
{{--                                            <li><strong>Subscription + Commission:</strong> Charge both subscription and per-order commission</li>--}}
{{--                                            <li><strong>Commission Only:</strong> Charge per-order commission only</li>--}}
{{--                                        </ul>--}}
{{--                                    </small>--}}
{{--                                </div>--}}

{{--                                <!-- Commission Rate -->--}}
{{--                                <div class="col-md-6 mb-3" id="commissionRateWrapper">--}}
{{--                                    <label for="commission_rate" class="form-label">--}}
{{--                                        Commission Rate (%) <span class="text-danger" id="commissionRateRequired">*</span>--}}
{{--                                    </label>--}}
{{--                                    <input type="number"--}}
{{--                                           name="commission_rate"--}}
{{--                                           id="commission_rate"--}}
{{--                                           class="form-control @error('commission_rate') is-invalid @enderror"--}}
{{--                                           value="{{ old('commission_rate', $setting?->commission_rate ?? 0) }}"--}}
{{--                                           step="0.01"--}}
{{--                                           min="0"--}}
{{--                                           max="100"--}}
{{--                                           placeholder="Enter commission rate">--}}
{{--                                    @error('commission_rate')--}}
{{--                                    <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                                    @enderror--}}
{{--                                    <small class="form-text text-muted">--}}
{{--                                        Enter commission rate for per-order commission (0-100%).--}}
{{--                                    </small>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <hr class="my-4">--}}

{{--                            <div class="row">--}}
{{--                                <!-- Payment Gateway Source -->--}}
{{--                                <div class="col-md-6 mb-3">--}}
{{--                                    <label for="payment_gateway_source" class="form-label">--}}
{{--                                        Payment Gateway Source <span class="text-danger">*</span>--}}
{{--                                    </label>--}}
{{--                                    <select name="payment_gateway_source"--}}
{{--                                            id="payment_gateway_source"--}}
{{--                                            class="form-select @error('payment_gateway_source') is-invalid @enderror">--}}
{{--                                        <option value="">-- Select Gateway Source --</option>--}}
{{--                                        @foreach($gatewaySources as $key => $label)--}}
{{--                                            <option value="{{ $key }}"--}}
{{--                                                {{ old('payment_gateway_source', $setting?->payment_gateway_source) === $key ? 'selected' : '' }}>--}}
{{--                                                {{ $label }}--}}
{{--                                            </option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                    @error('payment_gateway_source')--}}
{{--                                    <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                                    @enderror--}}
{{--                                    <small class="form-text text-muted">--}}
{{--                                        Choose whether to use your own gateway or tenant's default gateway.--}}
{{--                                    </small>--}}
{{--                                </div>--}}

{{--                                <!-- Selected Gateway (Conditional) -->--}}
{{--                                <div class="col-md-6 mb-3" id="gatewaySelectionWrapper" style="display: none;">--}}
{{--                                    <label for="selected_gateway" class="form-label">--}}
{{--                                        Select Gateway <span class="text-danger">*</span>--}}
{{--                                    </label>--}}
{{--                                    <select name="selected_gateway"--}}
{{--                                            id="selected_gateway"--}}
{{--                                            class="form-select @error('selected_gateway') is-invalid @enderror">--}}
{{--                                        <option value="">-- Select Gateway --</option>--}}
{{--                                        @foreach($availableGateways as $key => $name)--}}
{{--                                            <option value="{{ $key }}"--}}
{{--                                                {{ old('selected_gateway', $setting?->selected_gateway) === $key ? 'selected' : '' }}>--}}
{{--                                                {{ $name }}--}}
{{--                                            </option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                    @error('selected_gateway')--}}
{{--                                    <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                                    @enderror--}}
{{--                                    <small class="form-text text-muted">--}}
{{--                                        Select the payment gateway for processing transactions.--}}
{{--                                    </small>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="row mt-4">--}}
{{--                                <div class="col-12">--}}
{{--                                    <button type="submit" class="btn btn-primary">--}}
{{--                                        <i class="fas fa-save me-1"></i> Save Settings--}}
{{--                                    </button>--}}
{{--                                    <a href="{{ route('landlord.dashboard') }}" class="btn btn-secondary">--}}
{{--                                        <i class="fas fa-times me-1"></i> Cancel--}}
{{--                                    </a>--}}
{{--                                    @if($setting)--}}
{{--                                        <button type="button" class="btn btn-outline-danger float-end" data-bs-toggle="modal" data-bs-target="#deleteModal">--}}
{{--                                            <i class="fas fa-trash me-1"></i> Delete Settings--}}
{{--                                        </button>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </form>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <!-- Commission Preview Card -->--}}
{{--                <div class="card mt-4" id="previewCard" style="display: none;">--}}
{{--                    <div class="card-header bg-light">--}}
{{--                        <h5 class="card-title mb-0">Commission Preview</h5>--}}
{{--                    </div>--}}
{{--                    <div class="card-body">--}}
{{--                        <div class="row">--}}
{{--                            <div class="col-md-3">--}}
{{--                                <label class="form-label">Sample Order Amount</label>--}}
{{--                                <input type="number" id="previewOrderAmount" class="form-control" value="1000" step="10" min="0">--}}
{{--                            </div>--}}
{{--                            <div class="col-md-9">--}}
{{--                                <div class="table-responsive mt-3">--}}
{{--                                    <table class="table table-sm">--}}
{{--                                        <tr>--}}
{{--                                            <td><strong>Order Amount:</strong></td>--}}
{{--                                            <td class="text-end" id="previewOrderTotal">$0.00</td>--}}
{{--                                        </tr>--}}
{{--                                        <tr id="previewCommissionRow" style="display: none;">--}}
{{--                                            <td><strong>Commission (<span id="previewRate">0</span>%):</strong></td>--}}
{{--                                            <td class="text-end text-danger" id="previewCommission">$0.00</td>--}}
{{--                                        </tr>--}}
{{--                                        <tr>--}}
{{--                                            <td><strong>Net Amount:</strong></td>--}}
{{--                                            <td class="text-end text-success fw-bold" id="previewNet">$0.00</td>--}}
{{--                                        </tr>--}}
{{--                                    </table>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <!-- Delete Confirmation Modal -->--}}
{{--    @if($setting)--}}
{{--        <div class="modal fade" id="deleteModal" tabindex="-1">--}}
{{--            <div class="modal-dialog">--}}
{{--                <div class="modal-content">--}}
{{--                    <div class="modal-header bg-danger text-white">--}}
{{--                        <h5 class="modal-title">Confirm Delete</h5>--}}
{{--                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>--}}
{{--                    </div>--}}
{{--                    <div class="modal-body">--}}
{{--                        <p>Are you sure you want to delete your commission settings?</p>--}}
{{--                        <p class="text-muted mb-0">This action cannot be undone.</p>--}}
{{--                    </div>--}}
{{--                    <div class="modal-footer">--}}
{{--                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>--}}
{{--                        <form action="{{ route('landlord.commission.settings.destroy', $setting->id) }}" method="POST" class="d-inline">--}}
{{--                            @csrf--}}
{{--                            @method('DELETE')--}}
{{--                            <button type="submit" class="btn btn-danger">Delete</button>--}}
{{--                        </form>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    @endif--}}
{{--@endsection--}}

{{--@push('scripts')--}}
{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', function() {--}}
{{--            const commissionType = document.getElementById('commission_type');--}}
{{--            const commissionRate = document.getElementById('commission_rate');--}}
{{--            const commissionRateWrapper = document.getElementById('commissionRateWrapper');--}}
{{--            const commissionRateRequired = document.getElementById('commissionRateRequired');--}}

{{--            const gatewaySource = document.getElementById('payment_gateway_source');--}}
{{--            const gatewayWrapper = document.getElementById('gatewaySelectionWrapper');--}}
{{--            const selectedGateway = document.getElementById('selected_gateway');--}}

{{--            const previewCard = document.getElementById('previewCard');--}}
{{--            const previewOrderAmount = document.getElementById('previewOrderAmount');--}}
{{--            const previewOrderTotal = document.getElementById('previewOrderTotal');--}}
{{--            const previewCommission = document.getElementById('previewCommission');--}}
{{--            const previewNet = document.getElementById('previewNet');--}}
{{--            const previewRate = document.getElementById('previewRate');--}}
{{--            const previewCommissionRow = document.getElementById('previewCommissionRow');--}}

{{--            // Toggle commission rate field based on commission type--}}
{{--            function toggleCommissionRate() {--}}
{{--                const type = commissionType.value;--}}
{{--                const requiresCommission = ['commission_only', 'subscription_and_commission'].includes(type);--}}

{{--                if (requiresCommission) {--}}
{{--                    commissionRateWrapper.style.display = 'block';--}}
{{--                    commissionRate.setAttribute('required', 'required');--}}
{{--                    commissionRateRequired.style.display = 'inline';--}}
{{--                    previewCard.style.display = 'block';--}}
{{--                } else {--}}
{{--                    commissionRateWrapper.style.display = 'block'; // Keep visible but not required--}}
{{--                    commissionRate.removeAttribute('required');--}}
{{--                    commissionRateRequired.style.display = 'none';--}}
{{--                    commissionRate.value = '0';--}}
{{--                    previewCard.style.display = type ? 'block' : 'none';--}}
{{--                }--}}

{{--                updatePreview();--}}
{{--            }--}}

{{--            // Toggle gateway selection based on gateway source--}}
{{--            function toggleGatewaySelection() {--}}
{{--                if (gatewaySource.value === 'landlord_gateway') {--}}
{{--                    gatewayWrapper.style.display = 'block';--}}
{{--                    selectedGateway.setAttribute('required', 'required');--}}
{{--                } else {--}}
{{--                    gatewayWrapper.style.display = 'none';--}}
{{--                    selectedGateway.removeAttribute('required');--}}
{{--                    selectedGateway.value = '';--}}
{{--                }--}}
{{--            }--}}

{{--            // Update commission preview--}}
{{--            function updatePreview() {--}}
{{--                const type = commissionType.value;--}}
{{--                const rate = parseFloat(commissionRate.value) || 0;--}}
{{--                const orderAmount = parseFloat(previewOrderAmount.value) || 0;--}}

{{--                const requiresCommission = ['commission_only', 'subscription_and_commission'].includes(type);--}}
{{--                const commissionAmount = requiresCommission ? (orderAmount * rate / 100) : 0;--}}
{{--                const netAmount = orderAmount - commissionAmount;--}}

{{--                previewOrderTotal.textContent = ' + orderAmount.toFixed(2);--}}
{{--                previewRate.textContent = rate.toFixed(2);--}}
{{--                previewCommission.textContent = '- + commissionAmount.toFixed(2);--}}
{{--                previewNet.textContent = ' + netAmount.toFixed(2);--}}

{{--                if (requiresCommission && rate > 0) {--}}
{{--                    previewCommissionRow.style.display = 'table-row';--}}
{{--                } else {--}}
{{--                    previewCommissionRow.style.display = 'none';--}}
{{--                }--}}
{{--            }--}}

{{--            // Initial state--}}
{{--            toggleCommissionRate();--}}
{{--            toggleGatewaySelection();--}}

{{--            // Event listeners--}}
{{--            commissionType.addEventListener('change', toggleCommissionRate);--}}
{{--            gatewaySource.addEventListener('change', toggleGatewaySelection);--}}
{{--            commissionRate.addEventListener('input', updatePreview);--}}
{{--            previewOrderAmount.addEventListener('input', updatePreview);--}}

{{--            // Form validation--}}
{{--            document.getElementById('commissionSettingsForm').addEventListener('submit', function(e) {--}}
{{--                const type = commissionType.value;--}}
{{--                const rate = parseFloat(commissionRate.value) || 0;--}}
{{--                const requiresCommission = ['commission_only', 'subscription_and_commission'].includes(type);--}}

{{--                if (requiresCommission && rate <= 0) {--}}
{{--                    e.preventDefault();--}}
{{--                    alert('Commission rate must be greater than 0% when commission is enabled.');--}}
{{--                    commissionRate.focus();--}}
{{--                    return false;--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
{{--@endpush--}}
