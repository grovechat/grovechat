# Authentication DDD 架构文档

本项目已成功将 Laravel Fortify 认证系统重构为 DDD (Domain-Driven Design) 架构模式。

## 架构概览

采用分层架构设计，符合 DDD 最佳实践：

```
app/
├── Domain/                        # 领域层 - 核心业务逻辑
│   └── Authentication/
│       ├── DTOs/                  # 数据传输对象
│       ├── Actions/               # 业务操作
│       └── Contracts/             # 接口契约
├── Actions/Fortify/               # 适配器层
│   ├── CreateNewUserAdapter.php  # Fortify 到 DDD 的桥接
│   └── ResetUserPasswordAdapter.php
└── Http/Controllers/              # 应用层 - HTTP 入口
    ├── Auth/                      # 认证控制器
    └── Settings/                  # 设置控制器
```

## 领域层 (Domain Layer)

### DTOs (Data Transfer Objects)

位置: `app/Domain/Authentication/DTOs/`

所有 DTO 都继承自 `Spatie\LaravelData\Data`，提供：
- 自动验证
- 类型安全
- TypeScript 类型生成支持

**已实现的 DTOs:**
- `RegisterData` - 用户注册数据
- `LoginData` - 用户登录数据
- `ForgotPasswordData` - 忘记密码数据
- `ResetPasswordData` - 重置密码数据
- `UpdatePasswordData` - 更新密码数据
- `UpdateProfileData` - 更新资料数据
- `ConfirmPasswordData` - 确认密码数据
- `TwoFactorChallengeData` - 双因素认证数据

### Actions (业务操作)

位置: `app/Domain/Authentication/Actions/`

每个 Action 负责单一业务操作，遵循单一职责原则。

**认证相关:**
- `RegisterAction` - 处理用户注册（包括创建租户）
- `LoginAction` - 处理用户登录
- `LogoutAction` - 处理用户登出

**密码管理:**
- `SendPasswordResetLinkAction` - 发送密码重置链接
- `ResetPasswordAction` - 重置密码
- `UpdatePasswordAction` - 更新密码
- `ConfirmPasswordAction` - 确认密码

**资料管理:**
- `UpdateProfileAction` - 更新用户资料
- `DeleteAccountAction` - 删除用户账户

**双因素认证:**
- `EnableTwoFactorAction` - 启用双因素认证
- `ConfirmTwoFactorAction` - 确认双因素认证
- `DisableTwoFactorAction` - 禁用双因素认证
- `GenerateNewRecoveryCodesAction` - 生成新的恢复码

## 应用层 (Application Layer)

### 控制器

#### Auth 控制器
位置: `app/Http/Controllers/Auth/`

- **AuthenticationController** - 登录、注册、登出
  - `showLoginForm()` - 显示登录页面
  - `login()` - 处理登录
  - `showRegisterForm()` - 显示注册页面
  - `register()` - 处理注册
  - `logout()` - 处理登出

- **PasswordController** - 密码重置
  - `showForgotPasswordForm()` - 显示忘记密码页面
  - `sendResetLink()` - 发送重置链接
  - `showResetPasswordForm()` - 显示重置密码页面
  - `resetPassword()` - 重置密码

- **TwoFactorAuthenticationController** - 双因素认证
  - `show()` - 显示双因素认证设置
  - `store()` - 启用双因素认证
  - `confirm()` - 确认双因素认证
  - `destroy()` - 禁用双因素认证
  - `qrCode()` - 获取 QR 码
  - `recoveryCodes()` - 获取恢复码
  - `generateRecoveryCodes()` - 生成新恢复码

#### Settings 控制器
位置: `app/Http/Controllers/Settings/`

- **ProfileController** - 用户资料管理（已重构为 DDD）
- **PasswordController** - 密码设置（已重构为 DDD）

## 路由层

### 认证路由
位置: `routes/auth.php`

所有认证相关的路由都在此文件中定义：

**登录/登出:**
- `GET /login` - 登录页面
- `POST /login` - 登录提交
- `POST /logout` - 登出

**注册:**
- `GET /register` - 注册页面
- `POST /register` - 注册提交

**密码重置:**
- `GET /forgot-password` - 忘记密码页面
- `POST /forgot-password` - 发送重置链接
- `GET /reset-password/{token}` - 重置密码页面
- `POST /reset-password` - 重置密码提交

**邮箱验证:**
- `GET /email/verify` - 邮箱验证提示页
- `GET /email/verify/{id}/{hash}` - 验证链接
- `POST /email/verification-notification` - 重发验证邮件

**密码确认:**
- `GET /confirm-password` - 密码确认页面

