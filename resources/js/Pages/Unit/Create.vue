<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  head: '',
});

const submit = () => {
  form.post(route('units.store'));
};
</script>

<template>
  <Head title="Tambah Unit" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-6">
        <Link :href="route('units.index')">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="transition-all duration-200 dark:text-gray-100 dark:hover:text-gray-400 hover:text-gray-700"
          >
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
        </Link>

        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          Tambah Unit
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg px-6 py-4"
        >
          <form @submit.prevent="submit">
            <div>
              <InputLabel for="name" value="Nama Unit" />

              <TextInput
                id="name"
                v-model="form.name"
                type="text"
                class="mt-1 block w-full"
                required
                autofocus
              />

              <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
              <InputLabel for="head" value="Kepala Unit" />

              <TextInput
                id="head"
                v-model="form.head"
                type="text"
                class="mt-1 block w-full"
                required
                autofocus
              />

              <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="flex items-center justify-end mt-4">
              <PrimaryButton
                class="ms-4"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                + Tambah
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
