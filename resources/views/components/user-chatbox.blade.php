{{-- resources/views/components/user-chatbox.blade.php --}}
<div 
    x-show="open && userId"
    x-transition
    :style="minimized ? 'width:4rem;height:4rem;padding:0;overflow:hidden;left:50%;transform:translateX(-50%);bottom:0!important;' : 'width:24rem;height:32rem;left:50%;transform:translateX(-50%);bottom:0!important;'"
    class="fixed bg-white border border-gray-300 rounded-full shadow-lg z-50"
    id="user-chatbox"
    style="display:none;"
>
    <!-- Minimized State -->
    <template x-if="minimized">
        <div class="flex items-center justify-center h-full w-full cursor-pointer" @click="minimized = false">
            <img 
                :src="messages.length && messages[0].sender && messages[0].sender.photo 
                    ? '/storage/' + messages[0].sender.photo 
                    : '/storage/default.png'"
                alt="Profile"
                class="w-12 h-12 rounded-full object-cover border-2 border-green-600"
            >
        </div>
    </template>
    <!-- Expanded State -->
    <template x-if="!minimized">
        <div class="flex flex-col h-full w-full rounded-xl overflow-hidden bg-white">
            <!-- Header -->
            <div class="flex items-center justify-between bg-green-600 text-white px-4 py-2">
                <div class="flex items-center gap-2">
                    <img 
                        :src="messages.length && messages[0].sender && messages[0].sender.profile && messages[0].sender.profile.photo 
                            ? '/storage/' + messages[0].sender.profile.photo 
                            : '/storage/default.png'"
                        alt="Profile"
                        class="w-8 h-8 rounded-full object-cover border border-white"
                    >
                    <span x-text="userName"></span>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="minimized = true" class="text-white text-xl leading-none" title="Minimize">
                        <i class="bi bi-dash-square"></i>
                    </button>
                    <button @click="closeChat()" class="text-white text-2xl leading-none">&times;</button>
                </div>
            </div>
            <!-- Messages (scrollable only) -->
            <div class="flex-1 overflow-y-auto p-4 bg-gray-50" id="user-chat-messages">
                <template x-for="msg in messages" :key="msg.id">
                    <div :class="msg.sender_id == userId ? 'text-right' : 'text-left'" class="my-2">
                        <div :class="msg.sender_id == userId ? 'inline-block bg-green-100 text-green-800' : 'inline-block bg-gray-100 text-gray-800'"
                            class="rounded-lg px-3 py-2 max-w-xs"
                            style="word-break:break-word;">
                            <template x-if="msg.image">
                                <img 
                                    :src="'/storage/' + msg.image" 
                                    class="max-w-[120px] rounded mb-2"
                                    style="display:block;margin-left:auto;margin-right:auto;"
                                />
                            </template>
                            <template x-if="msg.message">   
                                <div class="block w-full">
                                    <span x-text="msg.message"></span>
                                </div>
                            </template>
                        </div>
                        <div class="text-xs text-gray-400 mt-1">
                            <span x-text="new Date(msg.created_at).toLocaleString()"></span>
                        </div>
                    </div>
                </template>
                <div x-show="messages.length == 0" class="text-gray-400 text-center text-xs mt-10">No messages yet.</div>
            </div>
            <!-- Input Area (fixed at the bottom, always visible) -->
            <form @submit.prevent="sendMessage()" class="border-t px-2 py-3 bg-white sticky bottom-0 z-10">
                <div class="flex items-end gap-2 w-full">
                    <!-- Image Upload -->
                    <label class="cursor-pointer flex-shrink-0">
                        <i class="bi bi-image text-2xl text-green-700"></i>
                        <input type="file" accept="image/*" class="hidden" @change="handleImageUpload">
                    </label>

                    <!-- Image Preview -->
                    <template x-if="imageFile">
                        <img :src="URL.createObjectURL(imageFile)" class="max-w-[80px] max-h-[90px] rounded border flex-shrink-0" />
                    </template>

                    <!-- Textarea Input with Emoji Icon -->
                    <div class="relative flex-1" x-data="{ showEmoji: false }">
<textarea 
    x-model="message"
    id="chat-message-textarea"
    rows="1"
    class="w-full border border-gray-300 text-base rounded-full px-4 py-1 focus:ring-2 focus:ring-green-400 bg-gray-50 pr-10"
    placeholder="Type a message..."
    style="min-height: 40px; max-height: 80px; overflow-y: auto;"
    @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
></textarea>
                        <!-- Emoji icon -->
                        <button type="button" 
                            class="absolute right-2 top-2 text-xl text-green-600"
                            title="Insert Emoji"
                            @click="showEmoji = !showEmoji">
                            😊
                        </button>
                        <!-- Emoji Picker Above the Icon -->
                        <div 
                            x-show="showEmoji" 
                            @click.away="showEmoji = false" 
                            class="absolute bottom-full mb-2 right-0 z-50 bg-white shadow-lg border rounded"
                        >
                            <emoji-picker></emoji-picker>
                        </div>
                    </div>

                    <!-- Like or Send Button -->
                    <template x-if="!message.trim()">
                        <button type="button" 
                            @click="sendLike()" 
                            class="text-green-600 text-2xl px-2 flex-shrink-0 active:scale-225 transition-transform duration-150" 
                            title="Send Like">
                            <i class="bi bi-hand-thumbs-up-fill"></i>
                        </button>
                    </template>
                    <template x-if="message.trim()">
                        <button type="submit" 
                            class="ml-1 bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-lg flex items-center justify-center flex-shrink-0" 
                            title="Send">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </template>
                </div>
            </form>
        </div>
    </template>
</div>
