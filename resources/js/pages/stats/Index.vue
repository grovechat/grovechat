<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { useTenant } from '@/composables/useTenant';
import AppLayout from '@/layouts/AppLayout.vue';
import stats from '@/routes/stats';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const { tenantPath } = useTenant();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  {
    title: '统计',
    href: tenantPath.value ? stats.index(tenantPath.value).url : '#',
  },
]);
</script>

<template>
  <Head title="统计" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="px-4 py-6">
      <section class="max-w-2xl space-y-12">
        <div class="space-y-6">
          <HeadingSmall title="统计" description="查看系统统计数据和分析报告" />

          <div
            class="rounded-lg border border-border/40 bg-card/50 p-6 shadow-sm backdrop-blur-sm"
          >
            <p class="text-sm leading-relaxed text-muted-foreground">
              统计功能正在开发中。这里将展示系统的各项数据统计，包括会话数量、客户增长趋势、团队工作效率等关键指标。
              通过数据可视化图表，帮助您更好地了解业务运营状况，做出数据驱动的决策。
            </p>
          </div>
        </div>
      </section>
    </div>
  </AppLayout>
</template>
