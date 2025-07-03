<script>
import { inject } from "vue";
import { RouterLink, RouterView, useRouter } from "vue-router";

export default {
  setup() {
    const router = useRouter();
    const isAuthenticated = inject("isAuthenticated");

    return { router, isAuthenticated };
  },
  methods: {
    logOut() {
      localStorage.removeItem("token");
      this.isAuthenticated = false;

      this.router.replace("/login");
    }
  }
};
</script>

<template>
  <header v-if="this.isAuthenticated">
    <div class="wrapper">
      <nav>
        <RouterLink to="/">Home</RouterLink>
        <RouterLink to="/login">Login</RouterLink>
        <button @click="logOut">Logout</button>
      </nav>
    </div>
  </header>

  <RouterView />
</template>
