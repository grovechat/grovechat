import type { WorkspaceData } from '@/types/generated';
import { usePage } from '@inertiajs/vue3';
import { computed, type ComputedRef } from 'vue';

function getContextForError() {
  const page = usePage();
  const url =
    (page as any)?.url ??
    (typeof window !== 'undefined' ? window.location.pathname : '');
  const component = (page as any)?.component ?? 'unknown';
  return { url, component };
}

/**
 * 工作区页面使用：如果 currentWorkspace 不存在会立刻抛错，帮助尽早发现“在错误上下文使用工作区组件”的问题。
 */
export function useRequiredWorkspace(): ComputedRef<WorkspaceData> {
  const page = usePage();

  return computed(() => {
    const ws = (page.props as any)?.currentWorkspace as
      | WorkspaceData
      | null
      | undefined;

    if (!ws) {
      const { url, component } = getContextForError();
      throw new Error(
        `currentWorkspace 缺失：该页面/组件需要工作区上下文（/w/{slug}/*）。component=${component} url=${url}`,
      );
    }

    return ws;
  });
}
