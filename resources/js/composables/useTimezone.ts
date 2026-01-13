import { onMounted, ref } from 'vue';
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc';
import timezonePlugin from 'dayjs/plugin/timezone';

dayjs.extend(utc);
dayjs.extend(timezonePlugin);

// 获取时区的 UTC 偏移量
function getTimezoneOffset(timezone: string): string {
  try {
    const offset = dayjs().tz(timezone).format('Z'); // e.g. "+08:00"
    return `UTC${offset}`;
  } catch {
    return 'UTC+00:00';
  }
}

// 常用时区列表（按地区分组）
const commonTimezones = [
  // 亚洲
  'Asia/Shanghai',
  'Asia/Hong_Kong',
  'Asia/Taipei',
  'Asia/Tokyo',
  'Asia/Seoul',
  'Asia/Singapore',
  'Asia/Bangkok',
  'Asia/Jakarta',
  'Asia/Manila',
  'Asia/Kuala_Lumpur',
  'Asia/Dubai',
  'Asia/Kolkata',
  'Asia/Karachi',
  // 欧洲
  'Europe/London',
  'Europe/Paris',
  'Europe/Berlin',
  'Europe/Madrid',
  'Europe/Rome',
  'Europe/Amsterdam',
  'Europe/Brussels',
  'Europe/Vienna',
  'Europe/Stockholm',
  'Europe/Moscow',
  'Europe/Istanbul',
  // 美洲
  'America/New_York',
  'America/Chicago',
  'America/Denver',
  'America/Los_Angeles',
  'America/Toronto',
  'America/Vancouver',
  'America/Mexico_City',
  'America/Bogota',
  'America/Lima',
  'America/Santiago',
  'America/Sao_Paulo',
  'America/Buenos_Aires',
  // 大洋洲
  'Pacific/Auckland',
  'Australia/Sydney',
  'Australia/Melbourne',
  'Australia/Brisbane',
  'Australia/Perth',
  // 非洲
  'Africa/Cairo',
  'Africa/Johannesburg',
  'Africa/Lagos',
  'Africa/Nairobi',
];

// 城市名称映射（支持中英文）
const cityNames: Record<string, { zh: string; en: string }> = {
  'Asia/Shanghai': { zh: '上海', en: 'Shanghai' },
  'Asia/Hong_Kong': { zh: '香港', en: 'Hong Kong' },
  'Asia/Taipei': { zh: '台北', en: 'Taipei' },
  'Asia/Tokyo': { zh: '东京', en: 'Tokyo' },
  'Asia/Seoul': { zh: '首尔', en: 'Seoul' },
  'Asia/Singapore': { zh: '新加坡', en: 'Singapore' },
  'Asia/Bangkok': { zh: '曼谷', en: 'Bangkok' },
  'Asia/Jakarta': { zh: '雅加达', en: 'Jakarta' },
  'Asia/Manila': { zh: '马尼拉', en: 'Manila' },
  'Asia/Kuala_Lumpur': { zh: '吉隆坡', en: 'Kuala Lumpur' },
  'Asia/Dubai': { zh: '迪拜', en: 'Dubai' },
  'Asia/Kolkata': { zh: '加尔各答', en: 'Kolkata' },
  'Asia/Karachi': { zh: '卡拉奇', en: 'Karachi' },
  'Europe/London': { zh: '伦敦', en: 'London' },
  'Europe/Paris': { zh: '巴黎', en: 'Paris' },
  'Europe/Berlin': { zh: '柏林', en: 'Berlin' },
  'Europe/Madrid': { zh: '马德里', en: 'Madrid' },
  'Europe/Rome': { zh: '罗马', en: 'Rome' },
  'Europe/Amsterdam': { zh: '阿姆斯特丹', en: 'Amsterdam' },
  'Europe/Brussels': { zh: '布鲁塞尔', en: 'Brussels' },
  'Europe/Vienna': { zh: '维也纳', en: 'Vienna' },
  'Europe/Stockholm': { zh: '斯德哥尔摩', en: 'Stockholm' },
  'Europe/Moscow': { zh: '莫斯科', en: 'Moscow' },
  'Europe/Istanbul': { zh: '伊斯坦布尔', en: 'Istanbul' },
  'America/New_York': { zh: '纽约', en: 'New York' },
  'America/Chicago': { zh: '芝加哥', en: 'Chicago' },
  'America/Denver': { zh: '丹佛', en: 'Denver' },
  'America/Los_Angeles': { zh: '洛杉矶', en: 'Los Angeles' },
  'America/Toronto': { zh: '多伦多', en: 'Toronto' },
  'America/Vancouver': { zh: '温哥华', en: 'Vancouver' },
  'America/Mexico_City': { zh: '墨西哥城', en: 'Mexico City' },
  'America/Bogota': { zh: '波哥大', en: 'Bogota' },
  'America/Lima': { zh: '利马', en: 'Lima' },
  'America/Santiago': { zh: '圣地亚哥', en: 'Santiago' },
  'America/Sao_Paulo': { zh: '圣保罗', en: 'Sao Paulo' },
  'America/Buenos_Aires': { zh: '布宜诺斯艾利斯', en: 'Buenos Aires' },
  'Pacific/Auckland': { zh: '奥克兰', en: 'Auckland' },
  'Australia/Sydney': { zh: '悉尼', en: 'Sydney' },
  'Australia/Melbourne': { zh: '墨尔本', en: 'Melbourne' },
  'Australia/Brisbane': { zh: '布里斯班', en: 'Brisbane' },
  'Australia/Perth': { zh: '珀斯', en: 'Perth' },
  'Africa/Cairo': { zh: '开罗', en: 'Cairo' },
  'Africa/Johannesburg': { zh: '约翰内斯堡', en: 'Johannesburg' },
  'Africa/Lagos': { zh: '拉各斯', en: 'Lagos' },
  'Africa/Nairobi': { zh: '内罗毕', en: 'Nairobi' },
};

