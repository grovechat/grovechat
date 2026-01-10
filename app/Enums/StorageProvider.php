<?php

namespace App\Enums;

enum StorageProvider: string
{
    case AWS = 'aws';
    case R2 = 'r2';
    case ALIYUN = 'aliyun';
    case TENCENT = 'tencent';
    case BAIDU = 'baidu';
    case QINIU = 'qiniu';
    case HUAWEI = 'huawei';
    case UCLOUD = 'ucloud';
    case MINIO = 'minio';
    case RUSTFS = 'rustfs';

    public function label(): string
    {
        return match($this) {
            self::AWS => 'Amazon S3',
            self::R2 => 'Cloudflare R2',
            self::ALIYUN => '阿里云',
            self::TENCENT => '腾讯云',
            self::BAIDU => '百度云',
            self::QINIU => '七牛云',
            self::HUAWEI => '华为云',
            self::UCLOUD => 'UCloud优刻得',
            self::MINIO => 'MinIO',
            self::RUSTFS => 'RustFS',
        };
    }

    public function getHelpLink()
    {
        return match($this) {
            self::AWS => 'https://docs.aws.amazon.com/general/latest/gr/s3.html',
            self::R2 => 'https://developers.cloudflare.com/r2/api/s3/api/',
            self::ALIYUN => 'https://help.aliyun.com/zh/oss/user-guide/regions-and-endpoints',
            self::TENCENT => 'https://cloud.tencent.com/document/product/436/6224',
            self::BAIDU => 'https://cloud.baidu.com/doc/BOS/s/xjwvyq9l4',
            self::QINIU => 'https://developer.qiniu.com/kodo/4088/s3-access-domainname',
            self::HUAWEI => 'https://console.huaweicloud.com/apiexplorer/#/endpoint/OBS',
            self::UCLOUD => 'https://docs.ucloud.cn/ufile/s3/s3_introduction',
            self::MINIO => 'https://docs.min.io/enterprise/aistor-object-store/developers/sdk/go/api/#MakeBucket',
            self::RUSTFS => 'https://docs.rustfs.com.cn/developer/sdk/javascript.html#%E4%B8%89%E3%80%81%E5%88%9D%E5%A7%8B%E5%8C%96-s3-%E5%AE%A2%E6%88%B7%E7%AB%AF',
        };
    }    

