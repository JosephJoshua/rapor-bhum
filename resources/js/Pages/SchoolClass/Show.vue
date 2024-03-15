<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Student } from '@/types/student';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import { SchoolClass } from '@/types/school-class';
import { Unit } from '@/types/unit';
import Popper from 'vue3-popper';
import { Indicator } from '@/types/indicator';
import { WithSubIndicators } from '@/types/subindicator';
import { WithIndicatorGradeDescriptors } from '@/types/grade-descriptor';
import { ref, computed, watch } from 'vue';
import { AcademicTerm } from '@/types/academic-term';
import { formatEntireTerm } from '@/utils/format-term';

const props = defineProps<{
  data: SchoolClass;
  unit: Unit;
  students: WithIndicatorGradeDescriptors<WithSubIndicators<Student>>[];
  indicators: WithSubIndicators<Indicator>[];

  // eslint-disable-next-line vue/prop-name-casing
  academic_terms: AcademicTerm[];
}>();

const isLoadingToastOpen = ref(false);

const openLoadingToast = () => {
  isLoadingToastOpen.value = true;
};

const closeLoadingToast = () => {
  isLoadingToastOpen.value = false;
};

const INDICATOR_IDS_TO_SHOW_STORAGE_KEY = 'indicator-ids-to-show';
const SELECTED_TERM_ID_STORAGE_KEY = 'selected-term-id';

const selectedTermId = ref<number | null>(
  (() => {
    const storedValue = localStorage.getItem(SELECTED_TERM_ID_STORAGE_KEY);

    if (storedValue === null) {
      return null;
    }

    const parsed = JSON.parse(storedValue);
    const got = props.academic_terms.find((term) => term.id === parsed)?.id;

    return got ?? null;
  })(),
);

const indicatorIdsToShow = ref<Set<number>>(
  (() => {
    const storedValue = localStorage.getItem(INDICATOR_IDS_TO_SHOW_STORAGE_KEY);

    if (storedValue === null) {
      return new Set<number>();
    }

    const parsed = JSON.parse(storedValue);
    if (!Array.isArray(parsed)) {
      return new Set<number>();
    }

    const filtered = parsed.filter((el) => {
      return (
        props.indicators.find((indicator) => indicator.id === el) !== undefined
      );
    }) as number[];

    return new Set<number>(filtered);
  })(),
);

const indicatorsToShow = computed(() => {
  return props.indicators.filter((indicator) => {
    return indicatorIdsToShow.value.has(indicator.id);
  });
});

const selectedTerm = computed(() => {
  return (
    props.academic_terms.find((term) => term.id === selectedTermId.value) ??
    null
  );
});

watch(indicatorIdsToShow, () => {
  localStorage.setItem(
    INDICATOR_IDS_TO_SHOW_STORAGE_KEY,
    JSON.stringify(Array.from(indicatorIdsToShow.value)),
  );
});

watch(
  selectedTermId,
  () => {
    localStorage.setItem(
      SELECTED_TERM_ID_STORAGE_KEY,
      JSON.stringify(selectedTermId.value),
    );

    router.reload({
      only: ['students'],
      data: {
        academic_term_id: selectedTermId.value,
      },
      replace: true,
    });
  },
  { immediate: true },
);

const toggleIndicatorVisibility = (id: number, visibility: boolean) => {
  if (visibility) {
    indicatorIdsToShow.value.add(id);
  } else {
    indicatorIdsToShow.value.delete(id);
  }

  localStorage.setItem(
    INDICATOR_IDS_TO_SHOW_STORAGE_KEY,
    JSON.stringify(Array.from(indicatorIdsToShow.value)),
  );
};

const selectTerm = (id: number) => {
  selectedTermId.value = id;
};

let studentSubindicatorPromises: Promise<void>[] = [];
let studentSubindicatorStartTime: Date | null = null;

const LOADING_TOAST_MINIMUM_OPEN_TIME_MS = 500;

const toggleStudentSubindicator = async (
  studentId: number,
  subindicatorId: number,
  checked: boolean,
) => {
  if (selectedTermId.value === null) {
    return;
  }

  openLoadingToast();

  if (studentSubindicatorStartTime === null) {
    studentSubindicatorStartTime = new Date();
  }

  const data = {
    student: studentId,
    academic_term: selectedTermId.value,
    subindicator: subindicatorId,
  };

  let promise: Promise<void>;

  if (checked) {
    promise = axios.post(
      route('academic-terms.students.subindicators.store', data),
    );
  } else {
    promise = axios.delete(
      route('academic-terms.students.subindicators.destroy', data),
    );
  }

  studentSubindicatorPromises.push(promise);

  promise.finally(() => {
    studentSubindicatorPromises.splice(
      studentSubindicatorPromises.indexOf(promise),
      1,
    );

    if (studentSubindicatorPromises.length === 0) {
      const endTime = new Date();
      const elapsedMs =
        endTime.getTime() - (studentSubindicatorStartTime?.getTime() ?? 0);

      const close = () => {
        router.reload({ only: ['students'] });
        closeLoadingToast();
        studentSubindicatorStartTime = null;
      };

      if (elapsedMs <= LOADING_TOAST_MINIMUM_OPEN_TIME_MS) {
        setTimeout(close, LOADING_TOAST_MINIMUM_OPEN_TIME_MS - elapsedMs);
      } else {
        close();
      }
    }
  });

  await promise;
};

