<header class="election-header-minimal" style="padding: 10px 0; border-bottom: 1px solid #eee; margin-bottom: 15px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        {{-- Left Side: Title and Breadcrumbs --}}
        <div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #1a1a1a;">
                {{ $displayTitle }}
            </h3>
            
            <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; margin-top: 2px;">
                @if($scope === 'woreda')
                    {{ $regionName ?? '' }} &raquo; {{ $parentTitle ?? '' }}
                @elseif($scope === 'zone')
                    {{ $parentTitle ?? '' }} Region
                @else
                    National Election Results
                @endif
            </div>
        </div>

        {{-- Right Side: Discrete Status Dot --}}
        <div class="status-indicator">
            @php $status = strtolower((string)$election->status); @endphp
            @if(in_array($status, ['ongoing', 'published', '1', 'live']))
                <span style="display: flex; align-items: center; font-size: 0.7rem; font-weight: 700; color: #d32f2f; text-transform: uppercase;">
                    <span class="pulse-dot" style="height: 8px; width: 8px; background-color: #d32f2f; border-radius: 50%; display: inline-block; margin-right: 5px;"></span>
                    Live
                </span>
            @else
                <span style="font-size: 0.7rem; font-weight: 600; color: #666; text-transform: uppercase; background: #f0f0f0; padding: 2px 6px; border-radius: 4px;">
                    {{ $status }}
                </span>
            @endif
        </div>
    </div>
</header>