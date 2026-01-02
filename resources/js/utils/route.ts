/**
 * 路由辅助函数
 * 用于替代 wayfinder 生成的路由
 */

type RouteParams = Record<string, string | number | boolean | undefined>;
type QueryParams = Record<string, string | number | boolean | string[] | undefined>;
type HttpMethod = 'get' | 'post' | 'put' | 'patch' | 'delete';

interface RouteOptions {
  query?: QueryParams;
  mergeQuery?: QueryParams;
}

interface RouteDefinition {
  url: string;
  method: HttpMethod;
}

interface FormRouteDefinition {
  action: string;
  method: HttpMethod;
}

/**
 * 构建查询字符串
 */
function buildQueryString(params: QueryParams): string {
  const searchParams = new URLSearchParams();

  Object.entries(params).forEach(([key, value]) => {
    if (value === undefined || value === null) return;

    if (Array.isArray(value)) {
      value.forEach(v => searchParams.append(`${key}[]`, String(v)));
    } else {
      searchParams.append(key, String(value));
    }
  });

  const queryString = searchParams.toString();
  return queryString ? `?${queryString}` : '';
}

/**
 * 创建路由函数
 */
function createRoute(
  path: string,
  method: HttpMethod = 'get'
): (params?: RouteParams | string, options?: RouteOptions) => RouteDefinition {
  return (params?: RouteParams | string, options?: RouteOptions) => {
    let url = path;

    // 处理参数
    if (params) {
      if (typeof params === 'string') {
        // 如果是字符串,替换第一个参数占位符
        url = url.replace(/\{[^}]+\}/, params);
      } else {
        // 如果是对象,替换所有参数占位符
        Object.entries(params).forEach(([key, value]) => {
          if (value !== undefined) {
            url = url.replace(`{${key}}`, String(value));
          }
        });
      }
    }

    // 处理查询参数
    if (options?.query || options?.mergeQuery) {
      const queryParams = { ...options.mergeQuery, ...options.query };
      url += buildQueryString(queryParams);
    }

    return {
      url,
      method,
    };
  };
}

/**
 * 创建带有多种方法的路由辅助函数
 */
function route(path: string, defaultMethod: HttpMethod = 'get') {
  const mainFn = createRoute(path, defaultMethod);

  const urlFn = (params?: RouteParams | string, options?: RouteOptions) => {
    return mainFn(params, options).url;
  };

  const formFn = (params?: RouteParams | string, options?: RouteOptions): FormRouteDefinition => {
    const { url, method } = mainFn(params, options);
    return {
      action: url,
      method,
    };
  };

  const getFn = createRoute(path, 'get');
  const postFn = createRoute(path, 'post');
  const putFn = createRoute(path, 'put');
  const patchFn = createRoute(path, 'patch');
  const deleteFn = createRoute(path, 'delete');

  // 为每个 HTTP 方法添加 url 和 form 属性
  const enhanceMethod = (fn: ReturnType<typeof createRoute>) => {
    return Object.assign(fn, {
      url: (params?: RouteParams | string, options?: RouteOptions) => fn(params, options).url,
      form: (params?: RouteParams | string, options?: RouteOptions): FormRouteDefinition => {
        const { url, method } = fn(params, options);
        return { action: url, method };
      },
    });
  };

  return Object.assign(mainFn, {
    url: urlFn,
    form: formFn,
    get: enhanceMethod(getFn),
    post: enhanceMethod(postFn),
    put: enhanceMethod(putFn),
    patch: enhanceMethod(patchFn),
    delete: enhanceMethod(deleteFn),
  });
}

// ==================== 认证相关路由 ====================

export const login = route('/login', 'get');
export const loginStore = route('/login', 'post');
export const logout = route('/logout', 'post');
export const register = route('/register', 'get');
export const registerStore = route('/register', 'post');
export const twoFactorLogin = route('/two-factor-challenge', 'get');
export const twoFactorLoginStore = route('/two-factor-challenge', 'post');

// 密码重置
export const passwordRequest = route('/forgot-password', 'get');
export const passwordEmail = route('/forgot-password', 'post');
export const passwordReset = route('/reset-password/{token}', 'get');
export const passwordUpdate = route('/reset-password', 'post');

// 密码确认
export const passwordConfirm = route('/user/confirm-password', 'get');
export const passwordConfirmStore = route('/user/confirm-password', 'post');

// 邮箱验证
export const verificationNotice = route('/email/verify', 'get');
export const verificationSend = route('/email/verification-notification', 'post');
export const verificationVerify = route('/email/verify/{id}/{hash}', 'get');

// ==================== 仪表板路由 ====================

export const home = route('/', 'get');
export const dashboard = route('/dashboard', 'get');
export const tenantDashboard = route('/w/{tenant_path}/dashboard', 'get');

// ==================== 用户设置路由 ====================

export const profileEdit = route('/w/{tenant_path}/settings/profile', 'get');
export const profileUpdate = route('/w/{tenant_path}/settings/profile', 'patch');
export const profileDestroy = route('/w/{tenant_path}/settings/profile', 'delete');

export const userPasswordEdit = route('/w/{tenant_path}/settings/password', 'get');
export const userPasswordUpdate = route('/w/{tenant_path}/settings/password', 'put');

export const appearanceEdit = route('/w/{tenant_path}/settings/appearance', 'get');
export const languageEdit = route('/w/{tenant_path}/settings/language', 'get');

