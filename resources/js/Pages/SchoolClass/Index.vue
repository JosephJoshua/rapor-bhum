<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { SchoolClass } from '@/types/school-class';
import { WithOptionalTeacher } from '@/types/teacher';
import { WithUnit } from '@/types/unit';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
  data: WithOptionalTeacher<WithUnit<SchoolClass>>[];
}>();
</script>

<template>
  <Head title="Daftar Kelas" />

  <AuthenticatedLayout>
    <template #header>
      <h2
        class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
      >
        Daftar Kelas
      </h2>
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
                  <th scope="col" class="px-6 py-3">Unit</th>
                  <th scope="col" class="px-6 py-3">Nama</th>
                  <th scope="col" class="px-6 py-3 text-center">Guru</th>
                  <th scope="col" class="px-6 py-3">
                    <span class="sr-only">Aksi</span>
                  </th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(schoolClass, index) in data"
                  :key="schoolClass.id"
                  class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700"
                >
                  <th
                    scope="row"
                    class="text-center px-6 py-4 font-medium whitespace-nowrap"
                  >
                    {{ index + 1 }}
                  </th>

                  <td class="px-6 py-4">
                    {{ schoolClass.unit.name }}
                  </td>

                  <td class="px-6 py-4 text-gray-900 dark:text-white">
                    {{ schoolClass.name }}
                  </td>

                  <td class="px-6 py-4 text-center">
                    {{ schoolClass.teacher?.name ?? '-' }}
                  </td>

                  <td>
                    <div
                      class="px-6 py-4 text-right flex justify-end items-center gap-2"
                    >
                      <Link
                        :href="
                          route('units.school-classes.show', {
                            unit: schoolClass.unit.id,
                            school_class: schoolClass.id,
                          })
                        "
                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                      >
                        Lihat
                      </Link>
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
