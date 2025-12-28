<script setup lang="ts">
import { useTenant } from '@/composables/useTenant';
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';

withDefaults(
  defineProps<{
    canRegister: boolean;
  }>(),
  {
    canRegister: true,
  },
);

const { tenantPath } = useTenant();
</script>

<template>
  <Head title="Welcome" />
  <div
    class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 p-6 dark:from-gray-900 dark:to-gray-800"
  >
    <div class="w-full max-w-2xl text-center">
      <!-- Logo/Brand -->
      <div class="mb-8">
        <h1 class="text-5xl font-bold text-gray-900 dark:text-white">
          GroveChat
        </h1>
        <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">
          开源 AI 客服系统
        </p>
      </div>

      <!-- Description -->
      <div class="mb-12">
        <p class="text-base text-gray-700 dark:text-gray-300">
          一个支持私有化部署的开源客服系统，专注于简单易用的部署体验
        </p>
      </div>

      <!-- Actions -->
      <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
        <Link
          v-if="$page.props.auth.user && tenantPath"
          :href="dashboard(tenantPath)"
          class="inline-flex w-full items-center justify-center rounded-lg bg-gray-900 px-8 py-3 text-base font-medium text-white transition-colors hover:bg-gray-800 sm:w-auto dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
        >
          进入控制台
        </Link>
        <template v-else>
          <Link
            :href="login()"
            class="inline-flex w-full items-center justify-center rounded-lg bg-gray-900 px-8 py-3 text-base font-medium text-white transition-colors hover:bg-gray-800 sm:w-auto dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
          >
            登录
          </Link>
          <Link
            v-if="canRegister"
            :href="register()"
            class="inline-flex w-full items-center justify-center rounded-lg border-2 border-gray-300 px-8 py-3 text-base font-medium text-gray-700 transition-colors hover:border-gray-400 hover:bg-gray-50 sm:w-auto dark:border-gray-600 dark:text-gray-300 dark:hover:border-gray-500 dark:hover:bg-gray-800"
          >
            注册
          </Link>
        </template>
      </div>

      <!-- Features (optional, simple) -->
      <div class="mt-16 grid grid-cols-1 gap-6 text-left sm:grid-cols-3">
        <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
          <div class="mb-2 text-2xl">🚀</div>
          <h3 class="mb-2 font-semibold text-gray-900 dark:text-white">
            一键部署
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            单个二进制文件，无需安装依赖
          </p>
        </div>

        <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
          <div class="mb-2 text-2xl">🔒</div>
          <h3 class="mb-2 font-semibold text-gray-900 dark:text-white">
            自动 HTTPS
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            内置证书管理，开箱即用
          </p>
        </div>

        <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
          <div class="mb-2 text-2xl">💡</div>
          <h3 class="mb-2 font-semibold text-gray-900 dark:text-white">
            轻量化
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            ~136MB 包含完整应用
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
