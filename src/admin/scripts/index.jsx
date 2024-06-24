import '../styles/index.css';
import 'leaflet/dist/leaflet.css';
import MapPreview from './components/MapPreview';
import React from 'react';
import ReactDOM from 'react-dom/client';
window.addEventListener('DOMContentLoaded', function () {
	/**
	 * Init Map preview element
	 */
	let mapPreview = document.querySelector('#ecap-map-preview-root');
	if (mapPreview) {
		ReactDOM.createRoot(mapPreview).render(
			<React.StrictMode>
				<MapPreview />
			</React.StrictMode>
		);
	}
});
