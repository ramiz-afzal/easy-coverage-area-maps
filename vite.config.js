import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
	plugins: [react()],
	build: {
		rollupOptions: {
			input: {
				frontend: path.resolve(__dirname, './src/frontend/scripts/index.js'),
				admin: path.resolve(__dirname, './src/admin/scripts/index.js'),
			},
			output: {
				entryFileNames: '[name].js',
				assetFileNames: 'media/[name][ext]',
				dir: path.resolve(__dirname, 'assets/js/'),
			},
		},
		sourcemap: true,
	},
	resolve: {
		alias: {
			'@': path.resolve(__dirname, './src'),
		},
	},
	css: {
		preprocessorOptions: {
			scss: {
				// additionalData: `@import "src/styles/variables";`
			},
		},
	},
	optimizeDeps: {
		include: ['react', 'react-dom'],
	},
});
