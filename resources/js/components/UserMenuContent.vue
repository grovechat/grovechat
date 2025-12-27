<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import {
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { useI18n } from '@/composables/useI18n';
import { useTenant } from '@/composables/useTenant';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { LogOut, Settings } from 'lucide-vue-next';

interface Props {
  user: User;
}

const handleLogout = () => {
  router.flushAll();
};

defineProps<Props>();

const { t } = useI18n();
const { tenantPath } = useTenant();
</script>

<template>
  <DropdownMenuLabel class="p-0 font-normal">
    <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
      <UserInfo :user="user" :show-email="true" />
    </div>
  </DropdownMenuLabel>
  <DropdownMenuSeparator />
  <DropdownMenuGroup>
    <DropdownMenuItem :as-child="true">
      <Link class="block w-full" :href="tenantPath ? edit(tenantPath) : '#'" prefetch as="button">
        <Settings class="mr-2 h-4 w-4" />
        {{ t('个人资料') }}
      </Link>
    </DropdownMenuItem>
  </DropdownMenuGroup>
  <DropdownMenuSeparator />
  <DropdownMenuItem :as-child="true">
    <Link
      class="block w-full"
      :href="logout()"
      @click="handleLogout"
      as="button"
      data-test="logout-button"
    >
      <LogOut class="mr-2 h-4 w-4" />
      {{ t('退出登录') }}
    </Link>
  </DropdownMenuItem>
</template>
