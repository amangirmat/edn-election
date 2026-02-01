<header class="election-header-sleek" style="padding: 25px 0; text-align: center; font-family: 'Inter', sans-serif;">
    {{-- Top Meta Row: Minimalist Status --}}
    <div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-bottom: 8px;">
        @php $status = strtolower((string)$election->status); @endphp
        
        @if(in_array($status, ['ongoing', 'published', '1', 'live']))
            <div style="display: flex; align-items: center; background: rgba(211, 47, 47, 0.08); padding: 4px 12px; border-radius: 100px;">
                <span class="pulse-dot" style="height: 6px; width: 6px; background-color: #d32f2f; border-radius: 50%; display: inline-block; margin-right: 6px;"></span>
                <span style="color: #d32f2f; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;">Live Updates</span>
            </div>
        @endif
        
        <span style="font-size: 0.75rem; color: #999; font-weight: 400;">
            {{ $election->name }}
        </span>
    </div>

    {{-- The Title: Clean & Bold --}}
    <h2 style="margin: 0; font-size: 1.8rem; font-weight: 300; color: #111; letter-spacing: -0.02em;">
        <span style="font-weight: 800;">{{ $displayTitle }}</span> Result
    </h2>

    {{-- Sub-Navigation: Sophisticated Breadcrumbs --}}
    <div style="margin-top: 10px; display: flex; justify-content: center; align-items: center; font-size: 0.85rem; color: #666;">
        @if($scope === 'woreda')
            <span style="background: #f8f9fa; padding: 2px 10px; border-radius: 4px;">{{ $regionName }}</span>
            <span style="margin: 0 8px; color: #ccc;">/</span>
            <span style="background: #f8f9fa; padding: 2px 10px; border-radius: 4px;">{{ $parentTitle }}</span>
        @elseif($scope === 'zone')
            <span style="color: #888;">Part of</span> 
            <strong style="margin-left: 5px; color: #333;">{{ $parentTitle }} Region</strong>
        @else
            <span style="letter-spacing: 0.1em; text-transform: uppercase; font-size: 0.7rem; opacity: 0.6;">National Dashboard</span>
        @endif
    </div>

    {{-- Subtle underline accent --}}
    <div style="width: 40px; height: 3px; background: #000; margin: 20px auto 0; border-radius: 2px;"></div>
</header>