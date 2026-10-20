@php
    $name = strcasecmp((string) ($user->first_name ?? ''), 'Admin') === 0
        ? 'Admin'
        : (filled($user->full_name ?? null)
            ? $user->full_name
            : filament()->getUserName($user));
@endphp

@if (filled($name))
    <span
        class="fi-topbar-user-name text-sm font-medium text-white"
        style="max-width: 14rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
        title="{{ $name }}"
    >
        {{ $name }}
    </span>
@endif
