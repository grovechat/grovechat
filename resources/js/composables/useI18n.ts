import { defaultLocale, locales, type Locale, type Messages } from '@/locales';
import { onMounted, ref } from 'vue';

const setCookie = (name: string, value: string, days = 365) => {
  if (typeof document === 'undefined') {
    return;
  }

  const maxAge = days * 24 * 60 * 60;

  document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const getStoredLocale = (): Locale | null => {
  if (typeof window === 'undefined') {
    return null;
  }

  return localStorage.getItem('locale') as Locale | null;
};

const locale = ref<Locale>(defaultLocale);
const messages = ref<Messages>(locales[defaultLocale]);

export function initializeLocale() {
  if (typeof window === 'undefined') {
    return;
  }

  const savedLocale = getStoredLocale();
  if (savedLocale && locales[savedLocale]) {
    locale.value = savedLocale;
    messages.value = locales[savedLocale];
  }
}

export function useI18n() {
  onMounted(() => {
    const savedLocale = getStoredLocale();

    if (savedLocale && locales[savedLocale]) {
      locale.value = savedLocale;
      messages.value = locales[savedLocale];
    }
  });

  function updateLocale(newLocale: Locale) {
    if (!locales[newLocale]) {
      console.warn(`Locale "${newLocale}" is not available`);
      return;
    }

    locale.value = newLocale;
    messages.value = locales[newLocale];

    // Store in localStorage for client-side persistence
    localStorage.setItem('locale', newLocale);

    // Store in cookie for SSR
    setCookie('locale', newLocale);
  }

  // Translation function with nested key support
  function t(key: string): string {
    const keys = key.split('.');
    let value: any = messages.value;

    for (const k of keys) {
      if (value && typeof value === 'object' && k in value) {
        value = value[k];
      } else {
        console.warn(`Translation key "${key}" not found`);
        return key;
      }
    }

    return typeof value === 'string' ? value : key;
  }

  return {
    locale,
    messages,
    updateLocale,
    t,
  };
}
