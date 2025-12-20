<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { useI18n } from '@/composables/useI18n';
import { Monitor, Moon, Sun } from 'lucide-vue-next';
import { computed } from 'vue';

const { appearance, updateAppearance } = useAppearance();
const { t } = useI18n();

const tabs = computed(() => [
    { value: 'light', Icon: Sun, label: t('appearance.themes.light') },
    { value: 'dark', Icon: Moon, label: t('appearance.themes.dark') },
    { value: 'system', Icon: Monitor, label: t('appearance.themes.system') },
] as const);
</script>

<template>
    <div
        class="inline-flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800"
    >
        <button
            v-for="tab in tabs"
            :key="tab.value"
            @click="updateAppearance(tab.value)"
            :class="[
                'flex items-center rounded-md px-3.5 py-1.5 transition-colors',
                appearance === tab.value
                    ? 'bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100'
                    : 'text-neutral-500 hover:bg-neutral-200/60 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60',
            ]"
        >
            <component :is="tab.Icon" class="-ml-1 h-4 w-4" />
            <span class="ml-1.5 text-sm">{{ tab.label }}</span>
        </button>
    </div>
</template>
