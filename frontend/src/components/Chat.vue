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

    this.echo.private(`user.${this.userId}`).listen("HelpdeskMessageEvent", e => {
      console.log(e);
    });
  },

  data() {
    return {
      echo: {},
      chatOpen: false,
      chatMessages: []
    };
  },
  getChatMessages() {}
};
</script>

<template>
  <!-- Chat Floating Button -->
  <button v-if="!chatOpen" @click="chatOpen = !chatOpen" id="chat-button" class="hover:cursor-pointer fixed bottom-6 right-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg w-14 h-14 flex items-center justify-center shadow-lg focus:ring-4 focus:ring-indigo-300">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.985 9.985 0 0 1-4.35-1.02L3 21l1.56-4.66A7.985 7.985 0 0 1 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
    </svg>
  </button>

  <!-- Open Chat Window -->
  <div v-if="chatOpen" class="fixed bottom-6 right-6 w-80 max-w-full bg-white rounded-lg shadow-lg flex flex-col h-[500px] border border-gray-200">
    <!-- Header -->
    <div class="px-4 py-3 border-b flex items-center justify-between bg-indigo-600 rounded-t-lg">
      <h3 class="text-lg font-semibold text-white">Help desk</h3>
      <button @click="chatOpen = !chatOpen" type="button" class="hover:cursor-pointer text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
        </svg>
        <span class="sr-only">Close modal</span>
      </button>
    </div>

    <!-- Messages Area -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
      <!-- Example Incoming -->
      <div class="flex">
        <div class="bg-gray-200 p-3 rounded-lg max-w-[70%]">
          <p class="text-sm text-gray-800">Hello! How can I help you today?</p>
        </div>
      </div>
      <!-- Example Outgoing -->
      <div class="flex justify-end">
        <div class="bg-indigo-500 text-white p-3 rounded-lg max-w-[70%]">
          <p class="text-sm">I have a question about your service.</p>
        </div>
      </div>
    </div>

    <!-- Input -->
    <form class="p-4 border-t flex gap-2 bg-white">
      <input type="text" placeholder="Type your message..." class="flex-1 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required />
      <button type="submit" class="focus:outline-none text-white bg-indigo-600 hover:bg-white hover:text-indigo-600 border hover:border-indigo-600 focus:bg-white focus:text-indigo-600 focus:border focus:border-indigo-600 focus:ring-4 hover:border-indigo-600 hover:cursor-pointer focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2">Send</button>
    </form>
  </div>
</template>
