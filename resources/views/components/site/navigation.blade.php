<nav x-data="{ open: false }" class="bg-main2 border-b border-third">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard.index') }}" class="c-logo__container">
                        <x-application-logo class="block h-9 w-auto fill-current" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 lg:-my-px lg:ms-10 lg:flex">
                    <x-links.nav-link id="navigation-panel-control" :shepherd-text="trans('shepherd.navigation-panel-control')" :href="route('dashboard.index')" :active="request()->routeIs(['dashboard.index', 'dashboard.show'])">
                        {{ __('Dashboard') }}
                    </x-links.nav-link>
                </div>

                <div class="hidden space-x-8 lg:-my-px lg:ms-10 lg:flex">
                    <x-links.nav-link id="navigation-history" :shepherd-text="trans('shepherd.navigation-history')" :href="route('dashboard.history')" :active="request()->routeIs('dashboard.history')">
                        {{ __('See History') }}
                    </x-links.nav-link>
                </div>

                <div class="hidden space-x-8 lg:-my-px lg:ms-10 lg:flex">
                    <x-links.nav-link id="navigation-forecast" :shepherd-text="trans('shepherd.navigation-forecast')" :href="route('dashboard.forecast')" :active="request()->routeIs(['dashboard.forecast', 'dashboard.forecastShow'])">
                        {{ __('Forecast') }}
                    </x-links.nav-link>
                </div>

                <div class="hidden space-x-8 lg:-my-px lg:ms-10 lg:flex">
                    <x-links.nav-link id="navigation-clock" :shepherd-text="trans('shepherd.navigation-clock')" :href="route('dashboard.clock')" :active="request()->routeIs('dashboard.clock')">
                        {{ __('Clock') }}
                    </x-links.nav-link>
                </div>

                <div class="hidden space-x-8 lg:-my-px lg:ms-10 lg:flex">
                    <x-links.nav-link id="navigation-configuration" :shepherd-text="trans('shepherd.navigation-configuration')" :href="route('dashboard.requests')" :active="request()->routeIs('dashboard.requests')">
                        {{ __('Requests') }}
                    </x-links.nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden lg:flex gap-4 lg:items-center lg:ms-6">
                @if (request()->routeIs(['dashboard.index', 'dashboard.show']))
                    <x-ui.month-selector id="navigation-month-selector" :shepherd-text="trans('shepherd.navigation-month-selector')" class="lg:flex lg:items-center lg:ms-6" />
                @endif

                <x-ui.lang-selector id="navigation-lang-selector" :shepherd-text="trans('shepherd.navigation-lang-selector')" class="lg:flex lg:items-center" />

                <x-inputs.dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button id="navigation-user-dropdown" shepherd-text="{{trans('shepherd.navigation-user-dropdown')}}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-secondary bg-main1 hover:text-primary transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if (!request()->routeIs(['profile.edit', 'profile.configuration.edit', 'profile.accounts.edit', 'profile.import.edit', 'profile.export.edit', 'profile.categories']))
                            <x-inputs.dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-inputs.dropdown-link>

                            <x-inputs.dropdown-link :href="route('profile.accounts.edit')">
                                {{ __('Accounts') }}
                            </x-inputs.dropdown-link>

                            <x-inputs.dropdown-link :href="route('profile.categories')">
                                {{ __('Categories') }}
                            </x-inputs.dropdown-link>

                            <x-inputs.dropdown-link :href="route('profile.configuration.edit')">
                                {{ __('Configuration') }}
                            </x-inputs.dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-inputs.dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-inputs.dropdown-link>
                        </form>
                    </x-slot>
                </x-inputs.dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center lg:hidden">
                <div class="pt-4 pb-3 px-4 flex -md:gap-2">
                    @foreach(config('app.supported_locales') as $lang)
                        <x-inputs.dropdown-link href="{{ route('lang.switch', $lang) }}" :active="app()->getLocale() == $lang" class="rounded-lg uppercase md:!w-12 c-lang">
                            {{ $lang }}
                        </x-inputs.dropdown-link>
                    @endforeach
                </div>

                <x-ui.month-selector class="lg:flex lg:items-center lg:ms-6" />

                <button @click="open = ! open" class="line-through inline-flex items-center justify-center p-2 rounded-md text-secondary hover:text-primary transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-links.responsive-nav-link :href="route('dashboard.index')" :active="request()->routeIs(['dashboard.index', 'dashboard.show'])">
                {{ __('Dashboard') }}
            </x-links.responsive-nav-link>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-links.responsive-nav-link :href="route('dashboard.history')" :active="request()->routeIs('dashboard.history')">
                {{ __('See History') }}
            </x-links.responsive-nav-link>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-links.responsive-nav-link :href="route('dashboard.forecast')" :active="request()->routeIs(['dashboard.forecast', 'dashboard.forecastShow'])">
                {{ __('Forecast') }}
            </x-links.responsive-nav-link>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-links.responsive-nav-link :href="route('dashboard.clock')" :active="request()->routeIs('dashboard.clock')">
                {{ __('Clock') }}
            </x-links.responsive-nav-link>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-links.responsive-nav-link :href="route('dashboard.requests')" :active="request()->routeIs('dashboard.requests')">
                {{ __('Requests') }}
            </x-links.responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-third">
            <div class="px-4">
                <div class="font-medium text-base text-primary">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-secondary">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                @if (!request()->routeIs(['profile.edit', 'profile.configuration.edit', 'profile.accounts.edit', 'profile.import.edit', 'profile.export.edit', 'profile.categories']))
                    <x-links.responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-links.responsive-nav-link>

                    <x-links.responsive-nav-link :href="route('profile.accounts.edit')">
                        {{ __('Accounts') }}
                    </x-links.responsive-nav-link>

                    <x-links.responsive-nav-link :href="route('profile.categories')">
                        {{ __('Categories') }}
                    </x-links.responsive-nav-link>

                    <x-links.responsive-nav-link :href="route('profile.configuration.edit')">
                        {{ __('Configuration') }}
                    </x-links.responsive-nav-link>
                @endif


                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-links.responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-links.responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
