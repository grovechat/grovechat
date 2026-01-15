// 系统设置相关（英文）
export default {
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
  当前使用的存储配置: 'Current storage profile',
  请选择存储配置: 'Select a storage profile',
  存储配置管理: 'Storage Profile Management',
  '支持创建多个存储配置，并选择其中一个作为当前上传目标':
    'Create multiple storage profiles and choose one as the current upload target',
  新增配置: 'New profile',
  收起: 'Collapse',
  配置名称: 'Profile name',
  '例如：腾讯云 COS（生产）': 'e.g.: Tencent COS (Production)',
  存储提供商: 'Storage Provider',
  请选择存储提供商: 'Select a storage provider',
  查看文档: 'View docs',
  'Access Key / Access Key ID': 'Access Key / Access Key ID',
  '请输入 Access Key': 'Please enter Access Key',
  'Secret Key / Access Key Secret': 'Secret Key / Access Key Secret',
  '请输入 Secret Key': 'Please enter Secret Key',
  '留空表示不修改（首次启用必须填写）':
    'Leave blank to keep unchanged (required on first enable)',
  留空表示不修改: 'Leave blank to keep unchanged',
  'Bucket 名称': 'Bucket Name',
  '请输入 Bucket 名称': 'Please enter bucket name',
  '区域 (Region)': 'Region',
  请选择区域: 'Select a region',
  'Endpoint 地址': 'Endpoint URL',
  '使用外网 Endpoint': 'Use public Endpoint',
  '使用内网 Endpoint': 'Use internal Endpoint',
  '如果服务器和对象存储在同一区域，建议使用内网 Endpoint 以提高速度并节省流量费用':
    'If the server and the object storage are in the same region, using the internal Endpoint is recommended for better performance and lower traffic costs',
  '自定义域名 (可选)': 'Custom Domain (Optional)',
  '例如：https://cdn.example.com': 'e.g.: https://cdn.example.com',
  '如果配置了 CDN 或自定义域名，请在此填写，用于生成文件访问 URL':
    'If you have configured CDN or custom domain, enter it here for generating file access URLs',
  检测连接: 'Test connection',
} as const;
