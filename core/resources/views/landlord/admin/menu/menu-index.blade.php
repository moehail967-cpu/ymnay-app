@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Menus')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Menu List --}}
    <div class="lg:col-span-7">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-menu text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Menus')}}</h3>
                    <p class="text-xs text-muted">{{__('Manage your site navigation menus')}}</p>
                </div>
            </div>

            <div class="tw-table-wrap">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-main">
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Title')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                            <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Created')}}</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest text-right">{{__('Actions')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($all_menu as $data)
                        <tr class="border-b border-main hover:bg-muted transition-colors">
                            <td class="px-4 py-3.5">
                                <span class="text-[11px] font-bold text-primary">#{{$data->id}}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-sm font-semibold text-dark">{{$data->title}}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($data->status == 'default')
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-success-soft text-success text-[10px] font-bold uppercase">
                                        <i class="mdi mdi-check-circle text-xs"></i> {{__('Default')}}
                                    </span>
                                @else
                                    <form action="{{route(route_prefix().'admin.menu.default', $data->id)}}" method="post" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-secondary border border-main text-[11px] font-semibold text-dark hover:border-primary hover:text-primary transition set_default_menu">
                                            <i class="mdi mdi-star-outline text-xs"></i> {{__('Set Default')}}
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="hidden sm:table-cell px-4 py-3.5">
                                <span class="text-xs text-muted">{{$data->created_at->format('d M Y')}}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{route(route_prefix().'admin.menu.edit', $data->id)}}"
                                       title="{{__('Edit')}}"
                                       class="tw-btn-icon tw-btn-icon-view">
                                        <i class="mdi mdi-pencil-outline"></i>
                                    </a>
                                    @if($data->status != 'default')
                                        <x-delete-popover :url="route(route_prefix().'admin.menu.delete', $data->id)"/>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add New Menu --}}
    <div class="lg:col-span-5">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-plus-circle-outline text-info text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Add New Menu')}}</h3>
                    <p class="text-xs text-muted">{{__('Create a new navigation menu')}}</p>
                </div>
            </div>

            <form action="{{route(route_prefix().'admin.menu.new')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="p-4 sm:p-6">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Menu Title')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-format-title text-lg text-primary"></i>
                            <input type="text" name="title" placeholder="{{__('Enter menu title')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" required>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end px-4 sm:px-6 py-4 border-t border-main bg-secondary rounded-b-xl">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-plus text-base"></i> {{__('Create Menu')}}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection
