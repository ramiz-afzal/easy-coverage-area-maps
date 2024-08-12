import '../styles/index.scss';
import 'leaflet/dist/leaflet.css';
import React from 'react';
import ReactDOM from 'react-dom/client';
import MapPreview from './components/MapPreview';
import MapRegions from './components/MapRegions';
window.addEventListener('DOMContentLoaded', function () {
	/**
	 * Init Map preview element
	 */
	let mapPreview = document.querySelector('#ecap-map-preview-root');
	if (mapPreview) {
		let postId = mapPreview.dataset.postId ? mapPreview.dataset.postId : null;
		ReactDOM.createRoot(mapPreview).render(
			<React.StrictMode>
				<MapPreview postId={postId} />
			</React.StrictMode>
		);
	}

	/**
	 * Init Map regions element
	 */
	let mapRegions = document.querySelector('#ecap-map-regions-root');
	if (mapRegions) {
		let postId = mapRegions.dataset.postId ? mapRegions.dataset.postId : null;
		ReactDOM.createRoot(mapRegions).render(
			<React.StrictMode>
				<MapRegions postId={postId} />
			</React.StrictMode>
		);
	}
});
