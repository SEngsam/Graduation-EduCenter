@php($size = $size ?? 22)
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $stroke ?? 2 }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@switch($name)
    @case('cap')
        <path d="M2 7l10-4 10 4-10 4-10-4z"/><path d="M6 9v5c0 1.7 2.7 3 6 3s6-1.3 6-3V9"/>
        @break
    @case('flask')
        <path d="M9 3h6"/><path d="M10 3v6L4.5 18.5A1.7 1.7 0 006 21h12a1.7 1.7 0 001.5-2.5L14 9V3"/>
        @break
    @case('palette')
        <path d="M12 21a9 9 0 110-18c5 0 9 3.6 9 8 0 2.5-2 4-4.5 4H15a2 2 0 00-1.5 3.3A1.6 1.6 0 0112 21z"/><circle cx="7.5" cy="11" r="1"/><circle cx="11" cy="7" r="1"/><circle cx="16" cy="8" r="1"/>
        @break
    @case('screen')
        <rect x="3" y="4" width="18" height="12" rx="2"/><path d="M8 20h8"/><path d="M12 16v4"/>
        @break
    @case('book')
        <path d="M4 5a2 2 0 012-2h13v16H6a2 2 0 00-2 2V5z"/><path d="M4 19a2 2 0 002 2h13"/>
        @break
    @case('check')
        <path d="M5 12l5 5L20 7"/>
        @break
    @case('pin')
        <path d="M12 21s-7-6.2-7-11a7 7 0 0114 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>
        @break
    @case('news')
        <path d="M4 4h12a4 4 0 014 4v12H8a4 4 0 01-4-4V4z"/><path d="M8 9h8"/><path d="M8 13h8"/><path d="M8 17h5"/>
        @break
    @case('menu')
        <path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h16"/>
        @break
    @case('user')
        <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/>
        @break
@endswitch
</svg>
