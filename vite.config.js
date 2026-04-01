import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import { fileURLToPath, URL } from 'node:url';
import { visualizer } from 'rollup-plugin-visualizer';
import viteCompression from 'vite-plugin-compression';

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/main.css", "resources/js/app.js"],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
        viteCompression({
            verbose: true,
            disable: false,
            threshold: 10240,
            algorithm: 'gzip',
            ext: '.gz',
        }),
        // Thêm analyzer
        visualizer({
            open: true,
            filename: 'storage/app/bundle-stats.html',
            gzipSize: true,
            brotliSize: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                // Tách chunks chi tiết hơn
                manualChunks: (id) => {
                    if (id.includes('node_modules')) {

                        // Inertia
                        if (id.includes('@inertiajs')) {
                            return 'inertia';
                        }

                        // Axios
                        if (id.includes('axios')) {
                            return 'axios';
                        }

                        // UI Components
                        if (id.includes('@headlessui') || id.includes('@heroicons')) {
                            return 'ui-libs';
                        }

                        // Chart libraries
                        if (id.includes('chart') || id.includes('echarts') || id.includes('apexcharts')) {
                            return 'charts';
                        }

                        // Date libraries
                        if (id.includes('moment') || id.includes('dayjs') || id.includes('date-fns')) {
                            return 'date-utils';
                        }

                        // Form & Validation
                        if (id.includes('vee-validate') || id.includes('yup') || id.includes('zod')) {
                            return 'form-validation';
                        }

                        // Rich text editors
                        if (id.includes('tinymce') || id.includes('quill') || id.includes('tiptap') || id.includes('ckeditor')) {
                            return 'editors';
                        }

                        // Lodash
                        if (id.includes('lodash')) {
                            return 'lodash';
                        }

                        // Các vendor còn lại
                        return 'vendor';
                    }
                },
                chunkFileNames: 'assets/js/[name]-[hash].js',
                entryFileNames: 'assets/js/[name]-[hash].js',
                assetFileNames: 'assets/[ext]/[name]-[hash].[ext]',
            },
        },
        chunkSizeWarningLimit: 500,
        // Minify tối ưu
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: false,
                drop_debugger: true,
                pure_funcs: [],
            },
            format: {
                comments: false,
            },
        },
        // Tắt sourcemap cho production
        sourcemap: false,
        // CSS code splitting
        cssCodeSplit: true,
    },
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url))
        },
    },
    optimizeDeps: {
        include: ['vue', '@inertiajs/vue3', 'axios'],
        exclude: ['@vite/client', '@vite/env'],
    },
});
