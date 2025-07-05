<script>
import Echo from "laravel-echo";
import { inject } from "vue";

export default {
  setup() {
    const userId = inject("userId");

    return { userId };
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

    this.helpdeskEcho.private(`helpdesk`).listen("ChatMessageEvent", e => {
      console.log(e);
    });
  }
};
</script>
<template>
  <div class="helpdeskPage">
    <h1>Helpdesk page</h1>
  </div>
</template>
