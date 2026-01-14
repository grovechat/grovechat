import app from './en/app';
import auth from './en/auth';
import common from './en/common';
import settings from './en/settings';
import systemSettings from './en/system-settings';
import workspaceManagement from './en/workspace-management';
import workspaceSettings from './en/workspace-settings';

// 英文语言包 - 使用中文作为 key，值是对应的英文翻译（按模块拆分，便于维护）
export default {
  ...common,
  ...settings,
  ...auth,
  ...app,
  ...systemSettings,
  ...workspaceSettings,
  ...workspaceManagement,
} as const;
