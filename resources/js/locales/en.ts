export default {
  settings: {
    title: 'Settings',
    description: 'Manage your profile and account settings',
    nav: {
      profile: 'Profile',
      password: 'Password',
      twoFactor: 'Two-Factor Auth',
      appearance: 'Appearance',
      language: 'Language & Timezone',
    },
  },
  profile: {
    title: 'Profile settings',
    heading: 'Profile information',
    description: 'Update your name and email address',
    fields: {
      name: {
        label: 'Name',
        placeholder: 'Full name',
      },
      email: {
        label: 'Email address',
        placeholder: 'Email address',
      },
    },
    verification: {
      unverified: 'Your email address is unverified.',
      resend: 'Click here to resend the verification email.',
      linkSent: 'A new verification link has been sent to your email address.',
    },
    actions: {
      save: 'Save',
      saved: 'Saved.',
    },
  },
  password: {
    title: 'Password settings',
    heading: 'Update password',
    description:
      'Ensure your account is using a long, random password to stay secure',
    fields: {
      currentPassword: {
        label: 'Current password',
        placeholder: 'Current password',
      },
      newPassword: {
        label: 'New password',
        placeholder: 'New password',
      },
      confirmPassword: {
        label: 'Confirm password',
        placeholder: 'Confirm password',
      },
    },
    actions: {
      save: 'Save password',
      saved: 'Saved.',
    },
  },
  appearance: {
    title: 'Appearance settings',
    heading: 'Appearance settings',
    description: "Update your account's appearance settings",
    themes: {
      light: 'Light',
      dark: 'Dark',
      system: 'System',
    },
  },
  twoFactor: {
    title: 'Two-Factor Authentication',
    heading: 'Two-Factor Authentication',
    description: 'Manage your two-factor authentication settings',
    status: {
      enabled: 'Enabled',
      disabled: 'Disabled',
    },
    disabled: {
      description:
        'When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.',
      continueSetup: 'Continue Setup',
      enable: 'Enable 2FA',
    },
    enabled: {
      description:
        'With two-factor authentication enabled, you will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.',
      disable: 'Disable 2FA',
    },
    modal: {
      enableTitle: 'Enable Two-Factor Authentication',
      enableDescription:
        'To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app',
      enabledTitle: 'Two-Factor Authentication Enabled',
      enabledDescription:
        'Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.',
      verifyTitle: 'Verify Authentication Code',
      verifyDescription: 'Enter the 6-digit code from your authenticator app',
      orManual: 'or, enter the code manually',
      continue: 'Continue',
      close: 'Close',
      back: 'Back',
      confirm: 'Confirm',
    },
    recoveryCodes: {
      title: '2FA Recovery Codes',
      description:
        'Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.',
      view: 'View Recovery Codes',
      hide: 'Hide Recovery Codes',
      regenerate: 'Regenerate Codes',
      instructions:
        'Each recovery code can be used once to access your account and will be removed after use. If you need more, click',
      regenerateButton: 'Regenerate Codes',
    },
  },
  language: {
    title: 'Language and Timezone Settings',
    heading: 'Language preference',
    description: 'Select your preferred language',
    current: 'Current language',
    select: 'Select language',
    languages: {
      'zh-CN': '简体中文',
      en: 'English',
    },
  },
  timezone: {
    heading: 'Timezone Settings',
    description: 'Select your timezone for accurate time display',
    current: 'Current timezone',
    select: 'Select timezone',
    autoDetect: 'Auto Detect',
    browserTimezone: 'Browser timezone',
  },
  common: {
    loading: 'Loading...',
    error: 'An error occurred',
  },
  auth: {
    login: {
      title: 'Log in to your account',
      description: 'Enter your email and password below to log in',
      pageTitle: 'Log in',
      fields: {
        email: {
          label: 'Email address',
          placeholder: 'email@example.com',
        },
        password: {
          label: 'Password',
          placeholder: 'Password',
        },
        remember: 'Remember me',
      },
      forgotPassword: 'Forgot password?',
      submit: 'Log in',
      noAccount: "Don't have an account?",
      signUp: 'Sign up',
    },
    register: {
      title: 'Create an account',
      description: 'Enter your details below to create your account',
      pageTitle: 'Register',
      fields: {
        name: {
          label: 'Name',
          placeholder: 'Full name',
        },
        email: {
          label: 'Email address',
          placeholder: 'email@example.com',
        },
        password: {
          label: 'Password',
          placeholder: 'Password',
        },
        passwordConfirmation: {
          label: 'Confirm password',
          placeholder: 'Confirm password',
        },
      },
      submit: 'Create account',
      hasAccount: 'Already have an account?',
      logIn: 'Log in',
    },
    resetPassword: {
      title: 'Reset password',
      description: 'Please enter your new password below',
      pageTitle: 'Reset password',
      fields: {
        email: {
          label: 'Email',
        },
        password: {
          label: 'Password',
          placeholder: 'Password',
        },
        passwordConfirmation: {
          label: 'Confirm Password',
          placeholder: 'Confirm password',
        },
      },
      submit: 'Reset password',
    },
    twoFactorChallenge: {
      pageTitle: 'Two-Factor Authentication',
      code: {
        title: 'Authentication Code',
        description:
          'Enter the authentication code provided by your authenticator application.',
        toggleText: 'login using a recovery code',
      },
      recovery: {
        title: 'Recovery Code',
        description:
          'Please confirm access to your account by entering one of your emergency recovery codes.',
        toggleText: 'login using an authentication code',
        placeholder: 'Enter recovery code',
      },
      submit: 'Continue',
      orYouCan: 'or you can',
    },
    confirmPassword: {
      title: 'Confirm your password',
      description:
        'This is a secure area of the application. Please confirm your password before continuing.',
      pageTitle: 'Confirm password',
      fields: {
        password: {
          label: 'Password',
        },
      },
      submit: 'Confirm Password',
    },
    forgotPassword: {
      title: 'Forgot password',
      description: 'Enter your email to receive a password reset link',
      pageTitle: 'Forgot password',
      fields: {
        email: {
          label: 'Email address',
          placeholder: 'email@example.com',
        },
      },
      submit: 'Email password reset link',
      backToLogin: 'Or, return to',
      login: 'log in',
    },
    verifyEmail: {
      title: 'Verify email',
      description:
        'Please verify your email address by clicking on the link we just emailed to you.',
      pageTitle: 'Email verification',
      linkSent:
        'A new verification link has been sent to the email address you provided during registration.',
      submit: 'Resend verification email',
      logout: 'Log out',
    },
  },
  sidebarMenu: {
    dashboard: 'Dashboard',
    githubRepo: 'Github Repo',
    documentation: 'Documentation',
    settings: 'Settings',
    profile: 'Profile',
    logout: 'Log Out',
  },
} as const;
