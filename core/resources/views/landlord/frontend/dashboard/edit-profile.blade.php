@extends('landlord.frontend.dashboard.master')

@section('page-title')
    {{__('Edit Profile')}}
@endsection

@section('title')
    {{__('Edit Profile')}}
@endsection

@section('style')
        <x-media-upload.css/>
@endsection

@section('section')
<div class="col-span-full lg:col-span-9">
    <!-- Top Header -->
    <header class="bg-[#F8FAFB] lg:sticky top-[78px] z-40 border-b rounded-t-3xl">
        <div class="flex items-center justify-between px-6 py-3.5">
            <div class="flex items-center ">
                <!-- Mobile Menu Button -->
                <button id="menuBtn"
                        class="block mr-3 lg:hidden text-gray-600 hover:text-teal-600 focus:outline-none">
                    <i class="icon-base ti tabler-menu-2 icon-28px"></i>
                </button>

                <div class="">
                    <h1 class="text-lg font-medium text-secondary">{{__('Edit Profile')}}
                    </h1>
                    <p class=" text-xs lg:text-sm text-sub2Title mt-1">{{__('Update your personal information and profile settings')}}</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="p-6">
        <div class="">
            <div class="rounded-2xl border border-gray-100 shadow-sm px-6 py-6" style="background-color: var(--section-bg-1, #ffffff)">

                <x-error-msg-tw/>
                <x-flash-msg-tw/>

                <form action="{{route('landlord.user.profile.update')}}" method="post" enctype="multipart/form-data" class="flex flex-col gap-5">
                    @csrf

                    <!-- Avatar Section -->
                    <div class="mb-2">
                        <x-fields.tw-media-upload name="image" title="{{__('Profile Image')}}" id="{{$user_details->image}}" value="{{$user_details->image}}" dimentions="{{__('120 X 120 px image recommended')}}"/>
                    </div>

                    <hr class="border-gray-100 mb-2" />

                    <!-- Row 1: Full Name + Email -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-800">{{__('Full Name')}}</label>
                            <div class="relative">
                                <i class="ti tabler-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                <input type="text" name="name" value="{{$user_details->name}}" placeholder="{{__('Full name')}}"
                                       class="w-full border border-borderCS rounded-lg pl-10 pr-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition" />
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-800">{{__('Email Address')}}</label>
                            <div class="relative">
                                <i class="ti tabler-mail absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                <input type="email" name="email" value="{{$user_details->email}}" placeholder="{{__('Email address')}}"
                                       class="w-full border border-borderCS rounded-lg pl-10 pr-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition" />
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Mobile + Company -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-800">{{__('Mobile Number')}}</label>
                            <div class="relative">
                                <i class="ti tabler-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                <input type="tel" id="phone" name="mobile" value="{{$user_details->mobile}}" placeholder="{{__('Mobile number')}}"
                                       class="w-full border border-borderCS rounded-lg pl-10 pr-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition" />
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-800">{{__('Company')}}</label>
                            <div class="relative">
                                <i class="ti tabler-building absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                <input type="text" name="company" value="{{$user_details->company}}" placeholder="{{__('Company name')}}"
                                       class="w-full border border-borderCS rounded-lg pl-10 pr-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition" />
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Street Address (full width) -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-gray-800">{{__('Street Address')}}</label>
                        <div class="relative">
                            <i class="ti tabler-home absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                            <input type="text" name="address" value="{{$user_details->address}}" placeholder="{{__('Enter your street address..')}}"
                                   class="w-full border border-borderCS rounded-lg pl-10 pr-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition" />
                        </div>
                    </div>

                    <!-- Row 4: City + State -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-800">{{__('City')}}</label>
                            <div class="relative">
                                <i class="ti tabler-map-pin absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                <input type="text" name="city" value="{{$user_details->city}}" placeholder="{{__('Enter city')}}"
                                       class="w-full border border-borderCS rounded-lg pl-10 pr-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition" />
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-gray-800">{{__('State/Province')}}</label>
                            <div class="relative">
                                <i class="ti tabler-map absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                                <input type="text" name="state" value="{{$user_details->state}}" placeholder="{{__('Enter state/province')}}"
                                       class="w-full border border-borderCS rounded-lg pl-10 pr-4 py-3 text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition" />
                            </div>
                        </div>
                    </div>

                    <!-- Row 5: Country -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-gray-800">{{__('Country')}}</label>
                        <div class="relative">
                            <i class="ti tabler-flag absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-base z-10 pointer-events-none"></i>
                            <select id="country" name="country"
                                    class="w-full appearance-none border border-borderCS rounded-lg pl-10 pr-10 py-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 transition bg-white cursor-pointer">
                                <option value="">{{__('Select Country')}}</option>
                                <option value="Afganistan">Afghanistan</option>
                                <option value="Albania">Albania</option>
                                <option value="Algeria">Algeria</option>
                                <option value="American Samoa">American Samoa</option>
                                <option value="Andorra">Andorra</option>
                                <option value="Angola">Angola</option>
                                <option value="Anguilla">Anguilla</option>
                                <option value="Antigua & Barbuda">Antigua & Barbuda</option>
                                <option value="Argentina">Argentina</option>
                                <option value="Armenia">Armenia</option>
                                <option value="Aruba">Aruba</option>
                                <option value="Australia">Australia</option>
                                <option value="Austria">Austria</option>
                                <option value="Azerbaijan">Azerbaijan</option>
                                <option value="Bahamas">Bahamas</option>
                                <option value="Bahrain">Bahrain</option>
                                <option value="Bangladesh">Bangladesh</option>
                                <option value="Barbados">Barbados</option>
                                <option value="Belarus">Belarus</option>
                                <option value="Belgium">Belgium</option>
                                <option value="Belize">Belize</option>
                                <option value="Benin">Benin</option>
                                <option value="Bermuda">Bermuda</option>
                                <option value="Bhutan">Bhutan</option>
                                <option value="Bolivia">Bolivia</option>
                                <option value="Bonaire">Bonaire</option>
                                <option value="Bosnia & Herzegovina">Bosnia & Herzegovina</option>
                                <option value="Botswana">Botswana</option>
                                <option value="Brazil">Brazil</option>
                                <option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
                                <option value="Brunei">Brunei</option>
                                <option value="Bulgaria">Bulgaria</option>
                                <option value="Burkina Faso">Burkina Faso</option>
                                <option value="Burundi">Burundi</option>
                                <option value="Cambodia">Cambodia</option>
                                <option value="Cameroon">Cameroon</option>
                                <option value="Canada">Canada</option>
                                <option value="Canary Islands">Canary Islands</option>
                                <option value="Cape Verde">Cape Verde</option>
                                <option value="Cayman Islands">Cayman Islands</option>
                                <option value="Central African Republic">Central African Republic</option>
                                <option value="Chad">Chad</option>
                                <option value="Channel Islands">Channel Islands</option>
                                <option value="Chile">Chile</option>
                                <option value="China">China</option>
                                <option value="Christmas Island">Christmas Island</option>
                                <option value="Cocos Island">Cocos Island</option>
                                <option value="Colombia">Colombia</option>
                                <option value="Comoros">Comoros</option>
                                <option value="Congo">Congo</option>
                                <option value="Cook Islands">Cook Islands</option>
                                <option value="Costa Rica">Costa Rica</option>
                                <option value="Cote DIvoire">Cote DIvoire</option>
                                <option value="Croatia">Croatia</option>
                                <option value="Cuba">Cuba</option>
                                <option value="Curaco">Curacao</option>
                                <option value="Cyprus">Cyprus</option>
                                <option value="Czech Republic">Czech Republic</option>
                                <option value="Denmark">Denmark</option>
                                <option value="Djibouti">Djibouti</option>
                                <option value="Dominica">Dominica</option>
                                <option value="Dominican Republic">Dominican Republic</option>
                                <option value="East Timor">East Timor</option>
                                <option value="Ecuador">Ecuador</option>
                                <option value="Egypt">Egypt</option>
                                <option value="El Salvador">El Salvador</option>
                                <option value="Equatorial Guinea">Equatorial Guinea</option>
                                <option value="Eritrea">Eritrea</option>
                                <option value="Estonia">Estonia</option>
                                <option value="Ethiopia">Ethiopia</option>
                                <option value="Falkland Islands">Falkland Islands</option>
                                <option value="Faroe Islands">Faroe Islands</option>
                                <option value="Fiji">Fiji</option>
                                <option value="Finland">Finland</option>
                                <option value="France">France</option>
                                <option value="French Guiana">French Guiana</option>
                                <option value="French Polynesia">French Polynesia</option>
                                <option value="French Southern Ter">French Southern Ter</option>
                                <option value="Gabon">Gabon</option>
                                <option value="Gambia">Gambia</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Germany">Germany</option>
                                <option value="Ghana">Ghana</option>
                                <option value="Gibraltar">Gibraltar</option>
                                <option value="Great Britain">Great Britain</option>
                                <option value="Greece">Greece</option>
                                <option value="Greenland">Greenland</option>
                                <option value="Grenada">Grenada</option>
                                <option value="Guadeloupe">Guadeloupe</option>
                                <option value="Guam">Guam</option>
                                <option value="Guatemala">Guatemala</option>
                                <option value="Guinea">Guinea</option>
                                <option value="Guyana">Guyana</option>
                                <option value="Haiti">Haiti</option>
                                <option value="Hawaii">Hawaii</option>
                                <option value="Honduras">Honduras</option>
                                <option value="Hong Kong">Hong Kong</option>
                                <option value="Hungary">Hungary</option>
                                <option value="Iceland">Iceland</option>
                                <option value="Indonesia">Indonesia</option>
                                <option value="India">India</option>
                                <option value="Iran">Iran</option>
                                <option value="Iraq">Iraq</option>
                                <option value="Ireland">Ireland</option>
                                <option value="Isle of Man">Isle of Man</option>
                                <option value="Israel">Israel</option>
                                <option value="Italy">Italy</option>
                                <option value="Jamaica">Jamaica</option>
                                <option value="Japan">Japan</option>
                                <option value="Jordan">Jordan</option>
                                <option value="Kazakhstan">Kazakhstan</option>
                                <option value="Kenya">Kenya</option>
                                <option value="Kiribati">Kiribati</option>
                                <option value="Korea North">Korea North</option>
                                <option value="Korea Sout">Korea South</option>
                                <option value="Kuwait">Kuwait</option>
                                <option value="Kyrgyzstan">Kyrgyzstan</option>
                                <option value="Laos">Laos</option>
                                <option value="Latvia">Latvia</option>
                                <option value="Lebanon">Lebanon</option>
                                <option value="Lesotho">Lesotho</option>
                                <option value="Liberia">Liberia</option>
                                <option value="Libya">Libya</option>
                                <option value="Liechtenstein">Liechtenstein</option>
                                <option value="Lithuania">Lithuania</option>
                                <option value="Luxembourg">Luxembourg</option>
                                <option value="Macau">Macau</option>
                                <option value="Macedonia">Macedonia</option>
                                <option value="Madagascar">Madagascar</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Malawi">Malawi</option>
                                <option value="Maldives">Maldives</option>
                                <option value="Mali">Mali</option>
                                <option value="Malta">Malta</option>
                                <option value="Marshall Islands">Marshall Islands</option>
                                <option value="Martinique">Martinique</option>
                                <option value="Mauritania">Mauritania</option>
                                <option value="Mauritius">Mauritius</option>
                                <option value="Mayotte">Mayotte</option>
                                <option value="Mexico">Mexico</option>
                                <option value="Midway Islands">Midway Islands</option>
                                <option value="Moldova">Moldova</option>
                                <option value="Monaco">Monaco</option>
                                <option value="Mongolia">Mongolia</option>
                                <option value="Montserrat">Montserrat</option>
                                <option value="Morocco">Morocco</option>
                                <option value="Mozambique">Mozambique</option>
                                <option value="Myanmar">Myanmar</option>
                                <option value="Nambia">Nambia</option>
                                <option value="Nauru">Nauru</option>
                                <option value="Nepal">Nepal</option>
                                <option value="Netherland Antilles">Netherland Antilles</option>
                                <option value="Netherlands">Netherlands (Holland, Europe)</option>
                                <option value="Nevis">Nevis</option>
                                <option value="New Caledonia">New Caledonia</option>
                                <option value="New Zealand">New Zealand</option>
                                <option value="Nicaragua">Nicaragua</option>
                                <option value="Niger">Niger</option>
                                <option value="Nigeria">Nigeria</option>
                                <option value="Niue">Niue</option>
                                <option value="Norfolk Island">Norfolk Island</option>
                                <option value="Norway">Norway</option>
                                <option value="Oman">Oman</option>
                                <option value="Pakistan">Pakistan</option>
                                <option value="Palau Island">Palau Island</option>
                                <option value="Palestine">Palestine</option>
                                <option value="Panama">Panama</option>
                                <option value="Papua New Guinea">Papua New Guinea</option>
                                <option value="Paraguay">Paraguay</option>
                                <option value="Peru">Peru</option>
                                <option value="Phillipines">Philippines</option>
                                <option value="Pitcairn Island">Pitcairn Island</option>
                                <option value="Poland">Poland</option>
                                <option value="Portugal">Portugal</option>
                                <option value="Puerto Rico">Puerto Rico</option>
                                <option value="Qatar">Qatar</option>
                                <option value="Republic of Montenegro">Republic of Montenegro</option>
                                <option value="Republic of Serbia">Republic of Serbia</option>
                                <option value="Reunion">Reunion</option>
                                <option value="Romania">Romania</option>
                                <option value="Russia">Russia</option>
                                <option value="Rwanda">Rwanda</option>
                                <option value="St Barthelemy">St Barthelemy</option>
                                <option value="St Eustatius">St Eustatius</option>
                                <option value="St Helena">St Helena</option>
                                <option value="St Kitts-Nevis">St Kitts-Nevis</option>
                                <option value="St Lucia">St Lucia</option>
                                <option value="St Maarten">St Maarten</option>
                                <option value="St Pierre & Miquelon">St Pierre & Miquelon</option>
                                <option value="St Vincent & Grenadines">St Vincent & Grenadines</option>
                                <option value="Saipan">Saipan</option>
                                <option value="Samoa">Samoa</option>
                                <option value="Samoa American">Samoa American</option>
                                <option value="San Marino">San Marino</option>
                                <option value="Sao Tome & Principe">Sao Tome & Principe</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="Senegal">Senegal</option>
                                <option value="Seychelles">Seychelles</option>
                                <option value="Sierra Leone">Sierra Leone</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Slovakia">Slovakia</option>
                                <option value="Slovenia">Slovenia</option>
                                <option value="Solomon Islands">Solomon Islands</option>
                                <option value="Somalia">Somalia</option>
                                <option value="South Africa">South Africa</option>
                                <option value="Spain">Spain</option>
                                <option value="Sri Lanka">Sri Lanka</option>
                                <option value="Sudan">Sudan</option>
                                <option value="Suriname">Suriname</option>
                                <option value="Swaziland">Swaziland</option>
                                <option value="Sweden">Sweden</option>
                                <option value="Switzerland">Switzerland</option>
                                <option value="Syria">Syria</option>
                                <option value="Tahiti">Tahiti</option>
                                <option value="Taiwan">Taiwan</option>
                                <option value="Tajikistan">Tajikistan</option>
                                <option value="Tanzania">Tanzania</option>
                                <option value="Thailand">Thailand</option>
                                <option value="Togo">Togo</option>
                                <option value="Tokelau">Tokelau</option>
                                <option value="Tonga">Tonga</option>
                                <option value="Trinidad & Tobago">Trinidad & Tobago</option>
                                <option value="Tunisia">Tunisia</option>
                                <option value="Turkey">Turkey</option>
                                <option value="Turkmenistan">Turkmenistan</option>
                                <option value="Turks & Caicos Is">Turks & Caicos Is</option>
                                <option value="Tuvalu">Tuvalu</option>
                                <option value="Uganda">Uganda</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Ukraine">Ukraine</option>
                                <option value="United Arab Erimates">United Arab Emirates</option>
                                <option value="United States of America">United States of America</option>
                                <option value="Uraguay">Uruguay</option>
                                <option value="Uzbekistan">Uzbekistan</option>
                                <option value="Vanuatu">Vanuatu</option>
                                <option value="Vatican City State">Vatican City State</option>
                                <option value="Venezuela">Venezuela</option>
                                <option value="Vietnam">Vietnam</option>
                                <option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
                                <option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
                                <option value="Wake Island">Wake Island</option>
                                <option value="Wallis & Futana Is">Wallis & Futana Is</option>
                                <option value="Yemen">Yemen</option>
                                <option value="Zaire">Zaire</option>
                                <option value="Zambia">Zambia</option>
                                <option value="Zimbabwe">Zimbabwe</option>
                            </select>
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                <i class="ti tabler-chevron-down text-gray-400 text-base"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{route('landlord.user.home')}}"
                           class="px-6 py-3 rounded-lg text-sm font-semibold text-gray-700 bg-white border border-borderCS hover:bg-gray-50 transition">
                            {{__('Cancel')}}
                        </a>
                        <button type="submit"
                                class="px-6 py-3 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition bg-primary">
                            {{__('Save Changes')}}
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </main>
</div>

<x-media-upload.tw-markup userType="user"/>
@endsection

@section('scripts')
    @parent
    <script src="{{ asset('assets/new-landlord/js/active_page.js') }}"></script>

    <x-media-upload.tw-js/>

    <script>
        (function($){
            "use strict";
            // Select saved country
            $(document).ready(function(){
                var selectedCountry = "{{$user_details->country}}";
                if (selectedCountry) {
                    $('#country option[value="'+selectedCountry+'"]').attr('selected', true);
                }
            });
        }(jQuery));
    </script>

    <x-custom-js.phone-number-config selector="#phone"/>
    <script>
        $(document).ready(function () {
            setTimeout(() => {
                $('#phone').val(`{{$user_details->mobile}}`);
            }, 800);
        });
    </script>

    <script>
        $('.close-bars, .body-overlay').on('click', function() {
            $('.dashboard-close, .dashboard-close-main, .body-overlay').removeClass('active');
        });
        $('.sidebar-icon').on('click', function() {
            $('.dashboard-close, .dashboard-close-main, .body-overlay').addClass('active');
        });
    </script>
@endsection
