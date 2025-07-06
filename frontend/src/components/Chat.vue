<script>
import { configureEcho, useEcho } from "@laravel/echo-vue";
import Echo from "laravel-echo";
import { inject } from "vue";

export default {
  setup() {
    const userId = inject("userId");

    return { userId };
  },
  mounted() {
    this.echo = new Echo({
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

    this.getChat();

    this.scrollToBottom();
  },

  data() {
    return {
      error: "",
      isChatLoading: true,
      isSendUnderProgress: false,
      echo: {},
      isChatOpen: false,
      chatMessages: [],
      chat: {},
      echo: {},
      message: "",
      response: {}
    };
  },
  methods: {
    getChat() {
      this.axios
        .get("/chat")
        .then(res => {
          this.chat = res.data.chat;

          this.connectChat();

          this.isChatLoading = false;
        })
        .catch(err => {
          this.error = [err.message];
        });
    },
    sendMessage() {
      this.response = {};
      this.isSendUnderProgress = true;
      this.axios
        .post("/chat/message", {
          message: this.message,
          chat_id: this.chat.id
        })
        .then(res => {
          console.log(res);
          if (res.data.message) this.chat.messages.push(res.data.message);
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
    handleToAgent() {
      this.axios
        .patch("/chat/ask-for-agent", {
          chat_id: this.chat.id
        })
        .then(res => {
          this.chat = res.data.chat;
        })
        .catch(err => {
          this.error = [err.message];
        });
    },
    connectChat() {
      this.echo.private(`user.${this.chat.id}`).listen("HelpdeskMessageEvent", e => {
        this.chat.messages.push(e.message);

        this.scrollToBottom();
      });
    },
    toggleChat() {
      if (!this.isChatLoading) {
        this.isChatOpen = !this.isChatOpen;
        this.scrollToBottom();
      }
    },
    scrollToBottom() {
      this.$nextTick(() => {
        const container = this.$refs.chatWindow;
        if (container) {
          container.scrollTop = container.scrollHeight;
        }
      });
    }
  }
};
</script>

<template>
  <!-- Chat Floating Button -->
  <button v-if="!isChatOpen" @click="toggleChat" id="chat-button" class="hover:cursor-pointer fixed bottom-6 right-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg w-14 h-14 flex items-center justify-center shadow-lg focus:ring-4 focus:ring-indigo-300">
    <Loading v-if="isChatLoading" />
    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.985 9.985 0 0 1-4.35-1.02L3 21l1.56-4.66A7.985 7.985 0 0 1 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
    </svg>
  </button>

  <!-- Open Chat Window -->
  <div v-if="isChatOpen" class="fixed bottom-6 right-6 w-80 max-w-full bg-white rounded-lg shadow-lg flex flex-col h-[500px] border border-gray-200">
    <!-- Header -->
    <div class="px-4 py-1 border-b flex items-center justify-between bg-indigo-600 rounded-t-lg">
      <h3 class="text-lg font-semibold text-white">Help desk</h3>
      <button @click="toggleChat" type="button" class="hover:cursor-pointer text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
        </svg>
        <span class="sr-only">Close modal</span>
      </button>
    </div>
    <div v-if="!chat.handled_to_agent" @click.prevent="handleToAgent" class="px-4 py-1 text flex items-center justify-between bg-gray-200">
      <button class="hover:cursor-pointer"><h3 class="font-semibold">Click here for agent help</h3></button>
    </div>

    <!-- Messages Area -->
    <div ref="chatWindow" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
      <InputError v-bind:message="error" />
      <!-- Example Incoming -->
      <div v-for="message in chat.messages" :key="message.id">
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
    <form @submit.prevent="sendMessage" class="p-2 flex gap-2 bg-gray-200 h-12">
      <input v-model="message" type="text" placeholder="Type your message..." class="h-full block w-full rounded-md bg-white px-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" required />

      <Loading v-if="isSendUnderProgress" />
      <button v-else @submit.prevent type="submit" class="focus:outline-none text-white bg-indigo-600 hover:bg-white hover:text-indigo-600 border hover:border-indigo-600 focus:bg-white focus:text-indigo-600 focus:border focus:border-indigo-600 focus:ring-4 hover:border-indigo-600 hover:cursor-pointer focus:ring-indigo-300 font-medium rounded-lg text-sm px-5">Send</button>
    </form>
  </div>
</template>
