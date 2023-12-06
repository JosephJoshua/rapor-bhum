<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import axios from 'axios';
import { Teacher } from '@/types/teacher';
import { WithSchoolClasses } from '@/types/school-class';

const props = defineProps<{
  data: WithSchoolClasses<Teacher>[];
}>();

const handleDelete = async (id: number) => {
  await axios.delete(route('teachers.destroy', { teacher: id }));
  router.reload({ only: ['data'] });
};
</script>

<template>
  <Head title="Daftar Guru" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between gap-4">
        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          Daftar Guru
        </h2>

        <Link :href="route('teachers.create')">
          <SecondaryButton>+ Tambah</SecondaryButton>
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
          <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table
              class="w-full text-left rtl:text-right text-gray-500 dark:text-gray-400"
            >
              <thead
                class="text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
              >
                <tr>
                  <th scope="col" class="px-6 py-3 text-center">#</th>
                  <th scope="col" class="px-6 py-3">Nama</th>
                  <th scope="col" class="px-6 py-3">E-mail</th>
                  <th scope="col" class="px-6 py-3">Kelas</th>
                  <th scope="col" class="px-6 py-3">
                    <span class="sr-only">Aksi</span>
                  </th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(teacher, index) in data"
                  :key="teacher.id"
                  class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700"
                >
                  <th
                    scope="row"
                    class="text-center px-6 py-4 font-medium whitespace-nowrap"
                  >
                    {{ index + 1 }}
                  </th>

                  <td class="px-6 py-4 text-gray-900 dark:text-white">
                    {{ teacher.name }}
                  </td>

                  <td class="px-6 py-4">
                    {{ teacher.email }}
                  </td>

                  <td class="px-6 py-4">
                    {{
                      teacher.school_classes.length === 0
                        ? '-'
                        : teacher.school_classes.map((c) => c.name).join(', ')
                    }}
                  </td>

                  <td
                    class="px-6 py-4 text-right flex justify-end items-center gap-2"
                  >
                    <Link
                      :href="route('teachers.edit', { teacher: teacher.id })"
                      class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                    >
                      Ubah
                    </Link>

                    <DeleteButton
                      :prompt="`Apakah Anda yakin ingin menghapus guru ${teacher.name}? Ini akan mempengaruhi data lain seperti kelas yang terkait dengan guru ini.`"
                      @delete="() => handleDelete(teacher.id)"
                    />
                  </td>
                </tr>

                <tr v-if="data.length === 0">
                  <td colspan="12">
                    <div class="flex justify-center items-center py-12 px-4">
                      Tidak dapat menemukan data
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
