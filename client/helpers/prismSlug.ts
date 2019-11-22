import langs from '../../resources/languages.json';

const isAlias = (x: string): x is keyof typeof langs.aliases =>
  x in langs.aliases;

export default function prismSlug(slug: string): string {
  return isAlias(slug) ? langs.aliases[slug] : slug;
}