export const twoFactorShow = route('/w/{tenant_path}/settings/two-factor', 'get');
export const twoFactorEnable = route('/user/two-factor-authentication', 'post');
export const twoFactorDisable = route('/user/two-factor-authentication', 'delete');
export const twoFactorConfirm = route('/user/confirmed-two-factor-authentication', 'post');
export const twoFactorQrCode = route('/user/two-factor-qr-code', 'get');
export const twoFactorRecoveryCodes = route('/user/two-factor-recovery-codes', 'get');
export const twoFactorRegenerateRecoveryCodes = route('/user/two-factor-recovery-codes', 'post');
export const twoFactorSecretKey = route('/user/two-factor-secret-key', 'get');

// ==================== 联系人路由 ====================

export const contactIndex = route('/w/{tenant_path}/contacts/{type}/index', 'get');
export const contactConversations = route('/w/{tenant_path}/contacts/conversations', 'get');

// ==================== 统计路由 ====================

export const statsIndex = route('/w/{tenant_path}/stats/overview', 'get');

// ==================== 系统设置路由 ====================

export const systemSettingGetGeneralSettings = route('/w/{tenant_path}/system-settings/general', 'get');
export const systemSettingUpdateGeneralSettings = route('/w/{tenant_path}/system-settings/general', 'put');
export const systemSettingGetStorageSettings = route('/w/{tenant_path}/system-settings/storage', 'get');
export const systemSettingGetMailSettings = route('/w/{tenant_path}/system-settings/mail', 'get');
export const systemSettingGetMaintenanceSettings = route('/w/{tenant_path}/system-settings/maintenance', 'get');
export const systemSettingGetSecuritySettings = route('/w/{tenant_path}/system-settings/security', 'get');
export const systemSettingGetIntegrationSettings = route('/w/{tenant_path}/system-settings/integration', 'get');

// ==================== 租户设置路由 ====================

export const tenantSettingTeamateIndex = route('/w/{tenant_path}/tenant-settings/teammate/index', 'get');
export const tenantSettingTenantGeneral = route('/w/{tenant_path}/tenant-settings/tenant/general', 'get');
export const tenantSettingChannelsWeb = route('/w/{tenant_path}/tenant-settings/channels/web', 'get');
export const tenantSettingDatasTag = route('/w/{tenant_path}/tenant-settings/datas/tag', 'get');
export const tenantSettingDatasAttribute = route('/w/{tenant_path}/tenant-settings/datas/attribute', 'get');

// ==================== API 路由 ====================

export const apiUploadImage = route('/api/upload-image', 'post');

// ==================== 兼容性路由对象 ====================
// 为了兼容之前从 @/routes 模块导入的方式

export const password = {
  request: passwordRequest,
  email: passwordEmail,
  reset: passwordReset,
  update: passwordUpdate,
  confirm: passwordConfirm,
  confirmStore: passwordConfirmStore,
};

export const verification = {
  notice: verificationNotice,
  send: verificationSend,
  verify: verificationVerify,
};

export const profile = {
  edit: profileEdit,
  update: profileUpdate,
  destroy: profileDestroy,
};

export const userPassword = {
  edit: userPasswordEdit,
  update: userPasswordUpdate,
};

export const appearance = {
  edit: appearanceEdit,
};

export const language = {
  edit: languageEdit,
};

export const twoFactor = {
  show: twoFactorShow,
  enable: twoFactorEnable,
  disable: twoFactorDisable,
  confirm: twoFactorConfirm,
  qrCode: twoFactorQrCode,
  recoveryCodes: twoFactorRecoveryCodes,
  regenerateRecoveryCodes: twoFactorRegenerateRecoveryCodes,
  secretKey: twoFactorSecretKey,
  login: twoFactorLogin,
  loginStore: twoFactorLoginStore,
};

export const contact = {
  index: contactIndex,
  conversations: contactConversations,
};

export const stats = {
  index: statsIndex,
};

export const systemSetting = {
  getGeneralSettings: systemSettingGetGeneralSettings,
  updateGeneralSettings: systemSettingUpdateGeneralSettings,
  getStorageSettings: systemSettingGetStorageSettings,
  getMailSettings: systemSettingGetMailSettings,
  getMaintenanceSettings: systemSettingGetMaintenanceSettings,
  getSecuritySettings: systemSettingGetSecuritySettings,
  getIntegrationSettings: systemSettingGetIntegrationSettings,
};

export const tenantSetting = {
  teammate: {
    index: tenantSettingTeamateIndex,
  },
  tenant: {
    general: tenantSettingTenantGeneral,
  },
  channels: {
    web: tenantSettingChannelsWeb,
  },
  datas: {
    tag: tenantSettingDatasTag,
    attribute: tenantSettingDatasAttribute,
  },
};

// ==================== Actions 控制器对象 ====================
// 为了兼容之前从 @/actions 导入的方式

export const ProfileController = {
  edit: profileEdit,
  update: profileUpdate,
  destroy: profileDestroy,
};

export const PasswordController = {
  edit: userPasswordEdit,
  update: userPasswordUpdate,
};

export const AppearanceController = {
  edit: appearanceEdit,
};

export const LanguageController = {
  edit: languageEdit,
};

export const TwoFactorAuthenticationController = {
  show: twoFactorShow,
};

export default {
  login,
  loginStore,
  logout,
  register,
  registerStore,
  twoFactorLogin,
  twoFactorLoginStore,
  password,
  verification,
  home,
  dashboard,
  tenantDashboard,
  profile,
  profileEdit,
  profileUpdate,
  profileDestroy,
  userPassword,
  userPasswordEdit,
  userPasswordUpdate,
  appearance,
  appearanceEdit,
  language,
  languageEdit,
  twoFactor,
  contact,
  contactIndex,
  contactConversations,
  stats,
  statsIndex,
  systemSetting,
  tenantSetting,
  apiUploadImage,
  ProfileController,
  PasswordController,
  AppearanceController,
  LanguageController,
  TwoFactorAuthenticationController,
};
