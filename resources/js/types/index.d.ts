import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';
import type {
  GeneralSettingsData,
  WorkspaceData,
  WorkspaceUserContextData,
} from './generated';

export interface Auth {
  user: User;
}

export interface BreadcrumbItem {
  title: string;
  href: string;
}

export interface NavItem {
  title: string;
  href: NonNullable<InertiaLinkProps['href']>;
  icon?: LucideIcon;
  isActive?: boolean;
}

export type AppPageProps<T extends object = Record<string, never>> = T & {
  name: string;
  quote: { message: string; author: string };
  auth: Auth;
  sidebarOpen: boolean;
  generalSettings: GeneralSettingsData;
  workspaces?: WorkspaceData[];
  workspaceUserContext?: WorkspaceUserContextData;
};

export interface User {
  id: string;
  name: string;
  email: string;
  avatar?: string;
  is_super_admin?: boolean;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
