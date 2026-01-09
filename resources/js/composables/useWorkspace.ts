import { WorkspaceData } from '@/types/generated';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export interface Workspace {
  id: number;
  slug: string;
  name?: string;
  logo_url?: string;
}

export function useWorkspace() {
  const page = usePage();

  const currentWorkspace = computed(
    () => page.props.currentWorkspace as WorkspaceData | undefined,
  );

  const workspaceSlug = computed(() => currentWorkspace.value?.slug);

  return {
    currentWorkspace,
    workspaceSlug,
  };
}
