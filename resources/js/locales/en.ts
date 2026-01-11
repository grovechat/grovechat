// 英文语言包 - 使用中文作为 key，值是对应的英文翻译
export default {
  // 通用
  '加载中...': 'Loading...',
  发生错误: 'An error occurred',

  // Toast 通知
  成功: 'Success',
  错误: 'Error',
  警告: 'Warning',
  提示: 'Info',
  通知: 'Notice',

  // 设置
  设置: 'Settings',
  管理你的个人资料和账户设置: 'Manage your profile and account settings',
  个人资料: 'Profile',
  密码: 'Password',
  两步验证: 'Two-Factor Auth',
  外观: 'Appearance',
  语言和时区: 'Language & Timezone',

  // 个人资料设置
  个人资料设置: 'Profile settings',
  个人信息: 'Profile information',
  更新你的姓名和电子邮件地址: 'Update your name and email address',
  姓名: 'Name',
  请输入姓名: 'Full name',
  电子邮件地址: 'Email address',
  请输入电子邮件地址: 'Email address',
  '你的电子邮件地址未验证。': 'Your email address is unverified.',
  '点击这里重新发送验证邮件。': 'Click here to resend the verification email.',
  '新的验证链接已发送到你的电子邮件地址。':
    'A new verification link has been sent to your email address.',
  保存: 'Save',
  '已保存。': 'Saved.',

  // 密码设置
  密码设置: 'Password settings',
  修改密码: 'Update password',
  确保你的账户使用长且随机的密码以保证安全:
    'Ensure your account is using a long, random password to stay secure',
  当前密码: 'Current password',
  请输入当前密码: 'Current password',
  新密码: 'New password',
  请输入新密码: 'New password',
  确认密码: 'Confirm password',
  请再次输入新密码: 'Confirm password',
  保存密码: 'Save password',

  // 外观设置
  外观设置: 'Appearance settings',
  更新你账户的外观设置: "Update your account's appearance settings",
  浅色: 'Light',
  深色: 'Dark',
  跟随系统: 'System',

  // 两步验证
  两步验证设置: 'Two-Factor Authentication',
  管理你的两步验证设置: 'Manage your two-factor authentication settings',
  已启用: 'Enabled',
  已禁用: 'Disabled',
  '启用两步验证后，登录时将需要输入安全验证码。该验证码可以通过手机上支持 TOTP 的应用程序获取。':
    'When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.',
  继续设置: 'Continue Setup',
  启用两步验证: 'Enable 2FA',
  '启用两步验证后，登录时将需要输入安全的随机验证码，你可以通过手机上支持 TOTP 的应用程序获取该验证码。':
    'With two-factor authentication enabled, you will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.',
  禁用两步验证: 'Disable 2FA',
  '要完成两步验证的启用，请扫描二维码或在身份验证器应用中输入设置密钥':
    'To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app',
  两步验证现已启用: 'Two-Factor Authentication Enabled',
  '两步验证现已启用。扫描二维码或在身份验证器应用中输入设置密钥。':
    'Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.',
  验证身份验证码: 'Verify Authentication Code',
  '输入来自身份验证器应用的 6 位数字验证码':
    'Enter the 6-digit code from your authenticator app',
  '或者，手动输入密钥': 'or, enter the code manually',
  继续: 'Continue',
  关闭: 'Close',
  返回: 'Back',
  确认: 'Confirm',
  两步验证恢复码: '2FA Recovery Codes',
  '如果丢失两步验证设备，恢复码可以让你重新访问账户。请将它们存储在安全的密码管理器中。':
    'Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.',
  查看恢复码: 'View Recovery Codes',
  隐藏恢复码: 'Hide Recovery Codes',
  重新生成恢复码: 'Regenerate Codes',
  '每个恢复码只能使用一次来访问你的账户，使用后将被删除。如需更多恢复码，请点击上方的':
    'Each recovery code can be used once to access your account and will be removed after use. If you need more, click',
  '"重新生成恢复码"': 'Regenerate Codes',

  // 语言和时区
  语言和时区设置: 'Language and Timezone Settings',
  语言偏好: 'Language preference',
  选择你的首选语言: 'Select your preferred language',
  当前语言: 'Current language',
  选择语言: 'Select language',
  简体中文: '简体中文',
  English: 'English',
  时区设置: 'Timezone Settings',
  '选择你的时区，用于正确显示时间':
    'Select your timezone for accurate time display',
  当前时区: 'Current timezone',
  选择时区: 'Select timezone',
  自动检测: 'Auto Detect',
  浏览器时区: 'Browser timezone',

  // 认证 - 登录
  登录你的账户: 'Log in to your account',
  在下方输入你的邮箱和密码以登录:
    'Enter your email and password below to log in',
  登录: 'Log in',
  '忘记密码？': 'Forgot password?',
  记住我: 'Remember me',
  '还没有账户？': "Don't have an account?",
  注册: 'Sign up',

  // 认证 - 注册
  创建账户: 'Create an account',
  在下方输入你的详细信息以创建账户:
    'Enter your details below to create your account',
  '已有账户？': 'Already have an account?',

  // 认证 - 重置密码
  重置密码: 'Reset password',
  请在下方输入你的新密码: 'Please enter your new password below',
  电子邮件: 'Email',

  // 认证 - 两步验证挑战
  身份验证码: 'Authentication Code',
  '输入你的身份验证器应用程序提供的验证码。':
    'Enter the authentication code provided by your authenticator application.',
  使用恢复码登录: 'login using a recovery code',
  恢复码: 'Recovery Code',
  '请输入你的紧急恢复码之一来确认访问你的账户。':
    'Please confirm access to your account by entering one of your emergency recovery codes.',
  使用身份验证码登录: 'login using an authentication code',
  输入恢复码: 'Enter recovery code',
  或者你可以: 'or you can',

  // 认证 - 确认密码
  确认你的密码: 'Confirm your password',
  '这是应用程序的安全区域。请在继续之前确认你的密码。':
    'This is a secure area of the application. Please confirm your password before continuing.',
  确认密码页面: 'Confirm password',

  // 认证 - 忘记密码
  忘记密码: 'Forgot password',
  输入你的电子邮件以接收密码重置链接:
    'Enter your email to receive a password reset link',
  发送密码重置链接: 'Email password reset link',
  '或者，返回': 'Or, return to',

  // 认证 - 验证邮箱
  验证电子邮件: 'Verify email',
  '请点击我们刚刚发送给你的电子邮件中的链接来验证你的电子邮件地址。':
    'Please verify your email address by clicking on the link we just emailed to you.',
  邮箱验证: 'Email verification',
  '新的验证链接已发送到你注册时提供的电子邮件地址。':
    'A new verification link has been sent to the email address you provided during registration.',
  重新发送验证邮件: 'Resend verification email',
  退出登录: 'Log out',

  // 侧边栏菜单
  工作台: 'Dashboard',
  联系人: 'Contacts',
  统计: 'Statistics',
  管理中心: 'Admin Center',
  GitHub仓库: 'Github Repo',
  文档: 'Documentation',
  系统设置: 'Settings',

  // 工作台页面
  查看和处理会话: 'View and handle conversations',
  人工接待: 'Manual Support',
  我负责的: 'My Assignments',
  提到我的: 'Mentions',
  排队中: 'In Queue',
  AI智能体: 'AI Agent',
  需人工介入: 'Needs Intervention',
  AI接管中: 'AI Handling',
  渠道: 'Channels',
  '网站（网站1）': 'Website (Site 1)',
  '网站（网站2）': 'Website (Site 2)',
  同事的会话: "Colleagues' Conversations",
  求助: 'Help Requests',
  同事1: 'Colleague 1',
  同事2: 'Colleague 2',
  同事3: 'Colleague 3',
  查看我负责的会话: 'View conversations assigned to me',

  // 联系人页面
  管理您的联系人和会话: 'Manage your contacts and conversations',
  身份类型: 'Identity Type',
  全部: 'All',
  注册用户: 'Customers',
  潜在客户: 'Leads',
  会话记录: 'Conversations',

  // 租户设置(管理中心)页面
  管理工作区和团队设置: 'Manage workspace and team settings',
  工作区: 'Workspace',
  常规设置: 'General',
  客服: 'Support',
  多客服: 'Team',
  网站: 'Website',
  数据: 'Data',
  标签: 'Tags',
  自定义属性: 'Custom Attributes',

  // 系统设置页面
  管理系统的配置和设置: 'Manage system configurations and settings',
  基础设置: 'General Settings',
  存储设置: 'Storage Settings',
  邮箱服务器: 'Mail Server',
  集成: 'Integrations',
  安全: 'Security',
  维护: 'Maintenance',
  配置系统的基本信息和全局设置:
    'Configure basic system information and global settings',
  主机地址: 'Host URL',
  '请输入主机地址，例如：https://example.com':
    'Please enter host URL, e.g.: https://example.com',
  系统名称: 'System Name',
  请输入系统名称: 'Please enter system name',
  系统Logo: 'System Logo',
  '上传中...': 'Uploading...',
  '支持上传图片格式文件，选择后自动上传':
    'Support image file upload, automatically uploaded after selection',
  版权信息: 'Copyright',
  请输入版权信息: 'Please enter copyright information',
  备案信息: 'ICP Record',
  请输入备案信息: 'Please enter ICP record information',
  版本号: 'Version',
  未设置: 'Not set',

  // 存储设置页面
  '配置对象存储服务，支持 Amazon S3 和阿里云 OSS 等兼容服务':
    'Configure object storage services, supports Amazon S3, Alibaba Cloud OSS and compatible services',
  启用对象存储: 'Enable Object Storage',
  '启用后，文件将上传到配置的对象存储服务':
    'When enabled, files will be uploaded to the configured object storage service',
  存储提供商: 'Storage Provider',
  请选择存储提供商: 'Select a storage provider',
  查看文档: 'View docs',
  'Access Key / Access Key ID': 'Access Key / Access Key ID',
  '请输入 Access Key': 'Please enter Access Key',
  'Secret Key / Access Key Secret': 'Secret Key / Access Key Secret',
  '请输入 Secret Key': 'Please enter Secret Key',
  '留空表示不修改（首次启用必须填写）':
    'Leave blank to keep unchanged (required on first enable)',
  'Bucket 名称': 'Bucket Name',
  '请输入 Bucket 名称': 'Please enter bucket name',
  '区域 (Region)': 'Region',
  请选择区域: 'Select a region',
  'Endpoint 地址': 'Endpoint URL',
  '例如：https://s3.amazonaws.com': 'e.g.: https://s3.amazonaws.com',
  '使用外网 Endpoint': 'Use public Endpoint',
  '使用内网 Endpoint': 'Use internal Endpoint',
  '如果服务器和对象存储在同一区域，建议使用内网 Endpoint 以提高速度并节省流量费用':
    'If the server and the object storage are in the same region, using the internal Endpoint is recommended for better performance and lower traffic costs',
  '自定义域名 (可选)': 'Custom Domain (Optional)',
  '例如：https://cdn.example.com': 'e.g.: https://cdn.example.com',
  '如果配置了 CDN 或自定义域名，请在此填写，用于生成文件访问 URL':
    'If you have configured CDN or custom domain, enter it here for generating file access URLs',
  检测连接: 'Test connection',

  // 工作区设置 - 常规设置
  配置工作区的基本信息和设置: 'Configure basic workspace information and settings',
  工作区ID: 'Workspace ID',
  工作区ID不可修改: 'Workspace ID cannot be modified',
  工作区名称: 'Workspace Name',
  请输入工作区名称: 'Please enter workspace name',
  工作区Logo: 'Workspace Logo',
  '访问路径（slug）': 'Access Path',
  请输入访问路径: 'Please enter access path',
  危险操作: 'Danger Zone',
  '删除工作区将无法恢复，请谨慎操作': 'Deleting a workspace is irreversible, please proceed with caution',
  删除工作区: 'Delete Workspace',
  默认工作区不能删除: 'Default workspace cannot be deleted',
  确认删除工作区: 'Confirm Delete Workspace',
  '删除工作区后，所有相关数据将被永久删除，此操作无法撤销。确定要继续吗？':
    'After deleting the workspace, all related data will be permanently deleted. This action cannot be undone. Are you sure you want to continue?',
  取消: 'Cancel',
  '删除中...': 'Deleting...',
  确认删除: 'Confirm Delete',
} as const;
