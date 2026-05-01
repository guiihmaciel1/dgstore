@props(['route', 'params' => []])
<a href="{{ route($route, $params) }}"
   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background: #16a34a; color: white; font-weight: 500; border-radius: 0.5rem; text-decoration: none; font-size: 0.875rem;"
   onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    CSV
</a>