// 生成时区数据（包含中英文）
export const timezoneData = commonTimezones.map((tz) => {
  const offset = getTimezoneOffset(tz);
  const names = cityNames[tz] || {
    zh: tz.split('/')[1]?.replace(/_/g, ' ') || tz,
    en: tz.split('/')[1]?.replace(/_/g, ' ') || tz,
  };
  return {
    value: tz,
    zh: names.zh,
    en: names.en,
    offset,
  };
});

export type Timezone = (typeof commonTimezones)[number];

const getStoredTimezone = (): Timezone | null => {
  if (typeof window === 'undefined') {
    return null;
  }

  return localStorage.getItem('timezone') as Timezone | null;
};

// 获取浏览器默认时区
const getBrowserTimezone = (): Timezone => {
  if (typeof window === 'undefined') {
    return 'Asia/Shanghai';
  }

  const browserTz = Intl.DateTimeFormat().resolvedOptions().timeZone;

  // 检查浏览器时区是否在我们的列表中
  const found = timezoneData.find((tz) => tz.value === browserTz);
  if (found) {
    return found.value as Timezone;
  }

  // 如果不在列表中，返回默认的中国时区
  return 'Asia/Shanghai';
};

const timezone = ref<Timezone>(getBrowserTimezone());

export function initializeTimezone() {
  if (typeof window === 'undefined') {
    return;
  }

  const savedTimezone = getStoredTimezone();
  if (savedTimezone) {
    timezone.value = savedTimezone;
  }
}

export function useTimezone() {
  onMounted(() => {
    const savedTimezone = getStoredTimezone();

    if (savedTimezone) {
      timezone.value = savedTimezone;
    }
  });

  function updateTimezone(newTimezone: Timezone) {
    timezone.value = newTimezone;

    // Store in localStorage for persistence
    localStorage.setItem('timezone', newTimezone);
  }

  // 获取时区列表（根据语言返回对应的标签）
  function getTimezones(lang: 'zh-CN' | 'en' = 'zh-CN') {
    return timezoneData.map((tz) => ({
      value: tz.value,
      label:
        lang === 'zh-CN' ? `${tz.zh} ${tz.offset}` : `${tz.en} ${tz.offset}`,
      offset: tz.offset,
    }));
  }

  // 获取当前时区信息
  function getCurrentTimezoneInfo(lang: 'zh-CN' | 'en' = 'zh-CN') {
    const data =
      timezoneData.find((tz) => tz.value === timezone.value) || timezoneData[0];
    return {
      value: data.value,
      label:
        lang === 'zh-CN'
          ? `${data.zh} ${data.offset}`
          : `${data.en} ${data.offset}`,
      offset: data.offset,
    };
  }

  // 格式化时间到当前时区
  function formatToTimezone(
    date: Date | string,
    format = 'YYYY-MM-DD HH:mm:ss',
  ): string {
    const d = typeof date === 'string' ? dayjs(date) : dayjs(date);
    return d.tz(timezone.value).format(format);
  }

  return {
    timezone,
    timezoneData,
    updateTimezone,
    getTimezones,
    getCurrentTimezoneInfo,
    formatToTimezone,
  };
}
