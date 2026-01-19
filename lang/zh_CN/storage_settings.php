<?php

return [
    'check_success' => '检测成功',
    'validation_failed' => '验证未通过，请检查存储配置后重试。',
    'secret_required' => 'Secret Key 不能为空',
    'storage_not_selected' => '对象存储已启用，但未选择存储配置',
    'storage_not_found' => '当前存储配置不存在，请重新选择',
    'storage_key_secret_required' => '存储配置需要 Key/Secret，请先更新凭证',
    'connection_check_success' => '连接检测成功',
    'connection_check_failed' => '连接检测失败，请检查配置与网络连通性',
    'profile_is_active_cannot_delete' => '该配置正在被启用，无法删除',
    'profile_is_referenced_cannot_delete' => '该配置已被附件引用，无法删除',
    'profile_credentials_pair_required' => '更新凭证需要同时填写 Key 和 Secret',
    'providers' => [
        'aws' => 'Amazon S3',
        'r2' => 'Cloudflare R2',
        'aliyun' => '阿里云',
        'tencent' => '腾讯云',
        'baidu' => '百度云',
        'qiniu' => '七牛云',
        'huawei' => '华为云',
        'ucloud' => 'UCloud',
        'minio' => 'MinIO',
        'rustfs' => 'RustFS',
    ],
];
