<script setup lang="ts">
import { onMounted } from 'vue';
import { ref } from 'vue';

defineProps<{
  modelValue?: string;
}>();

defineEmits(['update:modelValue', 'change']);

const select = ref<HTMLSelectElement | null>(null);

onMounted(() => {
  if (select.value?.hasAttribute('autofocus')) {
    select.value?.focus();
  }
});

defineExpose({ focus: () => select.value?.focus() });
</script>

<template>
  <select
    ref="select"
    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
    :value="modelValue"
    @change="
      $emit('update:modelValue', ($event.target as HTMLSelectElement).value)
    "
  >
    <slot />
  </select>
</template>
