<script>
import { inject } from "vue";
import { RouterLink, RouterView, useRouter } from "vue-router";
import Chat from "./components/Chat.vue";

export default {
  setup() {
    const router = useRouter();
    const isAuthenticated = inject("isAuthenticated");
    const isHelpdeskAgent = inject("isHelpdeskAgent");
    const userId = inject("userId");

    return { router, isAuthenticated, isHelpdeskAgent, userId };
  },
  methods: {
    logOut() {
      localStorage.removeItem("token");
      this.isAuthenticated = false;

      localStorage.removeItem("isHelpdeskAgent");
      this.isHelpdeskAgent = false;

      localStorage.removeItem("userId");
      this.userId = 0;

      this.router.replace("/login");
    }
  }
};
</script>

<template>
  <header v-if="this.isAuthenticated">
    <div class="wrapper">
      <nav>
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
          <div class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
            <RouterLink class="hover:text-indigo-600" to="/">Events</RouterLink>
          </div>
          <div class="hidden w-full md:block md:w-auto" id="navbar-default">
            <div class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
              <RouterLink v-if="this.isHelpdeskAgent" to="/helpdesk-page" class="hover:text-indigo-600">Helpdesk Page</RouterLink>
              <button class="hover:cursor-pointer hover:text-indigo-600" @click="logOut">Logout</button>
            </div>
          </div>
        </div>
      </nav>
    </div>
  </header>

  <RouterView />
  <Chat v-if="this.isAuthenticated" />
</template>
