@if(session()->has('msg'))
    @php
        $type = session('type');
        $map = match($type) {
            'success' => [
                'bg'         => '#f0fdf4',
                'border'     => '#86efac',
                'accent'     => '#22c55e',
                'text'       => '#15803d',
                'label_text' => '#166534',
                'label'      => 'Success',
                'icon'       => 'tabler-circle-check',
                'icon_bg'    => '#22c55e',
            ],
            'danger' => [
                'bg'         => '#fff1f2',
                'border'     => '#fca5a5',
                'accent'     => '#ef4444',
                'text'       => '#dc2626',
                'label_text' => '#991b1b',
                'label'      => 'Error',
                'icon'       => 'tabler-alert-circle',
                'icon_bg'    => '#ef4444',
            ],
            'warning' => [
                'bg'         => '#fffbeb',
                'border'     => '#fcd34d',
                'accent'     => '#f59e0b',
                'text'       => '#b45309',
                'label_text' => '#92400e',
                'label'      => 'Warning',
                'icon'       => 'tabler-alert-triangle',
                'icon_bg'    => '#f59e0b',
            ],
            'info' => [
                'bg'         => '#eff6ff',
                'border'     => '#93c5fd',
                'accent'     => '#3b82f6',
                'text'       => '#1d4ed8',
                'label_text' => '#1e40af',
                'label'      => 'Info',
                'icon'       => 'tabler-info-circle',
                'icon_bg'    => '#3b82f6',
            ],
            default => [
                'bg'         => '#f9fafb',
                'border'     => '#d1d5db',
                'accent'     => '#6b7280',
                'text'       => '#374151',
                'label_text' => '#111827',
                'label'      => 'Notice',
                'icon'       => 'tabler-info-circle',
                'icon_bg'    => '#6b7280',
            ],
        };
    @endphp
    <div class="mb-4 rounded-xl px-4 py-3"
         style="background:{{ $map['bg'] }}; border: 1px solid {{ $map['border'] }}; border-left: 4px solid {{ $map['accent'] }};">
        <div class="flex items-start gap-2.5">
            <span class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center"
                  style="background:{{ $map['icon_bg'] }};">
                <i class="ti {{ $map['icon'] }} text-white" style="font-size:0.7rem;"></i>
            </span>
            <div class="flex-1">
                <p class="text-xs font-bold uppercase tracking-wide mb-0.5" style="color:{{ $map['label_text'] }};">{{ __($map['label']) }}</p>
                <span class="text-sm" style="color:{{ $map['text'] }};">{!! Purifier::clean(session('msg')) !!}</span>
            </div>
        </div>
    </div>
@endif
