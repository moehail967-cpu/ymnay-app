@if($errors->any())
    <div class="mb-4 rounded-xl border-l-4 border-red-500 bg-red-50 px-4 py-3" style="border: 1px solid #fca5a5; border-left: 4px solid #ef4444;">
        <div class="flex items-start gap-2.5">
            <span class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-red-500 flex items-center justify-center">
                <i class="ti tabler-x text-white" style="font-size:0.65rem; font-weight:900;"></i>
            </span>
            <div class="flex-1">
                <p class="text-xs font-bold text-red-700 uppercase tracking-wide mb-1">{{__('Please fix the following errors')}}</p>
                <ul class="text-sm text-red-600 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li class="flex items-start gap-1.5"><span class="mt-1.5 w-1 h-1 rounded-full bg-red-400 flex-shrink-0"></span>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
