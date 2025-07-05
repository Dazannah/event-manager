<script>
import { ref } from "vue";
import EventFormPanel from "@/components/forms/EventFormPanel.vue";

function initData() {
  return {
    events: [],
    form: {
      title: "",
      description: ""
    },
    response: {},
    error: ref(""),
    success: ref(""),
    isLoading: ref(false),
    isGetData: false,
    userTz: Intl.DateTimeFormat().resolvedOptions().timeZone,
    showEditModal: false,
    showNewModal: false
  };
}

export default {
  mounted() {
    this.getEvents();
  },
  data: initData,
  methods: {
    create(form) {
      this.isLoading = true;
      this.resetExceptForm();
      this.axios
        .post("/event/create", {
          title: form.title,
          description: form.description
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
    },
    openNewModal() {
      this.resetExceptForm();
      this.resetForm();

      this.showNewModal = true;
    },
    closeNewModal() {
      this.showNewModal = false;

      this.resetExceptForm();
      this.resetForm();
    },
    openEditModal(event) {
      this.resetExceptForm();
      this.resetForm();
      this.form = { ...event };

      this.showEditModal = true;
    },
    closeEditModal() {
      this.showEditModal = false;
      this.resetExceptForm();
      this.resetForm();
    },
    edit(form) {
      this.isLoading = true;
      this.resetExceptForm();
      this.axios
        .post(`/event/${form.id}/edit`, {
          description: form.description
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
    deleteEvent(id) {
      this.isLoading = true;
      this.resetExceptForm();
      this.axios
        .delete(`/event/${id}/delete`)
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
            <button @click="openNewModal" type="button" class="focus:outline-none text-white bg-indigo-600 hover:bg-white hover:text-indigo-600 hover:border hover:border-indigo-600 focus:bg-white focus:text-indigo-600 focus:border focus:border-indigo-600 focus:ring-4 hover:border-indigo-600 hover:cursor-pointer focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2">New Event</button>
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
            <button @click="openEditModal(event)" type="button" class="hover:cursor-pointer font-medium text-indigo-600 dark:text-indigo-500 hover:underline">Edit</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- new-event-modal -->
  <div v-if="showNewModal" tabindex="-1" class="flex bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create New Event</h3>
          <button @click="closeNewModal" type="button" class="hover:cursor-pointer text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>
        <!-- Modal body -->
        <EventFormPanel @on-submit="create" :isLoading="isLoading" :error="error" :success="success" :response="response" :form="form" />
      </div>
    </div>
  </div>

  <!-- new-event-modal -->
  <div v-if="showEditModal" id="edit-event-modal" tabindex="-1" class="flex bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
        <!-- Modal header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Event</h3>
          <button @click="closeEditModal" type="button" class="hover:cursor-pointer text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="edit-event-modal">
            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close modal</span>
          </button>
        </div>

        <!-- Modal body -->
        <EventFormPanel :form="form" @on-submit="edit" :deleteEvent="deleteEvent" :isEdit="true" :isLoading="isLoading" :error="error" :success="success" :response="response" />
      </div>
    </div>
  </div>
</template>
