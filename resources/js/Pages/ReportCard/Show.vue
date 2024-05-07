<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { AcademicTerm } from '@/types/academic-term';
import {
  GradeDescriptor,
  WithIndicatorGradeDescriptors,
} from '@/types/grade-descriptor';
import { Indicator } from '@/types/indicator';
import { WithModifiedSchoolClass } from '@/types/school-class';
import { WithSubIndicators } from '@/types/subindicator';
import { Student } from '@/types/student';
import {
  formatEntireTerm,
  formatTerm,
  formatAcademicYear,
} from '@/utils/format-term';
import { Head, router, usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { computed } from 'vue';
import { ref } from 'vue';
import Popper from 'vue3-popper';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import formatList from '@/utils/format-list';
import { WithUnit } from '@/types/unit';
import { SchoolClass } from '@/types/school-class';
import { WithTeacher } from '@/types/teacher';

const props = defineProps<{
  data: WithIndicatorGradeDescriptors<
    WithModifiedSchoolClass<Student, WithTeacher<WithUnit<SchoolClass>>>
  > | null;
  students: Student[];
  indicators: WithSubIndicators<Indicator>[];

  // eslint-disable-next-line vue/prop-name-casing
  academic_term: AcademicTerm | null;

  // eslint-disable-next-line vue/prop-name-casing
  academic_terms: AcademicTerm[];

  // eslint-disable-next-line vue/prop-name-casing
  grade_descriptors: GradeDescriptor[];

  // eslint-disable-next-line vue/prop-name-casing
  max_grade: number;
}>();

const {
  props: { auth },
} = usePage();

const selectedTermId = ref<number | null>(
  props.academic_term?.id ?? props.academic_terms.at(0)?.id ?? null,
);

const selectedTerm = computed(() => {
  return (
    props.academic_terms.find((term) => term.id === selectedTermId.value) ??
    null
  );
});

const selectedStudentId = ref<number | null>(props.data?.id ?? null);
const selectedStudent = computed(() => {
  return (
    props.students.find((student) => student.id === selectedStudentId.value) ??
    null
  );
});

const indicatorGradeDescriptors = computed(() => {
  return (
    props.data?.grade_descriptors.slice().sort((a, b) => {
      return (
        props.indicators.findIndex((i) => i.id === a.indicator.id) -
        props.indicators.findIndex((i) => i.id === b.indicator.id)
      );
    }) ?? null
  );
});

const description = computed(() => {
  if (props.data === null || props.academic_term === null) return '-';

  const desc: string[] = [];

  props.grade_descriptors.forEach((gd) => {
    const indicators =
      props.data?.grade_descriptors
        ?.filter((d) => d.grade_descriptor.id === gd.id)
        ?.map((d) => d.indicator) ?? [];

    if (indicators.length === 0) return;

    desc.push(
      [
        gd.description_prefix.trim().toLowerCase(),
        formatList(indicators.map((i) => i.name.toLowerCase())),
      ].join(' '),
    );
  });

  return [`Ananda ${props.data.name}`, formatList(desc)].join(' ').concat('.');
});

watch(
  [selectedTermId, selectedStudentId],
  () => {
    router.reload({
      only: ['data', 'academic_term', 'indicators'],
      data: {
        academic_term_id: selectedTermId.value,
        student_id: selectedStudentId.value,
      },
      replace: true,
    });
  },
  { immediate: true },
);

const selectTerm = (id: number) => {
  selectedTermId.value = id;
};

const selectStudent = (id: number) => {
  selectedStudentId.value = id;
};

const print = () => {
  window.print();
};

const gradeToPercentage = (grade: number) => {
  if (props.max_grade === 0 && grade === 0) {
    return 100;
  }

  return Math.round((grade / props.max_grade) * 100 * 100) / 100;
};

const gradePercentageRange = (minGrade: number, maxGrade: number) => {
  if (minGrade === maxGrade) return `${gradeToPercentage(minGrade)}%`;
  return `${gradeToPercentage(minGrade)}% - ${gradeToPercentage(maxGrade)}%`;
};
</script>

<template>
  <Head title="Laporan Perkembangan Budaya Humanis" />

  <AuthenticatedLayout force-light-content>
    <template #header>
      <div class="flex justify-between items-center gap-4">
        <h2
          class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
          Laporan Perkembangan Budaya Humanis
        </h2>

        <div class="flex items-center gap-4">
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
                width="24"
                height="24"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-4 h-4 mr-2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"
                />
              </svg>

              {{
                selectedStudent === null ? 'Pilih Murid' : selectedStudent.name
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
                  <li v-for="student in students" :key="student.id">
                    <div
                      class="cursor-pointer flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600"
                    >
                      <input
                        :id="`student-${student.id}`"
                        :checked="selectedStudentId === student.id"
                        type="radio"
                        class="cursor-pointer -4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                        @change="() => selectStudent(student.id)"
                      />

                      <label
                        :for="`student-${student.id}`"
                        class="cursor-pointer w-full ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300"
                      >
                        {{ student.name }}
                      </label>
                    </div>

                    <div v-if="students.length === 0">
                      Tidak dapat menemukan data
                    </div>
                  </li>
                </ul>
              </div>
            </template>
          </Popper>

          <SecondaryButton @click.stop="print">
            <div class="flex items-center gap-2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path
                  d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"
                ></path>
                <rect x="6" y="14" width="12" height="8"></rect>
              </svg>

              <span>Print</span>
            </div>
          </SecondaryButton>
        </div>
      </div>
    </template>

    <div v-if="data" class="px-4 py-8 font-serif">
      <div class="flex justify-between items-center gap-4">
        <img src="/images/cktc.png" alt="" class="h-[72px] w-auto" />

        <div class="text-center font-bold text-lg">
          <p class="tracking-wide">SEKOLAH CINTA KASIH TZU CHI CENGKARENG</p>
          <h1>Laporan Perkembangan Budaya Humanis Peserta Didik</h1>
          <p>
            Tahun Pelajaran
            {{
              academic_term === null
                ? '-'
                : formatAcademicYear(
                    academic_term.start_year,
                    academic_term.end_year,
                  )
            }}
          </p>
        </div>

        <div></div>
      </div>

      <div class="flex justify-between gap-4 mt-8">
        <div class="grid grid-cols-[auto_auto_auto] grid-rows-2 gap-x-2">
          <span>Nama</span>
          <span>:</span>
          <span>{{ data?.name ?? '-' }}</span>

          <span>Nomor Induk Siswa</span>
          <span>:</span>
          <span>{{ data?.nis ?? '-' }}</span>
        </div>

        <div class="grid grid-cols-[auto_auto_auto] grid-rows-2 gap-x-2">
          <span>Kelas</span>
          <span>:</span>
          <span>{{ data?.school_class?.name ?? '-' }}</span>

          <span>Semester</span>
          <span>:</span>
          <span>{{
            academic_term?.term === undefined
              ? '-'
              : formatTerm(academic_term.term)
          }}</span>
        </div>
      </div>

      <table class="border-collapse w-full mt-2">
        <thead>
          <tr>
            <th
              colspan="2"
              scope="col"
              class="px-4 py-2 text-start border border-gray-600"
            >
              Komponen dan Indikator Penilaian Pembiasaan Budaya Humanis
            </th>

            <th
              v-for="descriptor in grade_descriptors"
              :key="descriptor.id"
              scope="col"
              class="px-4 py-2 text-center border border-gray-600"
            >
              {{ descriptor.code }}
            </th>
          </tr>
        </thead>

        <tbody>
          <template v-for="(indicator, idx) in indicators" :key="indicator.id">
            <tr>
              <th scope="row" class="text-center border border-gray-600 p-2">
                {{ idx + 1 }}
              </th>
              <td class="font-bold border border-gray-600 px-4 py-2">
                {{ indicator.name }}
              </td>

              <td
                v-for="descriptor in grade_descriptors"
                :key="descriptor.id"
                :rowspan="indicator.subindicators.length + 1"
                class="text-center border border-gray-600"
              >
                {{
                  indicatorGradeDescriptors === null ||
                  indicatorGradeDescriptors[idx] === undefined
                    ? ''
                    : indicatorGradeDescriptors[idx].grade_descriptor.id ===
                        descriptor.id
                      ? '✓'
                      : ''
                }}
              </td>
            </tr>

            <tr
              v-for="subindicator in indicator.subindicators"
              :key="subindicator.id"
            >
              <td class="border border-gray-600"></td>
              <td class="border border-gray-600 px-4 py-2">
                {{ subindicator.name }}
              </td>
            </tr>
          </template>
        </tbody>
      </table>

      <div class="mt-4">
        <h2 class="font-bold mb-1">Keterangan</h2>
        <ul>
          <li
            v-for="gradeDescriptor in grade_descriptors"
            :key="gradeDescriptor.id"
          >
            {{ gradeDescriptor.code }}: {{ gradeDescriptor.name }} ({{
              gradePercentageRange(
                gradeDescriptor.min_grade,
                gradeDescriptor.max_grade,
              )
            }})
          </li>
        </ul>
      </div>

      <div class="mt-4">
        <h2 class="font-bold mb-1">Deskripsi</h2>
        <p>
          {{ description }}
        </p>
      </div>

      <div class="mt-4 flex justify-between items-end gap-4">
        <div>
          <p>Mengetahui,</p>
          <p>
            {{
              data?.school_class.unit.head ?? 'Kepala unit ini belum ditentukan'
            }}
          </p>

          <p
            class="mt-28 h-px w-full min-w-[16rem] border-t border-dotted border-black"
          ></p>
        </div>

        <div>
          <div class="flex items-end gap-2">
            <p>Kota Jakarta Barat,</p>
            <div
              class="h-px w-[192px] border-t border-dotted border-black"
            ></div>
          </div>

          <p>
            {{
              data?.school_class.teacher?.name ??
              'Tidak ada guru yang mengajar kelas ini'
            }}
          </p>

          <p class="mt-28 h-px w-full border-t border-dotted border-black"></p>
        </div>
      </div>
    </div>

    <div v-else>
      <div class="flex justify-center items-center h-[calc(100vh-10rem)]">
        <p class="max-w-[60ch]">
          Silakan pilih murid dan semester pada kotak di atas terlebih dahulu,
          atau kunjungi halaman
          {{ auth.user.role === 'admin' ? 'Daftar Unit' : 'Daftar Kelas' }}
          untuk melihat daftar murid, kemudian klik tombol 'Lihat rapor' yang
          terdapat pada samping nama murid.
        </p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
