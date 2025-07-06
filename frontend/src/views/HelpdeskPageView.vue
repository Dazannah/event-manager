<script>
import Echo from "laravel-echo";
import { inject } from "vue";

export default {
  setup() {
    const userId = inject("userId");

    return { userId };
  },
  data() {
    return {
      error: "",
      message: "",
      isChatsLoading: true,
      isMessagesLoading: false,
      isSendUnderProgress: false,
      chatOpen: false,
      activeChat: {},
      helpdeskEcho: {},
      response: {},
      chats: {}
    };
  },
  mounted() {
    this.helpdeskEcho = new Echo({
      broadcaster: "reverb",
      key: import.meta.env.VITE_REVERB_APP_KEY,
      wsHost: import.meta.env.VITE_REVERB_HOST,
      wsPort: import.meta.env.VITE_REVERB_PORT,
      wssPort: import.meta.env.VITE_REVERB_PORT,
      forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
      enabledTransports: ["ws", "wss"],
      authEndpoint: `${import.meta.env.VITE_BACKEND_URL}/broadcasting/auth`,
      auth: {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`
        }
      }
    });

    this.getChats();
  },
  methods: {
    getChats() {
      this.axios
        .get("/helpdesk/chat")
        .then(res => {
          if (res.data.chats) this.chats = res.data.chats;
          if (res.data.error) this.error = [res.data.error];
          if (res.data.validation_errors) this.response = res.data;

          this.isChatsLoading = false;
        })
        .catch(err => {
          this.error = [err.message];
          this.isChatsLoading = false;
        });
    },
    getFreshActiveChats(chat_id) {
      this.activeChat = {};
      this.axios
        .get(`/helpdesk/chat/${chat_id}`)
        .then(res => {
          if (res.data.chat) this.activeChat = res.data.chat;
          if (res.data.error) this.error = [res.data.error];
          if (res.data.validation_errors) this.response = res.data;

          this.chatOpen = res.data.chat.chat_status?.technical_name == "open";

          this.isMessagesLoading = false;

          console.log(this.activeChat);
          this.scrollToBottom();
        })
        .catch(err => {
          this.error = [err.message];
          this.isMessagesLoading = false;
        });
    },
    startListenChat() {
      this.helpdeskEcho.private(`helpdesk.${this.activeChat.id}`).listen("ChatMessageEvent", e => {
        this.activeChat.messages.push(e.message);

        this.scrollToBottom();
      });
    },
    stopListenChat() {
      this.helpdeskEcho.leave(`helpdesk.${this.activeChat.id}`);
    },
    loadMessages(chat) {
      this.chatOpen = false;
      this.isMessagesLoading = true;
      this.stopListenChat();
      this.getFreshActiveChats(chat.id);

      this.startListenChat();
    },
    scrollToBottom() {
      this.$nextTick(() => {
        const container = this.$refs.helpdeskChatWindow;
        if (container) {
          container.scrollTop = container.scrollHeight;
        }
      });
    },
    sendAgentMessage() {
      this.isSendUnderProgress = true;
      this.axios
        .post("/helpdesk/message", {
          message: this.message,
          chat_id: this.activeChat.id
        })
        .then(res => {
          if (res.data.message) this.activeChat.messages.push(res.data.message);
          if (res.data.error) this.error = [res.data.error];
          if (res.data.validation_errors) this.response = res.data;

          this.error = [];
          this.message = "";
          this.isSendUnderProgress = false;

          this.scrollToBottom();
        })
        .catch(err => {
          this.error = [err.message];

          this.isSendUnderProgress = false;
        });
    },
    closeCurrentChat() {
      this.axios
        .patch(`/helpdesk/chat/${this.activeChat.id}/close`)
        .then(res => {
          if (res.data.chat) this.activeChat = res.data.chat;
          if (res.data.error) this.error = [res.data.error];
          if (res.data.validation_errors) this.response = res.data;

          this.chatOpen = res.data.chat.chat_status?.technical_name == "open";

          this.isMessagesLoading = false;

          this.getChats();
          this.scrollToBottom();
        })
        .catch(err => {
          this.error = [err.message];
        });
    },
    claimChat() {
      this.axios
        .patch(`/helpdesk/chat/${this.activeChat.id}/claim`)
        .then(res => {
          console.log(res);
          if (res.data.chat) this.activeChat = res.data.chat;
          if (res.data.error) this.error = [res.data.error];
          if (res.data.validation_errors) this.response = res.data;

          this.chatOpen = res.data.chat.chat_status?.technical_name == "open";

          this.isMessagesLoading = false;

          this.getChats();
          this.scrollToBottom();
        })
        .catch(err => {
          this.error = [err.message];
        });
    }
  }
};
</script>
<template>
  <div class="flex max-h-full">
    <div id="default-sidebar" class="flex flex-col max-h-[80vh] z-40 w-64 h-fit transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
      <div class="h-fit px-3 py-4 overflow-y-auto bg-indigo-50 dark:bg-gray-800 rounded-lg">
        <ul class="space-y-2 font-medium">
          <Loading v-if="isChatsLoading" />
          <li v-else v-for="chat in chats" :key="`chat-button-${chat.id}`">
            <button @click="loadMessages(chat)" class="hover:cursor-pointer flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:text-indigo-600 dark:hover:bg-gray-700 group">
              <svg class="shrink-0 w-5 h-5 text-gray-900 transition duration-75 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                <path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z" />
              </svg>
              <span class="flex-1 ms-3 whitespace-nowrap">{{ chat.user.name }}</span>
              <span class="inline-flex items-center justify-center px-2 ms-3 text-sm font-medium text-gray-800 rounded-full dark:bg-gray-700 dark:text-gray-300" :class="chat.chat_status.technical_name == 'open' ? 'bg-green-300' : 'bg-red-300'">
                {{ chat.chat_status.display_name }}
              </span>
              <span v-if="!chat.handled_to_agent" class="inline-flex items-center justify-center px-2 ms-3 text-sm font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">AI</span>
            </button>
          </li>
        </ul>
      </div>
    </div>

    <div v-if="activeChat" class="flex flex-col max-h-[80vh] p-4 ml-2 grow border-2 border-indigo-200 border-dashed rounded-lg">
      <div v-if="activeChat.chat_status?.technical_name == 'open'" class="px-4 py-1 bg-indigo-50 rounded-lg">
        <button v-if="!activeChat.handled_to_agent" @click="claimChat" type="button" class="focus:outline-none text-white bg-indigo-600 hover:bg-white hover:text-indigo-600 border hover:border-indigo-600 focus:bg-white focus:text-indigo-600 focus:border focus:border-indigo-600 focus:ring-4 hover:border-indigo-600 hover:cursor-pointer focus:ring-indigo-300 font-medium rounded-lg text-sm px-5">Claim Chat</button>
        <button v-if="activeChat.chat_status?.technical_name == 'open'" @click="closeCurrentChat" type="button" class="focus:outline-none text-white bg-red-600 hover:bg-white hover:text-red-600 border hover:border-red-600 focus:bg-white focus:text-red-600 focus:border focus:border-red-600 focus:ring-4 hover:border-red-600 hover:cursor-pointer focus:ring-red-300 font-medium rounded-lg text-sm px-5">Close Chat</button>
      </div>
      <!-- Messages Area -->
      <Loading v-if="isMessagesLoading" />
      <div v-else ref="helpdeskChatWindow" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
        <InputError v-bind:message="error" />
        <!-- Example Incoming -->
        <div v-for="message in activeChat.messages" :key="`message-${message.id}`">
          <!-- Outgoing -->
          <div v-if="message.user_id == userId" class="flex justify-end">
            <div class="flex items-start gap-2.5">
              <div class="flex flex-col w-full max-w-[320px] leading-1.5 p-4 border-gray-200 bg-indigo-500 rounded-tl-xl rounded-tr-xl rounded-bl-xl dark:bg-gray-700">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                  <span class="text-sm font-semibold text-white dark:text-white">{{ message.user?.name ?? "AI assistant" }}</span>
                  <span class="text-sm font-normal text-gray-100 dark:text-gray-400">{{ new Date(`${message.created_at}`).toLocaleString() }}</span>
                </div>
                <p class="text-sm font-normal py-2.5 text-white dark:text-white">{{ message.text }}</p>
              </div>
            </div>
          </div>

          <div v-else class="flex">
            <div class="flex items-start gap-2.5">
              <div class="flex flex-col w-full max-w-[320px] leading-1.5 p-4 border-gray-200 bg-gray-200 rounded-e-xl rounded-es-xl dark:bg-gray-700">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                  <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ message.user?.name ?? "AI assistant" }}</span>
                  <span class="text-sm font-normal text-gray-700 dark:text-gray-400">{{ new Date(`${message.created_at}`).toLocaleString() }}</span>
                </div>
                <p class="text-sm font-normal py-2.5 text-gray-900 dark:text-white">{{ message.text }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Input -->
      <InputError v-bind:message="response.validation_errors?.message" />
      <form v-if="chatOpen" @submit.prevent="sendAgentMessage" class="p-2 flex gap-2 bg-gray-200 h-12 rounded-lg">
        <input v-model="message" type="text" placeholder="Type your message..." class="h-full block w-full rounded-md bg-white px-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" required />

        <Loading v-if="isSendUnderProgress" />
        <button v-else @submit.prevent type="submit" class="focus:outline-none text-white bg-indigo-600 hover:bg-white hover:text-indigo-600 border hover:border-indigo-600 focus:bg-white focus:text-indigo-600 focus:border focus:border-indigo-600 focus:ring-4 hover:border-indigo-600 hover:cursor-pointer focus:ring-indigo-300 font-medium rounded-lg text-sm px-5">Send</button>
      </form>
    </div>
  </div>
</template>
