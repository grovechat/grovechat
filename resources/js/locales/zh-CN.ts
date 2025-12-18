export default {
  settings: {
    title: '设置',
    description: '管理你的个人资料和账户设置',
    nav: {
      profile: '个人资料',
      password: '密码',
      twoFactor: '两步验证',
      appearance: '外观',
      language: '语言和时区',
    },
  },
  profile: {
    title: '个人资料设置',
    heading: '个人信息',
    description: '更新你的姓名和电子邮件地址',
    fields: {
      name: {
        label: '姓名',
        placeholder: '请输入姓名',
      },
      email: {
        label: '电子邮件地址',
        placeholder: '请输入电子邮件地址',
      },
    },
    verification: {
      unverified: '你的电子邮件地址未验证。',
      resend: '点击这里重新发送验证邮件。',
      linkSent: '新的验证链接已发送到你的电子邮件地址。',
    },
    actions: {
      save: '保存',
      saved: '已保存。',
    },
  },
  password: {
    title: '密码设置',
    heading: '修改密码',
    description: '确保你的账户使用长且随机的密码以保证安全',
    fields: {
      currentPassword: {
        label: '当前密码',
        placeholder: '请输入当前密码',
      },
      newPassword: {
        label: '新密码',
        placeholder: '请输入新密码',
      },
      confirmPassword: {
        label: '确认密码',
        placeholder: '请再次输入新密码',
      },
    },
    actions: {
      save: '保存密码',
      saved: '已保存。',
    },
  },
  appearance: {
    title: '外观设置',
    heading: '外观设置',
    description: '更新你账户的外观设置',
    themes: {
      light: '浅色',
      dark: '深色',
      system: '跟随系统',
    },
  },
  twoFactor: {
    title: '两步验证',
    heading: '两步验证',
    description: '管理你的两步验证设置',
    status: {
      enabled: '已启用',
      disabled: '已禁用',
    },
    disabled: {
      description: '启用两步验证后，登录时将需要输入安全验证码。该验证码可以通过手机上支持 TOTP 的应用程序获取。',
      continueSetup: '继续设置',
      enable: '启用两步验证',
    },
    enabled: {
      description: '启用两步验证后，登录时将需要输入安全的随机验证码，你可以通过手机上支持 TOTP 的应用程序获取该验证码。',
      disable: '禁用两步验证',
    },
    modal: {
      enableTitle: '启用两步验证',
      enableDescription: '要完成两步验证的启用，请扫描二维码或在身份验证器应用中输入设置密钥',
      enabledTitle: '两步验证已启用',
      enabledDescription: '两步验证现已启用。扫描二维码或在身份验证器应用中输入设置密钥。',
      verifyTitle: '验证身份验证码',
      verifyDescription: '输入来自身份验证器应用的 6 位数字验证码',
      orManual: '或者，手动输入密钥',
      continue: '继续',
      close: '关闭',
      back: '返回',
      confirm: '确认',
    },
    recoveryCodes: {
      title: '两步验证恢复码',
      description: '如果丢失两步验证设备，恢复码可以让你重新访问账户。请将它们存储在安全的密码管理器中。',
      view: '查看恢复码',
      hide: '隐藏恢复码',
      regenerate: '重新生成恢复码',
      instructions: '每个恢复码只能使用一次来访问你的账户，使用后将被删除。如需更多恢复码，请点击上方的',
      regenerateButton: '"重新生成恢复码"',
    },
  },
  language: {
    title: '语言和时区设置',
    heading: '语言偏好',
    description: '选择你的首选语言',
    current: '当前语言',
    select: '选择语言',
    languages: {
      'zh-CN': '简体中文',
      'en': 'English',
    },
  },
  timezone: {
    heading: '时区设置',
    description: '选择你的时区，用于正确显示时间',
    current: '当前时区',
    select: '选择时区',
    autoDetect: '自动检测',
    browserTimezone: '浏览器时区',
  },
  common: {
    loading: '加载中...',
    error: '发生错误',
  },
} as const;
