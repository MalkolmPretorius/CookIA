@extends('templates.index')

@section('content')
    <style>
        .bg-white {
            background-color: #ffffff;
        }

        .text-black {
            color: #000000;
        }

        .dark-mode .bg-white {
            background-color: #1a1a1a;
        }

        .dark-mode .text-black {
            color: #ffffff;
        }

        .dark-mode #sidebar {
            background-color: #1a1a1a;
            border-color: #666666;
        }

        .dark-mode #chat-container {
            background-color: #1a1a1a;
            border-color: #666666;
        }
        #chat-container{
            /* background-image: url({{ asset('images/logo_cookia2.png') }}); */
        }
    </style>

    <div id="app-container"
        class="grid grid-cols-4 h-screen bg-white shadow-lg rounded-lg overflow-hidden relative transition-all"
        x-data="chatApp()" :class="{ 'dark-mode': darkMode }">
        <div id="sidebar" class="border-r border-gray-300 p-5 bg-gray-200 transition-transform duration-300"
            :class="{ '-translate-x-full': !sidebarOpen }">
            <div class="flex justify-between items-center">
                <h2 class="text-black text-2xl underline underline-offset-4">Historique</h2>
            </div>
            <div id="history" class="mt-5 overflow-y-auto h-[calc(100%-40px)]">
                <!-- Previous chat sessions will be listed here -->
            </div>
        </div>
        <div id="chat-container" class="flex flex-col col-span-3 justify-end relative">
            <div class="absolute top-2 right-2 flex space-x-2">
                <div x-data="{ userMenuOpen: false }">
                    <button @click="userMenuOpen = !userMenuOpen" class="text-white">
                        <img src="{{ asset('images/user.webp') }}" alt="" class="w-16" />
                    </button>

                    <div x-show="userMenuOpen" @click.away="userMenuOpen = false"
                        class="absolute right-0 mt-2 w-48 bg-gray-100 rounded-md shadow-lg pb-1 z-50">
                        <div class="text-gray-200 px-4 py-2 bg-gray-400 text-center">
                            {{ Auth::user()->name }}
                        </div>
                        <a class="block text-gray-700 px-4 py-2 hover:bg-gray-200" href="{{ route('favorite._index') }}">Mes
                            favoris</a>
                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">Mon
                            Profil</a>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">
                            Se Déconnecter
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
                <button @click="toggleDarkMode" class="p-2 text-black focus:outline-none">
                    <i class="fas" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
                </button>
            </div>

            
            <div id="app" class="text-black" data-page="{{ json_encode($page) }}"></div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chatApp', () => ({
                sidebarOpen: true,
                darkMode: localStorage.getItem('darkMode') === 'true',

                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                },
            }))
        })

   

    </script>

@stop

@vite('resources/js/app.js')
