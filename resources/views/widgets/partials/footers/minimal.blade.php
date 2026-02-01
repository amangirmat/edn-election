<footer class="election-footer-simple" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; margin-top: 15px; border-top: 1px solid #f0f0f0;">

    {{-- Right side: Clean Timestamp --}}
    <div class="footer-timestamp" style="font-size: 0.7rem; color: #999; display: flex; align-items: center;">
        <svg xmlns="http://www.w3.org/2000/svg" style="height: 12px; width: 12px; margin-right: 4px; opacity: 0.6;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Updated: {{ $lastUpdated }}
    </div>
</footer>