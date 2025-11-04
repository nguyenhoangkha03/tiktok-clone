<x-layouts.tiktok>
    <div class="min-h-screen bg-white dark:bg-gray-900">
        <div class="max-w-4xl mx-auto h-screen flex flex-col">
            <!-- Chat Header -->
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <!-- Back Button -->
                        <a href="{{ route('messages.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                            <i class="ri-arrow-left-line text-2xl"></i>
                        </a>

                        <!-- User Info -->
                        <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0">
                            @if($user->avatar)
                                <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                                    <span class="text-sm font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ '@' . $user->username }}</p>
                        </div>
                    </div>

                    <!-- Profile Link -->
                    <a href="{{ route('profile.show', $user->username) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        <i class="ri-user-line text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Messages Container -->
            <div id="messages-container" class="flex-1 overflow-y-auto px-4 py-6 space-y-4 bg-gray-50 dark:bg-gray-900">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="flex items-end gap-2 max-w-[70%] {{ $message->sender_id === auth()->id() ? 'flex-row-reverse' : 'flex-row' }}">
                            <!-- Avatar (only for received messages) -->
                            @if($message->sender_id !== auth()->id())
                                <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0">
                                    @if($message->sender->avatar)
                                        <img src="{{ asset($message->sender->avatar) }}" alt="{{ $message->sender->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                                            <span class="text-xs font-bold text-white">{{ substr($message->sender->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Message Bubble -->
                            <div class="message-bubble" data-message-id="{{ $message->id }}">
                                <div class="rounded-2xl px-4 py-2 {{ $message->sender_id === auth()->id() ? 'bg-[#FE2C55] text-white' : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700' }}">
                                    <p class="text-sm break-words whitespace-pre-wrap">{{ $message->message }}</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 {{ $message->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                                    {{ $message->created_at->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <i class="ri-chat-3-line text-gray-400 dark:text-gray-500 text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No messages yet</h3>
                        <p class="text-gray-600 dark:text-gray-400">Start the conversation!</p>
                    </div>
                @endforelse
            </div>

            <!-- Message Input -->
            <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-4 py-4">
                <form id="message-form" class="flex items-end gap-3" x-data="{ showEmojiPicker: false }">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $user->id }}">

                    <!-- Emoji Button -->
                    <div class="relative">
                        <button
                            type="button"
                            @click="showEmojiPicker = !showEmojiPicker"
                            class="w-10 h-10 text-gray-500 dark:text-gray-400 hover:text-[#FE2C55] dark:hover:text-[#FE2C55] rounded-full flex items-center justify-center transition"
                        >
                            <i class="ri-emotion-happy-line text-2xl"></i>
                        </button>

                        <!-- Emoji Picker Dropdown -->
                        <div
                            x-show="showEmojiPicker"
                            @click.away="showEmojiPicker = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="absolute bottom-full left-0 mb-2 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 p-3 w-80 max-h-64 overflow-y-auto"
                            style="display: none;"
                        >
                            <div class="grid grid-cols-8 gap-2">
                                @foreach(['😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '🙃', '😉', '😊', '😇', '🥰', '😍', '🤩', '😘', '😗', '😚', '😙', '🥲', '😋', '😛', '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🤫', '🤔', '🤐', '🤨', '😐', '😑', '😶', '😏', '😒', '🙄', '😬', '🤥', '😌', '😔', '😪', '🤤', '😴', '😷', '🤒', '🤕', '🤢', '🤮', '🤧', '🥵', '🥶', '😶‍🌫️', '🥴', '😵', '🤯', '🤠', '🥳', '😎', '🤓', '🧐', '😕', '😟', '🙁', '☹️', '😮', '😯', '😲', '😳', '🥺', '😦', '😧', '😨', '😰', '😥', '😢', '😭', '😱', '😖', '😣', '😞', '😓', '😩', '😫', '🥱', '😤', '😡', '😠', '🤬', '👍', '👎', '👌', '✌️', '🤞', '🤟', '🤘', '🤙', '👏', '🙌', '👐', '🤲', '🤝', '🙏', '✍️', '💪', '🦾', '🦿', '🦵', '🦶', '👂', '🦻', '👃', '🧠', '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '🔥', '✨', '💫', '⭐', '🌟', '💯', '🎉', '🎊', '🎈', '🎁', '🏆', '🥇', '🥈', '🥉'] as $emoji)
                                <button
                                    type="button"
                                    @click="
                                        const input = document.getElementById('message-input');
                                        const start = input.selectionStart;
                                        const end = input.selectionEnd;
                                        const text = input.value;
                                        input.value = text.substring(0, start) + '{{ $emoji }}' + text.substring(end);
                                        input.selectionStart = input.selectionEnd = start + {{ mb_strlen($emoji) }};
                                        input.focus();
                                        input.dispatchEvent(new Event('input'));
                                        showEmojiPicker = false;
                                    "
                                    class="text-2xl hover:bg-gray-100 dark:hover:bg-gray-700 rounded p-1 transition"
                                >{{ $emoji }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Message Textarea -->
                    <div class="flex-1">
                        <textarea
                            id="message-input"
                            name="message"
                            rows="1"
                            placeholder="Type a message..."
                            class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-700 border-0 rounded-full text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-[#FE2C55] resize-none"
                            style="max-height: 120px;"
                        ></textarea>
                    </div>

                    <!-- Send Button -->
                    <button
                        type="submit"
                        id="send-button"
                        class="w-12 h-12 bg-[#FE2C55] hover:bg-[#d91f46] text-white rounded-full flex items-center justify-center flex-shrink-0 disabled:opacity-50 disabled:cursor-not-allowed transition"
                        disabled
                    >
                        <i class="ri-send-plane-fill text-xl"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messagesContainer = document.getElementById('messages-container');
            const messageForm = document.getElementById('message-form');
            const messageInput = document.getElementById('message-input');
            const sendButton = document.getElementById('send-button');
            let lastMessageId = {{ $messages->last()?->id ?? 0 }};
            let isPolling = false;

            // Auto-resize textarea
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';

                // Enable/disable send button
                sendButton.disabled = this.value.trim() === '';
            });

            // Allow Enter to send, Shift+Enter for new line
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (this.value.trim() !== '') {
                        messageForm.dispatchEvent(new Event('submit'));
                    }
                }
            });

            // Scroll to bottom
            function scrollToBottom(smooth = false) {
                messagesContainer.scrollTo({
                    top: messagesContainer.scrollHeight,
                    behavior: smooth ? 'smooth' : 'auto'
                });
            }

            // Initial scroll to bottom
            scrollToBottom(false);

            // Send message
            messageForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const message = messageInput.value.trim();
                if (!message) return;

                // Disable form while sending
                messageInput.disabled = true;
                sendButton.disabled = true;

                try {
                    const response = await fetch('{{ route('messages.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            receiver_id: {{ $user->id }},
                            message: message
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();

                        // Clear input
                        messageInput.value = '';
                        messageInput.style.height = 'auto';

                        // Add message to UI
                        addMessageToUI(data.message, true);
                        lastMessageId = data.message.id;

                        // Scroll to bottom
                        scrollToBottom(true);
                    } else {
                        alert('Failed to send message. Please try again.');
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                    alert('Failed to send message. Please try again.');
                } finally {
                    messageInput.disabled = false;
                    sendButton.disabled = false;
                    messageInput.focus();
                }
            });

            // Add message to UI
            function addMessageToUI(message, isSent) {
                // Check if message already exists (prevent duplicates)
                const existingMessage = messagesContainer.querySelector(`[data-message-id="${message.id}"]`);
                if (existingMessage) {
                    return; // Skip if already exists
                }

                const messageDiv = document.createElement('div');
                messageDiv.className = `flex ${isSent ? 'justify-end' : 'justify-start'}`;

                // Use server-formatted time to avoid timezone issues
                const time = message.formatted_time;

                messageDiv.innerHTML = `
                    <div class="flex items-end gap-2 max-w-[70%] ${isSent ? 'flex-row-reverse' : 'flex-row'}">
                        ${!isSent ? `
                            <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0">
                                ${message.sender.avatar ?
                                    `<img src="${message.sender.avatar}" alt="${message.sender.name}" class="w-full h-full object-cover">` :
                                    `<div class="w-full h-full bg-gradient-to-br from-[#667eea] to-[#764ba2] flex items-center justify-center">
                                        <span class="text-xs font-bold text-white">${message.sender.name.charAt(0)}</span>
                                    </div>`
                                }
                            </div>
                        ` : ''}
                        <div class="message-bubble" data-message-id="${message.id}">
                            <div class="rounded-2xl px-4 py-2 ${isSent ? 'bg-[#FE2C55] text-white' : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700'}">
                                <p class="text-sm break-words whitespace-pre-wrap">${message.message}</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ${isSent ? 'text-right' : 'text-left'}">
                                ${time}
                            </p>
                        </div>
                    </div>
                `;

                // Remove empty state if exists
                const emptyState = messagesContainer.querySelector('.text-center.py-20');
                if (emptyState) {
                    emptyState.remove();
                }

                messagesContainer.appendChild(messageDiv);
            }

            // Poll for new messages
            async function pollMessages() {
                if (isPolling) return;
                isPolling = true;

                try {
                    const response = await fetch(`{{ route('messages.get', $user->username) }}?last_message_id=${lastMessageId}`);

                    if (response.ok) {
                        const data = await response.json();

                        if (data.messages && data.messages.length > 0) {
                            data.messages.forEach(message => {
                                addMessageToUI(message, message.sender_id === {{ auth()->id() }});
                                lastMessageId = message.id;
                            });

                            // Scroll to bottom if user is near bottom
                            const isNearBottom = messagesContainer.scrollHeight - messagesContainer.scrollTop - messagesContainer.clientHeight < 100;
                            if (isNearBottom) {
                                scrollToBottom(true);
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error polling messages:', error);
                } finally {
                    isPolling = false;
                }
            }

            // Poll every 2 seconds
            setInterval(pollMessages, 2000);

            // Focus input on load
            messageInput.focus();
        });
    </script>
    @endpush
</x-layouts.tiktok>
