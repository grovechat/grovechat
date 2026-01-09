<script setup lang="ts">
import WorkspaceSettingController from '@/actions/App/Http/Controllers/WorkspaceSettingController';
import defaultWorkspaceUrl from '@/assets/images/workspace.png';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useI18n } from '@/composables/useI18n';
import workspace from '@/routes/workspace';
import { router, usePage } from '@inertiajs/vue3';
import { Check, ChevronsUpDown, Plus } from 'lucide-vue-next';
import { computed } from 'vue';

interface Workspace {
  id: number;
  name: string;
  slug: string;
  logo_id: string | null;
  logo_url: string | null;
  owner_id: number | null;
}

const { t } = useI18n();
const page = usePage();

const workspaces = computed(() => (page.props.workspaces as Workspace[]) || []);
const currentWorkspace = computed(() => page.props.currentWorkspace as Workspace | null);
const workspaceLogo = computed(() => currentWorkspace.value?.logo_url || defaultWorkspaceUrl)
const switchWorkspace = (selectedWorkspace: Workspace) => {
  if (selectedWorkspace.slug !== currentWorkspace.value?.slug) {
    router.visit(workspace.dashboard.url(selectedWorkspace.slug), {
      preserveState: false,
      preserveScroll: false,
    });
  }
};

const goToCreateWorkspace = () => {
  if (currentWorkspace.value) {
    router.visit(WorkspaceSettingController.showCreateWorkspacePage.url(currentWorkspace.value.slug));
  }
};
</script>

<template>
  <DropdownMenu v-if="currentWorkspace">
    <DropdownMenuTrigger as-child>
      <button
        class="flex w-full items-center gap-1.5 rounded-md px-1.5 py-1 text-left text-xs hover:bg-sidebar-accent hover:text-sidebar-accent-foreground transition-colors"
      >
        <div class="flex h-5 w-5 items-center justify-center rounded overflow-hidden text-sidebar-primary-foreground shrink-0">
          <img
            :src="workspaceLogo"
            :alt="currentWorkspace.name"
            class="h-full w-full object-cover"
          />
        </div>
        <span class="flex-1 truncate text-xs font-medium">
          {{ currentWorkspace.name }}
        </span>
        <ChevronsUpDown class="h-3 w-3 shrink-0 opacity-50" />
      </button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="start" class="w-64">
      <div class="px-2 py-1.5 text-xs font-semibold text-muted-foreground">
        {{ t('切换工作区') }}
      </div>
      <DropdownMenuItem
        v-for="workspace in workspaces"
        :key="workspace.id"
        class="flex items-center gap-2 cursor-pointer"
        @click="switchWorkspace(workspace)"
      >
        <div class="flex h-6 w-6 items-center justify-center rounded-md overflow-hidden text-sidebar-primary-foreground shrink-0">
          <img
            :src="workspaceLogo"
            :alt="workspace.name"
            class="h-full w-full object-cover"
          />
        </div>
        <span class="flex-1 truncate">{{ workspace.name }}</span>
        <Check
          v-if="workspace.slug === currentWorkspace?.slug"
          class="h-4 w-4 shrink-0"
        />
      </DropdownMenuItem>
      <DropdownMenuSeparator />
      <DropdownMenuItem
        class="flex items-center gap-2 cursor-pointer"
        @click="goToCreateWorkspace"
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
