<script>
import InputError from "@/components/InputError.vue";
import { useRouter } from "vue-router";
import { inject } from "vue";

function initData() {
  return {
    form: {
      email: "",
      password: ""
    },
    response: {},
    error: ""
  };
}

export default {
  setup() {
    const router = useRouter();
    const isAuthenticated = inject("isAuthenticated");
    const isHelpdeskAgent = inject("isHelpdeskAgent");

    return { router, isAuthenticated, isHelpdeskAgent };
  },
  data: initData,
  methods: {
    login() {
      this.resetExceptForm();
      this.axios
        .post("/login", {
          email: this.form.email,
          password: this.form.password
        })
        .then(res => {
          if (res.data.error) this.response = res.data;
          else {
            localStorage.setItem("token", res.data.token);
            this.axios.defaults.headers.common["Authorization"] = localStorage.getItem("token");
            this.isAuthenticated = true;

            const arrayToken = localStorage.getItem("token").split(".");
            const tokenPayload = JSON.parse(atob(arrayToken[1]));
            const isHelpdeskAgent = tokenPayload.scopes[0] == "helpdeskAgent" ? true : false;
            console.log(tokenPayload.scopes[0]);

            this.isHelpdeskAgent = isHelpdeskAgent;

            localStorage.setItem("isHelpdeskAgent", isHelpdeskAgent);

            this.router.replace("/dashboard");
          }
        })
        .catch(err => {
          this.error = [err.message];
        });
    },

    resetExceptForm() {
      this.response = {};
      this.error = "";
    }
  }
};
</script>

<template>
  <div class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Sign in to your account</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form class="space-y-6" @submit.prevent="login">
        <div>
          <label for="email" class="block text-sm/6 font-medium text-gray-900">Email address</label>
          <div class="mt-2">
            <input type="email" v-model="form.email" autocomplete="email" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
            <InputError v-bind:message="response.error?.email" />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>
            <div class="text-sm">
              <RouterLink to="/forgot-password" class="font-semibold text-indigo-600 hover:text-indigo-500">Forgot password?</RouterLink>
            </div>
          </div>
          <div class="mt-2">
            <input type="password" v-model="form.password" autocomplete="current-password" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
            <InputError v-bind:message="response.error?.password" />
          </div>
        </div>

        <div>
          <InputError v-bind:message="error" />
          <button type="submit" class="flex w-full hover:cursor-pointer justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Sign in</button>
        </div>
      </form>
    </div>
  </div>
</template>