const handleDeleteStudent = async (id: number) => {
  await axios.delete(
    route('units.school-classes.students.destroy', {
      student: id,
      school_class: props.data.id,
      unit: props.unit.id,
    }),
  );

  router.reload({ only: ['students'] });
};

const getReportCardUrl = (studentId: number) => {
  const url = new URL(route('report-card'));

  selectedTermId.value &&
    url.searchParams.append(
      'academic_term_id',
      selectedTermId.value.toString(),
    );

  url.searchParams.append('student_id', studentId.toString());

  return url.href;
};
</script>

<template>
  <Head :title="`Unit ${unit.name} | Kelas ${data.name}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-6">
        <Link
          :href="
            $page.props.auth.user.role === 'teacher'
              ? route('school-classes.index')
              : route('units.show', { unit: unit.id })
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
          Unit {{ unit.name }} | Kelas {{ data.name }}
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg px-6 py-4"
        >
          <div class="flex items-center justify-between gap-4 mb-2">
            <h3
              class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
              Daftar Murid
            </h3>

            <Link
              :href="
                route('units.school-classes.students.create', {
                  school_class: data.id,
                  unit: unit.id,
                })
              "
            >
              <SecondaryButton>+ Tambah</SecondaryButton>
            </Link>
          </div>

          <div class="mb-4 flex items-center gap-4">
            <Popper arrow>
              <button
                class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-2 focus:ring-gray-200 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                type="button"
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
                  class="w-3 h-3 mr-2"
                >
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>

                {{
                  selectedTerm === null
                    ? 'Pilih Semester'
                    : formatEntireTerm(
                        selectedTerm.start_year,
                        selectedTerm.end_year,
                        selectedTerm.term,
                      )
                }}

                <svg
                  class="w-2.5 h-2.5 ml-4"
                  aria-hidden="true"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 10 6"
                >
                  <path
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="m1 1 4 4 4-4"
                  />
                </svg>
              </button>

              <template #content>
                <div
                  class="w-56 bg-white divide-gray-100 rounded-lg shadow-lg dark:bg-gray-700 dark:divide-gray-600"
                >
                  <ul
                    class="max-h-64 px-3 py-4 overflow-y-auto text-sm text-gray-700 dark:text-gray-200"
                  >
                    <li v-for="term in academic_terms" :key="term.id">
                      <div
                        class="cursor-pointer flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600"
                      >
                        <input
                          :id="`academic-term-${term.id}`"
                          :checked="selectedTermId === term.id"
                          type="radio"
                          class="cursor-pointer -4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                          @change="() => selectTerm(term.id)"
                        />

                        <label
                          :for="`academic-term-${term.id}`"
                          class="cursor-pointer w-full ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300"
                        >
                          {{
                            formatEntireTerm(
                              term.start_year,
                              term.end_year,
                              term.term,
                            )
                          }}
                        </label>
                      </div>

                      <div v-if="academic_terms.length === 0">
                        Tidak dapat menemukan data
                      </div>
                    </li>
                  </ul>
                </div>
              </template>
            </Popper>

            <Popper arrow>
              <button
                class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-2 focus:ring-gray-200 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                type="button"
              >
                <svg
                  class="w-3 h-3 mr-2"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <polygon
                    points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"
                  ></polygon>
                </svg>

                Pilih Indikator

                <svg
                  class="w-2.5 h-2.5 ml-4"
                  aria-hidden="true"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 10 6"
                >
                  <path
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="m1 1 4 4 4-4"
                  />
                </svg>
              </button>

              <template #content>
                <div
                  class="w-56 bg-white divide-gray-100 rounded-lg shadow-lg dark:bg-gray-700 dark:divide-gray-600"
                >
                  <ul
                    class="max-h-64 px-3 py-4 overflow-y-auto text-sm text-gray-700 dark:text-gray-200"
                  >
                    <li v-for="indicator in indicators" :key="indicator.id">
                      <div
                        class="cursor-pointer flex items-center p-2 rounded transition-colors duration-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                      >
                        <input
                          :id="`indicator-${indicator.id}`"
                          :checked="indicatorIdsToShow.has(indicator.id)"
                          type="checkbox"
                          class="cursor-pointer w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                          @change="
                            (e) =>
                              toggleIndicatorVisibility(
                                indicator.id,
                                (e.currentTarget as HTMLInputElement).checked,
                              )
                          "
                        />

                        <label
                          :for="`indicator-${indicator.id}`"
                          class="select-none cursor-pointer w-full ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300"
                        >
                          {{ indicator.name }}
                        </label>
                      </div>
                    </li>

                    <div v-if="indicators.length === 0">
                      Tidak dapat menemukan data
                    </div>
                  </ul>
                </div>
              </template>
            </Popper>
          </div>

          <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table
              class="w-full text-left rtl:text-right text-gray-500 dark:text-gray-400 border-collapse"
            >
              <thead
                class="text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
              >
                <tr>
                  <th scope="col" class="px-6 py-3 text-center" rowspan="2">
                    #
                  </th>

                  <th scope="col" class="px-6 py-3" rowspan="2">Nama</th>
                  <th scope="col" class="px-6 py-3" rowspan="2">NIS</th>

                  <th
                    v-for="indicator in indicatorsToShow"
                    :key="indicator.id"
                    :colspan="indicator.subindicators.length + 1"
                    scope="colgroup"
                    class="px-6 pt-3 pb-1 text-center"
                  >
                    {{ indicator.name }}
                  </th>

                  <th scope="col" class="px-6 py-3" rowspan="2">
                    <span class="sr-only">Aksi</span>
                  </th>
                </tr>

                <tr scope="col">
                  <template
                    v-for="indicator in indicatorsToShow"
                    :key="indicator.id"
                  >
                    <th
                      v-for="subindicator in indicator.subindicators"
                      :key="subindicator.id"
                      scope="col"
                      class="px-6 pt-1 pb-3 text-center whitespace-nowrap"
                    >
                      {{ subindicator.name }}
                    </th>

                    <th scope="col" class="px-6 pt-1 pb-3 text-center">
                      Predikat
                    </th>
                  </template>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(student, index) in students"
                  :key="student.id"
                  class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700"
                >
                  <th
                    scope="row"
                    class="text-center px-6 py-4 font-medium whitespace-nowrap"
                  >
                    {{ index + 1 }}
                  </th>

                  <td
                    class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white"
                  >
                    {{ student.name }}
                  </td>

                  <td class="px-6 py-4 whitespace-nowrap">
                    {{ student.nis }}
                  </td>

                  <template
                    v-for="(indicator, idx) in indicatorsToShow"
                    :key="indicator.id"
                  >
                    <td
                      v-for="subindicator in indicator.subindicators"
                      :key="subindicator.id"
                      class="border-r dark:border-gray-700 text-center"
                      scope="col"
                    >
                      <input
                        :checked="
                          student.subindicators?.find(
                            (s) => s.id === subindicator.id,
                          ) !== undefined
                        "
                        :disabled="selectedTermId === null"
                        type="checkbox"
                        class="disabled:cursor-not-allowed cursor-pointer w-6 h-6 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                        @change="
                          (e) =>
                            toggleStudentSubindicator(
                              student.id,
                              subindicator.id,
                              (e.currentTarget as HTMLInputElement).checked,
                            )
                        "
                      />
                    </td>

                    <td
                      scope="col"
                      :class="[
                        idx < indicatorsToShow.length - 1
                          ? 'border-r dark:border-gray-700'
                          : '',
                        'text-center text-sm whitespace-nowrap text-gray-900 dark:text-white px-8 py-4',
                      ]"
                    >
                      {{
                        student.grade_descriptors.find(
                          (el) => el.indicator.id === indicator.id,
                        )?.grade_descriptor?.code ?? '-'
                      }}
                    </td>
                  </template>

                  <td
                    class="px-6 py-4 text-right flex justify-end items-center gap-2 whitespace-nowrap"
                  >
                    <Link
                      :href="getReportCardUrl(student.id)"
                      class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                    >
                      Lihat Rapor
                    </Link>

                    <Link
                      :href="
                        route('units.school-classes.students.edit', {
                          school_class: data.id,
                          student: student.id,
                          unit: unit.id,
                        })
                      "
                      class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                    >
                      Ubah
                    </Link>

                    <DeleteButton
                      :prompt="`Apakah Anda yakin ingin menghapus murid ${student.name}?`"
                      @delete="() => handleDeleteStudent(student.id)"
                    />
                  </td>
                </tr>

                <tr v-if="students.length === 0">
                  <td colspan="12">
                    <div
                      class="flex justify-center items-center py-12 px-4 text-center"
                    >
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
    </div>
  </AuthenticatedLayout>
</template>
