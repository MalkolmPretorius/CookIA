@extends('templates.index')

@section('content')
    @php
        $user = auth()->user();
    @endphp
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
            background-color: #27272a;
            border-color: #666666;
        }


        @media (max-width: 990px) {
            #sidebar h2 {
                display: none;
            }
        }



        .dark-mode #chat-container {
            background-color: #1a1a1a;
            border-color: #666666;
        }

        #chat-container {
            /* background-image: url({{ asset('images/logo_cookia2.png') }}); */
        }

        .dark-mode .toggle-sidebar-btn {
            cursor: pointer;
            color: #ffffff;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            transition: background-color 0.3s;
            position: absolute;
            top: 1.5rem;
            left: 1rem;
            z-index: 50;
            /* Ensure the button is above other elements */
        }

        .toggle-sidebar-btn {
            cursor: pointer;
            color: #1a1a1a;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            transition: background-color 0.3s;
            position: absolute;
            top: 1.5rem;
            left: 1rem;
            z-index: 50;
            /* Ensure the button is above other elements */
        }



        .dark-mode .darkButton {
            color: #ccc;
        }

        .darkButton {
            color: #2d3748;
        }
    </style>
    <div id="app-container" class="grid grid-cols-4 h-screen bg-white shadow-lg overflow-hidden relative transition-all"
        x-data="chatApp()" :class="{ 'dark-mode': darkMode }">

        <!-- Sidebar -->
        <div id="sidebar" class="border-r border-gray-300 p-5 bg-gray-300 transition-transform duration-300 transform"
            :class="{ 'hidden': !sidebarOpen }">
            <div class="flex justify-center">
                <h2 class="text-black text-3xl underline underline-offset-4">Historique</h2>
                <button @click="toggleSidebar" class="toggle-sidebar-btn" :class="{ 'hidden': !sidebarOpen }">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- Bouton pour une nouvelle conversation -->
            <button @click="createNewConversation" class="mt-5 ml-10 bg-blue-600 text-white p-2 rounded">Nouvelle
                conversation</button>

            <div id="history" class="mt-5 ml-10 overflow-y-auto h-[calc(100%-40px)]">

                <!-- Historique des conversations -->
                <template x-for="(conversation, index) in conversations" :key="index">
                    <div class="mb-4 p-2 bg-gray-200 rounded cursor-pointer" @click="loadConversation(conversation)">
                        <!-- Affiche l'ID correct de la conversation -->
                        <p class="text-sm text-gray-700" x-text="'Conversation ' + conversation[0].id"
                            @click="alert('Conversation ' + conversation[0].id)">
                        </p>
                        <!-- Affiche l'ID correct -->
                        <button @click.stop="deleteConversation(conversation[0].id)"
                            class="ml-2 text-red-600 hover:text-red-800">
                            <i class="fas fa-trash-alt"></i> <!-- Icône de poubelle -->
                        </button>
                    </div>
                </template>
            </div>

        </div>

        <!-- Bouton pour ouvrir la sidebar sur mobile -->
        <button @click="toggleSidebar" class="toggle-sidebar-btn" :class="{ 'hidden': sidebarOpen }">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Conteneur principal du chat -->
        <div id="chat-container" class="flex flex-col col-span-3 justify-end relative transition-all"
            :class="{ 'col-span-4': !sidebarOpen }">
            <div class="absolute top-2 right-2 flex space-x-2">
                <div x-data="{ userMenuOpen: false }">
                    <!-- Menu utilisateur -->
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

                <!-- Bouton pour basculer le mode sombre -->
                <button @click="toggleDarkMode" class="p-2 darkButton focus:outline-none">
                    <i class="fas" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
                </button>
            </div>

            <!-- Conteneur de l'application Vue.js -->
            <div id="app" class="text-black" data-page="{{ json_encode($page) }}">
                <chat-box :selected-conversation="selectedConversation"></chat-box>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chatApp', () => ({
                sidebarOpen: localStorage.getItem('sidebarOpen') === 'true',
                darkMode: localStorage.getItem('darkMode') === 'true',
                conversations: [], // Array to store conversations
                selectedConversation: null,
    
                // Toggle dark mode
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                },
    
                // Toggle sidebar and store its state in localStorage
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                    localStorage.setItem('sidebarOpen', this.sidebarOpen);
                },
    
                // Load conversations from localStorage
                loadConversations() {
                    this.conversations = [];
                    Object.keys(localStorage).forEach(key => {
                        if (key.startsWith('messageHistory')) {
                            const conversationData = JSON.parse(localStorage.getItem(key));
                            if (conversationData) {
                                this.conversations.push(conversationData);
                            }
                        }
                    });
                },
    
                // Create a new conversation
                createNewConversation() {
                    const newId = this.conversations.length;
    
                    // New conversation structure
                    let newConversation = [
                        { id: newId },
                        { role: "user", content: "coucou" },
                        { role: "assistant", content: "Salut! Comment puis-je vous aider aujourd'hui en cuisine?" }
                    ];
                    localStorage.setItem("chatMessages", JSON.stringify([]));
                    
                    // Save the conversation in localStorage
                    localStorage.setItem(`messageHistory${newId}`, JSON.stringify(newConversation));
    
                    // Add the new conversation to the conversations array
                    this.conversations.push(newConversation);
                    this.selectedConversation = newConversation;
                    window.location.reload();
                },
    
                // Delete a conversation
                deleteConversation(conversationId) {
                    const conversationIndex = this.conversations.findIndex(conversation =>
                        conversation[0]?.id === conversationId
                    );
    
                    if (conversationIndex !== -1) {
                        localStorage.removeItem(`messageHistory${conversationId}`);
                        this.conversations.splice(conversationIndex, 1);
    
                        if (this.selectedConversation && this.selectedConversation[0].id === conversationId) {
                            this.selectedConversation = null;
                        }
                    }
                },
    
                // Load a conversation in the chat
                loadConversation(conversation) {
                    this.clearChatBox();
                    this.selectedConversation = conversation;
                },
    
                // Clear the chat box
                clearChatBox() {
                    console.log('Chat box cleared');
                    this.selectedConversation = null;
                },
    
                // Initialize by loading conversations and sidebar state
                init() {
                    this.loadConversations();
                    this.sidebarOpen = localStorage.getItem('sidebarOpen') === 'true'; // Set sidebar state
                },
            }));
        });
    </script>
    





@stop

@vite('resources/js/app.js')
