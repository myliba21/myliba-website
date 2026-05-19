export interface StrapiImage {
  id: number;
  attributes: {
    url: string;
    width: number;
    height: number;
    alternativeText: string | null;
    formats: Record<string, { url: string; width: number; height: number }>;
  };
}

export interface StrapiMeta {
  pagination?: {
    page: number;
    pageSize: number;
    pageCount: number;
    total: number;
  };
}

export interface StrapiData<T> {
  id: number;
  attributes: T;
}
