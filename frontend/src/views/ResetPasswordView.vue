<script>
import InputError from "@/components/InputError.vue";
import SuccessMessage from "@/components/SuccessMessage.vue";
import { useRouter } from "vue-router";
import { inject } from "vue";

function initData() {
  return {
    form: {
      email: this.$route.query.email,
      password: "",
      passwordConfirm: "",
      token: this.$route.query.token
    },
    response: {},
    error: "",
    success: "",
    isLoading: false
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
    resetPassword() {
      this.isLoading = true;
      this.resetExceptForm();

      if (this.form.password != this.form.passwordConfirm) {
        this.error = ["The two password are not the same."];

        this.isLoading = false;
        return;
      }

      this.axios
        .post("/reset-password", {
          email: this.form.email,
          password: this.form.password,
          passwordConfirm: this.form.passwordConfirm,
          token: this.form.token
        })
        .then(res => {
          if (res.data.validation_errors) this.response = res.data;
          else if (res.data.error) this.error = res.data.error;
          else {
            this.success = ["Password success fully updated."];
            this.router.replace("/login");
          }

          this.isLoading = false;
        })
        .catch(err => {
          this.error = [err.message];
          this.isLoading = false;
        });
    },

    resetExceptForm() {
      this.response = {};
      this.error = "";
      this.success = "";
    }
  }
};
</script>
<template>
  <div class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Reset password</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form class="space-y-6" @submit.prevent="resetPassword">
        <div>
          <div class="flex items-center justify-between">
            <label for="email" class="block text-sm/6 font-medium text-gray-900">Email address</label>
            <div class="text-sm">
              <RouterLink to="/login" class="font-semibold text-indigo-600 hover:text-indigo-500">To login</RouterLink>
            </div>
          </div>
          <div class="mt-2">
            <input type="email" v-model="form.email" autocomplete="email" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
            <InputError v-bind:message="response.validation_errors?.email" />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>
          </div>
          <div class="mt-2">
            <input type="password" v-model="form.password" autocomplete="current-password" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
            <InputError v-bind:message="response.validation_errors?.password" />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label for="password" class="block text-sm/6 font-medium text-gray-900">Confirm Password</label>
          </div>
          <div class="mt-2">
            <input type="password" v-model="form.passwordConfirm" autocomplete="current-password" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
            <InputError v-bind:message="response.validation_errors?.passwordConfirm" />
          </div>
        </div>

        <div>
          <InputError v-bind:message="response.validation_errors?.token" />
          <InputError v-bind:message="error" />
          <SuccessMessage v-bind:message="success" />
          <Loading v-if="isLoading"></Loading>
          <button v-else type="submit" class="flex w-full hover:cursor-pointer justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Reset password</button>
        </div>
      </form>
    </div>
  </div>
</template>
