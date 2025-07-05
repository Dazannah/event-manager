<script>
export default {
  props: ["error", "success", "isLoading", "response", "form", "showDeleteButton", "isEdit", "deleteEvent"]
};
</script>

<template>
  <form @submit.prevent="$emit('on-submit', form)" class="p-4 md:p-5">
    <div class="grid gap-4 mb-4 grid-cols-2">
      <div class="col-span-2">
        <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</label>
        <input v-model="form.title" :disabled="isEdit" type="text" name="title" id="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Type event title" required />
        <InputError v-bind:message="response.validation_errors?.title" />
      </div>
      <div v-if="isEdit" class="col-span-2">
        <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Occurrence</label>
        <input v-model="form.occurrence" :disabled="isEdit" type="datetime-local" name="title" id="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Type event title" required />
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
    <div v-else>
      <button class="text-white inline-flex items-center bg-indigo-600 hover:bg-indigo-800 hover:cursor-pointer focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save</button>
      <button @click="deleteEvent(form.id)" v-if="isEdit" class="text-white inline-flex items-center mx-3 bg-red-600 hover:bg-red-800 hover:cursor-pointer focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Delete</button>
    </div>
  </form>
</template>
