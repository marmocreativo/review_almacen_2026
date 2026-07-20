<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="rq">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'RQ Almacen') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-base-200 min-h-screen">

        <div x-data="{ sidebarOpen: false, collapsed: false }" class="flex min-h-screen">

            {{-- ===================== OFFCANVAS MÓVIL ===================== --}}
            {{-- Overlay --}}
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sidebarOpen = false"
                class="fixed inset-0 bg-black/50 z-40 lg:hidden">
            </div>

            {{-- Drawer offcanvas móvil --}}
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="fixed top-0 left-0 h-screen w-72 bg-primary flex flex-col z-50 lg:hidden shadow-2xl">

                {{-- Header offcanvas --}}
                <div class="flex items-center justify-between bg-white border-b border-base-300 h-16 px-4">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/logo_menu.svg') }}" alt="{{ config('app.name') }}" class="h-12 w-auto">
                    </a>
                    <button @click="sidebarOpen = false" class="btn btn-ghost btn-square btn-sm">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                {{-- Nav offcanvas --}}
                <ul class="menu flex-1 px-2 py-4 gap-1 overflow-y-auto
                    [&_a]:text-primary-content [&_a]:hover:bg-primary-content/20
                    [&_a.active]:bg-primary-content/30 [&_a.active]:text-primary-content
                    [&_.menu-title]:text-primary-content/60">

                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <x-heroicon-o-home class="w-5 h-5 flex-shrink-0" />
                            Dashboard
                        </a>
                    </li>

                    <li class="menu-title mt-2">Almacén</li>

                    <li>
                        <a href="{{ route('admin.articulos.index') }}" class="{{ request()->routeIs('admin.articulos*') ? 'active' : '' }}">
                            <x-heroicon-o-cube class="w-5 h-5 flex-shrink-0" />
                            Inventario
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.solicitudes.index') }}" class="{{ request()->routeIs('admin.solicitudes*') ? 'active' : '' }}">
                            <x-heroicon-o-clipboard-document-list class="w-5 h-5 flex-shrink-0" />
                            Solicitudes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.envios.index') }}" class="{{ request()->routeIs('admin.envios*') ? 'active' : '' }}">
                            <x-heroicon-o-truck class="w-5 h-5 flex-shrink-0" />
                            Envíos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.devoluciones.index') }}" class="{{ request()->routeIs('admin.devoluciones*') ? 'active' : '' }}">
                            <x-heroicon-o-arrow-uturn-left class="w-5 h-5 flex-shrink-0" />
                            Devoluciones
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.facturacion.index') }}" class="{{ request()->routeIs('admin.facturacion*') ? 'active' : '' }}">
                            <x-heroicon-o-document-currency-dollar class="w-5 h-5 flex-shrink-0" />
                            Facturación
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.cobranza.index') }}" class="{{ request()->routeIs('admin.cobranza*') ? 'active' : '' }}">
                            <x-heroicon-o-banknotes class="w-5 h-5 flex-shrink-0" />
                            Cobranza
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.destruccion.index') }}" class="{{ request()->routeIs('admin.destruccion*') ? 'active' : '' }}">
                            <x-heroicon-o-trash class="w-5 h-5 flex-shrink-0" />
                            Destrucción
                        </a>
                    </li>

                    <li class="menu-title mt-2">Administración</li>

                    <li>
                        <a href="{{ route('admin.empresas.index') }}" class="{{ request()->routeIs('admin.empresas*') ? 'active' : '' }}">
                            <x-heroicon-o-building-office-2 class="w-5 h-5 flex-shrink-0" />
                            Empresas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tipo-examenes.index') }}" class="{{ request()->routeIs('admin.tipo-examenes*') ? 'active' : '' }}">
                            <x-heroicon-o-document-text class="w-5 h-5 flex-shrink-0" />
                            Tipos de examen
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.usuarios.index') }}" class="{{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}">
                            <x-heroicon-o-users class="w-5 h-5 flex-shrink-0" />
                            Usuarios
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles*') ? 'active' : '' }}">
                            <x-heroicon-o-shield-check class="w-5 h-5 flex-shrink-0" />
                            Roles
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.importacion.index') }}" class="{{ request()->routeIs('admin.importacion*') ? 'active' : '' }}">
                            <x-heroicon-o-arrow-up-tray class="w-5 h-5 flex-shrink-0" />
                            Importación
                        </a>
                    </li>
                </ul>
                {{-- Usuario offcanvas --}}
                <div class="border-t border-primary/20 p-3">
                    <div class="flex items-center gap-3 mb-2 px-1">
                        <div class="avatar placeholder flex-shrink-0">
                            <div class="bg-primary-content/20 text-primary-content rounded-full w-8 flex items-center justify-center">
                                <span class="text-xs">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate text-primary-content">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-primary-content/60 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm w-full justify-start gap-2 text-primary-content hover:bg-primary-content/20">
                            <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 flex-shrink-0" />
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>

            {{-- ===================== SIDEBAR DESKTOP ===================== --}}
            <aside
                class="hidden lg:flex flex-col fixed top-0 left-0 h-screen bg-primary border-r border-primary/20 z-40 transition-all duration-300 ease-in-out"
                :class="collapsed ? 'w-16' : 'w-64'">

                {{-- Logo / Collapse --}}
                <div class="flex items-center bg-white border-b border-base-300 h-16 px-3 gap-2 overflow-hidden">
                    <button @click="collapsed = !collapsed" class="btn btn-ghost btn-square btn-sm flex-shrink-0">
                        <x-heroicon-o-bars-3 class="w-5 h-5" />
                    </button>
                    <a x-show="!collapsed" x-transition.opacity href="{{ route('dashboard') }}" class="truncate">
                        <img src="{{ asset('images/logo_menu.svg') }}" alt="{{ config('app.name') }}" class="h-12 w-auto">
                    </a>
                </div>

                {{-- Nav desktop --}}
                <ul class="menu flex-1 px-2 py-4 gap-1 overflow-y-auto overflow-x-hidden
                    [&_a]:text-primary-content [&_a]:hover:bg-primary-content/20
                    [&_a.active]:bg-primary-content/30 [&_a.active]:text-primary-content
                    [&_.menu-title]:text-primary-content/60">

                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            :class="collapsed ? 'justify-center' : ''"
                            :title="collapsed ? 'Dashboard' : ''">
                            <x-heroicon-o-home class="w-5 h-5 flex-shrink-0" />
                            <span x-show="!collapsed" x-transition.opacity class="truncate">Dashboard</span>
                        </a>
                    </li>

                    <li x-show="!collapsed" class="menu-title mt-2">Almacén</li>
                    <li x-show="collapsed" class="divider my-1"></li>

                    <li>
                        <a href="{{ route('admin.articulos.index') }}"
                            class="{{ request()->routeIs('admin.articulos*') ? 'active' : '' }}"
                            :class="collapsed ? 'justify-center' : ''"
                            :title="collapsed ? 'Inventario' : ''">
                            <x-heroicon-o-cube class="w-5 h-5 flex-shrink-0" />
                            <span x-show="!collapsed" x-transition.opacity class="truncate">Inventario</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.solicitudes.index') }}"
                            class="{{ request()->routeIs('admin.solicitudes*') ? 'active' : '' }}"
                            :class="collapsed ? 'justify-center' : ''"
                            :title="collapsed ? 'Solicitudes' : ''">
                            <x-heroicon-o-clipboard-document-list class="w-5 h-5 flex-shrink-0" />
                            <span x-show="!collapsed" x-transition.opacity class="truncate">Solicitudes</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.envios.index') }}"
                            class="{{ request()->routeIs('admin.envios*') ? 'active' : '' }}"
                            :class="collapsed ? 'justify-center' : ''"
                            :title="collapsed ? 'Envíos' : ''">
                            <x-heroicon-o-truck class="w-5 h-5 flex-shrink-0" />
                            <span x-show="!collapsed" x-transition.opacity class="truncate">Envíos</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.devoluciones.index') }}"
                            class="{{ request()->routeIs('admin.devoluciones*') ? 'active' : '' }}"
                            :class="collapsed ? 'justify-center' : ''"
                            :title="collapsed ? 'Devoluciones' : ''">
                            <x-heroicon-o-arrow-uturn-left class="w-5 h-5 flex-shrink-0" />
                            <span x-show="!collapsed" x-transition.opacity class="truncate">Devoluciones</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.facturacion.index') }}"
                            class="{{ request()->routeIs('admin.facturacion*') ? 'active' : '' }}"
                            :class="collapsed ? 'justify-center' : ''"
                            :title="collapsed ? 'Facturación' : ''">
                            <x-heroicon-o-document-currency-dollar class="w-5 h-5 flex-shrink-0" />
                            <span x-show="!collapsed" x-transition.opacity class="truncate">Facturación</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.cobranza.index') }}"
                            class="{{ request()->routeIs('admin.cobranza*') ? 'active' : '' }}"
                            :class="collapsed ? 'justify-center' : ''"
                            :title="collapsed ? 'Cobranza' : ''">
                            <x-heroicon-o-banknotes class="w-5 h-5 flex-shrink-0" />
                            <span x-show="!collapsed" x-transition.opacity class="truncate">Cobranza</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.destruccion.index') }}"
                            class="{{ request()->routeIs('admin.destruccion*') ? 'active' : '' }}"
                            :class="collapsed ? 'justify-center' : ''"
                            :title="collapsed ? 'Destrucción' : ''">
                            <x-heroicon-o-trash class="w-5 h-5 flex-shrink-0" />
                            <span x-show="!collapsed" x-transition.opacity class="truncate">Destrucción</span>
                        </a>
                    </li>

                    <li x-show="!collapsed" class="menu-title mt-2">Administración</li>
                    <li x-show="collapsed" class="divider my-1"></li>

                    <li>
                        <a href="{{ route('admin.empresas.index') }}"
                            class="{{ request()->routeIs('admin.empresas*') ? 'active' : '' }}"
                            :class="collapsed ? 'justify-center' : ''"
                            :title="collapsed ? 'Empresas' : ''">
                            <x-heroicon-o-building-office-2 class="w-5 h-5 flex-shrink-0" />
                            <span x-show="!collapsed" x-transition.opacity class="truncate">Empresas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tipo-examenes.index') }}"
                            class="{{ request()->routeIs('admin.tipo-examenes*') ? 'active' : '' }}"
                            :class="collapsed ? 'justify-center' : ''"
                            :title="collapsed ? 'Tipos de examen' : ''">
                            <x-heroicon-o-document-text class="w-5 h-5 flex-shrink-0" />
                            <span x-show="!collapsed" x-transition.opacity class="truncate">Tipos de examen</span>
                        </a>
                    </li>
                    <li x-show="!collapsed" class="menu-title mt-2">Accesos</li>
                    <li x-show="collapsed" class="divider my-1"></li>
                    <li>
                        <a href="{{ route('admin.usuarios.index') }}"
                            class="{{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}"
                            :class="collapsed ? 'justify-center' : ''"
                            :title="collapsed ? 'Usuarios' : ''">
                            <x-heroicon-o-users class="w-5 h-5 flex-shrink-0" />
                            <span x-show="!collapsed" x-transition.opacity class="truncate">Usuarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.roles.index') }}"
                            class="{{ request()->routeIs('admin.roles*') ? 'active' : '' }}"
                            :class="collapsed ? 'justify-center' : ''"
                            :title="collapsed ? 'Roles' : ''">
                            <x-heroicon-o-shield-check class="w-5 h-5 flex-shrink-0" />
                            <span x-show="!collapsed" x-transition.opacity class="truncate">Roles</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.importacion.index') }}"
                            class="{{ request()->routeIs('admin.importacion*') ? 'active' : '' }}"
                            :class="collapsed ? 'justify-center' : ''"
                            :title="collapsed ? 'Importación' : ''">
                            <x-heroicon-o-arrow-up-tray class="w-5 h-5 flex-shrink-0" />
                            <span x-show="!collapsed" x-transition.opacity class="truncate">Importación</span>
                        </a>
                    </li>
                </ul>

                {{-- Usuario desktop --}}
                <div class="border-t border-primary/20 p-3">
                    <div x-show="!collapsed" x-transition.opacity class="flex items-center gap-3 mb-2 px-1">
                        <div class="avatar placeholder flex-shrink-0">
                            <div class="bg-primary-content/20 text-primary-content rounded-full w-8">
                                <span class="text-xs">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate text-primary-content">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-primary-content/60 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="btn btn-ghost btn-sm w-full gap-2 text-primary-content hover:bg-primary-content/20"
                            :class="collapsed ? 'justify-center px-0' : 'justify-start'"
                            :title="collapsed ? 'Cerrar sesión' : ''">
                            <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 flex-shrink-0" />
                            <span x-show="!collapsed" x-transition.opacity>Cerrar sesión</span>
                        </button>
                    </form>
                </div>
            </aside>

            {{-- ===================== CONTENIDO PRINCIPAL ===================== --}}
            <div class="flex flex-col flex-1 min-w-0 transition-all duration-300 ease-in-out lg:ml-64"
                :class="collapsed ? 'lg:ml-16' : 'lg:ml-64'">

                {{-- Navbar móvil --}}
                <div class="navbar bg-base-100 shadow-sm lg:hidden sticky top-0 z-30">
                    <div class="flex-none">
                        <button @click="sidebarOpen = true" class="btn btn-square btn-ghost">
                            <x-heroicon-o-bars-3 class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="flex-1 px-2">
                        <img src="{{ asset('images/logo_menu.svg') }}" alt="{{ config('app.name') }}" class="h-10 w-auto">
                    </div>
                </div>

                {{-- Page heading --}}
                @isset($header)
                    <div class="bg-base-100 border-b border-base-300 px-4 md:px-6 py-4">
                        {{ $header }}
                    </div>
                @endisset

                {{-- Contenido --}}
                <main class="p-4 md:p-6 flex-1 pb-20 lg:pb-6">
                    {{ $slot }}
                </main>
            </div>

        </div>

        {{-- ===================== BOTTOM NAVBAR MÓVIL ===================== --}}
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bg-base-100 border-t border-base-300 safe-area-bottom">
            <div class="flex items-center justify-around h-16">

                <a href="{{ route('admin.articulos.index') }}"
                    class="flex flex-col items-center gap-1 px-4 py-2 rounded-lg transition-colors
                        {{ request()->routeIs('admin.articulos*') ? 'text-primary' : 'text-base-content/50' }}">
                    <x-heroicon-o-cube class="w-6 h-6" />
                    <span class="text-xs font-medium">Inventario</span>
                </a>

                <a href="{{ route('admin.solicitudes.index') }}"
                    class="flex flex-col items-center gap-1 px-4 py-2 rounded-lg transition-colors
                        {{ request()->routeIs('admin.solicitudes*') ? 'text-primary' : 'text-base-content/50' }}">
                    <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
                    <span class="text-xs font-medium">Solicitudes</span>
                </a>

                <a href="{{ route('dashboard.buscar') }}"
                    class="flex flex-col items-center gap-1 px-4 py-2 rounded-lg transition-colors
                        {{ request()->routeIs('dashboard.buscar') ? 'text-primary' : 'text-base-content/50' }}">
                        <x-heroicon-o-magnifying-glass class="w-6 h-6" />
                        <span class="text-xs font-medium">Buscar</span>
                    </a>

            </div>
        </nav>
    </body>
</html>