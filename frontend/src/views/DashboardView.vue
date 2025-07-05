<script>
import { initFlowbite } from "flowbite";

function initData() {
  return {
    events: [],
    form: {
      title: "",
      description: ""
    },
    response: {},
    error: "",
    success: "",
    isLoading: false,
    isGetData: false,
    userTz: Intl.DateTimeFormat().resolvedOptions().timeZone
  };
}

export default {
  mounted() {
    initFlowbite();

    this.getEvents();
  },
  data: initData,
  methods: {
    create() {
      this.isLoading = true;
      this.resetExceptForm();
      this.axios
        .post("/event/create", {
          title: this.form.title,
          description: this.form.description
        })
        .then(res => {
          if (res.data.validation_errors) this.response = res.data;
          else if (res.data.error) this.error = res.data.error;
          else {
            this.success = ["Event created."];

            this.resetForm();

            this.getEvents();
          }

          this.isLoading = false;
        })
        .catch(err => {
          this.error = [err.message];
          this.isLoading = false;
        });
    },
    getEvents() {
      this.isGetData = true;

      this.axios
        .get("/event")
        .then(res => {
          this.events = res.data;
          this.isGetData = false;
        })
        .catch(err => {
          this.isGetData = false;
        });
    },
    resetExceptForm() {
      this.response = {};
      this.success = "";
      this.error = "";
    },
    resetForm() {
      this.form = {};
    },
    localTzDate(utcDate) {
      return new Date(`${utcDate}Z`).toLocaleString();
    }
  }
};
</script>
<template>
  <nav>
    <div class="max-w-screen-xl px-4 py-3 mx-auto">
      <div class="flex items-center">
        <ul class="flex flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
          <li>
            <button data-modal-target="event-modal" data-modal-toggle="event-modal" type="button" class="focus:outline-none text-white bg-indigo-600 hover:bg-white hover:text-indigo-600 hover:border hover:border-indigo-600 focus:bg-white focus:text-indigo-600 focus:border focus:border-indigo-600 focus:ring-4 hover:border-indigo-600 hover:cursor-pointer focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2">New Event</button>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <Loading v-if="isGetData"></Loading>
  <div v-else class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
          <th scope="col" class="px-6 py-3">Title</th>
          <th scope="col" class="px-6 py-3">Occurrence</th>
          <th scope="col" class="px-6 py-3">Description</th>
          <th scope="col" class="px-6 py-3">
            <span class="sr-only">Edit</span>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="event in events" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
          <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ event.title }}</th>
          <td class="px-6 py-4">{{ localTzDate(event.occurrence) }}</td>
          <td class="px-6 py-4">{{ event.description }}</td>
          <td class="px-6 py-4 text-right">
            <a href="#" class="font-medium text-indigo-600 dark:text-indigo-500 hover:underline">Edit</a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- event form -->
  <!-- Main modal -->
  <div id="event-modal" tabindex="-1" aria-hidden="true" class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create New Event</h3>
          <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="event-modal">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <!-- Modal body -->
        <form class="p-4 md:p-5">
          <div class="grid gap-4 mb-4 grid-cols-2">
            <div class="col-span-2">
              <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</label>
              <input v-model="form.title" type="text" name="title" id="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Type event title" required />
              <InputError v-bind:message="response.validation_errors?.title" />
            </div>
            <div class="col-span-2">
              <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
              <textarea v-model="form.description" id="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write event description here"></textarea>
            </div>
          </div>
          <InputError v-bind:message="error" />
          <SuccessMessage v-bind:message="success" />
          <Loading v-if="isLoading"></Loading>
          <button v-else @click.prevent="create" class="text-white inline-flex items-center bg-indigo-600 hover:bg-indigo-800 hover:cursor-pointer focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
            Add new event
          </button>
        </form>
      </div>
    </div>
  </div>
</template>
