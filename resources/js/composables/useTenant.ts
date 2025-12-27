import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export interface Tenant {
  id: number;
  path: string;
  name?: string;
}

export function useTenant() {
  const page = usePage();

  const currentTenant = computed(() => page.props.currentTenant as Tenant | undefined);

  const tenantPath = computed(() => currentTenant.value?.path);

  return {
    currentTenant,
    tenantPath,
  };
}
