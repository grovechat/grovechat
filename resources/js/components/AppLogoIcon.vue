<script setup lang="ts">
import logoUrl from '@/assets/images/logo2.png';
import { usePage } from '@inertiajs/vue3';
import { computed, type HTMLAttributes } from 'vue';

defineOptions({
  inheritAttrs: false,
});

interface Props {
  className?: HTMLAttributes['class'];
}

defineProps<Props>();

const page = usePage();
const logo = computed(() => page.props.generalSettings?.logo || logoUrl);
const systemName = computed(() => page.props.generalSettings?.name || 'GroveChat');
</script>

<template>
  <img
    :src="logo"
    :alt="systemName + ' Logo'"
    :class="className"
    v-bind="$attrs"
  />
</template>
