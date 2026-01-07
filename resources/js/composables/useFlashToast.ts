import { router } from '@inertiajs/vue3';
import { onUnmounted } from 'vue';
import { useToast } from './useToast';

export function useFlashToast() {
  const { toast } = useToast();

  const removeListener = router.on('error', (event) => {
    const errors = event.detail.errors as any;

    if (errors?.toast && typeof errors.toast === 'string') {
      toast.error(errors.toast);
    }
  });

  onUnmounted(removeListener);
}
