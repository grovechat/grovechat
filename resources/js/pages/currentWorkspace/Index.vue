<script setup lang="ts">
  import DeleteCurrentWorkspaceAction from '@/actions/App/Actions/Manage/DeleteCurrentWorkspaceAction';
  import UpdateWorkspaceAction from '@/actions/App/Actions/Manage/UpdateWorkspaceAction';
  import CommonController from '@/actions/App/Http/Controllers/Api/CommonController';
  import HeadingSmall from '@/components/HeadingSmall.vue';
  import InputError from '@/components/InputError.vue';
  import { Button } from '@/components/ui/button';
  import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
  } from '@/components/ui/dialog';
  import { Input } from '@/components/ui/input';
  import { Label } from '@/components/ui/label';
  import { useI18n } from '@/composables/useI18n';
  import AppLayout from '@/layouts/AppLayout.vue';
  import WorkspaceSettingsLayout from '@/layouts/WorkspaceSettingsLayout.vue';
  import { getCurrentWorkspace } from '@/routes';
  import { type BreadcrumbItem } from '@/types';
  import { Form, Head, router, usePage } from '@inertiajs/vue3';
  import axios from 'axios';
  import { Check, Copy } from 'lucide-vue-next';
  import { computed, ref } from 'vue';
  
  const { t } = useI18n();
  const page = usePage();
  const generalSettings = computed(() => page.props.generalSettings);
  const currentWorkspace = computed(() => page.props.currentWorkspace);
  const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
    {
      title: t('常规设置'),
      href: getCurrentWorkspace.url(currentWorkspace.value.slug),
    },
  ]);
  
  const logoPreview = ref<string>(currentWorkspace.value.logo_url || '');
  const logoId = ref<string>(currentWorkspace.value.logo_id || '');
  const uploading = ref(false);
  const selectedLogoFileName = ref<string>('');
  const slugInput = ref<string>(currentWorkspace.value.slug || '');
  const copied = ref(false);
  const showDeleteDialog = ref(false);
  const deleting = ref(false);
  
  // 计算完整的访问路径
  const fullAccessUrl = computed(() => {
    const baseUrl = generalSettings.value?.base_url || '';
    return `${baseUrl}/w/${slugInput.value}`;
  });
  
  // 判断是否是默认工作区
  const isDefaultWorkspace = computed(() => {
    return currentWorkspace.value.owner_id !== null;
  });
  
  const handleLogoChange = async (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
  
    if (!file) return;
    selectedLogoFileName.value = file.name;
  
    // 先显示本地预览
    const reader = new FileReader();
    reader.onload = (e) => {
      logoPreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  
    // 上传文件到服务器
    const formData = new FormData();
    formData.append('file', file);
  
    try {
      uploading.value = true;
      const response = await axios.post(CommonController.uploadImage.url(), formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
      logoId.value = response.data.id;
    } catch {
      logoPreview.value = currentWorkspace.value.logo_url || '';
      selectedLogoFileName.value = '';
    } finally {
      uploading.value = false;
    }
  };
  
  const copyToClipboard = async () => {
    try {
      await navigator.clipboard.writeText(fullAccessUrl.value);
      copied.value = true;
      setTimeout(() => {
        copied.value = false;
      }, 2000);
    } catch (err) {
      console.error('Failed to copy:', err);
    }
  };
  
  const handleDelete = () => {
    deleting.value = true;
    router.delete(
      DeleteCurrentWorkspaceAction.url(currentWorkspace.value.slug),
      {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
          showDeleteDialog.value = false;
        },
        onFinish: () => {
          deleting.value = false;
        },
      },
    );
  };
  </script>
  
  <template>
    <AppLayout :breadcrumbs="breadcrumbItems">
      <Head :title="t('常规设置')" />
  
      <WorkspaceSettingsLayout>
        <div class="space-y-6">
          <HeadingSmall
            :title="t('常规设置')"
            :description="t('配置工作区的基本信息和设置')"
          />
  
          <Form
            v-bind="UpdateWorkspaceAction.form(currentWorkspace.slug)"
            class="space-y-6"
            v-slot="{ errors, processing, recentlySuccessful }"
          >
            <div class="grid gap-2">
              <Label for="slug">{{ t('工作区ID') }}</Label>
              <Input
                id="slug"
                name="slug"
                class="mt-1 block w-full bg-gray-50"
                :default-value="currentWorkspace.slug"
                disabled
                readonly
              />
              <p class="text-sm text-muted-foreground">
                {{ t('工作区ID不可修改') }}
              </p>
            </div>
  
            <div class="grid gap-2">
              <Label for="name">{{ t('工作区名称') }}</Label>
              <Input
                id="name"
                name="name"
                class="mt-1 block w-full"
                :default-value="currentWorkspace.name"
                required
                :placeholder="t('请输入工作区名称')"
              />
              <InputError class="mt-2" :message="errors.name" />
            </div>
  
            <div class="grid gap-2">
              <Label for="logo_id">{{ t('工作区Logo') }}</Label>
              <div class="mt-1 space-y-3">
                <div
                  v-if="logoPreview"
                  class="w-32 h-32 border rounded-md overflow-hidden bg-gray-50 flex items-center justify-center relative"
                >
                  <img
                    :src="logoPreview"
                    alt="Logo预览"
                    class="max-w-full max-h-full object-contain"
                  />
                  <div
                    v-if="uploading"
                    class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center"
                  >
                    <span class="text-white text-sm">{{ t('上传中...') }}</span>
                  </div>
                </div>
                <input
                  id="logo_id"
                  name="logo_id"
                  type="hidden"
                  :value="logoId"
                />
                <div class="flex items-center gap-3">
                  <input
                    id="logoFile"
                    type="file"
                    accept="image/*"
                    class="sr-only"
                    :disabled="uploading"
                    @change="handleLogoChange"
                  />
                  <Button as-child variant="outline" :disabled="uploading">
                    <Label for="logoFile" class="cursor-pointer">
                      {{ t('选择文件') }}
                    </Label>
                  </Button>
                  <span class="text-sm text-muted-foreground">
                    {{ selectedLogoFileName || t('未选择任何文件') }}
                  </span>
                </div>
                <p class="text-sm text-muted-foreground">
                  {{ t('支持上传图片格式文件，选择后自动上传') }}
                </p>
              </div>
              <InputError class="mt-2" :message="errors.logo" />
            </div>
  
            <div class="grid gap-2">
              <Label for="slug">{{ t('访问路径') }}</Label>
              <Input
                id="slug"
                name="slug"
                class="mt-1 block w-full"
                :default-value="currentWorkspace.slug"
                v-model="slugInput"
                required
                :placeholder="t('请输入访问路径')"
              />
              <div class="flex items-center gap-1.5 mt-1">
                <p class="text-sm text-muted-foreground">
                  {{ fullAccessUrl }}
                </p>
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  @click="copyToClipboard"
                  class="shrink-0 h-6 px-2"
                >
                  <Check v-if="copied" class="h-3.5 w-3.5" />
                  <Copy v-else class="h-3.5 w-3.5" />
                </Button>
              </div>
              <InputError class="mt-2" :message="errors.slug" />
            </div>
  
            <div class="flex items-center gap-4">
              <Button
                type="submit"
                :disabled="processing"
                data-test="update-workspace-button"
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
  
          <div class="border-t pt-8 mt-12">
            <HeadingSmall
              :title="t('危险操作')"
              :description="t('删除工作区将无法恢复，请谨慎操作')"
            />
            <div class="mt-6">
              <Button
                variant="destructive"
                :disabled="isDefaultWorkspace"
                @click="showDeleteDialog = true"
              >
                {{ t('删除工作区') }}
              </Button>
              <p v-if="isDefaultWorkspace" class="text-sm text-muted-foreground mt-2">
                {{ t('默认工作区不能删除') }}
              </p>
            </div>
          </div>
        </div>
      </WorkspaceSettingsLayout>
  
      <Dialog v-model:open="showDeleteDialog">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{{ t('确认删除工作区') }}</DialogTitle>
            <DialogDescription>
              {{ t('删除工作区后，所有相关数据将被永久删除，此操作无法撤销。确定要继续吗？') }}
            </DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <Button
              variant="outline"
              @click="showDeleteDialog = false"
              :disabled="deleting"
            >
              {{ t('取消') }}
            </Button>
            <Button
              variant="destructive"
              @click="handleDelete"
              :disabled="deleting"
            >
              {{ deleting ? t('删除中...') : t('确认删除') }}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </AppLayout>
  </template>
  