<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import axios from 'axios';
import { GradeDescriptor } from '@/types/grade-descriptor';

const props = defineProps<{
  data: GradeDescriptor[];

  // eslint-disable-next-line vue/prop-name-casing
  max_grade: number;
}>();

const gradeToPercentage = (grade: number) => {
  if (props.max_grade === 0 && grade === 0) {
    return 100;
  }

  return Math.round((grade / props.max_grade) * 100 * 100) / 100;
};

const handleDelete = async (id: number) => {
  await axios.delete(
    route('grade-descriptors.destroy', { grade_descriptor: id }),
  );

  router.reload({ only: ['data', 'max_grade'] });
};
</script>

<template>
  <Head title="Daftar Predikat" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between gap-4">
        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          Daftar Predikat
        </h2>

        <Link :href="route('grade-descriptors.create')">
          <SecondaryButton>+ Tambah</SecondaryButton>
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg px-6 py-4"
        >
          <div class="flex items-center justify-between gap-4 mb-4">
            <span class="font-semibold text-gray-800 dark:text-gray-200">
              Skala dari 0 sampai {{ max_grade }}
            </span>
          </div>

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
                  <th scope="col" class="px-6 py-3">Kode</th>
                  <th scope="col" class="px-6 py-3">Prefiks Deskripsi</th>
                  <th scope="col" class="px-6 py-3 text-center">
                    Skor Minimum
                  </th>
                  <th scope="col" class="px-6 py-3 text-center">
                    Skor Maksimum
                  </th>
                  <th scope="col" class="px-6 py-3 text-center">Persentase</th>
                  <th scope="col" class="px-6 py-3">
                    <span class="sr-only">Aksi</span>
                  </th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(gradeDescriptor, index) in data"
                  :key="gradeDescriptor.id"
                  class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700"
                >
                  <th
                    scope="row"
                    class="text-center px-6 py-4 font-medium whitespace-nowrap"
                  >
                    {{ index + 1 }}
                  </th>

                  <td class="px-6 py-4 text-gray-900 dark:text-white">
                    {{ gradeDescriptor.name }}
                  </td>

                  <td class="px-6 py-4 text-gray-900 dark:text-white">
                    {{ gradeDescriptor.code }}
                  </td>

                  <td class="px-6 py-4 text-gray-900 dark:text-white">
                    {{ gradeDescriptor.description_prefix }}
                  </td>

                  <td class="px-6 py-4 text-center">
                    {{ gradeDescriptor.min_grade }}
                  </td>

                  <td class="px-6 py-4 text-center">
                    {{ gradeDescriptor.max_grade }}
                  </td>

                  <td class="px-6 py-4 text-center">
                    {{ gradeToPercentage(gradeDescriptor.min_grade) }}% -
                    {{ gradeToPercentage(gradeDescriptor.max_grade) }}%
                  </td>

                  <td class="px-6 py-4 text-right">
                    <div class="flex justify-end items-center gap-2">
                      <Link
                        :href="
                          route('grade-descriptors.edit', {
                            grade_descriptor: gradeDescriptor.id,
                          })
                        "
                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                      >
                        Ubah
                      </Link>

                      <DeleteButton
                        :prompt="`Apakah Anda yakin ingin menghapus predikat ${gradeDescriptor.name}?`"
                        @delete="() => handleDelete(gradeDescriptor.id)"
                      />
                    </div>
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
