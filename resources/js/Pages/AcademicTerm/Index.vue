<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import axios from 'axios';
import { AcademicTerm } from '@/types/academic-term';
import { formatEntireTerm, formatTerm, formatYear } from '@/utils/format-term';

const props = defineProps<{
  data: AcademicTerm[];
}>();

const handleDelete = async (id: number) => {
  await axios.delete(route('academic-terms.destroy', { academic_term: id }));
  router.reload({ only: ['data'] });
};
</script>

<template>
  <Head title="Daftar Semester" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between gap-4">
        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          Daftar Semester
        </h2>

        <Link :href="route('academic-terms.create')">
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
                  <th scope="col" class="px-6 py-3">Tahun Ajaran</th>
                  <th scope="col" class="px-6 py-3">Semester</th>
                  <th scope="col" class="px-6 py-3">
                    <span class="sr-only">Aksi</span>
                  </th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(term, index) in data"
                  :key="term.id"
                  class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700"
                >
                  <th
                    scope="row"
                    class="text-center px-6 py-4 font-medium whitespace-nowrap"
                  >
                    {{ index + 1 }}
                  </th>

                  <td class="px-6 py-4 text-gray-900 dark:text-white">
                    {{ formatYear(term.start_year, term.end_year) }}
                  </td>

                  <td class="px-6 py-4 text-gray-900 dark:text-white">
                    {{ formatTerm(term.term) }}
                  </td>

                  <td
                    class="px-6 py-4 text-right flex justify-end items-center gap-2"
                  >
                    <Link
                      :href="
                        route('academic-terms.edit', { academic_term: term.id })
                      "
                      class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                    >
                      Ubah
                    </Link>

                    <DeleteButton
                      :prompt="`Apakah Anda yakin ingin menghapus semester ${formatEntireTerm(
                        term.start_year,
                        term.end_year,
                        term.term,
                      )}?`"
                      @delete="() => handleDelete(term.id)"
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
