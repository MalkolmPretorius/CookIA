    <div id="app-container"
        class="grid grid-cols-4 h-screen bg-white shadow-lg rounded-lg overflow-hidden relative transition-all"
        x-data="{ sidebarOpen: true }">
        <div id="sidebar" class="border-r border-gray-300 p-5 bg-gray-200 transition-transform duration-300"
            :class="{ '-translate-x-full': !sidebarOpen }">
            <div class="flex justify-between items-center">
                <h2 class="text-black text-2xl underline underline-offset-4">Chat History</h2>
                <button @click="sidebarOpen = !sidebarOpen" class="text-black focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div id="history" class="mt-5 overflow-y-auto h-[calc(100%-40px)]">
                <!-- Previous chat sessions will be listed here -->
            </div>
        </div>
        <div id="chat-container" class="flex flex-col col-span-3 justify-between relative">
            <!-- Bouton utilisateur et thème en haut à droite -->
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
                        <a class="block text-gray-700 px-4 py-2 hover:bg-gray-200"
                            href="{{ route('favorite._index') }}">Mes favoris</a>
                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">Mon Profil</a>
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
                <button @click="darkMode = !darkMode" class="p-2 text-black focus:outline-none">
                    <i class="fas" :class="darkMode ? 'fa-sun' : 'fa-moon'">icone</i>
                </button>
            </div>

            <div id="chat-box" class="flex-grow p-5 overflow-y-auto border-b border-gray-300 mt-16">
                <div id="messages" class="flex flex-col">
                </div>
            </div>
            <div id="input-container" class="flex m-2 p-3">
                <input type="text" id="user-input" placeholder="Type your message here..."
                    class="flex-grow p-2 border text-black border-gray-300 rounded-lg mr-2">
                <button id="send-button" class="p-2 rounded-lg bg-blue-600 text-white cursor-pointer ml-2">Send</button>
            </div>
        </div>
    </div>

   

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chatApp', () => ({
                sidebarOpen: true,
                darkMode: false,
            }))
        })

        document.getElementById('send-button').addEventListener('click', sendMessage);

        document.getElementById('user-input').addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        });

        function sendMessage() {
            const userInput = document.getElementById('user-input').value;
            if (userInput.trim() === '') return;

            addMessage(userInput, 'user-message');
            document.getElementById('user-input').value = '';

            // Simulate a bot response after a delay
            setTimeout(() => {
                const botReply = "This is a simulated response.";
                addMessage(botReply, 'bot-message');
            }, 1000);
        }

        function addMessage(text, className) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${className} mb-2 p-2 rounded`;
            messageDiv.textContent = text;
            if (className === 'user-message') {
                messageDiv.classList.add('bg-gray-400', 'self-end');
                document.getElementById('messages').appendChild(messageDiv);
            } else {
                messageDiv.classList.add('bg-gray-400', 'self-start');
                document.getElementById('messages').appendChild(messageDiv);
                addMessageActions(messageDiv);
            }
        }


        function addMessageActions(messageDiv) {
            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'flex space-x-2 mt-2 text-sm text-gray-600';

            const favoriteButton = document.createElement('button');
            favoriteButton.className = 'text-gray-700 focus:outline-none';
            favoriteButton.innerHTML = '<i class="fas fa-heart"></i>';
            favoriteButton.addEventListener('click', () => {
                // Logic to add message to favorites
                alert('Added to favorites!');
            });

            const copyButton = document.createElement('button');
            copyButton.className = 'text-gray-700 focus:outline-none';
            copyButton.innerHTML = '<i class="fas fa-copy"></i>';
            copyButton.addEventListener('click', () => {
                navigator.clipboard.writeText(messageDiv.textContent).then(() => {
                    alert('Copied to clipboard!');
                });
            });

            actionsDiv.appendChild(favoriteButton);
            actionsDiv.appendChild(copyButton);
            messageDiv.insertAdjacentElement('afterend', actionsDiv);
        }
    </script>
