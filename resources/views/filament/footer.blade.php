<style>
    @media (min-width: 64rem) {
        .fi-panel-footer {
            position: relative;
            z-index: 21;
            margin-inline-start: calc(-1 * var(--sidebar-width));
            width: calc(100% + var(--sidebar-width));
        }
    }
</style>

<footer class="fi-panel-footer w-full p-4 bg-white border-t border-gray-200 shadow md:flex md:items-center md:justify-end md:p-6 dark:bg-gray-800 dark:border-gray-600"
    style="text-align: right;">
    <span class="text-xs text-right text-gray-500 dark:text-gray-400" style="font-size: 12px; margin-right: 10px;">© {{ date('Y') }}
        <a href="#" class="hover:underline">{{ config('app.name') }} Grading System</a>. All Rights Reserved.
    </span>
</footer>
