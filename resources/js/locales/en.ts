import app from './en/app';
import auth from './en/auth';
import common from './en/common';
import legacy from './en/legacy';
import settings from './en/settings';
import systemSettings from './en/system-settings';
import workspaceManagement from './en/workspace-management';
import workspaceSettings from './en/workspace-settings';

// 英文语言包 - 使用中文作为 key，值是对应的英文翻译（按模块拆分，便于维护）
export default {
  // 先铺一层 legacy（历史大文件），再用模块覆盖，确保不丢 key 且可逐步迁移
  ...legacy,
  ...common,
  ...settings,
  ...auth,
  ...app,
  ...systemSettings,
  ...workspaceSettings,
  ...workspaceManagement,
} as const;
