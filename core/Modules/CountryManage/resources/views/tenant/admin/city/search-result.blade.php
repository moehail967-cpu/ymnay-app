<div class="tw-table-wrap">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-main">
                <th class="px-5 py-3 w-10 no-sort">
                    <input type="checkbox" id="check_all_city"
                           class="w-4 h-4 rounded border-main text-primary focus:ring-primary cursor-pointer">
                </th>
                <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('City')}}</th>
                <th class="hidden sm:table-cell px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('State')}}</th>
                <th class="hidden md:table-cell px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Country')}}</th>
                <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
            </tr>
        </thead>
        <tbody>
        @forelse($all_cities as $city)
            <tr class="border-b border-main hover:bg-muted/40 transition-colors">
                <td class="px-5 py-3.5">
                    <input type="checkbox" name="ids[]" value="{{$city->id}}"
                           class="bulk-checkbox w-4 h-4 rounded border-main text-primary focus:ring-primary cursor-pointer">
                </td>
                <td class="px-5 py-3.5">
                    <span class="text-xs font-bold text-primary">#{{$city->id}}</span>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                            <i class="mdi mdi-city-variant text-warning text-sm"></i>
                        </div>
                        <span class="text-sm font-semibold text-dark">{{$city->name}}</span>
                    </div>
                </td>
                <td class="hidden sm:table-cell px-5 py-3.5">
                    <span class="text-xs font-medium text-dark">{{$city->state?->name}}</span>
                </td>
                <td class="hidden md:table-cell px-5 py-3.5">
                    <span class="text-xs font-medium text-dark">{{$city->country?->name}}</span>
                </td>
                <td class="px-5 py-3.5">
                    @if($city->status === 'publish')
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-semibold text-dark">{{__('Publish')}}</span>
                        </div>
                    @else
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <span class="text-xs font-semibold text-dark">{{__('Draft')}}</span>
                        </div>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end gap-1.5">
                        <button type="button" title="{{__('Edit')}}"
                                class="edit_city_modal w-8 h-8 rounded-lg bg-primary-soft border border-main flex items-center justify-center text-primary hover:bg-primary hover:text-white hover:border-primary transition-all"
                                data-city="{{$city->name}}"
                                data-city_id="{{$city->id}}"
                                data-state_id="{{$city->state_id}}"
                                data-country_id="{{$city->country_id}}">
                            <i class="mdi mdi-pencil-outline text-sm"></i>
                        </button>

                        <x-status-change-tw :title="__('Change Status')" :url="route('tenant.admin.city.status', $city->id)"/>

                        <button type="button" title="{{__('Delete')}}"
                                class="swal_delete_button w-8 h-8 rounded-lg bg-danger-soft border border-main flex items-center justify-center text-danger hover:bg-danger hover:text-white hover:border-danger transition-all">
                            <i class="mdi mdi-delete-outline text-sm"></i>
                        </button>
                        <form method="post" action="{{route('tenant.admin.city.delete', $city->id)}}" class="hidden">
                            @csrf
                            <button type="submit" class="swal_form_submit_btn"></button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-5 py-8 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <i class="mdi mdi-city-variant-outline text-3xl text-muted"></i>
                        <p class="text-sm text-muted">{{__('No cities found')}}</p>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@if($all_cities->hasPages())
    <div class="px-5 py-4 border-t border-main">
        {{ $all_cities->links() }}
    </div>
@endif
