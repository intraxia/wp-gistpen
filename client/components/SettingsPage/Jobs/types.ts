export type Job = {
  name: string;
  description: string;
  slug: string;
  status: 'idle' | 'processing';
};