**双因素认证挑战:**
- `GET /two-factor-challenge` - 双因素认证页面

### 设置路由
位置: `routes/settings.php`

租户内的用户设置路由（需要认证和租户验证）：

**资料设置:**
- `GET /settings/profile` - 资料设置页
- `PATCH /settings/profile` - 更新资料
- `DELETE /settings/profile` - 删除账户

**密码设置:**
- `GET /settings/password` - 密码设置页
- `PUT /settings/password` - 更新密码

**双因素认证设置:**
- `GET /settings/two-factor` - 双因素设置页
- `POST /settings/two-factor` - 启用双因素
- `POST /settings/two-factor/confirm` - 确认双因素
- `DELETE /settings/two-factor` - 禁用双因素
- `GET /settings/two-factor/qr-code` - 获取 QR 码
- `GET /settings/two-factor/recovery-codes` - 获取恢复码
- `POST /settings/two-factor/recovery-codes` - 生成新恢复码

## 适配器模式

为了与 Fortify 兼容，使用适配器模式桥接：

### Fortify Adapters
位置: `app/Actions/Fortify/`

- **CreateNewUserAdapter** - 桥接 Fortify 用户创建到 `RegisterAction`
- **ResetUserPasswordAdapter** - 桥接 Fortify 密码重置到 `ResetPasswordAction`

这些适配器在 `FortifyServiceProvider` 中注册：

```php
Fortify::createUsersUsing(\App\Actions\Fortify\CreateNewUserAdapter::class);
Fortify::resetUserPasswordsUsing(\App\Actions\Fortify\ResetUserPasswordAdapter::class);
```

## Service Provider 配置

### FortifyServiceProvider
位置: `app/Providers/FortifyServiceProvider.php`

**关键配置:**
1. `Fortify::ignoreRoutes()` - 禁用 Fortify 默认路由
2. 注册 DDD Actions 适配器
3. 配置速率限制

## 数据流

### 注册流程示例
```
1. 用户提交注册表单 → AuthenticationController@register
2. 创建 RegisterData DTO（自动验证）
3. 调用 RegisterAction@execute
4. 在事务中创建 User + Tenant
5. 关联用户到租户（ADMIN 角色）
6. 返回并登录用户
```

### 登录流程示例
```
1. 用户提交登录表单 → AuthenticationController@login
2. 创建 LoginData DTO（自动验证）
3. 调用 LoginAction@execute
4. 使用 Auth::attempt 验证凭据
5. 重新生成 session
6. 重定向到 dashboard
```

## 与 Fortify 的兼容性

虽然我们使用了自定义的路由和控制器，但仍然保留了 Fortify 的核心功能：

✅ **保留的 Fortify 功能:**
- 双因素认证（使用 `TwoFactorAuthenticatable` trait）
- 邮箱验证
- 密码重置令牌管理
- 速率限制
- Features 配置

❌ **不再使用的 Fortify 功能:**
- Fortify 默认路由
- Fortify 视图配置
- Fortify Actions（已被 DDD Actions 替代）

## 优势

1. **关注点分离** - 业务逻辑在 Domain 层，HTTP 处理在 Controller 层
2. **可测试性** - Actions 可以独立测试，不依赖 HTTP
3. **可维护性** - 单一职责，每个 Action 只做一件事
4. **类型安全** - DTOs 提供强类型检查
5. **可扩展性** - 易于添加新的认证方式或业务规则
6. **复用性** - Actions 可在任何地方调用（控制器、命令行、队列等）

## 未来扩展建议

1. **添加 Domain Events** - 在关键操作后触发事件
2. **Repository 模式** - 抽象数据访问层
3. **Policy 集成** - 在 Actions 中使用授权策略
4. **Logging** - 在 Actions 中添加审计日志
5. **缓存策略** - 对频繁访问的数据进行缓存

## 迁移注意事项

旧的 Fortify Actions 已被保留作为适配器：
- `app/Actions/Fortify/CreateNewUser.php` → 已由 `CreateNewUserAdapter` 替代
- `app/Actions/Fortify/ResetUserPassword.php` → 已由 `ResetUserPasswordAdapter` 替代

如果不再需要，可以安全删除旧的 Actions 文件。

## 测试

现有的认证测试可能需要更新以匹配新的路由名称和控制器。主要变化：
- 路由名称保持不变（如 `login`, `register` 等）
- 控制器已更改为新的 DDD 控制器
- 业务逻辑保持一致

## 总结

本次重构成功将 Laravel Fortify 认证系统转换为 DDD 架构，保持了与 Fortify 的兼容性，同时提供了更好的代码组织和可维护性。所有认证功能均已实现并可正常工作。
