<x-layouts.tiktok>
    <div class="max-w-7xl mx-auto px-4 py-8"
         x-data="liveStreamViewer({{ $liveStream->id }}, {{ $liveStream->user_id === auth()->id() ? 'true' : 'false' }})"
         x-init="init()">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Live Stream Player (Left - 2/3) -->
            <div class="lg:col-span-2">
                <!-- Player Card -->
                <div class="bg-black rounded-2xl overflow-hidden shadow-2xl">
                    <!-- Video Player -->
                    <div class="aspect-video bg-gradient-to-br from-purple-900 via-pink-900 to-red-900 relative">
                        <!-- Video Element (hidden until stream starts) -->
                        <video
                            x-ref="remoteVideo"
                            x-show="hasRemoteStream"
                            autoplay
                            playsinline
                            class="absolute inset-0 w-full h-full object-cover"
                        ></video>

                        <!-- Local Video (for streamer) -->
                        @if($liveStream->user_id === auth()->id())
                        <video
                            x-ref="localVideo"
                            x-show="isStreaming"
                            autoplay
                            muted
                            playsinline
                            class="absolute inset-0 w-full h-full object-cover"
                        ></video>
                        @endif

                        <!-- LIVE Badge -->
                        <div class="absolute top-6 left-6 px-4 py-2 bg-red-600 text-white font-bold rounded-full flex items-center gap-2 animate-pulse z-10">
                            <span class="w-3 h-3 bg-white rounded-full"></span>
                            LIVE
                        </div>

                        <!-- Viewers Count -->
                        <div class="absolute top-6 right-6 px-4 py-2 bg-black/60 backdrop-blur-sm text-white font-semibold rounded-full flex items-center gap-2 z-10">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                            <span x-text="viewerCount">{{ number_format($liveStream->viewers_count) }}</span>
                        </div>

                        <!-- Placeholder (shown when no stream) -->
                        <div x-show="!hasRemoteStream && !isStreaming" class="absolute inset-0 flex items-center justify-center text-center">
                            <div>
                                <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                    </svg>
                                </div>
                                <p class="text-white text-xl font-bold">
                                    <span x-show="isStreamer">Start your camera to go live</span>
                                    <span x-show="!isStreamer">Waiting for stream...</span>
                                </p>
                            </div>
                        </div>

                        @if($liveStream->user_id === auth()->id())
                        <!-- Streamer Controls -->
                        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 z-10 flex items-center gap-3">
                            <!-- Start/Stop Camera -->
                            <button
                                @click="toggleCamera()"
                                x-show="!isStreaming"
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                </svg>
                                Start Camera
                            </button>

                            <!-- Mute/Unmute -->
                            <button
                                x-show="isStreaming"
                                @click="toggleAudio()"
                                :class="audioEnabled ? 'bg-gray-700' : 'bg-red-600'"
                                class="w-12 h-12 rounded-full hover:opacity-90 text-white transition flex items-center justify-center">
                                <svg x-show="audioEnabled" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7 4a3 3 0 016 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 015 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z" clip-rule="evenodd"/>
                                </svg>
                                <svg x-show="!audioEnabled" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                </svg>
                            </button>

                            <!-- Video On/Off -->
                            <button
                                x-show="isStreaming"
                                @click="toggleVideo()"
                                :class="videoEnabled ? 'bg-gray-700' : 'bg-red-600'"
                                class="w-12 h-12 rounded-full hover:opacity-90 text-white transition flex items-center justify-center">
                                <svg x-show="videoEnabled" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                </svg>
                                <svg x-show="!videoEnabled" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                </svg>
                            </button>

                            <!-- Screen Share -->
                            <button
                                x-show="isStreaming"
                                @click="toggleScreenShare()"
                                :class="isScreenSharing ? 'bg-blue-600' : 'bg-gray-700'"
                                class="px-4 py-3 rounded-lg hover:opacity-90 text-white transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"/>
                                </svg>
                                <span x-text="isScreenSharing ? 'Stop Share' : 'Share Screen'"></span>
                            </button>
                        </div>

                        <!-- End Stream Button -->
                        <div class="absolute bottom-6 right-6 z-10">
                            <form action="{{ route('live.end', $liveStream) }}" method="POST" onsubmit="return confirm('Are you sure you want to end this live stream?');">
                                @csrf
                                <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold transition flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/>
                                    </svg>
                                    End Stream
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <!-- Stream Info -->
                    <div class="bg-gray-900 px-6 py-5">
                        <!-- User Info -->
                        <div class="flex items-center gap-4 mb-4">
                            @if($liveStream->user->avatar)
                                <img src="{{ asset($liveStream->user->avatar) }}" alt="{{ $liveStream->user->name }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-white/20">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center ring-2 ring-white/20">
                                    <span class="text-white font-bold text-lg">{{ substr($liveStream->user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="font-bold text-white">{{ $liveStream->user->name }}</h3>
                                <p class="text-sm text-gray-400">{{ '@' . $liveStream->user->username }}</p>
                            </div>
                            @if($liveStream->user_id !== auth()->id())
                                <x-follow-button :user="$liveStream->user" />
                            @endif
                        </div>

                        <!-- Title & Description -->
                        <h2 class="text-xl font-bold text-white mb-2">{{ $liveStream->title }}</h2>
                        @if($liveStream->description)
                            <p class="text-gray-300 text-sm leading-relaxed">{{ $liveStream->description }}</p>
                        @endif

                        <!-- Started Time -->
                        <p class="text-xs text-gray-500 mt-3">
                            Started {{ $liveStream->started_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Chat Sidebar (Right - 1/3) -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col" style="height: 600px;">
                    <!-- Chat Header -->
                    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                            </svg>
                            Live Chat
                        </h3>
                    </div>

                    <!-- Chat Messages -->
                    <div x-ref="chatContainer" class="flex-1 overflow-y-auto p-4 space-y-3">
                        <!-- Empty State -->
                        <div x-show="messages.length === 0" class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No messages yet</p>
                            <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Be the first to chat!</p>
                        </div>

                        <!-- Messages -->
                        <template x-for="msg in messages" :key="msg.id">
                            <div class="flex items-start gap-2">
                                <!-- Avatar -->
                                <div class="flex-shrink-0">
                                    <img x-show="msg.user.avatar" :src="`/storage/${msg.user.avatar}`" :alt="msg.user.name" class="w-8 h-8 rounded-full object-cover">
                                    <div x-show="!msg.user.avatar" class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                        <span class="text-white text-xs font-bold" x-text="msg.user.name.charAt(0)"></span>
                                    </div>
                                </div>
                                <!-- Message -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm">
                                        <span class="font-semibold text-gray-900 dark:text-white" x-text="msg.user.name"></span>
                                        <span class="text-gray-600 dark:text-gray-300 ml-1" x-text="msg.message"></span>
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Chat Input -->
                    @auth
                    <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                        <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                            <input
                                x-model="newMessage"
                                type="text"
                                placeholder="Send a message..."
                                maxlength="500"
                                class="flex-1 px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-full text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#FE2C55] focus:border-transparent text-sm"
                            >
                            <button
                                type="submit"
                                :disabled="!newMessage.trim() || isSendingMessage"
                                :class="!newMessage.trim() || isSendingMessage ? 'opacity-50 cursor-not-allowed' : ''"
                                class="w-10 h-10 rounded-full bg-[#FE2C55] hover:bg-[#FE2C55]/90 flex items-center justify-center transition">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="border-t border-gray-200 dark:border-gray-700 p-4 text-center">
                        <a href="{{ route('login') }}" class="text-[#FE2C55] hover:text-[#FE2C55]/90 font-semibold">
                            Login to chat
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function liveStreamViewer(liveStreamId, isStreamer) {
            return {
                liveStreamId: liveStreamId,
                isStreamer: isStreamer,

                // Chat
                messages: [],
                newMessage: '',
                isSendingMessage: false,

                // Viewer count
                viewerCount: {{ $liveStream->viewers_count }},

                // Stream state
                isStreaming: false,
                hasRemoteStream: false,
                audioEnabled: true,
                videoEnabled: true,
                isScreenSharing: false,

                // WebRTC
                localStream: null,
                screenStream: null,
                peerConnection: null, // For viewers (single connection)
                peerConnections: new Map(), // For streamers (multiple connections)

                init() {
                    // Subscribe to live stream channel
                    window.Echo.channel(`live-stream.${this.liveStreamId}`)
                        .listen('LiveChatMessageSent', (e) => {
                            this.messages.push(e);
                            this.$nextTick(() => {
                                this.scrollChatToBottom();
                            });
                        })
                        .listen('ViewerCountUpdated', (e) => {
                            this.viewerCount = e.viewer_count;
                        })
                        .listen('ViewerJoined', async (e) => {
                            // Streamer creates offer for new viewer
                            if (this.isStreamer && this.isStreaming) {
                                console.log('New viewer joined:', e.viewer_name);
                                await this.createOfferForViewer(e.viewer_id);
                            }
                        })
                        .listen('StreamStarted', async (e) => {
                            // Viewer: Stream has started, request offer
                            if (!this.isStreamer && e.streamer_id !== {{ auth()->id() ?? 'null' }}) {
                                console.log('Stream started, requesting offer...');
                                await this.requestOffer();
                            }
                        })
                        .listen('ViewerRequestedOffer', async (e) => {
                            // Streamer: Create offer for viewer who requested
                            if (this.isStreamer && this.isStreaming && e.viewer_id !== {{ auth()->id() ?? 'null' }}) {
                                console.log('Viewer requested offer:', e.viewer_name);
                                await this.createOfferForViewer(e.viewer_id);
                            }
                        });

                    // Load initial messages
                    this.loadMessages();

                    // If streamer, setup WebRTC for broadcasting
                    if (this.isStreamer) {
                        // Auto-start will be manual via button
                    } else {
                        // Viewer: listen for WebRTC offers
                        this.setupViewerWebRTC();
                    }
                },

                async loadMessages() {
                    try {
                        const response = await fetch(`/api/live/${this.liveStreamId}/chat`);
                        const data = await response.json();
                        this.messages = data.messages || [];
                        this.$nextTick(() => {
                            this.scrollChatToBottom();
                        });
                    } catch (error) {
                        console.error('Failed to load messages:', error);
                    }
                },

                async sendMessage() {
                    if (!this.newMessage.trim() || this.isSendingMessage) return;

                    this.isSendingMessage = true;
                    const message = this.newMessage;
                    this.newMessage = '';

                    try {
                        const response = await fetch(`/api/live/${this.liveStreamId}/chat`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ message }),
                        });

                        const data = await response.json();

                        if (response.ok) {
                            // Add own message immediately
                            this.messages.push(data.chat);
                            this.$nextTick(() => {
                                this.scrollChatToBottom();
                            });
                        } else {
                            alert(data.error || 'Failed to send message');
                            this.newMessage = message; // Restore message
                        }
                    } catch (error) {
                        console.error('Failed to send message:', error);
                        this.newMessage = message; // Restore message
                    } finally {
                        this.isSendingMessage = false;
                    }
                },

                scrollChatToBottom() {
                    const container = this.$refs.chatContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                },

                // WebRTC Methods
                async toggleCamera() {
                    if (this.isStreaming) {
                        this.stopStreaming();
                    } else {
                        await this.startStreaming();
                    }
                },

                async startStreaming() {
                    try {
                        this.localStream = await navigator.mediaDevices.getUserMedia({
                            video: { width: 1280, height: 720 },
                            audio: true
                        });

                        this.$refs.localVideo.srcObject = this.localStream;
                        this.isStreaming = true;
                        this.videoEnabled = true;
                        this.audioEnabled = true;

                        // Setup WebRTC to broadcast
                        await this.setupStreamerWebRTC();

                        // Notify all viewers that stream has started
                        await this.notifyStreamStarted();
                    } catch (error) {
                        console.error('Failed to start camera:', error);
                        alert('Failed to access camera. Please check permissions.');
                    }
                },

                stopStreaming() {
                    if (this.localStream) {
                        this.localStream.getTracks().forEach(track => track.stop());
                        this.localStream = null;
                    }
                    if (this.screenStream) {
                        this.screenStream.getTracks().forEach(track => track.stop());
                        this.screenStream = null;
                    }
                    if (this.peerConnection) {
                        this.peerConnection.close();
                        this.peerConnection = null;
                    }
                    // Close all viewer connections (for streamers)
                    this.peerConnections.forEach((pc, viewerId) => {
                        pc.close();
                    });
                    this.peerConnections.clear();

                    this.isStreaming = false;
                    this.isScreenSharing = false;
                },

                toggleAudio() {
                    if (this.localStream) {
                        const audioTrack = this.localStream.getAudioTracks()[0];
                        if (audioTrack) {
                            audioTrack.enabled = !audioTrack.enabled;
                            this.audioEnabled = audioTrack.enabled;
                        }
                    }
                },

                toggleVideo() {
                    if (this.localStream) {
                        const videoTrack = this.localStream.getVideoTracks()[0];
                        if (videoTrack) {
                            videoTrack.enabled = !videoTrack.enabled;
                            this.videoEnabled = videoTrack.enabled;
                        }
                    }
                },

                async toggleScreenShare() {
                    if (this.isScreenSharing) {
                        // Stop screen share, back to camera
                        if (this.screenStream) {
                            this.screenStream.getTracks().forEach(track => track.stop());
                            this.screenStream = null;
                        }
                        if (this.localStream) {
                            this.$refs.localVideo.srcObject = this.localStream;
                        }
                        this.isScreenSharing = false;
                    } else {
                        // Start screen share
                        try {
                            this.screenStream = await navigator.mediaDevices.getDisplayMedia({
                                video: { width: 1920, height: 1080 },
                                audio: false
                            });

                            this.$refs.localVideo.srcObject = this.screenStream;
                            this.isScreenSharing = true;

                            // Handle when user stops sharing via browser UI
                            this.screenStream.getVideoTracks()[0].onended = () => {
                                this.toggleScreenShare();
                            };
                        } catch (error) {
                            console.error('Failed to share screen:', error);
                        }
                    }
                },

                async setupStreamerWebRTC() {
                    // Listen for answers and ICE candidates from viewers
                    window.Echo.channel(`live-stream.${this.liveStreamId}`)
                        .listen('StreamAnswerCreated', async (e) => {
                            // Only process if this answer is for us and from a viewer
                            if (e.target_user_id === {{ auth()->id() ?? 'null' }} && e.viewer_id !== {{ auth()->id() ?? 'null' }}) {
                                const pc = this.peerConnections.get(e.viewer_id);
                                if (pc) {
                                    try {
                                        await pc.setRemoteDescription(new RTCSessionDescription(e.answer));
                                        console.log('Set remote description for viewer:', e.viewer_id);
                                    } catch (error) {
                                        console.error('Failed to set remote description:', error);
                                    }
                                }
                            }
                        })
                        .listen('IceCandidateShared', async (e) => {
                            // Only process if this ICE is for us
                            if (e.target_user_id === {{ auth()->id() ?? 'null' }} && e.user_id !== {{ auth()->id() ?? 'null' }}) {
                                const pc = this.peerConnections.get(e.user_id);
                                if (pc) {
                                    try {
                                        await pc.addIceCandidate(new RTCIceCandidate(e.candidate));
                                    } catch (error) {
                                        console.error('Failed to add ICE candidate:', error);
                                    }
                                }
                            }
                        });
                },

                async createOfferForViewer(viewerId) {
                    const configuration = {
                        iceServers: [
                            { urls: 'stun:stun.l.google.com:19302' },
                            { urls: 'stun:stun1.l.google.com:19302' },
                        ]
                    };

                    const pc = new RTCPeerConnection(configuration);
                    this.peerConnections.set(viewerId, pc);

                    // Add local stream tracks
                    this.localStream.getTracks().forEach(track => {
                        pc.addTrack(track, this.localStream);
                    });

                    // Handle ICE candidates
                    pc.onicecandidate = async (event) => {
                        if (event.candidate) {
                            await this.sendIceCandidate(event.candidate, viewerId);
                        }
                    };

                    // Handle connection state
                    pc.onconnectionstatechange = () => {
                        console.log('Connection state for viewer', viewerId, ':', pc.connectionState);
                        if (pc.connectionState === 'disconnected' || pc.connectionState === 'failed' || pc.connectionState === 'closed') {
                            this.peerConnections.delete(viewerId);
                        }
                    };

                    // Create and send offer
                    try {
                        const offer = await pc.createOffer();
                        await pc.setLocalDescription(offer);
                        await this.sendOffer(offer, viewerId);
                        console.log('Sent offer to viewer:', viewerId);
                    } catch (error) {
                        console.error('Failed to create offer for viewer:', error);
                        this.peerConnections.delete(viewerId);
                    }
                },

                setupViewerWebRTC() {
                    // Listen for offers from streamer (targeted to this viewer)
                    window.Echo.channel(`live-stream.${this.liveStreamId}`)
                        .listen('StreamOfferCreated', async (e) => {
                            // Only process if this offer is specifically for this viewer
                            if (e.target_user_id === {{ auth()->id() ?? 'null' }} && e.streamer_id !== {{ auth()->id() ?? 'null' }}) {
                                console.log('Received offer from streamer');
                                await this.handleStreamOffer(e.offer, e.streamer_id);
                            }
                        })
                        .listen('IceCandidateShared', async (e) => {
                            // Only process ICE candidates targeted to this viewer
                            if (e.target_user_id === {{ auth()->id() ?? 'null' }} && e.user_id !== {{ auth()->id() ?? 'null' }} && this.peerConnection) {
                                try {
                                    await this.peerConnection.addIceCandidate(new RTCIceCandidate(e.candidate));
                                } catch (error) {
                                    console.error('Failed to add ICE candidate:', error);
                                }
                            }
                        });
                },

                async handleStreamOffer(offer, streamerId) {
                    const configuration = {
                        iceServers: [
                            { urls: 'stun:stun.l.google.com:19302' },
                            { urls: 'stun:stun1.l.google.com:19302' },
                        ]
                    };

                    this.peerConnection = new RTCPeerConnection(configuration);

                    // Handle incoming stream
                    this.peerConnection.ontrack = (event) => {
                        console.log('Received remote stream');
                        this.$refs.remoteVideo.srcObject = event.streams[0];
                        this.hasRemoteStream = true;
                    };

                    // Handle ICE candidates
                    this.peerConnection.onicecandidate = async (event) => {
                        if (event.candidate) {
                            await this.sendIceCandidate(event.candidate, streamerId);
                        }
                    };

                    try {
                        await this.peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
                        const answer = await this.peerConnection.createAnswer();
                        await this.peerConnection.setLocalDescription(answer);
                        await this.sendAnswer(answer, streamerId);
                        console.log('Sent answer to streamer');
                    } catch (error) {
                        console.error('Failed to handle offer:', error);
                    }
                },

                async sendOffer(offer, targetUserId) {
                    try {
                        await fetch(`/api/live/${this.liveStreamId}/webrtc/offer`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                offer,
                                target_user_id: targetUserId
                            }),
                        });
                    } catch (error) {
                        console.error('Failed to send offer:', error);
                    }
                },

                async sendAnswer(answer, targetUserId) {
                    try {
                        await fetch(`/api/live/${this.liveStreamId}/webrtc/answer`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                answer,
                                target_user_id: targetUserId
                            }),
                        });
                    } catch (error) {
                        console.error('Failed to send answer:', error);
                    }
                },

                async sendIceCandidate(candidate, targetUserId) {
                    try {
                        await fetch(`/api/live/${this.liveStreamId}/webrtc/ice-candidate`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                candidate,
                                target_user_id: targetUserId
                            }),
                        });
                    } catch (error) {
                        console.error('Failed to send ICE candidate:', error);
                    }
                },

                async notifyStreamStarted() {
                    try {
                        await fetch(`/api/live/${this.liveStreamId}/webrtc/start`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });
                        console.log('Notified viewers that stream started');
                    } catch (error) {
                        console.error('Failed to notify stream started:', error);
                    }
                },

                async requestOffer() {
                    try {
                        await fetch(`/api/live/${this.liveStreamId}/webrtc/request-offer`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });
                        console.log('Requested offer from streamer');
                    } catch (error) {
                        console.error('Failed to request offer:', error);
                    }
                }
            }
        }
    </script>
    @endpush
</x-layouts.tiktok>
