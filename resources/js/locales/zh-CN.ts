import common from './zh-CN/common';
import settings from './zh-CN/settings';
import auth from './zh-CN/auth';
import app from './zh-CN/app';
import systemSettings from './zh-CN/system-settings';
import workspaceManagement from './zh-CN/workspace-management';
import workspaceSettings from './zh-CN/workspace-settings';

export default {
  ...common,
  ...settings,
  ...auth,
  ...app,
  ...systemSettings,
  ...workspaceManagement,
  ...workspaceSettings,
} as const;
