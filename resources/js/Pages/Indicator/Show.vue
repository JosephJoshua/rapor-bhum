<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import { Indicator } from '@/types/indicator';
import { SubIndicator } from '@/types/subindicator';

const props = defineProps<{
  data: Indicator;
  subindicators: SubIndicator[];
}>();

const handleDeleteSubindicator = async (id: number) => {
  await axios.delete(
    route('indicators.subindicators.destroy', {
      indicator: props.data.id,
      subindicator: id,
    }),
  );

  router.reload({ only: ['subindicators'] });
};
</script>

<template>
  <Head :title="`Indikator ${data.name}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-6">
        <Link :href="route('indicators.index')">
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
          Indikator {{ data.name }}
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
              Daftar Subindikator
            </h3>

            <Link
              :href="
                route('indicators.subindicators.create', { indicator: data.id })
              "
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
                  <th scope="col" class="px-6 py-3">
                    <span class="sr-only">Aksi</span>
                  </th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(subindicator, index) in subindicators"
                  :key="subindicator.id"
                  class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700"
                >
                  <th
                    scope="row"
                    class="text-center px-6 py-4 font-medium whitespace-nowrap"
                  >
                    {{ index + 1 }}
                  </th>

                  <td class="px-6 py-4 text-gray-900 dark:text-white">
                    {{ subindicator.name }}
                  </td>

                  <td class="px-6 py-4">
                    <div class="flex justify-end items-center gap-2">
                      <Link
                        :href="
                          route('indicators.subindicators.edit', {
                            indicator: data.id,
                            subindicator: subindicator.id,
                          })
                        "
                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                      >
                        Ubah
                      </Link>

                      <DeleteButton
                        :prompt="`Apakah Anda yakin ingin menghapus subindikator ${subindicator.name}?`"
                        @delete="
                          () => handleDeleteSubindicator(subindicator.id)
                        "
                      />
                    </div>
                  </td>
                </tr>

                <tr v-if="subindicators.length === 0">
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
