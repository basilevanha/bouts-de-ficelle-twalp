/**
 * SVG sprite generation
 *
 * Concatenates every src/icons/*.svg into a single sprite file made of one
 * <symbol> per icon, written to the theme as assets/icons/sprite.svg. Twig
 * references an icon with <use href=".../sprite.svg#icon-NAME"> — see
 * src/views/partials/icon.twig.
 *
 * This module is the single place that builds the sprite, used both by the
 * dev watcher (on any change under src/icons/) and by the production sync.
 *
 * Source SVGs are normalised minimally: width/height attributes are stripped
 * from the root <svg> so icons size from CSS, and the viewBox is preserved.
 * The symbol id is `icon-` + the file name (without extension).
 */

import { readFileSync, writeFileSync, readdirSync, mkdirSync } from 'fs';
import { resolve, basename, dirname } from 'path';
import { fileURLToPath } from 'url';

/**
 * Turn one .svg file's contents into a <symbol> string.
 * Returns null if the file has no usable <svg> root.
 *
 * Beyond viewBox, presentation attributes set on the root <svg> are carried
 * over to the <symbol> — Lucide / Feather / Heroicons icons declare stroke,
 * fill and stroke-* there, so preserving them lets such icons be dropped in
 * as-is and still render. width/height are intentionally NOT carried over:
 * icons size from CSS (the .icon rule / Tailwind classes).
 */
const CARRIED_ATTRS = [
  'fill', 'stroke', 'stroke-width', 'stroke-linecap',
  'stroke-linejoin', 'stroke-miterlimit',
];

function svgToSymbol(svgContent, name) {
  const openTag = svgContent.match(/<svg\b[^>]*>/i);
  if (!openTag) return null;

  const viewBox = openTag[0].match(/viewBox\s*=\s*["']([^"']+)["']/i);
  // Inner content: everything between the opening <svg ...> and closing </svg>.
  const inner = svgContent
    .slice(openTag.index + openTag[0].length)
    .replace(/<\/svg>\s*$/i, '')
    .trim();

  let attrs = viewBox ? ` viewBox="${viewBox[1]}"` : '';
  for (const attr of CARRIED_ATTRS) {
    const match = openTag[0].match(new RegExp(`\\b${attr}\\s*=\\s*["']([^"']+)["']`, 'i'));
    if (match) attrs += ` ${attr}="${match[1]}"`;
  }

  return `<symbol id="icon-${name}"${attrs}>${inner}</symbol>`;
}

/**
 * Build the sprite from every .svg in `iconsDir` and write it to `outPath`.
 * Returns the number of icons included (0 if the source dir is missing/empty).
 */
export function buildSprite(iconsDir, outPath) {
  let entries;
  try {
    entries = readdirSync(iconsDir).filter((f) => f.endsWith('.svg')).sort();
  } catch {
    return 0; // src/icons/ doesn't exist yet — nothing to do
  }

  if (entries.length === 0) return 0;

  const symbols = [];
  for (const entry of entries) {
    const name = basename(entry, '.svg');
    const symbol = svgToSymbol(readFileSync(resolve(iconsDir, entry), 'utf8'), name);
    if (symbol) {
      symbols.push(symbol);
    } else {
      console.error(`[sprite] Skipped ${entry}: no <svg> root found`);
    }
  }

  const sprite =
    `<svg xmlns="http://www.w3.org/2000/svg" style="display:none">` +
    symbols.join('') +
    `</svg>\n`;

  mkdirSync(dirname(outPath), { recursive: true });
  writeFileSync(outPath, sprite);
  return symbols.length;
}

// Allow running standalone: `node bin/build-sprite.js` (used by `npm run sprite`).
// fileURLToPath decodes URL escapes (e.g. %20) so the comparison holds even
// when the project path contains spaces.
if (process.argv[1] && fileURLToPath(import.meta.url) === resolve(process.argv[1])) {
  const { config } = await import('dotenv');
  const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '..');
  config({ path: resolve(ROOT, '.env') });
  const themeDir = resolve(ROOT, process.env.THEME_DIR || './public/wp-content/themes/wp-twalp');
  const count = buildSprite(
    resolve(ROOT, 'src/icons'),
    resolve(themeDir, 'assets/icons/sprite.svg'),
  );
  console.log(`[sprite] Built sprite with ${count} icon(s)`);
}
