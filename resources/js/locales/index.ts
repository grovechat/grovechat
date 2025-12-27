import en from './en';
import zhCN from './zh-CN';

export type Locale = 'zh-CN' | 'en';
export type Messages = typeof zhCN;

export const locales: Record<Locale, Messages> = {
  'zh-CN': zhCN,
  en: en as Messages,
};

export const defaultLocale: Locale = 'zh-CN';

export const availableLocales: Array<{ value: Locale; label: string }> = [
  { value: 'zh-CN', label: '简体中文' },
  { value: 'en', label: 'English' },
];
