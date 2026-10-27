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
    @case('users')
        <circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><path d="M16 4.5a3.5 3.5 0 010 7"/><path d="M18 14c2.2.6 3.5 2.8 3.5 6"/>
        @break
    @case('calendar')
        <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18"/><path d="M8 3v4"/><path d="M16 3v4"/>
        @break
    @case('clock')
        <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
        @break
    @case('phone')
        <path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2"/>
        @break
    @case('mail')
        <rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>
        @break
    @case('search')
        <circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>
        @break
    @case('bell')
        <path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/>
        @break
    @case('logout')
        <path d="M15 4h3a2 2 0 012 2v12a2 2 0 01-2 2h-3"/><path d="M10 17l-5-5 5-5"/><path d="M5 12h11"/>
        @break
    @case('grid')
        <rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>
        @break
    @case('alert')
        <circle cx="12" cy="12" r="9"/><path d="M12 8v5"/><path d="M12 16.5v.5"/>
        @break
    @case('moon')
        <path d="M21 12.8A9 9 0 1111.2 3a7 7 0 009.8 9.8z"/>
        @break
    @case('sun')
        <circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M4.9 4.9l1.4 1.4"/><path d="M17.7 17.7l1.4 1.4"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="M4.9 19.1l1.4-1.4"/><path d="M17.7 6.3l1.4-1.4"/>
        @break
    @case('plus')
        <path d="M12 5v14"/><path d="M5 12h14"/>
        @break
    @case('play')
        <rect x="3" y="6" width="13" height="12" rx="2"/><path d="M16 10l5-3v10l-5-3"/>
        @break
    @case('file')
        <path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8z"/><path d="M14 3v5h5"/>
        @break
    @case('link')
        <path d="M10 14a4 4 0 005.7 0l3-3a4 4 0 00-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 00-5.7 0l-3 3a4 4 0 005.7 5.7l1-1"/>
        @break
    @case('clip')
        <rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V3h6v1"/><path d="M9 10h6"/><path d="M9 14h6"/><path d="M9 18h3"/>
        @break
    @case('upload')
        <path d="M12 16V4"/><path d="M7 9l5-5 5 5"/><path d="M4 16v3a2 2 0 002 2h12a2 2 0 002-2v-3"/>
        @break
    @case('download')
        <path d="M12 4v12"/><path d="M7 11l5 5 5-5"/><path d="M4 16v3a2 2 0 002 2h12a2 2 0 002-2v-3"/>
        @break
    @case('lock')
        <rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/>
        @break
    @case('message')
        <path d="M4 5h16v11H8l-4 4V5z"/>
        @break
    @case('qr')
        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3z"/><path d="M20 14v1"/><path d="M14 20h1"/><path d="M18 18h3v3"/>
        @break
@endswitch
</svg>
