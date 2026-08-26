@props([
    'title',
    'key',
    'countries'
])

<div class="contact-parent-wrapper dr-contact-section">
    <div class="dr-contact-header">
        <div class="flex items-center gap-3">
            <div>
                <h4 class="text-sm font-bold text-dark">{{__($title)}}</h4>
                <label class="flex items-center gap-2 mt-1 cursor-pointer">
                    <input type="hidden" class="was_unchecked" name="{{$key}}_was_unchecked">
                    <input class="same-contact-admin w-4 h-4 rounded border-2 border-main accent-primary"
                           type="checkbox" id="{{$key}}-same-contact-admin"
                           name="{{$key}}_same_contact_admin" value="selected" checked>
                    <span class="text-xs text-muted">{{__('Same as contact admin')}}</span>
                </label>
            </div>
        </div>
        <a class="expand-collapse-btn w-8 h-8 rounded-lg border border-main flex items-center justify-center text-muted hover:text-dark hover:border-sidebar-hover transition" href="">
            <i class="las la-angle-down text-sm"></i>
        </a>
    </div>

    <div class="contact-form-wrapper dr-contact-body hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="lnd-label">{{__('First name')}} <span class="text-danger">*</span></label>
                <input type="text" class="lnd-input" name="{{$key}}_nameFirst" value="{{old("{$key}_nameFirst")}}">
            </div>
            <div>
                <label class="lnd-label">{{__('Last name')}} <span class="text-danger">*</span></label>
                <input type="text" class="lnd-input" name="{{$key}}_nameLast" value="{{old("{$key}_nameLast")}}">
            </div>
        </div>

        <div class="mt-4">
            <label class="lnd-label">{{__('Middle name')}}</label>
            <input type="text" class="lnd-input" name="{{$key}}_nameMiddle" value="{{old("{$key}_nameMiddle")}}">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="lnd-label">{{__('Email')}} <span class="text-danger">*</span></label>
                <input type="email" class="lnd-input" name="{{$key}}_email" value="{{old("{$key}_email")}}">
            </div>
            <div>
                <label class="lnd-label">{{__('Phone')}} <span class="text-danger">*</span></label>
                <input type="text" class="lnd-input" name="{{$key}}_phone" value="{{old("{$key}_phone")}}">
            </div>
            <div>
                <label class="lnd-label">{{__('Fax')}}</label>
                <input type="text" class="lnd-input" name="{{$key}}_fax" value="{{old("{$key}_fax")}}">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="lnd-label">{{__('Job Title')}}</label>
                <input type="text" class="lnd-input" name="{{$key}}_jobTitle" value="{{old("{$key}_jobTitle")}}">
            </div>
            <div>
                <label class="lnd-label">{{__('Organization')}}</label>
                <input type="text" class="lnd-input" name="{{$key}}_organization" value="{{old("{$key}_organization")}}">
            </div>
        </div>

        <div class="mt-5 pt-4 border-t border-main">
            <h5 class="text-xs font-bold text-dark uppercase tracking-wider mb-3">{{__('Address')}}</h5>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label class="lnd-label">{{__('Country')}} <span class="text-danger">*</span></label>
                    <select class="lnd-input country" name="{{$key}}_country" id="country">
                        <option value="">{{__('Select a country')}}</option>
                        @foreach($countries ?? [] as $country)
                            <option value="{{$country->countryKey}}">{{$country->label}}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="lnd-label">{{__('State')}} <span class="text-danger">*</span></label>
                    <select class="lnd-input state" name="{{$key}}_state" id="state"></select>
                </div>
                <div>
                    <label class="lnd-label">{{__('City')}} <span class="text-danger">*</span></label>
                    <input type="text" class="lnd-input" name="{{$key}}_city" id="city" value="{{old("{$key}_city")}}">
                </div>
                <div>
                    <label class="lnd-label">{{__('Postal Code')}} <span class="text-danger">*</span></label>
                    <input type="text" class="lnd-input" name="{{$key}}_postalCode" id="postalCode" value="{{old("{$key}_postalCode")}}">
                </div>
            </div>

            <div class="mt-4">
                <label class="lnd-label">{{__('Address One')}} <span class="text-danger">*</span></label>
                <textarea name="{{$key}}_address1" class="lnd-input" id="address1" rows="3">{{old("{$key}_address1")}}</textarea>
            </div>
            <div class="mt-4">
                <label class="lnd-label">{{__('Address Two')}}</label>
                <textarea name="{{$key}}_address2" class="lnd-input" id="address2" rows="3">{{old("{$key}_address2")}}</textarea>
            </div>
        </div>
    </div>
</div>
