<script setup lang="ts">
import InputError from '@/components/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import axios from 'axios';
import { computed, ref, watch } from 'vue';

type ImageUploadFieldVariant = 'avatar' | 'logo';

const props = withDefaults(
  defineProps<{
    label: string;
    name: string;
    uploadUrl: string;
    responseKey: string;
    folder?: string;
    initialPreview?: string;
    initialValue?: string;
    accept?: string;
    disabled?: boolean;
    helpText?: string;
    buttonText?: string;
    error?: string;
    variant?: ImageUploadFieldVariant;
    inputId?: string;
  }>(),
  {
    accept: 'image/*',
    disabled: false,
    helpText: undefined,
    buttonText: undefined,
    error: undefined,
    variant: 'avatar',
    folder: undefined,
    initialPreview: '',
    initialValue: '',
    inputId: undefined,
  },
);

const { t } = useI18n();

const effectiveButtonText = computed(() => props.buttonText ?? t('选择图片'));
const effectiveHelpText = computed(
  () => props.helpText ?? t('支持上传图片格式文件，选择后自动上传'),
);

const uploading = ref(false);
const selectedFileName = ref<string>('');
const previewUrl = ref<string>(props.initialPreview || '');
const value = ref<string>(props.initialValue || '');

watch(
  () => props.initialPreview,
  (next) => {
    if (uploading.value) return;
    if (selectedFileName.value) return;
    previewUrl.value = next || '';
  },
);

watch(
  () => props.initialValue,
  (next) => {
    if (uploading.value) return;
    if (selectedFileName.value) return;
    value.value = next || '';
  },
);

const fileInputId = computed(() => props.inputId || `${props.name}File`);
const hiddenInputValue = computed(() => value.value || props.initialValue || '');

const previewWrapperClass = computed(() => {
  if (props.variant === 'logo') {
    return 'relative flex h-32 w-32 items-center justify-center overflow-hidden rounded-md border bg-gray-50';
  }
  return 'relative flex h-20 w-20 items-center justify-center overflow-hidden rounded-full border bg-gray-50';
});

const previewImageClass = computed(() => {
  if (props.variant === 'logo') {
    return 'max-h-full max-w-full object-contain';
  }
  return 'h-full w-full object-cover';
});

const handleFileChange = async (event: Event) => {
  if (props.disabled) return;
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];

  if (!file) return;

  selectedFileName.value = file.name;

  const reader = new FileReader();
  reader.onload = (e) => {
    previewUrl.value = e.target?.result as string;
  };
  reader.readAsDataURL(file);

  const formData = new FormData();
  formData.append('file', file);
  if (props.folder) {
    formData.append('folder', props.folder);
  }

  try {
    uploading.value = true;
    const response = await axios.post(props.uploadUrl, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    value.value = String(response.data?.[props.responseKey] ?? '');
  } catch {
    previewUrl.value = props.initialPreview || '';
    value.value = props.initialValue || '';
    selectedFileName.value = '';
  } finally {
    uploading.value = false;
    target.value = '';
  }
};
</script>

<template>
  <div class="grid gap-2">
    <Label :for="fileInputId">{{ label }}</Label>
    <div class="mt-1 space-y-3">
      <div v-if="previewUrl" :class="previewWrapperClass">
        <img :src="previewUrl" :alt="t('图片预览')" :class="previewImageClass" />
        <div
          v-if="uploading"
          class="absolute inset-0 flex items-center justify-center bg-black/50"
        >
          <span class="text-sm text-white">{{ t('上传中...') }}</span>
        </div>
      </div>

      <input :id="name" :name="name" type="hidden" :value="hiddenInputValue" />

      <div class="flex items-center gap-3">
        <input
          :id="fileInputId"
          type="file"
          :accept="accept"
          class="sr-only"
          :disabled="uploading || disabled"
          @change="handleFileChange"
        />
        <Button as-child variant="outline" :disabled="uploading || disabled">
          <Label :for="fileInputId" class="cursor-pointer">
            {{ effectiveButtonText }}
          </Label>
        </Button>
        <span class="text-sm text-muted-foreground">
          {{ selectedFileName || t('未选择任何文件') }}
        </span>
      </div>

      <p v-if="effectiveHelpText" class="text-sm text-muted-foreground">
        {{ effectiveHelpText }}
      </p>
    </div>
    <InputError class="mt-2" :message="error" />
  </div>
</template>
