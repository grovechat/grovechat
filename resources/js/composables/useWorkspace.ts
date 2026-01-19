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

export function useCurrentWorkspace(): ComputedRef<WorkspaceData | null> {
  const page = usePage();
  return computed(() => {
    const slug = (page.props as any)?.workspaceUserContext?.workspace_slug as
      | string
      | null
      | undefined;
    if (!slug) {
      return null;
    }

    const workspaces = ((page.props as any)?.workspaces ??
      []) as WorkspaceData[];
    return workspaces.find((w) => w.slug === slug) ?? null;
  });
}

/**
 * 工作区页面使用：从 `workspaceUserContext.workspace_slug` + `workspaces` 推导当前工作区。
 * 如果缺失会立刻抛错，帮助尽早发现“在错误上下文使用工作区组件”的问题。
 */
export function useRequiredWorkspace(): ComputedRef<WorkspaceData> {
  const page = usePage();

  return computed(() => {
    const slug = (page.props as any)?.workspaceUserContext?.workspace_slug as
      | string
      | null
      | undefined;
    const workspaces = ((page.props as any)?.workspaces ??
      []) as WorkspaceData[];

    const ws = slug ? (workspaces.find((w) => w.slug === slug) ?? null) : null;

    if (!ws) {
      const { url, component } = getContextForError();
      throw new Error(
        `当前工作区缺失：该页面/组件需要工作区上下文（IdentifyWorkspace 中间件应提供 workspaceUserContext + workspaces）。component=${component} url=${url}`,
      );
    }

    return ws;
  });
}
