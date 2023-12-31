<script setup lang="ts">
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import filterTruthy from '@/utils/filter-truthy';
import { Link, usePage } from '@inertiajs/vue3';
import { reactive } from 'vue';

defineProps<{
  forceLightContent?: boolean;
}>();

const showingNavigationDropdown = ref(false);

const {
  props: { auth },
} = usePage();

const routes = reactive(
  [
    auth.user.role === 'admin' && {
      name: 'Daftar Semester',
      href: route('academic-terms.index'),
      active:
        route().current('academic-terms.index') ||
        route().current('academic-terms.create') ||
        route().current('academic-terms.edit'),
    },
    auth.user.role === 'admin' && {
      name: 'Daftar Guru',
      href: route('teachers.index'),
      active:
        route().current('teachers.index') ||
        route().current('teachers.create') ||
        route().current('teachers.edit') ||
        route().current('teachers.show'),
    },
    auth.user.role === 'admin' && {
      name: 'Daftar Predikat',
      href: route('grade-descriptors.index'),
      active: route().current('grade-descriptors.index'),
    },
    auth.user.role === 'admin' && {
      name: 'Daftar Indikator',
      href: route('indicators.index'),
      active:
        route().current('indicators.index') ||
        route().current('indicators.create') ||
        route().current('indicators.edit') ||
        route().current('indicators.show') ||
        route().current('indicators.subindicators.create') ||
        route().current('indicators.subindicators.edit'),
    },
    auth.user.role === 'admin' && {
      name: 'Daftar Unit',
      href: route('units.index'),
      active:
        route().current('units.index') ||
        route().current('units.create') ||
        route().current('units.edit') ||
        route().current('units.show') ||
        route().current('units.school-classes.create') ||
        route().current('units.school-classes.edit') ||
        route().current('units.school-classes.show') ||
        route().current('units.school-classes.students.create') ||
        route().current('units.school-classes.students.edit'),
    },
    auth.user.role === 'teacher' && {
      name: 'Daftar Kelas',
      href: route('school-classes.index'),
      active:
        route().current('school-classes.index') ||
        route().current('units.school-classes.show') ||
        route().current('units.school-classes.students.create') ||
        route().current('units.school-classes.students.edit'),
    },
    {
      name: 'Rapor',
      href: route('report-card'),
      active: route().current('report-card'),
    },
  ].filter(filterTruthy),
);
</script>

<template>
  <div>
    <div
      :class="[
        'min-h-screen bg-gray-100',
        !forceLightContent && 'dark:bg-gray-900',
      ]"
    >
      <nav
        class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 print:hidden"
      >
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex">
              <!-- Navigation Links -->
              <div class="hidden space-x-8 sm:-my-px sm:flex">
                <NavLink
                  v-for="route in routes"
                  :key="route.href"
                  :href="route.href"
                  :active="route.active"
                >
                  {{ route.name }}
                </NavLink>
              </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
              <!-- Settings Dropdown -->
              <div class="ms-3 relative">
                <Dropdown align="right" width="48">
                  <template #trigger>
                    <span class="inline-flex rounded-md">
                      <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                      >
                        {{ $page.props.auth.user.name }}

                        <svg
                          class="ms-2 -me-0.5 h-4 w-4"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </span>
                  </template>

                  <template #content>
                    <DropdownLink :href="route('profile.edit')">
                      Profil
                    </DropdownLink>
                    <DropdownLink
                      :href="route('logout')"
                      method="post"
                      as="button"
                    >
                      Keluar
                    </DropdownLink>
                  </template>
                </Dropdown>
              </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
              <button
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                @click="showingNavigationDropdown = !showingNavigationDropdown"
              >
                <svg
                  class="h-6 w-6"
                  stroke="currentColor"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <path
                    :class="{
                      hidden: showingNavigationDropdown,
                      'inline-flex': !showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                    :class="{
                      hidden: !showingNavigationDropdown,
                      'inline-flex': showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div
          :class="{
            block: showingNavigationDropdown,
            hidden: !showingNavigationDropdown,
          }"
          class="sm:hidden"
        >
          <div class="pt-2 pb-3 space-y-1">
            <ResponsiveNavLink
              v-for="route in routes"
              :key="route.href"
              :href="route.href"
              :active="route.active"
            >
              {{ route.name }}
            </ResponsiveNavLink>
          </div>

          <!-- Responsive Settings Options -->
          <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
              <div
                class="font-medium text-base text-gray-800 dark:text-gray-200"
              >
                {{ $page.props.auth.user.name }}
              </div>
              <div class="font-medium text-sm text-gray-500">
                {{ $page.props.auth.user.email }}
              </div>
            </div>

            <div class="mt-3 space-y-1">
              <ResponsiveNavLink :href="route('profile.edit')">
                Profil
              </ResponsiveNavLink>
              <ResponsiveNavLink
                :href="route('logout')"
                method="post"
                as="button"
              >
                Keluar
              </ResponsiveNavLink>
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Heading -->
      <header
        v-if="$slots.header"
        class="bg-white dark:bg-gray-800 shadow print:hidden"
      >
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>

      <!-- Page Content -->
      <main>
        <slot />
      </main>
    </div>
  </div>
</template>
