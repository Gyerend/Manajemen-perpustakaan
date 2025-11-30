{{-- File: resources/views/layouts/navigation.blade.php (Kode Lengkap) --}}
@php
    $user = Auth::user();
    // Mendapatkan URL Dashboard yang benar
    $dashboardUrl = $user ? App\Providers\RouteServiceProvider::getHomeRoute($user->role) : route('home');
    // Mendapatkan nama route untuk penanda aktif
    $dashboardRouteName = $user ? $user->role . '.dashboard' : 'home';
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ $dashboardUrl }}" class="flex items-center space-x-2 text-indigo-600 hover:text-indigo-800 transition duration-150">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.414 9.362 5 8 5c-4 0-4 4-4 4v7c0 4 4 4 4 4h8c4 0 4-4 4-4v-7c0-4-4-4-4-4 0 0-1.5-.414-3.5-1.253zM18 9h-4M8 9H6"></path></svg>
                        <span class="font-bold text-lg hidden sm:inline">{{ config('app.name', 'Perpus Digital') }}</span>
                    </a>
                </div>

                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex items-center">
                    {{-- Link Dashboard --}}
                    <x-nav-link :href="$dashboardUrl" :active="request()->routeIs($dashboardRouteName)">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @auth
                        @if ($user->isMahasiswa())
                            <x-nav-link :href="route('catalog.index')" :active="request()->routeIs('catalog.index')">
                                {{ __('Katalog Buku') }}
                            </x-nav-link>
                        @elseif ($user->isPegawai() || $user->isAdmin())
                            <x-nav-link :href="route('books.index')" :active="request()->routeIs('books.index')">
                                {{ __('Kelola Buku') }}
                            </x-nav-link>
                            <x-nav-link :href="route('pegawai.loans.pending')" :active="request()->routeIs('pegawai.loans.pending')">
                                {{ __('Proses Pinjaman') }}
                            </x-nav-link>
                        @endif
                        @if ($user->isAdmin())
                             <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')">
                                {{ __('Kelola User') }}
                            </x-nav-link>
                             <x-nav-link :href="route('admin.analytics.index')" :active="request()->routeIs('admin.analytics.index')">
                                {{ __('Analitik') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 9a2 2 0 01-2 2H5a2 2 0 01-2-2v-1a4 4 0 014-4h10a4 4 0 014 4v1z"></path></svg>
                                    <span>{{ Auth::user()->name }}</span>
                                </div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium">Login</a>
                    <a href="{{ route('register') }}" class="ml-4 text-gray-600 hover:text-gray-900 font-medium">Register</a>
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="$dashboardUrl" :active="request()->routeIs($dashboardRouteName)">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            {{-- Tambahan Link Responsif --}}
             @auth
                @if ($user->isMahasiswa())
                    <x-responsive-nav-link :href="route('catalog.index')" :active="request()->routeIs('catalog.index')">
                        {{ __('Katalog Buku') }}
                    </x-responsive-nav-link>
                @elseif ($user->isPegawai() || $user->isAdmin())
                    <x-responsive-nav-link :href="route('books.index')" :active="request()->routeIs('books.collection.index')">
                        {{ __('Kelola Buku') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pegawai.loans.pending')" :active="request()->routeIs('pegawai.loans.pending')">
                        {{ __('Proses Pinjaman') }}
                    </x-responsive-nav-link>
                @endif
                @if ($user->isAdmin())
                     <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')">
                        {{ __('Kelola User') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            @auth {{-- FIX: Membungkus bagian yang membutuhkan $user --}}
            <div class="px-4">
                {{-- FIX: Baris 58 ada di sekitar sini --}}
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
            @else
                <div class="px-4">
                    <x-responsive-nav-link :href="route('login')">Login</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">Register</x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>
