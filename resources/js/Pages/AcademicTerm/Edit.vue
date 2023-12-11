<script setup lang="ts">
import Combobox from '@/Components/Combobox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { AcademicTerm } from '@/types/academic-term';
import { formatEntireTerm } from '@/utils/format-term';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
  data: AcademicTerm;
}>();

const form = useForm({
  start_year: props.data.start_year,
  end_year: props.data.end_year,
  term: props.data.term,
});

const submit = () => {
  form.put(route('academic-terms.update', { academic_term: props.data.id }));
};
</script>

<template>
  <Head
    :title="`Ubah Semester - ${formatEntireTerm(
      data.start_year,
      data.end_year,
      data.term,
    )}`"
  />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-6">
        <Link :href="route('academic-terms.index')">
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
          Ubah Semester -
          {{ formatEntireTerm(data.start_year, data.end_year, data.term) }}
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
              <InputLabel for="end-year" value="Tahun Ajaran Mulai" />

              <TextInput
                id="start-year"
                v-model="form.start_year"
                type="text"
                class="mt-1 block w-full"
                inputmode="numeric"
                required
                autofocus
              />

              <InputError class="mt-2" :message="form.errors.start_year" />
            </div>

            <div class="mt-4">
              <InputLabel for="end-year" value="Tahun Ajaran Akhir" />

              <TextInput
                id="code"
                v-model="form.end_year"
                type="text"
                class="mt-1 block w-full"
                inputmode="numeric"
                required
              />

              <InputError class="mt-2" :message="form.errors.end_year" />
            </div>

            <div class="mt-4">
              <InputLabel for="term" value="Semester" />

              <Combobox id="term" v-model="form.term" class="mt-1">
                <option value="1">Ganjil</option>
                <option value="2">Genap</option>
              </Combobox>

              <InputError class="mt-2" :message="form.errors.term" />
            </div>

            <div class="flex items-center justify-end mt-4">
              <PrimaryButton
                class="ms-4"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                Simpan
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
