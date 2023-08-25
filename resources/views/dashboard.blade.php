<x-app-layout :title="__('Dashboard')">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <section class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="py-4 sm:py-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div>
                    <ul class="grid grid-cols-1 divide-y">
                        <li><a href="{{ route('user.index') }}" class="block px-6">Users</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>