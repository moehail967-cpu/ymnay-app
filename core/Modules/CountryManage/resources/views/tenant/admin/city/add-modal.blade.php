{{-- Add City Modal --}}
<div id="addModal" class="fixed inset-0 z-[800] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('addModal')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="bg-surface rounded-2xl shadow-xl border border-main w-full max-w-md pointer-events-auto">
            <div class="px-6 py-4 border-b border-main flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-success-soft flex items-center justify-center">
                        <i class="mdi mdi-city-variant text-success text-sm"></i>
                    </div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Add City')}}</h3>
                </div>
                <button type="button" onclick="closeModal('addModal')"
                        class="w-8 h-8 rounded-lg bg-secondary flex items-center justify-center text-muted hover:text-dark transition">
                    <i class="mdi mdi-close text-base"></i>
                </button>
            </div>
            <form action="{{route('tenant.admin.city.all')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('City Name')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-city-variant-outline text-lg text-primary"></i>
                            <input type="text" name="city" id="city" placeholder="{{__('Enter city name')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Country')}}</label>
                        <div class="bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <select name="country" id="country" class="w-full bg-transparent text-sm text-dark outline-none select2-country">
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
                            <select name="state" id="state" class="w-full bg-transparent text-sm text-dark outline-none get_country_state select2-state">
                                <option value="">{{__('Select State')}}</option>
                            </select>
                        </div>
                        <p class="info_msg text-xs mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-toggle-switch-outline text-lg text-primary"></i>
                            <select name="status" id="status"
                                    class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option value="publish">{{__('Publish')}}</option>
                                <option value="draft">{{__('Draft')}}</option>
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-main flex justify-end gap-2">
                    <button type="button" onclick="closeModal('addModal')"
                            class="px-4 py-2 rounded-xl bg-secondary border border-main text-sm font-semibold text-dark hover:bg-muted transition">
                        {{__('Cancel')}}
                    </button>
                    <button type="submit"
                            class="add_city inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-plus text-base"></i> {{__('Save')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
