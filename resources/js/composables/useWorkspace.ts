import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export interface Workspace {
  id: number;
  path: string;
  name?: string;
}

export function useWorkspace() {
  const page = usePage();

  const currentWorkspace = computed(
    () => page.props.currentWorkspace as Workspace | undefined,
  );

  const workspacePath = computed(() => currentWorkspace.value?.path);

  return {
    currentWorkspace,
    workspacePath,
  };
}
