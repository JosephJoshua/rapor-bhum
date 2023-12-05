<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Unit } from '@/types/unit';
import { SchoolClass } from '@/types/school-class';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import { Teacher, WithOptionalTeacher } from '@/types/teacher';
import Combobox from '@/Components/Combobox.vue';
import { ref } from 'vue';

const props = defineProps<{
  data: Unit;
  classes: WithOptionalTeacher<SchoolClass>[];
  teachers: Teacher[];
}>();

const isLoadingToastOpen = ref(false);

const openLoadingToast = () => {
  isLoadingToastOpen.value = true;
};

const closeLoadingToast = () => {
  isLoadingToastOpen.value = false;
};

const handleDeleteClass = async (id: number) => {
  await axios.delete(
    route('units.school-classes.destroy', {
      school_class: id,
      unit: props.data.id,
    }),
  );

  router.reload({ only: ['classes'] });
};

const handleChangeTeacher = async (
  schoolClassId: number,
  teacherId: number,
) => {
  openLoadingToast();

  const startTime = new Date();
  const minimumOpenTimeMs = 500;

  try {
    await axios.put(
      route('units.school-classes.update-teacher', {
        school_class: schoolClassId,
        unit: props.data.id,
      }),
      {
        teacher_id: teacherId,
      },
    );

    router.reload({ only: ['classes'] });
  } finally {
    const endTime = new Date();
    const elapsedMs = endTime.getTime() - startTime.getTime();

    if (elapsedMs <= minimumOpenTimeMs) {
      setTimeout(closeLoadingToast, minimumOpenTimeMs - elapsedMs);
    } else {
      closeLoadingToast();
    }
  }
};
</script>

<template>
  <Head :title="`Unit ${data.name}`" />

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
          Unit {{ data.name }}
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg px-6 py-4"
        >
          <div class="flex items-center justify-between gap-4 mb-4">
            <h3
              class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
              Daftar Kelas
            </h3>

            <Link
              :href="route('units.school-classes.create', { unit: data.id })"
            >
              <SecondaryButton>+ Tambah</SecondaryButton>
            </Link>
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
                  <th scope="col" class="px-6 py-3">Guru</th>
                  <th scope="col" class="px-6 py-3">
                    <span class="sr-only">Aksi</span>
                  </th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(schoolClass, index) in classes"
                  :key="schoolClass.id"
                  class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700"
                >
                  <th
                    scope="row"
                    class="text-center px-6 py-4 font-medium whitespace-nowrap"
                  >
                    {{ index + 1 }}
                  </th>

                  <td class="px-6 py-4 text-gray-900 dark:text-white">
                    {{ schoolClass.name }}
                  </td>

                  <td class="px-6 py-4">
                    <Combobox
                      :model-value="schoolClass.teacher?.id?.toString()"
                      @update:model-value="
                        (teacherId) =>
                          handleChangeTeacher(schoolClass.id, teacherId)
                      "
                    >
                      <option value="">-- Tanpa Guru --</option>

                      <option
                        v-for="teacher in teachers"
                        :key="teacher.id"
                        :value="teacher.id"
                      >
                        {{ teacher.name }}
                      </option>
                    </Combobox>
                  </td>

                  <td class="px-6 py-4">
                    <div class="flex justify-end items-center gap-2">
                      <Link
                        :href="
                          route('units.school-classes.show', {
                            school_class: schoolClass.id,
                            unit: data.id,
                          })
                        "
                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                      >
                        Lihat
                      </Link>

                      <Link
                        :href="
                          route('units.school-classes.edit', {
                            school_class: schoolClass.id,
                            unit: data.id,
                          })
                        "
                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                      >
                        Ubah
                      </Link>

                      <DeleteButton
                        :prompt="`Apakah Anda yakin ingin menghapus kelas ${schoolClass.name}?`"
                        @delete="() => handleDeleteClass(schoolClass.id)"
                      />
                    </div>
                  </td>
                </tr>

                <tr v-if="classes.length === 0">
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

    <div
      :class="[
        isLoadingToastOpen ? 'flex' : 'hidden',
        'fixed bottom-8 inset-x-0 mx-auto items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800',
      ]"
      role="alert"
    >
      <div
        class="select-none inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-blue-500 bg-blue-100 rounded-lg dark:bg-blue-800 dark:text-blue-200"
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
          class="feather feather-save"
        >
          <path
            d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"
          ></path>
          <polyline points="17 21 17 13 7 13 7 21"></polyline>
          <polyline points="7 3 7 8 15 8"></polyline>
        </svg>
      </div>

      <div class="ms-3 text-sm font-normal select-none">
        Sedang menyimpan perubahan...
      </div>

      <button
        type="button"
        class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
        aria-label="Close"
      >
        <span class="sr-only">Tutup</span>
        <svg
          class="w-3 h-3"
          aria-hidden="true"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 14 14"
        >
          <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
          />
        </svg>
      </button>
    </div>
  </AuthenticatedLayout>
</template>
