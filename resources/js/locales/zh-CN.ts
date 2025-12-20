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
      description:
        '启用两步验证后，登录时将需要输入安全验证码。该验证码可以通过手机上支持 TOTP 的应用程序获取。',
      continueSetup: '继续设置',
      enable: '启用两步验证',
    },
    enabled: {
      description:
        '启用两步验证后，登录时将需要输入安全的随机验证码，你可以通过手机上支持 TOTP 的应用程序获取该验证码。',
      disable: '禁用两步验证',
    },
    modal: {
      enableTitle: '启用两步验证',
      enableDescription:
        '要完成两步验证的启用，请扫描二维码或在身份验证器应用中输入设置密钥',
      enabledTitle: '两步验证已启用',
      enabledDescription:
        '两步验证现已启用。扫描二维码或在身份验证器应用中输入设置密钥。',
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
      description:
        '如果丢失两步验证设备，恢复码可以让你重新访问账户。请将它们存储在安全的密码管理器中。',
      view: '查看恢复码',
      hide: '隐藏恢复码',
      regenerate: '重新生成恢复码',
      instructions:
        '每个恢复码只能使用一次来访问你的账户，使用后将被删除。如需更多恢复码，请点击上方的',
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
      en: 'English',
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
  auth: {
    login: {
      title: '登录你的账户',
      description: '在下方输入你的邮箱和密码以登录',
      pageTitle: '登录',
      fields: {
        email: {
          label: '电子邮件地址',
          placeholder: 'email@example.com',
        },
        password: {
          label: '密码',
          placeholder: '密码',
        },
        remember: '记住我',
      },
      forgotPassword: '忘记密码？',
      submit: '登录',
      noAccount: '还没有账户？',
      signUp: '注册',
    },
    register: {
      title: '创建账户',
      description: '在下方输入你的详细信息以创建账户',
      pageTitle: '注册',
      fields: {
        name: {
          label: '姓名',
          placeholder: '请输入姓名',
        },
        email: {
          label: '电子邮件地址',
          placeholder: 'email@example.com',
        },
        password: {
          label: '密码',
          placeholder: '密码',
        },
        passwordConfirmation: {
          label: '确认密码',
          placeholder: '确认密码',
        },
      },
      submit: '创建账户',
      hasAccount: '已有账户？',
      logIn: '登录',
    },
    resetPassword: {
      title: '重置密码',
      description: '请在下方输入你的新密码',
      pageTitle: '重置密码',
      fields: {
        email: {
          label: '电子邮件',
        },
        password: {
          label: '密码',
          placeholder: '密码',
        },
        passwordConfirmation: {
          label: '确认密码',
          placeholder: '确认密码',
        },
      },
      submit: '重置密码',
    },
    twoFactorChallenge: {
      pageTitle: '两步验证',
      code: {
        title: '身份验证码',
        description: '输入你的身份验证器应用程序提供的验证码。',
        toggleText: '使用恢复码登录',
      },
      recovery: {
        title: '恢复码',
        description: '请输入你的紧急恢复码之一来确认访问你的账户。',
        toggleText: '使用身份验证码登录',
        placeholder: '输入恢复码',
      },
      submit: '继续',
      orYouCan: '或者你可以',
    },
    confirmPassword: {
      title: '确认你的密码',
      description: '这是应用程序的安全区域。请在继续之前确认你的密码。',
      pageTitle: '确认密码',
      fields: {
        password: {
          label: '密码',
        },
      },
      submit: '确认密码',
    },
    forgotPassword: {
      title: '忘记密码',
      description: '输入你的电子邮件以接收密码重置链接',
      pageTitle: '忘记密码',
      fields: {
        email: {
          label: '电子邮件地址',
          placeholder: 'email@example.com',
        },
      },
      submit: '发送密码重置链接',
      backToLogin: '或者，返回',
      login: '登录',
    },
    verifyEmail: {
      title: '验证电子邮件',
      description:
        '请点击我们刚刚发送给你的电子邮件中的链接来验证你的电子邮件地址。',
      pageTitle: '邮箱验证',
      linkSent: '新的验证链接已发送到你注册时提供的电子邮件地址。',
      submit: '重新发送验证邮件',
      logout: '退出登录',
    },
  },
  sidebarMenu: {
    dashboard: '工作台',
    githubRepo: 'GitHub仓库',
    documentation: '文档',
    settings: '系统设置',
    profile: '个人资料',
    logout: '退出登录',
  },
} as const;
