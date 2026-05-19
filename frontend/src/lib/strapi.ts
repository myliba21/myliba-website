const STRAPI_URL = process.env.NEXT_PUBLIC_STRAPI_URL || 'http://localhost:1337';

interface StrapiResponse<T> {
  data: T;
  meta: Record<string, unknown>;
}

export async function fetchAPI<T>(
  path: string,
  options: {
    locale?: string;
    populate?: string | string[];
    filters?: Record<string, unknown>;
    pagination?: { page?: number; pageSize?: number };
  } = {}
): Promise<StrapiResponse<T>> {
  const { locale, populate, filters, pagination } = options;
  const params = new URLSearchParams();

  if (locale) params.append('locale', locale);
  if (populate) {
    if (Array.isArray(populate)) {
      populate.forEach((p) => params.append('populate', p));
    } else {
      params.append('populate', populate);
    }
  }
  if (pagination?.page) params.append('pagination[page]', String(pagination.page));
  if (pagination?.pageSize) params.append('pagination[pageSize]', String(pagination.pageSize));
  if (filters) {
    Object.entries(filters).forEach(([key, value]) => {
      params.append(`filters${key}`, String(value));
    });
  }

  const url = `${STRAPI_URL}/api${path}?${params.toString()}`;
  const res = await fetch(url, { next: { revalidate: 60 } });

  if (!res.ok) {
    throw new Error(`Strapi API error: ${res.status} ${res.statusText}`);
  }

  return res.json();
}

export function getStrapiMedia(url: string | null): string | null {
  if (!url) return null;
  if (url.startsWith('http') || url.startsWith('//')) return url;
  return `${STRAPI_URL}${url}`;
}

export { STRAPI_URL };
