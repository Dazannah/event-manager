<script>
import SuccessMessage from "@/components/SuccessMessage.vue";
import InputError from "@/components/InputError.vue";

export default {
  data() {
    return {
      form: {
        email: ""
      },
      response: {},
      error: "",
      success: "",
      isLoading: false
    };
  },
  methods: {
    send() {
      this.isLoading = true;
      this.resetExceptForm();
      this.axios
        .post("/forgot-password", {
          email: this.form.email
        })
        .then(res => {
          if (res.data.validation_errors) this.response = res.data;
          else if (res.data.error) this.error = res.data.error;
          else if (res.data.success) this.success = res.data.success;

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
      <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Forgot password?</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form class="space-y-6" @submit.prevent="send">
        <div>
          <label for="email" class="block text-sm/6 font-medium text-gray-900">Email address</label>
          <div class="mt-2">
            <input type="email" v-model="form.email" autocomplete="email" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
            <InputError v-bind:message="response.validation_errors?.email" />
          </div>
        </div>

        <div>
          <InputError v-bind:message="error" />
          <SuccessMessage v-bind:message="success" />
          <Loading v-if="isLoading"></Loading>
          <button v-else type="submit" class="flex w-full hover:cursor-pointer justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Send email</button>
        </div>
      </form>
    </div>
  </div>
</template>
