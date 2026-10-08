import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import liveReload from 'vite-plugin-live-reload';
import path from 'path';
import { config } from 'dotenv';

config();

const THEME_DIR = process.env.THEME_DIR || './public/wp-content/themes/wp-twalp';
const resolvedThemeDir = path.resolve(__dirname, THEME_DIR);

const isProduction = process.env.NODE_ENV === 'production';
const themeBase = '/wp-content/themes/' + path.basename(resolvedThemeDir) + '/dist/';

export default defineConfig({
  plugins: [
    tailwindcss(),
    // Watch PHP and Twig files in the theme directory for live reload
    liveReload([
      `${resolvedThemeDir}/**/*.php`,
      `${resolvedThemeDir}/views/**/*.twig`,
    ]),
  ],

  root: 'src',

  // In dev: serve from root so @vite/client and entry points work at simple paths
  // In prod: prefix with theme path for correct asset URLs
  base: isProduction ? themeBase : '/',

  css: {
    preprocessorOptions: {
      scss: {
        // Legacy SCSS (src/scss): resolve `@import 'sass-mq/mq'` from node_modules
        // and silence Dart Sass deprecations (@import, lighten/darken…) until
        // it is rewritten with Tailwind.
        loadPaths: [path.resolve(__dirname, 'node_modules')],
        quietDeps: true,
        silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'slash-div'],
      },
    },
  },

  build: {
    outDir: path.resolve(resolvedThemeDir, 'dist'),
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        main: path.resolve(__dirname, 'src/js/main.js'),
      },
    },
  },

  server: {
    port: 5173,
    strictPort: false,
    cors: true,
    origin: 'http://localhost:5173',
  },
});
