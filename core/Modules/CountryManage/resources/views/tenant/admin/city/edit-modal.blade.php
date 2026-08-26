{{-- Edit City Modal --}}
<div id="editCityModal" class="fixed inset-0 z-[800] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('editCityModal')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="bg-surface rounded-2xl shadow-xl border border-main w-full max-w-md pointer-events-auto">
            <div class="px-6 py-4 border-b border-main flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center">
                        <i class="mdi mdi-city-variant text-primary text-sm"></i>
                    </div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Edit City')}}</h3>
                </div>
                <button type="button" onclick="closeModal('editCityModal')"
                        class="w-8 h-8 rounded-lg bg-secondary flex items-center justify-center text-muted hover:text-dark transition">
                    <i class="mdi mdi-close text-base"></i>
                </button>
            </div>
            <form action="{{route('tenant.admin.city.edit')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="city_id" id="city_id" value="">
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('City Name')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-city-variant-outline text-lg text-primary"></i>
                            <input type="text" name="city" id="city_name" placeholder="{{__('Enter city name')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Country')}}</label>
                        <div class="bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <select name="country" id="country_id" class="w-full bg-transparent text-sm text-dark outline-none select22-country">
                                <option value="">{{__('Select Country')}}</option>
                                @foreach($all_countries as $data)
                                    <option value="{{$data->id}}">{{$data->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('State')}}</label>
                        <div class="bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <select name="state" id="state_id" class="w-full bg-transparent text-sm text-dark outline-none get_country_state select22-state">
                                <option value="">{{__('Select State')}}</option>
                            </select>
                        </div>
                        <span class="info_msg text-xs mt-1"></span>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-main flex justify-end gap-2">
                    <button type="button" onclick="closeModal('editCityModal')"
                            class="px-4 py-2 rounded-xl bg-secondary border border-main text-sm font-semibold text-dark hover:bg-muted transition">
                        {{__('Cancel')}}
                    </button>
                    <button type="submit"
                            class="edit_city inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-check text-base"></i> {{__('Update')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
