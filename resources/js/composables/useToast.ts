import { ref, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from './useI18n';

export type ToastType = 'default' | 'success' | 'error' | 'warning' | 'info';

export interface Toast {
  id: string;
  title?: string;
  description?: string;
  type?: ToastType;
  duration?: number;
  action?: {
    label: string;
    onClick: () => void;
  };
}

const toasts = ref<Toast[]>([]);
let toastIdCounter = 0;

export function useToast() {
  const { t } = useI18n();

  const addToast = (toast: Omit<Toast, 'id'>): string => {
    const id = `toast-${++toastIdCounter}`;
    toasts.value.push({ id, ...toast });
    return id;
  };

  const removeToast = (id: string) => {
    const index = toasts.value.findIndex((t) => t.id === id);
    if (index !== -1) {
      toasts.value.splice(index, 1);
    }
  };

  const toast = {
    success: (message: string) => {
      return addToast({ title: t('成功'), description: message, type: 'success' });
    },
    error: (message: string) => {
      return addToast({ title: t('错误'), description: message, type: 'error' });
    },
    warning: (message: string) => {
      return addToast({ title: t('警告'), description: message, type: 'warning' });
    },
    info: (message: string) => {
      return addToast({ title: t('提示'), description: message, type: 'info' });
    },
    default: (message: string) => {
      return addToast({ title: t('通知'), description: message, type: 'default' });
    },
  };

  return {
    toasts,
    toast,
    addToast,
    removeToast,
  };
}

let apiInterceptorSetup = false;

/**
 * 统一设置错误处理
 * 同时处理 Inertia 表单错误和 API 请求错误
 */
export function useErrorHandling() {
  const { toast } = useToast();

  // 处理 Inertia 表单错误
  const removeErrorListener = router.on('error', (event) => {
    const errors = event.detail.errors as any;

    if (errors?.toast && typeof errors.toast === 'string') {
      toast.error(errors.toast);
    }
  });

  const removeNavigateListener = router.on('navigate', () => {
    toasts.value = [];
  });

  onUnmounted(() => {
    removeErrorListener();
    removeNavigateListener();
  });

  // 处理 API 请求错误
  if (!apiInterceptorSetup) {
    import('axios').then(({ default: axios }) => {
      axios.interceptors.response.use(
        (response) => response,
        (error) => {
          // 提取错误消息
          const message =
            error.response?.data?.message ||
            error.message ||
            '请求失败，请稍后重试';
          toast.error(message);

          return Promise.reject(error);
        }
      );

      apiInterceptorSetup = true;
    });
  }
}
