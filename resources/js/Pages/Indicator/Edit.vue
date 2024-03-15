<script setup lang="ts">
import Combobox from '@/Components/Combobox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Indicator } from '@/types/indicator';
import { Unit, WithUnits } from '@/types/unit';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
  units: Unit[];
  data: WithUnits<Indicator>;
}>();

const form = useForm<{
  name: string;
  unit_ids: number[];
}>({
  name: props.data.name,
  unit_ids: props.data.units.map((u) => u.id),
});

const selectedUnit = ref<string | undefined>(undefined);

const handleAddUnit = () => {
  if (!selectedUnit.value) return;

  form.unit_ids.push(Number(selectedUnit.value));
  selectedUnit.value = undefined;
};

const handleDeleteUnit = (unitId: number) => {
  form.unit_ids = form.unit_ids.filter((id) => id !== unitId);
};

const submit = () => {
  form.put(route('indicators.update', { indicator: props.data.id }));
};
</script>

<template>
  <Head :title="`Ubah Indikator - ${data.name}`" />

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
          Ubah Indikator - {{ data.name }}
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
              <InputLabel for="name" value="Nama Indikator" />

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
              <InputLabel for="unit_ids" value="Unit" />

              <div class="flex items-stretch gap-2 mt-1">
                <Combobox id="unit_ids" v-model="selectedUnit" class="flex-1">
                  <option v-for="unit in units" :key="unit.id" :value="unit.id">
                    {{ unit.name }}
                  </option>
                </Combobox>

                <SecondaryButton @click="handleAddUnit">+</SecondaryButton>
              </div>

              <ul class="mt-4 flex flex-wrap gap-2">
                <li
                  v-for="unitId in form.unit_ids"
                  :key="unitId"
                  class="bg-gray-700 text-white px-4 py-2 rounded-md flex items-center gap-2"
                >
                  <div>
                    {{ units.find((unit) => unit.id === unitId)?.name }}
                  </div>

                  <button
                    type="button"
                    class="hover:bg-white/10 transition-colors duration-200 rounded-full p-1"
                    @click="() => handleDeleteUnit(unitId)"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="w-4 h-4"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18 18 6M6 6l12 12"
                      />
                    </svg>
                  </button>
                </li>
              </ul>

              <InputError class="mt-2" :message="form.errors.unit_ids" />
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
