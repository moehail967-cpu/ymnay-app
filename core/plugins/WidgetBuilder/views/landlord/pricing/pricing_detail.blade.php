<section style="background-color: var(--section-bg-3, #F4F8FB); padding: 60px 0">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h3 class="font-urbanist font-semibold text-secondary text-2xl sm:text-3xl md:text-4xl lg:text-5xl mt-4 lg:mt-6 leading-7 lg:leading-[3.25rem] mb-4">
                {{ $title }}
            </h3>
            <span class="font-normal select-none" style="color: var(--body-color, #666666)">{{ $subtitle }}</span>
        </div>

        <div class="rounded-2xl border overflow-hidden my-14" style="background-color: var(--section-bg-1, #FFFFFF); border-color: var(--extra-light-color, #e5e7eb)">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-sm">

                    <!-- Header -->
                    <thead>
                    <tr style="background-color: {{ $headerBg }};" class="text-white">
                        <th class="py-5 px-6 text-left font-semibold text-base tracking-wide">
                            Features
                        </th>
                        @foreach($planNames as $name)
                        <th class="py-5 px-6 font-semibold text-base tracking-wide">
                            {{ $name }}
                        </th>
                        @endforeach
                    </tr>
                    </thead>

                    <tbody class="divide-y" style="border-color: var(--extra-light-color, #e5e7eb)">
                    @foreach($featureRows as $index => $row)
                    <tr class="transition-colors" style="{{ $index % 2 === 0 ? 'background-color: var(--section-bg-3, #F4F8FB)' : '' }}">
                        <td class="py-4 px-6 font-medium text-secondary">{{ $row['name'] }}</td>
                        @foreach($row['values'] as $value)
                        <td class="py-4 px-6 text-center">
                            @if($row['type'] === 'check')
                                @if($value === '✓')
                                    <span class="text-2xl text-primary">✓</span>
                                @elseif($value === '✕')
                                    <span class="text-xl" style="color: var(--extra-light-color, #9ca3af)">✕</span>
                                @else
                                    <span class="text-sm text-secondary">{{ $value }}</span>
                                @endif
                            @else
                                <span class="font-medium text-secondary">{{ $value }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