    public function getRegions()
    {
        return match($this) {
            self::AWS => [
                [
                    'id' => 'us-east-1',
                    'name' => 'US East (N. Virginia)',
                    'endpoint' => 'https://s3.us-east-1.amazonaws.com',
                ],
                [
                    'id' => 'us-east-2'
                    'name' => 'US East (Ohio)',
                    'endpoint' => 'https://s3.us-east-2.amazonaws.com',
                ],
                'us-west-1' => [
                    'name' => 'US West (N. California)',
                    'endpoint' => 'https://s3.us-west-1.amazonaws.com',
                ],
                'us-west-2' => [
                    'name' => 'US West (Oregon)',
                    'endpoint' => 'https://s3.us-west-2.amazonaws.com',
                ],
                'af-south-1' => [
                    'name' => 'Africa (Cape Town)',
                    'endpoint' => 'https://s3.af-south-1.amazonaws.com',
                ],
                'ap-south-2' => [
                    'name' => 'Asia Pacific (Hyderabad)',
                    'endpoint' => 'https://s3.ap-south-2.amazonaws.com',
                ],
                'ap-east-1' => [
                    'name' => 'Asia Pacific (Hong Kong)',
                    'endpoint' => 'https://s3.ap-east-1.amazonaws.com',
                ],
                'ap-east-2' => [
                    'name' => 'Asia Pacific (Taipei)',
                    'endpoint' => 'https://s3.ap-east-2.amazonaws.com',
                ],
                'ap-southeast-1' => [
                    'name' => 'Asia Pacific (Singapore)',
                    'endpoint' => 'https://s3.ap-southeast-1.amazonaws.com',
                ],
                'ap-southeast-2' => [
                    'name' => 'Asia Pacific (Sydney)',
                    'endpoint' => 'https://s3.ap-southeast-2.amazonaws.com',
                ],
                'ap-southeast-3' => [
                    'name' => 'Asia Pacific (Jakarta)',
                    'endpoint' => 'https://s3.ap-southeast-3.amazonaws.com',
                ],
                'ap-southeast-4' => [
                    'name' => 'Asia Pacific (Melbourne)',
                    'endpoint' => 'https://s3.ap-southeast-4.amazonaws.com',
                ],
                'ap-southeast-5' => [
                    'name' => 'Asia Pacific (Malaysia)',
                    'endpoint' => 'https://s3.ap-southeast-5.amazonaws.com',
                ],
                'ap-southeast-6' => [
                    'name' => 'Asia Pacific (New Zealand)',
                    'endpoint' => 'https://s3.ap-southeast-6.amazonaws.com',
                ],
                'ap-southeast-7' => [
                    'name' => 'Asia Pacific (Thailand)',
                    'endpoint' => 'https://s3.ap-southeast-7.amazonaws.com',
                ],
                'ap-south-1' => [
                    'name' => 'Asia Pacific (Mumbai)',
                    'endpoint' => 'https://s3.ap-south-1.amazonaws.com',
                ],
                'ap-northeast-1' => [
                    'name' => 'Asia Pacific (Tokyo)',
                    'endpoint' => 'https://s3.ap-northeast-1.amazonaws.com',
                ],
                'ap-northeast-2' => [
                    'name' => 'Asia Pacific (Seoul)',
                    'endpoint' => 'https://s3.ap-northeast-2.amazonaws.com',
                ],
                'ap-northeast-3' => [
                    'name' => 'Asia Pacific (Osaka)',
                    'endpoint' => 'https://s3.ap-northeast-3.amazonaws.com',
                ],
                'ca-central-1' => [
                    'name' => 'Canada (Central)',
                    'endpoint' => 'https://s3.ca-central-1.amazonaws.com',
                ],
                'ca-west-1' => [
                    'name' => 'Canada West (Calgary)',
                    'endpoint' => 'https://s3.ca-west-1.amazonaws.com',
                ],
                'eu-central-1' => [
                    'name' => 'Europe (Frankfurt)',
                    'endpoint' => 'https://s3.eu-central-1.amazonaws.com',
                ],
                'eu-central-2' => [
                    'name' => 'Europe (Zurich)',
                    'endpoint' => 'https://s3.eu-central-2.amazonaws.com',
                ],
                'eu-west-1' => [
                    'name' => 'Europe (Ireland)',
                    'endpoint' => 'https://s3.eu-west-1.amazonaws.com',
                ],
                'eu-west-2' => [
                    'name' => 'Europe (London)',
                    'endpoint' => 'https://s3.eu-west-2.amazonaws.com',
                ],
                'eu-west-3' => [
                    'name' => 'Europe (Paris)',
                    'endpoint' => 'https://s3.eu-west-3.amazonaws.com',
                ],
                'eu-south-1' => [
                    'name' => 'Europe (Milan)',
                    'endpoint' => 'https://s3.eu-south-1.amazonaws.com',
                ],
                'eu-south-2' => [
                    'name' => 'Europe (Spain)',
                    'endpoint' => 'https://s3.eu-south-2.amazonaws.com',
                ],
                'eu-north-1' => [
                    'name' => 'Europe (Stockholm)',
                    'endpoint' => 'https://s3.eu-north-1.amazonaws.com',
                ],
                'il-central-1' => [
                    'name' => 'Israel (Tel Aviv)',
                    'endpoint' => 'https://s3.il-central-1.amazonaws.com',
                ],
                'mx-central-1' => [
                    'name' => 'Mexico (Central)',
                    'endpoint' => 'https://s3.mx-central-1.amazonaws.com',
                ],
                'me-south-1' => [
                    'name' => 'Middle East (Bahrain)',
                    'endpoint' => 'https://s3.me-south-1.amazonaws.com',
                ],
                'me-central-1' => [
                    'name' => 'Middle East (UAE)',
                    'endpoint' => 'https://s3.me-central-1.amazonaws.com',
                ],
                'sa-east-1' => [
                    'name' => 'South America (São Paulo)',
                    'endpoint' => 'https://s3.sa-east-1.amazonaws.com',
                ],
                'us-gov-east-1' => [
                    'name' => 'AWS GovCloud (US-East)',
                    'endpoint' => 'https://s3.us-gov-east-1.amazonaws.com',
                ],
                'us-gov-west-1' => [
                    'name' => 'AWS GovCloud (US-West)',
                    'endpoint' => 'https://s3.us-gov-west-1.amazonaws.com',
                ],
            ],
            self::R2 => [
                'auto' => [
                    'name' => 'auto',
                    'endpoint' => 'https://<ACCOUNT_ID>.r2.cloudflarestorage.com',
                ],
            ],
            self::ALIYUN => [
                // 亚太-中国
                'cn-hangzhou' => [
                    'name'                  => '华东1（杭州）',
                    'endpoint'              => 'https://oss-cn-hangzhou.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-hangzhou-internal.aliyuncs.com',
                ],
                'cn-shanghai' => [
                    'name'                  => '华东2（上海）',
                    'endpoint'              => 'https://oss-cn-shanghai.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-shanghai-internal.aliyuncs.com',
                ],
                'cn-nanjing' => [
                    'name'                  => '华东5（南京-本地地域）',
                    'endpoint'              => 'https://oss-cn-nanjing.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-nanjing-internal.aliyuncs.com',
                ],
                'cn-fuzhou' => [
                    'name'                  => '华东6（福州-本地地域）',
                    'endpoint'              => 'https://oss-cn-fuzhou.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-fuzhou-internal.aliyuncs.com',
                ],
                'cn-wuhan-lr' => [
                    'name'                  => '华中1（武汉-本地地域）',
                    'endpoint'              => 'https://oss-cn-wuhan-lr.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-wuhan-lr-internal.aliyuncs.com',
                ],
                'cn-qingdao' => [
                    'name'                  => '华北1（青岛）',
                    'endpoint'              => 'https://oss-cn-qingdao.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-qingdao-internal.aliyuncs.com',
                ],
                'cn-beijing' => [
                    'name'                  => '华北2（北京）',
                    'endpoint'              => 'https://oss-cn-beijing.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-beijing-internal.aliyuncs.com',
                ],
                'cn-zhangjiakou' => [
                    'name'                  => '华北3（张家口）',
                    'endpoint'              => 'https://oss-cn-zhangjiakou.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-zhangjiakou-internal.aliyuncs.com',
                ],
                'cn-huhehaote' => [
                    'name'                  => '华北5（呼和浩特）',
                    'endpoint'              => 'https://oss-cn-huhehaote.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-huhehaote-internal.aliyuncs.com',
                ],
                'cn-wulanchabu' => [
                    'name'                  => '华北6（乌兰察布）',
                    'endpoint'              => 'https://oss-cn-wulanchabu.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-wulanchabu-internal.aliyuncs.com',
                ],
                'cn-shenzhen' => [
                    'name'                  => '华南1（深圳）',
                    'endpoint'              => 'https://oss-cn-shenzhen.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-shenzhen-internal.aliyuncs.com',
                ],
                'cn-heyuan' => [
                    'name'                  => '华南2（河源）',
                    'endpoint'              => 'https://oss-cn-heyuan.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-heyuan-internal.aliyuncs.com',
                ],
                'cn-guangzhou' => [
                    'name'                  => '华南3（广州）',
                    'endpoint'              => 'https://oss-cn-guangzhou.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-guangzhou-internal.aliyuncs.com',
                ],
                'cn-chengdu' => [
                    'name'                  => '西南1（成都）',
                    'endpoint'              => 'https://oss-cn-chengdu.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-chengdu-internal.aliyuncs.com',
                ],
                'cn-hongkong' => [
                    'name'                  => '中国香港',
                    'endpoint'              => 'https://oss-cn-hongkong.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-hongkong-internal.aliyuncs.com',
                ],

                // 亚太-其他
                'ap-northeast-1' => [
                    'name'                  => '日本（东京）',
                    'endpoint'              => 'https://oss-ap-northeast-1.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-ap-northeast-1-internal.aliyuncs.com',
                ],
                'ap-northeast-2' => [
                    'name'                  => '韩国（首尔）',
                    'endpoint'              => 'https://oss-ap-northeast-2.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-ap-northeast-2-internal.aliyuncs.com',
                ],
                'ap-southeast-1' => [
                    'name'                  => '新加坡',
                    'endpoint'              => 'https://oss-ap-southeast-1.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-ap-southeast-1-internal.aliyuncs.com',
                ],
                'ap-southeast-3' => [
                    'name'                  => '马来西亚（吉隆坡）',
                    'endpoint'              => 'https://oss-ap-southeast-3.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-ap-southeast-3-internal.aliyuncs.com',
                ],
                'ap-southeast-5' => [
                    'name'                  => '印度尼西亚（雅加达）',
                    'endpoint'              => 'https://oss-ap-southeast-5.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-ap-southeast-5-internal.aliyuncs.com',
                ],
                'ap-southeast-6' => [
                    'name'                  => '菲律宾（马尼拉）',
                    'endpoint'              => 'https://oss-ap-southeast-6.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-ap-southeast-6-internal.aliyuncs.com',
                ],
                'ap-southeast-7' => [
                    'name'                  => '泰国（曼谷）',
                    'endpoint'              => 'https://oss-ap-southeast-7.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-ap-southeast-7-internal.aliyuncs.com',
                ],

                // 欧洲与美洲
                'eu-central-1' => [
                    'name'                  => '德国（法兰克福）',
                    'endpoint'              => 'https://oss-eu-central-1.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-eu-central-1-internal.aliyuncs.com',
                ],
                'eu-west-1' => [
                    'name'                  => '英国（伦敦）',
                    'endpoint'              => 'https://oss-eu-west-1.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-eu-west-1-internal.aliyuncs.com',
                ],
                'us-west-1' => [
                    'name'                  => '美国（硅谷）',
                    'endpoint'              => 'https://oss-us-west-1.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-us-west-1-internal.aliyuncs.com',
                ],
                'us-east-1' => [
                    'name'                  => '美国（弗吉尼亚）',
                    'endpoint'              => 'https://oss-us-east-1.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-us-east-1-internal.aliyuncs.com',
                ],
                'na-south-1' => [
                    'name'                  => '墨西哥',
                    'endpoint'              => 'https://oss-na-south-1.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-na-south-1-internal.aliyuncs.com',
                ],
                'me-east-1' => [
                    'name'                  => '阿联酋（迪拜）',
                    'endpoint'              => 'https://oss-me-east-1.aliyuncs.com',
                    'internal_endpoint'     => 'https://oss-cn-nanjing-internal.aliyuncs.com',
                ],
                
            ],
            self::TENCENT => [
                // 中国大陆
                'ap-beijing-1' => [
                    'name'                  => '北京一区',
                    'endpoint'              => 'https://cos.ap-beijing-1.myqcloud.com',
                ],
                'ap-beijing' => [
                    'name'                  => '北京',
                    'endpoint'              => 'https://cos.ap-beijing.myqcloud.com',
                ],
                'ap-nanjing' => [
                    'name'                  => '南京',
                    'endpoint'              => 'https://cos.ap-nanjing.myqcloud.com',
                ],
                'ap-shanghai' => [
                    'name'                  => '上海',
                    'endpoint'              => 'https://cos.ap-shanghai.myqcloud.com',
                ],
                'ap-guangzhou' => [
                    'name'                  => '广州',
                    'endpoint'              => 'https://cos.ap-guangzhou.myqcloud.com',
                ],
                'ap-chengdu' => [
                    'name'                  => '成都',
                    'endpoint'              => 'https://cos.ap-chengdu.myqcloud.com',
                ],
                'ap-chongqing' => [
                    'name'                  => '重庆',
                    'endpoint'              => 'https://cos.ap-chongqing.myqcloud.com',
                ],

                // 中国香港及境外地域
                'ap-hongkong' => [
                    'name'                  => '中国香港',
                    'endpoint'              => 'https://cos.ap-hongkong.myqcloud.com',
                ],
                'ap-singapore' => [
                    'name'                  => '新加坡',
                    'endpoint'              => 'https://cos.ap-singapore.myqcloud.com',
                ],
                'ap-jakarta' => [
                    'name'                  => '雅加达',
                    'endpoint'              => 'https://cos.ap-jakarta.myqcloud.com',
                ],
                'ap-seoul' => [
                    'name'                  => '首尔',
                    'endpoint'              => 'https://cos.ap-seoul.myqcloud.com',
                ],
                'ap-bangkok' => [
                    'name'                  => '曼谷',
                    'endpoint'              => 'https://cos.ap-bangkok.myqcloud.com',
                ],
                'ap-tokyo' => [
                    'name'                  => '东京',
                    'endpoint'              => 'https://cos.ap-tokyo.myqcloud.com',
                ],
                'na-siliconvalley' => [
                    'name'                  => '硅谷（美西）',
                    'endpoint'              => 'https://cos.na-siliconvalley.myqcloud.com',
                ],
                'na-ashburn' => [
                    'name'                  => '弗吉尼亚（美东）',
                    'endpoint'              => 'https://cos.na-ashburn.myqcloud.com',
                ],
                'sa-saopaulo' => [
                    'name'                  => '圣保罗',
                    'endpoint'              => 'https://cos.sa-saopaulo.myqcloud.com',
                ],
                'eu-frankfurt' => [
                    'name'                  => '法兰克福',
                    'endpoint'              => 'https://cos.eu-frankfurt.myqcloud.com',
                ],
            ],
            self::BAIDU => [
                's3.bj' => [
                    'name'          => '北京',
                    'endpoint'      => 'https://s3.bj.bcebos.com',
                ],
                's3.gz' => [
                    'name'          => '广州',
                    'endpoint'      => 'https://s3.gz.bcebos.com',
                ],
                's3.su' => [
                    'name'          => '苏州',
                    'endpoint'      => 'https://s3.su.bcebos.com',
                ],
                's3.bd' => [
                    'name'          => '保定',
                    'endpoint'      => 'https://s3.bd.bcebos.com',
                ],
                's3.fwh' => [
                    'name'          => '金融云武汉专区',
                    'endpoint'      => 'https://s3.fwh.bcebos.com',
                ],
                's3.fsh' => [
                    'name'          => '金融云上海专区',
                    'endpoint'      => 'https://s3.fsh.bcebos.com',
                ],
                's3.hkg' => [
                    'name'          => '香港',
                    'endpoint'      => 'https://s3.hkg.bcebos.com',
                ],
            ],
            self::QINIU => [
                'cn-east-1' => [
                    'name'          => '华东-浙江',
                    'endpoint'      => 'https://s3.cn-east-1.qiniucs.com',
                ],
                'cn-east-2' => [
                    'name'          => '华东-浙江2',
                    'endpoint'      => 'https://s3.cn-east-2.qiniucs.com',
                ],
                'cn-north-1' => [
                    'name'          => '华北-河北',
                    'endpoint'      => 'https://s3.cn-north-1.qiniucs.com',
                ],
                'cn-south-1' => [
                    'name'          => '华南-广东',
                    'endpoint'      => 'https://s3.cn-south-1.qiniucs.com',
                ],
                'cn-northwest-1' => [
                    'name'          => '西北-陕西1',
                    'endpoint'      => 'https://s3.cn-northwest-1.qiniucs.com',
                ],
                'us-north-1' => [
                    'name'          => '北美-洛杉矶',
                    'endpoint'      => 'https://s3.us-north-1.qiniucs.com',
                ],
                'ap-southeast-1' => [
                    'name'          => '亚太-新加坡（原东南亚）',
                    'endpoint'      => 'https://s3.ap-southeast-1.qiniucs.com',
                ],
                'ap-southeast-2' => [
                    'name'          => '亚太-河内',
                    'endpoint'      => 'https://s3.ap-southeast-2.qiniucs.com',
                ],
                'ap-southeast-3' => [
                    'name'          => '亚太-胡志明',
                    'endpoint'      => 'https://s3.ap-southeast-3.qiniucs.com',
                ],
            ],
            self::HUAWEI => [
                'cn-north-1' => [
                    'name'          => '华北-北京一',
                    'endpoint'      => 'https://obs.cn-north-1.myhuaweicloud.com',
                ],
                'cn-north-4' => [
                    'name'          => '华北-北京四',
                    'endpoint'      => 'https://obs.cn-north-4.myhuaweicloud.com',
                ],
                'cn-north-9' => [
                    'name'          => '华北-乌兰察布一',
                    'endpoint'      => 'https://obs.cn-north-9.myhuaweicloud.com',
                ],
                'cn-east-2'  => [
                    'name'          => '华东-上海二',
                    'endpoint'      => 'https://obs.cn-east-2.myhuaweicloud.com',
                ],
                'cn-east-3'  => [
                    'name'          => '华东-上海一',
                    'endpoint'      => 'https://obs.cn-east-3.myhuaweicloud.com',
                ],
                'ap-southeast-1' => [
                    'name'          => '中国香港',
                    'endpoint'      => 'https://obs.ap-southeast-1.myhuaweicloud.com',
                ],
                'ap-southeast-2' => [
                    'name'          => '亚太-曼谷',
                    'endpoint'      => 'https://obs.ap-southeast-2.myhuaweicloud.com',
                ],
                'ap-southeast-3' => [
                    'name'          => '亚太-新加坡',
                    'endpoint'      => 'https://obs.ap-southeast-3.myhuaweicloud.com',
                ],
                'ap-southeast-4' => [
                    'name'          => '亚太-雅加达',
                    'endpoint'      => 'https://obs.ap-southeast-4.myhuaweicloud.com',
                ],
                'af-south-1' => [
                    'name'          => '非洲-约翰内斯堡',
                    'endpoint'      => 'https://obs.af-south-1.myhuaweicloud.com',
                ],
            ],
            self::UCLOUD => [
                's3-cn-bj' => [
                    'name'          => '华北一',
                    'endpoint'      => 'https://s3-cn-bj.ufileos.com',
                ],
                's3-cn-wlcb' => [
                    'name'          => '华北二',
                    'endpoint'      => 'https://s3-cn-wlcb.ufileos.com',
                ],
                's3-cn-sh' => [
                    'name'          => '上海',
                    'endpoint'      => 'https://s3-cn-sh2.ufileos.com',
                ],
                's3-cn-gd' => [
                    'name'          => '广州',
                    'endpoint'      => 'https://s3-cn-gd.ufileos.com',
                ],
                's3-hk' => [
                    'name'          => '香港',
                    'endpoint'      => 'https://s3-hk.ufileos.com',
                ],
                's3-us-ca' => [
                    'name'          => '洛杉矶',
                    'endpoint'      => 'https://s3-us-ca.ufileos.com',
                ],
                's3-sg' => [
                    'name'          => '新加坡',
                    'endpoint'      => 'https://s3-sg.ufileos.com',
                ],
                's3-idn-jakarta' => [
                    'name'          => '雅加达',
                    'endpoint'      => 'https://s3-idn-jakarta.ufileos.com',
                ],
                's3-tw-tp'  => [
                    'name'          => '台北',
                    'endpoint'      => 'https://s3-tw-tp.ufileos.com',
                ],
                's3-afr-nigeria' => [
                    'name'          => '拉各斯',
                    'endpoint'      => 'https://s3-afr-nigeria.ufileos.com',
                ],
                's3-bra-saopaulo' => [
                    'name'          => '圣保罗',
                    'endpoint'      => 'https://s3-bra-saopaulo.ufileos.com',
                ],
                's3-uae-dubai' => [
                    'name'          => '迪拜',
                    'endpoint'      => 'https://s3-uae-dubai.ufileos.com',
                ],
                's3-ge-fra' => [
                    'name'          => '法兰克福',
                    'endpoint'      => 'https://s3-ge-fra.ufileos.com',
                ],
                's3-vn-sng' => [
                    'name'          => '胡志明市',
                    'endpoint'      => 'https://s3-vn-sng.ufileos.com',
                ],
                's3-us-ws' => [
                    'name'          => '华盛顿',
                    'endpoint'      => 'https://s3-us-ws.ufileos.com',
                ],
                's3-ind-mumbai' => [
                    'name'          => '孟买',
                    'endpoint'      => 'https://s3-ind-mumbai.ufileos.com',
                ],
                's3-kr-seoul' => [
                    'name'          => '首尔',
                    'endpoint'      => 'https://s3-kr-seoul.ufileos.com',
                ],
                's3-jpn-tky' => [
                    'name'          => '东京',
                    'endpoint'      => 'https://s3-jpn-tky.ufileos.com',
                ],
                's3-th-bkk' => [
                    'name'          => '曼谷',
                    'endpoint'      => 'https://s3-th-bkk.ufileos.com',
                ],
                's3-uk-london' => [
                    'name'          => '伦敦',
                    'endpoint'      => 'https://s3-uk-london.ufileos.com',
                ],
            ],
            self::MINIO => [
                'us-east-1' => [
                    'name'      => 'us-east-1',
                    'endpoint'  => '',
                ],
                'us-east-2' => [
                    'name'      => 'us-east-2',
                    'endpoint'  => '',
                ],
                'us-west-1' => [
                    'name'  => 'us-west-1',
                    'endpoint' => '',
                ],
                'us-west-2' => [
                    'name' => 'us-west-2',
                    'endpoint' => '',
                ],
                'ca-central-1' => [
                    'name' => 'ca-central-1',
                    'endpoint' => '',
                ],
                'eu-west-1' => [
                    'name' => 'eu-west-1',
                    'endpoint' => '',
                ],
                'eu-west-2' => [
                    'name' => 'eu-west-2',
                    'endpoint' => '',
                ],
                'eu-west-3' => [
                    'name' => 'eu-west-3',
                    'endpoint' => '',
                ],
                'eu-central-1' => [
                    'name' => 'eu-central-1',
                    'endpoint' => '',
                ],
                'eu-north-1' => [
                    'name' => 'eu-north-1',
                    'endpoint' => '',
                ],
                'ap-east-1' => [
                    'name' => 'ap-east-1',
                    'endpoint' => '',
                ],
                'ap-south-1' => [
                    'name' => 'ap-south-1',
                    'endpoint' => '',
                ],
                'ap-southeast-1' => [
                    'name' => 'ap-southeast-1',
                    'endpoint' => '',
                ],
                'ap-southeast-2' => [
                    'name' => 'ap-southeast-2',
                    'endpoint' => '',
                ],
                'ap-northeast-1' => [
                    'name' => 'ap-northeast-1',
                    'endpoint' => '',
                ],
                'ap-northeast-2' => [
                    'name' => 'ap-northeast-2',
                    'endpoint' => '',
                ],
                'ap-northeast-3' => [
                    'name' => 'ap-northeast-3',
                    'endpoint' => '',
                ],
                'me-south-1' => [
                    'name' => 'me-south-1',
                    'endpoint' => '',
                ],
                'sa-east-1' => [
                    'name' => 'sa-east-1',
                    'endpoint' => '',
                ],
                'us-gov-west-1' => [
                    'name' => 'us-gov-west-1',
                    'endpoint' => '',
                ],
                'us-gov-east-1' => [
                    'name' => 'us-gov-east-1',
                    'endpoint' => '',
                ],
                'cn-north-1' => [
                    'name' => 'cn-north-1',
                    'endpoint' => '',
                ],
                'cn-northwest-1' => [
                    'name' => 'cn-northwest-1',
                    'endpoint' => '',
                ],
            ],
            self::RUSTFS => [
                'auto' => [
                    'name' => 'auto',
                    'endpoint' => '',
                ],
            ],
        }
    }
}
