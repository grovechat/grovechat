<script setup lang="ts">
import TenantSettingController from '@/actions/App/Http/Controllers/TenantSettingController';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useI18n } from '@/composables/useI18n';
import tenant from '@/routes/tenant';
import { router, usePage } from '@inertiajs/vue3';
import { Check, ChevronsUpDown, Plus } from 'lucide-vue-next';
import { computed } from 'vue';

interface Tenant {
  id: number;
  name: string;
  slug: string;
  logo: string | null;
  path: string;
  owner_id: number | null;
}

const { t } = useI18n();
const page = usePage();

const tenants = computed(() => (page.props.tenants as Tenant[]) || []);
const currentTenant = computed(() => page.props.currentTenant as Tenant | null);

const switchTenant = (selectedTenant: Tenant) => {
  if (selectedTenant.path !== currentTenant.value?.path) {
    router.visit(tenant.dashboard.url(selectedTenant.path), {
      preserveState: false,
      preserveScroll: false,
    });
  }
};

const goToCreateTenant = () => {
  if (currentTenant.value) {
    router.visit(TenantSettingController.showCreateTenantPage.url(currentTenant.value.path));
  }
};
</script>

<template>
  <DropdownMenu v-if="currentTenant">
    <DropdownMenuTrigger as-child>
      <button
        class="flex w-full items-center gap-1.5 rounded-md px-1.5 py-1 text-left text-xs hover:bg-sidebar-accent hover:text-sidebar-accent-foreground transition-colors"
      >
        <div
          v-if="currentTenant.logo"
          class="flex h-5 w-5 items-center justify-center rounded overflow-hidden bg-sidebar-primary text-sidebar-primary-foreground shrink-0"
        >
          <img
            :src="currentTenant.logo"
            :alt="currentTenant.name"
            class="h-full w-full object-cover"
          />
        </div>
        <div
          v-else
          class="flex h-5 w-5 items-center justify-center rounded bg-sidebar-primary text-sidebar-primary-foreground shrink-0"
        >
          <span class="text-[10px] font-semibold">
            {{ currentTenant.name.charAt(0).toUpperCase() }}
          </span>
        </div>
        <span class="flex-1 truncate text-xs font-medium">
          {{ currentTenant.name }}
        </span>
        <ChevronsUpDown class="h-3 w-3 shrink-0 opacity-50" />
      </button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="start" class="w-64">
      <div class="px-2 py-1.5 text-xs font-semibold text-muted-foreground">
        {{ t('工作区') }}
      </div>
      <DropdownMenuItem
        v-for="tenant in tenants"
        :key="tenant.id"
        class="flex items-center gap-2 cursor-pointer"
        @click="switchTenant(tenant)"
      >
        <div
          v-if="tenant.logo"
          class="flex h-6 w-6 items-center justify-center rounded-md overflow-hidden bg-sidebar-primary text-sidebar-primary-foreground shrink-0"
        >
          <img
            :src="tenant.logo"
            :alt="tenant.name"
            class="h-full w-full object-cover"
          />
        </div>
        <div
          v-else
          class="flex h-6 w-6 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground shrink-0"
        >
          <span class="text-xs font-semibold">
            {{ tenant.name.charAt(0).toUpperCase() }}
          </span>
        </div>
        <span class="flex-1 truncate">{{ tenant.name }}</span>
        <Check
          v-if="tenant.path === currentTenant?.path"
          class="h-4 w-4 shrink-0"
        />
      </DropdownMenuItem>
      <DropdownMenuSeparator />
      <DropdownMenuItem
        class="flex items-center gap-2 cursor-pointer"
        @click="goToCreateTenant"
      >
        <div
          class="flex h-6 w-6 items-center justify-center rounded-md border border-dashed shrink-0"
        >
          <Plus class="h-4 w-4" />
        </div>
        <span>{{ t('添加工作区') }}</span>
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
