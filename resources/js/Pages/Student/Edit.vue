<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { SchoolClass } from '@/types/school-class';
import { Student } from '@/types/student';
import { Unit } from '@/types/unit';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
  data: Student;
  schoolClass: SchoolClass;
  unit: Unit;
}>();

const form = useForm({
  name: props.data.name,
  nis: props.data.nis,
});

const submit = () => {
  form.put(
    route('units.school-classes.students.update', {
      student: props.data.id,
      school_class: props.schoolClass.id,
      unit: props.unit.id,
    }),
  );
};
</script>

<template>
  <Head
    :title="`Unit ${unit.name} | Kelas ${schoolClass.name} | Ubah Murid - ${data.name}`"
  />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-6">
        <Link
          :href="
            route('units.school-classes.show', {
              unit: unit.id,
              school_class: schoolClass.id,
            })
          "
        >
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
          Unit {{ unit.name }} | Kelas {{ schoolClass.name }} | Ubah Murid -
          {{ data.name }}
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
              <InputLabel for="name" value="Nama Murid" />

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
              <InputLabel for="nis" value="Nomor Induk Siswa" />

              <TextInput
                id="nis"
                v-model="form.nis"
                type="text"
                class="mt-1 block w-full"
                inputmode="numeric"
                required
              />

              <InputError class="mt-2" :message="form.errors.nis" />
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
