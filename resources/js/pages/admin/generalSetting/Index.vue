<script setup lang="ts">
import UploadImageAction from '@/actions/App/Actions/Attachment/UploadImageAction';
import SystemSetting from '@/actions/App/Actions/SystemSetting';
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import ImageUploadField from '@/components/common/ImageUploadField.vue';
import InputError from '@/components/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import SystemAppLayout from '@/layouts/SystemAppLayout.vue';
import { getGeneralSetting } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const { t } = useI18n();
const generalSettings = computed(() => page.props.generalSettings);
const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('基础设置'),
    href: getGeneralSetting.url(),
  },
]);
</script>

<template>
  <SystemAppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('基础设置')" />

    <div class="px-4 py-6 sm:px-6">
      <div class="mx-auto w-full max-w-none space-y-12">
        <div class="space-y-6">
          <HeadingSmall
            :title="t('基础设置')"
            :description="t('配置系统的基本信息和全局设置')"
          />

          <Form
            v-bind="SystemSetting.UpdateGeneralSettingAction.form()"
            class="space-y-6"
            v-slot="{ errors, processing, recentlySuccessful }"
          >
            <div class="grid gap-2">
              <Label for="base_url">{{ t('主机地址') }}</Label>
              <Input
                id="base_url"
                name="base_url"
                type="url"
                class="mt-1 block w-full"
                :default-value="generalSettings.base_url || undefined"
                required
                :placeholder="t('请输入主机地址，例如：https://example.com')"
              />
              <InputError class="mt-2" :message="errors.baseUrl" />
            </div>

            <div class="grid gap-2">
              <Label for="name">{{ t('系统名称') }}</Label>
              <Input
                id="name"
                name="name"
                class="mt-1 block w-full"
                :default-value="generalSettings.name || undefined"
                required
                :placeholder="t('请输入系统名称')"
              />
              <InputError class="mt-2" :message="errors.name" />
            </div>

            <ImageUploadField
              :label="t('系统Logo')"
              name="logo_id"
              :upload-url="UploadImageAction.url()"
              response-key="id"
              :initial-preview="generalSettings.logo_url || ''"
              :initial-value="generalSettings.logo_id || ''"
              variant="logo"
              :error="errors.logo_id"
            />

            <div class="grid gap-2">
              <Label for="copyright">{{ t('版权信息') }}</Label>
              <Input
                id="copyright"
                name="copyright"
                class="mt-1 block w-full"
                :default-value="generalSettings.copyright || undefined"
                :placeholder="t('请输入版权信息')"
              />
              <InputError class="mt-2" :message="errors.copyright" />
            </div>

            <div class="grid gap-2">
              <Label for="icp_record">{{ t('备案信息') }}</Label>
              <Input
                id="icp_record"
                name="icp_record"
                class="mt-1 block w-full"
                :default-value="generalSettings.icp_record || undefined"
                :placeholder="t('请输入备案信息')"
              />
              <InputError class="mt-2" :message="errors.icp_record" />
            </div>

            <div class="grid gap-2">
              <Label>{{ t('版本号') }}</Label>
              <div class="py-2 text-sm text-muted-foreground">
                {{ generalSettings.version || t('未设置') }}
              </div>
            </div>

            <div class="flex items-center gap-4">
              <Button
                type="submit"
                :disabled="processing"
                data-test="update-general-settings-button"
              >
                {{ t('保存') }}
              </Button>

              <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
              >
                <p v-show="recentlySuccessful" class="text-sm text-neutral-600">
                  {{ t('已保存。') }}
                </p>
              </Transition>
            </div>
          </Form>
        </div>
      </div>
    </div>
  </SystemAppLayout>
</template>
