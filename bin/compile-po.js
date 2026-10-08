/**
 * Gettext .po → .mo compilation
 *
 * WordPress only reads the binary .mo at runtime, never the editable .po.
 * Whenever a .po is edited — through Loco, Poedit, or by hand in an editor —
 * the matching .mo must be recompiled. This module is the single place that
 * does it, used both by the dev watcher (per-file, on change) and by the
 * production sync (all files, as a build-time safety net).
 */

import { readFileSync, writeFileSync, readdirSync } from 'fs';
import { resolve, basename } from 'path';
import gettextParser from 'gettext-parser';

/**
 * Compile one .po file into a .mo sitting next to it.
 * Returns true on success, false if the file could not be compiled.
 */
export function compilePo(poPath) {
  try {
    const parsed = gettextParser.po.parse(readFileSync(poPath));
    const mo = gettextParser.mo.compile(parsed);
    const moPath = poPath.replace(/\.po$/, '.mo');
    writeFileSync(moPath, mo);
    return true;
  } catch (err) {
    console.error(`[i18n] Failed to compile ${basename(poPath)}: ${err.message}`);
    return false;
  }
}

/**
 * Compile every .po file in a directory. Returns the count compiled.
 */
export function compileAllPo(languagesDir) {
  let entries;
  try {
    entries = readdirSync(languagesDir);
  } catch {
    return 0; // Directory doesn't exist yet — nothing to do
  }

  let count = 0;
  for (const entry of entries) {
    if (entry.endsWith('.po') && compilePo(resolve(languagesDir, entry))) {
      count++;
    }
  }
  return count;
}
