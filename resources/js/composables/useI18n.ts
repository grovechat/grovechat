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

  // Translation function - key is the default text (usually Chinese)
  function t(key: string): string {
    // Check if the key exists in current locale messages
    if (messages.value && key in messages.value) {
      return messages.value[key as keyof Messages] as string;
    }

    // If not found, return the key itself (which should be the Chinese text)
    return key;
  }

  return {
    locale,
    messages,
    updateLocale,
    t,
  };
}
