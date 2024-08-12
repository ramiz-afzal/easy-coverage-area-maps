import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';
import { createFilter } from '@rollup/pluginutils';
import { promises as fs } from 'fs';

// Custom plugin to move CSS files to a specific directory
function moveCssToAssets() {
	const filter = createFilter(['**/*.css']);

	return {
		name: 'move-css-to-assets',
		async generateBundle(options, bundle) {
			for (const [fileName, asset] of Object.entries(bundle)) {
				if (filter(fileName)) {
					// Move CSS asset to the desired directory
					const cssPath = path.resolve(__dirname, 'assets/css/', path.basename(fileName));
					await fs.mkdir(path.dirname(cssPath), { recursive: true });
					await fs.writeFile(cssPath, asset.source);
					delete bundle[fileName];
				}
			}
		},
	};
}

export default defineConfig({
	plugins: [
		react(),
		moveCssToAssets(), // Add the custom plugin
	],
	build: {
		rollupOptions: {
			input: {
				frontend: path.resolve(__dirname, './src/frontend/scripts/index.jsx'),
				admin: path.resolve(__dirname, './src/admin/scripts/index.jsx'),
			},
			output: {
				entryFileNames: '[name].js',
				assetFileNames: 'media/[name].[ext]',
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
	optimizeDeps: {
		include: ['react', 'react-dom'],
	},
});
