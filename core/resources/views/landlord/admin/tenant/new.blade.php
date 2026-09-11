@extends('landlord.admin.admin-master')
@section('title') {{__('Add New Tenant')}} @endsection

@section('style')
    {{-- Tailwind media uploader needs no extra CSS --}}
@endsection

@section('content')

    <div class="bg-surface rounded-xl shadow-main overflow-hidden">

        <div class="px-6 py-4 border-b border-main flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center">
                    <i class="mdi mdi-account-plus text-success text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('New Tenant')}}</h3>
                    <p class="text-xs text-muted">{{__('Create a new tenant account')}}</p>
                </div>
            </div>
            <a href="{{route('landlord.admin.tenant')}}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                <i class="mdi mdi-arrow-left text-base"></i> {{__('All Tenants')}}
            </a>
        </div>

        <div class="p-6 md:p-8">
            <x-landlord-flash-msg/>
            <x-landlord-error-msg/>

            <form action="{{route('landlord.admin.tenant.new')}}" method="post">
                @csrf

                {{-- Row 1: Full Name + Email --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    {{-- Full Name --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Full Name')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-account-outline text-lg text-primary"></i>
                            <input type="text" name="name" value="{{old('name')}}"
                                   placeholder="{{__('E.g. Julian Vane')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                        <p class="text-[11px] text-muted mt-1.5">{{__('Visible in the tenant directory.')}}</p>
                    </div>

                    {{-- Email Address --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Email Address')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-at text-lg text-primary"></i>
                            <input type="email" name="email" value="{{old('email')}}"
                                   placeholder="{{__('tenant@example.com')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                        <p class="text-[11px] text-muted mt-1.5">{{__('Account credentials will be sent to this email.')}}</p>
                    </div>

                </div>

                {{-- Row 2: Username + Mobile --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Username')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-card-account-details text-lg text-primary"></i>
                            <input type="text" name="username" value="{{old('username')}}"
                                   placeholder="{{__('Enter username')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                        <p class="text-[11px] text-danger mt-1.5">{{__('User will login using this username.')}}</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Mobile')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-phone-outline text-lg text-primary"></i>
                            <input type="text" name="mobile" value="{{old('mobile')}}"
                                   placeholder="{{__('+1 000 000 0000')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                </div>

                {{-- Section: Location Details --}}
                <div class="mb-6">
                    <p class="text-[10px] font-bold tracking-widest text-muted uppercase mb-4">{{__('Location Details')}}</p>

                    {{-- Row 3: Country + City --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                        {{-- Country --}}
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Country')}}</label>
                            <div class="relative">
                                <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                                    <i class="mdi mdi-earth text-lg text-primary"></i>
                                    <select name="country"
                                            class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                        <option value="">{{__('Select Country')}}</option>
                                        <option value="Afganistan" @selected(old('country') == 'Afganistan')>Afghanistan</option>
                                        <option value="Albania" @selected(old('country') == 'Albania')>Albania</option>
                                        <option value="Algeria" @selected(old('country') == 'Algeria')>Algeria</option>
                                        <option value="American Samoa" @selected(old('country') == 'American Samoa')>American Samoa</option>
                                        <option value="Andorra" @selected(old('country') == 'Andorra')>Andorra</option>
                                        <option value="Angola" @selected(old('country') == 'Angola')>Angola</option>
                                        <option value="Anguilla" @selected(old('country') == 'Anguilla')>Anguilla</option>
                                        <option value="Antigua &amp; Barbuda" @selected(old('country') == 'Antigua & Barbuda')>Antigua &amp; Barbuda</option>
                                        <option value="Argentina" @selected(old('country') == 'Argentina')>Argentina</option>
                                        <option value="Armenia" @selected(old('country') == 'Armenia')>Armenia</option>
                                        <option value="Aruba" @selected(old('country') == 'Aruba')>Aruba</option>
                                        <option value="Australia" @selected(old('country') == 'Australia')>Australia</option>
                                        <option value="Austria" @selected(old('country') == 'Austria')>Austria</option>
                                        <option value="Azerbaijan" @selected(old('country') == 'Azerbaijan')>Azerbaijan</option>
                                        <option value="Bahamas" @selected(old('country') == 'Bahamas')>Bahamas</option>
                                        <option value="Bahrain" @selected(old('country') == 'Bahrain')>Bahrain</option>
                                        <option value="Bangladesh" @selected(old('country') == 'Bangladesh')>Bangladesh</option>
                                        <option value="Barbados" @selected(old('country') == 'Barbados')>Barbados</option>
                                        <option value="Belarus" @selected(old('country') == 'Belarus')>Belarus</option>
                                        <option value="Belgium" @selected(old('country') == 'Belgium')>Belgium</option>
                                        <option value="Belize" @selected(old('country') == 'Belize')>Belize</option>
                                        <option value="Benin" @selected(old('country') == 'Benin')>Benin</option>
                                        <option value="Bermuda" @selected(old('country') == 'Bermuda')>Bermuda</option>
                                        <option value="Bhutan" @selected(old('country') == 'Bhutan')>Bhutan</option>
                                        <option value="Bolivia" @selected(old('country') == 'Bolivia')>Bolivia</option>
                                        <option value="Bonaire" @selected(old('country') == 'Bonaire')>Bonaire</option>
                                        <option value="Bosnia &amp; Herzegovina" @selected(old('country') == 'Bosnia & Herzegovina')>Bosnia &amp; Herzegovina</option>
                                        <option value="Botswana" @selected(old('country') == 'Botswana')>Botswana</option>
                                        <option value="Brazil" @selected(old('country') == 'Brazil')>Brazil</option>
                                        <option value="British Indian Ocean Ter" @selected(old('country') == 'British Indian Ocean Ter')>British Indian Ocean Ter</option>
                                        <option value="Brunei" @selected(old('country') == 'Brunei')>Brunei</option>
                                        <option value="Bulgaria" @selected(old('country') == 'Bulgaria')>Bulgaria</option>
                                        <option value="Burkina Faso" @selected(old('country') == 'Burkina Faso')>Burkina Faso</option>
                                        <option value="Burundi" @selected(old('country') == 'Burundi')>Burundi</option>
                                        <option value="Cambodia" @selected(old('country') == 'Cambodia')>Cambodia</option>
                                        <option value="Cameroon" @selected(old('country') == 'Cameroon')>Cameroon</option>
                                        <option value="Canada" @selected(old('country') == 'Canada')>Canada</option>
                                        <option value="Canary Islands" @selected(old('country') == 'Canary Islands')>Canary Islands</option>
                                        <option value="Cape Verde" @selected(old('country') == 'Cape Verde')>Cape Verde</option>
                                        <option value="Cayman Islands" @selected(old('country') == 'Cayman Islands')>Cayman Islands</option>
                                        <option value="Central African Republic" @selected(old('country') == 'Central African Republic')>Central African Republic</option>
                                        <option value="Chad" @selected(old('country') == 'Chad')>Chad</option>
                                        <option value="Channel Islands" @selected(old('country') == 'Channel Islands')>Channel Islands</option>
                                        <option value="Chile" @selected(old('country') == 'Chile')>Chile</option>
                                        <option value="China" @selected(old('country') == 'China')>China</option>
                                        <option value="Christmas Island" @selected(old('country') == 'Christmas Island')>Christmas Island</option>
                                        <option value="Cocos Island" @selected(old('country') == 'Cocos Island')>Cocos Island</option>
                                        <option value="Colombia" @selected(old('country') == 'Colombia')>Colombia</option>
                                        <option value="Comoros" @selected(old('country') == 'Comoros')>Comoros</option>
                                        <option value="Congo" @selected(old('country') == 'Congo')>Congo</option>
                                        <option value="Cook Islands" @selected(old('country') == 'Cook Islands')>Cook Islands</option>
                                        <option value="Costa Rica" @selected(old('country') == 'Costa Rica')>Costa Rica</option>
                                        <option value="Cote DIvoire" @selected(old('country') == 'Cote DIvoire')>Cote DIvoire</option>
                                        <option value="Croatia" @selected(old('country') == 'Croatia')>Croatia</option>
                                        <option value="Cuba" @selected(old('country') == 'Cuba')>Cuba</option>
                                        <option value="Curaco" @selected(old('country') == 'Curaco')>Curacao</option>
                                        <option value="Cyprus" @selected(old('country') == 'Cyprus')>Cyprus</option>
                                        <option value="Czech Republic" @selected(old('country') == 'Czech Republic')>Czech Republic</option>
                                        <option value="Denmark" @selected(old('country') == 'Denmark')>Denmark</option>
                                        <option value="Djibouti" @selected(old('country') == 'Djibouti')>Djibouti</option>
                                        <option value="Dominica" @selected(old('country') == 'Dominica')>Dominica</option>
                                        <option value="Dominican Republic" @selected(old('country') == 'Dominican Republic')>Dominican Republic</option>
                                        <option value="East Timor" @selected(old('country') == 'East Timor')>East Timor</option>
                                        <option value="Ecuador" @selected(old('country') == 'Ecuador')>Ecuador</option>
                                        <option value="Egypt" @selected(old('country') == 'Egypt')>Egypt</option>
                                        <option value="El Salvador" @selected(old('country') == 'El Salvador')>El Salvador</option>
                                        <option value="Equatorial Guinea" @selected(old('country') == 'Equatorial Guinea')>Equatorial Guinea</option>
                                        <option value="Eritrea" @selected(old('country') == 'Eritrea')>Eritrea</option>
                                        <option value="Estonia" @selected(old('country') == 'Estonia')>Estonia</option>
                                        <option value="Ethiopia" @selected(old('country') == 'Ethiopia')>Ethiopia</option>
                                        <option value="Falkland Islands" @selected(old('country') == 'Falkland Islands')>Falkland Islands</option>
                                        <option value="Faroe Islands" @selected(old('country') == 'Faroe Islands')>Faroe Islands</option>
                                        <option value="Fiji" @selected(old('country') == 'Fiji')>Fiji</option>
                                        <option value="Finland" @selected(old('country') == 'Finland')>Finland</option>
                                        <option value="France" @selected(old('country') == 'France')>France</option>
                                        <option value="French Guiana" @selected(old('country') == 'French Guiana')>French Guiana</option>
                                        <option value="French Polynesia" @selected(old('country') == 'French Polynesia')>French Polynesia</option>
                                        <option value="French Southern Ter" @selected(old('country') == 'French Southern Ter')>French Southern Ter</option>
                                        <option value="Gabon" @selected(old('country') == 'Gabon')>Gabon</option>
                                        <option value="Gambia" @selected(old('country') == 'Gambia')>Gambia</option>
                                        <option value="Georgia" @selected(old('country') == 'Georgia')>Georgia</option>
                                        <option value="Germany" @selected(old('country') == 'Germany')>Germany</option>
                                        <option value="Ghana" @selected(old('country') == 'Ghana')>Ghana</option>
                                        <option value="Gibraltar" @selected(old('country') == 'Gibraltar')>Gibraltar</option>
                                        <option value="Great Britain" @selected(old('country') == 'Great Britain')>Great Britain</option>
                                        <option value="Greece" @selected(old('country') == 'Greece')>Greece</option>
                                        <option value="Greenland" @selected(old('country') == 'Greenland')>Greenland</option>
                                        <option value="Grenada" @selected(old('country') == 'Grenada')>Grenada</option>
                                        <option value="Guadeloupe" @selected(old('country') == 'Guadeloupe')>Guadeloupe</option>
                                        <option value="Guam" @selected(old('country') == 'Guam')>Guam</option>
                                        <option value="Guatemala" @selected(old('country') == 'Guatemala')>Guatemala</option>
                                        <option value="Guinea" @selected(old('country') == 'Guinea')>Guinea</option>
                                        <option value="Guyana" @selected(old('country') == 'Guyana')>Guyana</option>
                                        <option value="Haiti" @selected(old('country') == 'Haiti')>Haiti</option>
                                        <option value="Hawaii" @selected(old('country') == 'Hawaii')>Hawaii</option>
                                        <option value="Honduras" @selected(old('country') == 'Honduras')>Honduras</option>
                                        <option value="Hong Kong" @selected(old('country') == 'Hong Kong')>Hong Kong</option>
                                        <option value="Hungary" @selected(old('country') == 'Hungary')>Hungary</option>
                                        <option value="Iceland" @selected(old('country') == 'Iceland')>Iceland</option>
                                        <option value="Indonesia" @selected(old('country') == 'Indonesia')>Indonesia</option>
                                        <option value="India" @selected(old('country') == 'India')>India</option>
                                        <option value="Iran" @selected(old('country') == 'Iran')>Iran</option>
                                        <option value="Iraq" @selected(old('country') == 'Iraq')>Iraq</option>
                                        <option value="Ireland" @selected(old('country') == 'Ireland')>Ireland</option>
                                        <option value="Isle of Man" @selected(old('country') == 'Isle of Man')>Isle of Man</option>
                                        <option value="Israel" @selected(old('country') == 'Israel')>Israel</option>
                                        <option value="Italy" @selected(old('country') == 'Italy')>Italy</option>
                                        <option value="Jamaica" @selected(old('country') == 'Jamaica')>Jamaica</option>
                                        <option value="Japan" @selected(old('country') == 'Japan')>Japan</option>
                                        <option value="Jordan" @selected(old('country') == 'Jordan')>Jordan</option>
                                        <option value="Kazakhstan" @selected(old('country') == 'Kazakhstan')>Kazakhstan</option>
                                        <option value="Kenya" @selected(old('country') == 'Kenya')>Kenya</option>
                                        <option value="Kiribati" @selected(old('country') == 'Kiribati')>Kiribati</option>
                                        <option value="Korea North" @selected(old('country') == 'Korea North')>Korea North</option>
                                        <option value="Korea Sout" @selected(old('country') == 'Korea Sout')>Korea South</option>
                                        <option value="Kuwait" @selected(old('country') == 'Kuwait')>Kuwait</option>
                                        <option value="Kyrgyzstan" @selected(old('country') == 'Kyrgyzstan')>Kyrgyzstan</option>
                                        <option value="Laos" @selected(old('country') == 'Laos')>Laos</option>
                                        <option value="Latvia" @selected(old('country') == 'Latvia')>Latvia</option>
                                        <option value="Lebanon" @selected(old('country') == 'Lebanon')>Lebanon</option>
                                        <option value="Lesotho" @selected(old('country') == 'Lesotho')>Lesotho</option>
                                        <option value="Liberia" @selected(old('country') == 'Liberia')>Liberia</option>
                                        <option value="Libya" @selected(old('country') == 'Libya')>Libya</option>
                                        <option value="Liechtenstein" @selected(old('country') == 'Liechtenstein')>Liechtenstein</option>
                                        <option value="Lithuania" @selected(old('country') == 'Lithuania')>Lithuania</option>
                                        <option value="Luxembourg" @selected(old('country') == 'Luxembourg')>Luxembourg</option>
                                        <option value="Macau" @selected(old('country') == 'Macau')>Macau</option>
                                        <option value="Macedonia" @selected(old('country') == 'Macedonia')>Macedonia</option>
                                        <option value="Madagascar" @selected(old('country') == 'Madagascar')>Madagascar</option>
                                        <option value="Malaysia" @selected(old('country') == 'Malaysia')>Malaysia</option>
                                        <option value="Malawi" @selected(old('country') == 'Malawi')>Malawi</option>
                                        <option value="Maldives" @selected(old('country') == 'Maldives')>Maldives</option>
                                        <option value="Mali" @selected(old('country') == 'Mali')>Mali</option>
                                        <option value="Malta" @selected(old('country') == 'Malta')>Malta</option>
                                        <option value="Marshall Islands" @selected(old('country') == 'Marshall Islands')>Marshall Islands</option>
                                        <option value="Martinique" @selected(old('country') == 'Martinique')>Martinique</option>
                                        <option value="Mauritania" @selected(old('country') == 'Mauritania')>Mauritania</option>
                                        <option value="Mauritius" @selected(old('country') == 'Mauritius')>Mauritius</option>
                                        <option value="Mayotte" @selected(old('country') == 'Mayotte')>Mayotte</option>
                                        <option value="Mexico" @selected(old('country') == 'Mexico')>Mexico</option>
                                        <option value="Midway Islands" @selected(old('country') == 'Midway Islands')>Midway Islands</option>
                                        <option value="Moldova" @selected(old('country') == 'Moldova')>Moldova</option>
                                        <option value="Monaco" @selected(old('country') == 'Monaco')>Monaco</option>
                                        <option value="Mongolia" @selected(old('country') == 'Mongolia')>Mongolia</option>
                                        <option value="Montserrat" @selected(old('country') == 'Montserrat')>Montserrat</option>
                                        <option value="Morocco" @selected(old('country') == 'Morocco')>Morocco</option>
                                        <option value="Mozambique" @selected(old('country') == 'Mozambique')>Mozambique</option>
                                        <option value="Myanmar" @selected(old('country') == 'Myanmar')>Myanmar</option>
                                        <option value="Nambia" @selected(old('country') == 'Nambia')>Nambia</option>
                                        <option value="Nauru" @selected(old('country') == 'Nauru')>Nauru</option>
                                        <option value="Nepal" @selected(old('country') == 'Nepal')>Nepal</option>
                                        <option value="Netherland Antilles" @selected(old('country') == 'Netherland Antilles')>Netherland Antilles</option>
                                        <option value="Netherlands" @selected(old('country') == 'Netherlands')>Netherlands (Holland, Europe)</option>
                                        <option value="Nevis" @selected(old('country') == 'Nevis')>Nevis</option>
                                        <option value="New Caledonia" @selected(old('country') == 'New Caledonia')>New Caledonia</option>
                                        <option value="New Zealand" @selected(old('country') == 'New Zealand')>New Zealand</option>
                                        <option value="Nicaragua" @selected(old('country') == 'Nicaragua')>Nicaragua</option>
                                        <option value="Niger" @selected(old('country') == 'Niger')>Niger</option>
                                        <option value="Nigeria" @selected(old('country') == 'Nigeria')>Nigeria</option>
                                        <option value="Niue" @selected(old('country') == 'Niue')>Niue</option>
                                        <option value="Norfolk Island" @selected(old('country') == 'Norfolk Island')>Norfolk Island</option>
                                        <option value="Norway" @selected(old('country') == 'Norway')>Norway</option>
                                        <option value="Oman" @selected(old('country') == 'Oman')>Oman</option>
                                        <option value="Pakistan" @selected(old('country') == 'Pakistan')>Pakistan</option>
                                        <option value="Palau Island" @selected(old('country') == 'Palau Island')>Palau Island</option>
                                        <option value="Palestine" @selected(old('country') == 'Palestine')>Palestine</option>
                                        <option value="Panama" @selected(old('country') == 'Panama')>Panama</option>
                                        <option value="Papua New Guinea" @selected(old('country') == 'Papua New Guinea')>Papua New Guinea</option>
                                        <option value="Paraguay" @selected(old('country') == 'Paraguay')>Paraguay</option>
                                        <option value="Peru" @selected(old('country') == 'Peru')>Peru</option>
                                        <option value="Phillipines" @selected(old('country') == 'Phillipines')>Philippines</option>
                                        <option value="Pitcairn Island" @selected(old('country') == 'Pitcairn Island')>Pitcairn Island</option>
                                        <option value="Poland" @selected(old('country') == 'Poland')>Poland</option>
                                        <option value="Portugal" @selected(old('country') == 'Portugal')>Portugal</option>
                                        <option value="Puerto Rico" @selected(old('country') == 'Puerto Rico')>Puerto Rico</option>
                                        <option value="Qatar" @selected(old('country') == 'Qatar')>Qatar</option>
                                        <option value="Republic of Montenegro" @selected(old('country') == 'Republic of Montenegro')>Republic of Montenegro</option>
                                        <option value="Republic of Serbia" @selected(old('country') == 'Republic of Serbia')>Republic of Serbia</option>
                                        <option value="Reunion" @selected(old('country') == 'Reunion')>Reunion</option>
                                        <option value="Romania" @selected(old('country') == 'Romania')>Romania</option>
                                        <option value="Russia" @selected(old('country') == 'Russia')>Russia</option>
                                        <option value="Rwanda" @selected(old('country') == 'Rwanda')>Rwanda</option>
                                        <option value="St Barthelemy" @selected(old('country') == 'St Barthelemy')>St Barthelemy</option>
                                        <option value="St Eustatius" @selected(old('country') == 'St Eustatius')>St Eustatius</option>
                                        <option value="St Helena" @selected(old('country') == 'St Helena')>St Helena</option>
                                        <option value="St Kitts-Nevis" @selected(old('country') == 'St Kitts-Nevis')>St Kitts-Nevis</option>
                                        <option value="St Lucia" @selected(old('country') == 'St Lucia')>St Lucia</option>
                                        <option value="St Maarten" @selected(old('country') == 'St Maarten')>St Maarten</option>
                                        <option value="St Pierre &amp; Miquelon" @selected(old('country') == 'St Pierre & Miquelon')>St Pierre &amp; Miquelon</option>
                                        <option value="St Vincent &amp; Grenadines" @selected(old('country') == 'St Vincent & Grenadines')>St Vincent &amp; Grenadines</option>
                                        <option value="Saipan" @selected(old('country') == 'Saipan')>Saipan</option>
                                        <option value="Samoa" @selected(old('country') == 'Samoa')>Samoa</option>
                                        <option value="Samoa American" @selected(old('country') == 'Samoa American')>Samoa American</option>
                                        <option value="San Marino" @selected(old('country') == 'San Marino')>San Marino</option>
                                        <option value="Sao Tome &amp; Principe" @selected(old('country') == 'Sao Tome & Principe')>Sao Tome &amp; Principe</option>
                                        <option value="Saudi Arabia" @selected(old('country') == 'Saudi Arabia')>Saudi Arabia</option>
                                        <option value="Senegal" @selected(old('country') == 'Senegal')>Senegal</option>
                                        <option value="Seychelles" @selected(old('country') == 'Seychelles')>Seychelles</option>
                                        <option value="Sierra Leone" @selected(old('country') == 'Sierra Leone')>Sierra Leone</option>
                                        <option value="Singapore" @selected(old('country') == 'Singapore')>Singapore</option>
                                        <option value="Slovakia" @selected(old('country') == 'Slovakia')>Slovakia</option>
                                        <option value="Slovenia" @selected(old('country') == 'Slovenia')>Slovenia</option>
                                        <option value="Solomon Islands" @selected(old('country') == 'Solomon Islands')>Solomon Islands</option>
                                        <option value="Somalia" @selected(old('country') == 'Somalia')>Somalia</option>
                                        <option value="South Africa" @selected(old('country') == 'South Africa')>South Africa</option>
                                        <option value="Spain" @selected(old('country') == 'Spain')>Spain</option>
                                        <option value="Sri Lanka" @selected(old('country') == 'Sri Lanka')>Sri Lanka</option>
                                        <option value="Sudan" @selected(old('country') == 'Sudan')>Sudan</option>
                                        <option value="Suriname" @selected(old('country') == 'Suriname')>Suriname</option>
                                        <option value="Swaziland" @selected(old('country') == 'Swaziland')>Swaziland</option>
                                        <option value="Sweden" @selected(old('country') == 'Sweden')>Sweden</option>
                                        <option value="Switzerland" @selected(old('country') == 'Switzerland')>Switzerland</option>
                                        <option value="Syria" @selected(old('country') == 'Syria')>Syria</option>
                                        <option value="Tahiti" @selected(old('country') == 'Tahiti')>Tahiti</option>
                                        <option value="Taiwan" @selected(old('country') == 'Taiwan')>Taiwan</option>
                                        <option value="Tajikistan" @selected(old('country') == 'Tajikistan')>Tajikistan</option>
                                        <option value="Tanzania" @selected(old('country') == 'Tanzania')>Tanzania</option>
                                        <option value="Thailand" @selected(old('country') == 'Thailand')>Thailand</option>
                                        <option value="Togo" @selected(old('country') == 'Togo')>Togo</option>
                                        <option value="Tokelau" @selected(old('country') == 'Tokelau')>Tokelau</option>
                                        <option value="Tonga" @selected(old('country') == 'Tonga')>Tonga</option>
                                        <option value="Trinidad &amp; Tobago" @selected(old('country') == 'Trinidad & Tobago')>Trinidad &amp; Tobago</option>
                                        <option value="Tunisia" @selected(old('country') == 'Tunisia')>Tunisia</option>
                                        <option value="Turkey" @selected(old('country') == 'Turkey')>Turkey</option>
                                        <option value="Turkmenistan" @selected(old('country') == 'Turkmenistan')>Turkmenistan</option>
                                        <option value="Turks &amp; Caicos Is" @selected(old('country') == 'Turks & Caicos Is')>Turks &amp; Caicos Is</option>
                                        <option value="Tuvalu" @selected(old('country') == 'Tuvalu')>Tuvalu</option>
                                        <option value="Uganda" @selected(old('country') == 'Uganda')>Uganda</option>
                                        <option value="United Kingdom" @selected(old('country') == 'United Kingdom')>United Kingdom</option>
                                        <option value="Ukraine" @selected(old('country') == 'Ukraine')>Ukraine</option>
                                        <option value="United Arab Erimates" @selected(old('country') == 'United Arab Erimates')>United Arab Emirates</option>
                                        <option value="United States of America" @selected(old('country') == 'United States of America')>United States of America</option>
                                        <option value="Uraguay" @selected(old('country') == 'Uraguay')>Uruguay</option>
                                        <option value="Uzbekistan" @selected(old('country') == 'Uzbekistan')>Uzbekistan</option>
                                        <option value="Vanuatu" @selected(old('country') == 'Vanuatu')>Vanuatu</option>
                                        <option value="Vatican City State" @selected(old('country') == 'Vatican City State')>Vatican City State</option>
                                        <option value="Venezuela" @selected(old('country') == 'Venezuela')>Venezuela</option>
                                        <option value="Vietnam" @selected(old('country') == 'Vietnam')>Vietnam</option>
                                        <option value="Virgin Islands (Brit)" @selected(old('country') == 'Virgin Islands (Brit)')>Virgin Islands (Brit)</option>
                                        <option value="Virgin Islands (USA)" @selected(old('country') == 'Virgin Islands (USA)')>Virgin Islands (USA)</option>
                                        <option value="Wake Island" @selected(old('country') == 'Wake Island')>Wake Island</option>
                                        <option value="Wallis &amp; Futana Is" @selected(old('country') == 'Wallis & Futana Is')>Wallis &amp; Futana Is</option>
                                        <option value="Yemen" @selected(old('country') == 'Yemen')>Yemen</option>
                                        <option value="Zaire" @selected(old('country') == 'Zaire')>Zaire</option>
                                        <option value="Zambia" @selected(old('country') == 'Zambia')>Zambia</option>
                                        <option value="Zimbabwe" @selected(old('country') == 'Zimbabwe')>Zimbabwe</option>
                                    </select>
                                    <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                                </div>
                            </div>
                        </div>

                        {{-- City --}}
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('City')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                                <i class="mdi mdi-city-variant-outline text-lg text-primary"></i>
                                <input type="text" name="city" value="{{old('city')}}"
                                       placeholder="{{__('Enter city')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>

                    </div>

                    {{-- Row 4: State + Company --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                        {{-- State --}}
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('State')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                                <i class="mdi mdi-map-marker-radius-outline text-lg text-primary"></i>
                                <input type="text" name="state" value="{{old('state')}}"
                                       placeholder="{{__('Enter state')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>

                        {{-- Company --}}
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Company')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                                <i class="mdi mdi-office-building-outline text-lg text-primary"></i>
                                <input type="text" name="company" value="{{old('company')}}"
                                       placeholder="{{__('Enter company name')}}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>

                    </div>

                    {{-- Row 5: Address (full width) --}}
                    <div class="mb-6">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Address')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-map-marker-outline text-lg text-primary"></i>
                            <input type="text" name="address" value="{{old('address')}}"
                                   placeholder="{{__('Enter full address')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                </div>

                {{-- Row 6: Password + Confirm Password --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Password')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-lock-outline text-lg text-primary"></i>
                            <input type="password" name="password"
                                   placeholder="{{__('••••••••')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Confirm Password')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-lock-check-outline text-lg text-primary"></i>
                            <input type="password" name="password_confirmation"
                                   placeholder="{{__('••••••••')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                </div>

                <div class="pt-5 border-t border-main">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-account-plus text-base"></i> {{__('Add New Tenant')}}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
@endsection
