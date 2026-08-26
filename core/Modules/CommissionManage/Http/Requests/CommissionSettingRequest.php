<?php

namespace Modules\CommissionManage\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommissionSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        $type = $this->input('commission_type');

        $rules = [
            'commission_type' => 'required|in:subscription_only,subscription_and_commission,commission_only',
        ];

        // Only require rate & gateway if not subscription_only
        if ($type && $type !== 'subscription_only') {
            $rules['commission_rate'] = 'required|numeric|gt:0|max:100';
            $rules['payment_gateway_source'] = 'required|in:landlord_gateway,tenant_default_gateway';

            if ($this->input('payment_gateway_source') === 'landlord_gateway') {
                $rules['payment_gateways'] = 'required|string';
            }
        } else {
            $rules['commission_rate'] = 'nullable|numeric';
            $rules['payment_gateway_source'] = 'nullable';
        }

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'commission_type.required' => 'Please select a commission type.',
            'commission_type.in' => 'Invalid commission type selected.',
            'commission_rate.required_if' => 'Commission rate is required when commission is enabled.',
            'commission_rate.numeric' => 'Commission rate must be a number.',
            'commission_rate.min' => 'Commission rate cannot be less than 0.',
            'commission_rate.max' => 'Commission rate cannot exceed 100.',
            'payment_gateway_source.required' => 'Please select a payment gateway source.',
            'payment_gateway_source.in' => 'Invalid payment gateway source selected.',
            'payment_gateways.required' => 'Please select at least one payment gateway when using Landlord Gateway.',
            'payment_gateways.string' => 'Selected gateways must be valid.',
            'withdraw_gateways.json' => 'Withdraw gateways data must be valid JSON.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Ensure commission_rate is 0 if not required
        if (
            !in_array($this->commission_type, ['commission_only', 'subscription_and_commission'])
            && !$this->has('commission_rate')
        ) {
            $this->merge([
                'commission_rate' => 0,
            ]);
        }

        // If using tenant gateway, clear selected gateways
        if ($this->payment_gateway_source !== 'landlord_gateway') {
            $this->merge([
                'payment_gateways' => null,
                'withdraw_gateways' => null,
            ]);
        }
    }
}
