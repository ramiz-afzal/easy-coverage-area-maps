import React, { useEffect, useState } from 'react';
import L from 'leaflet';
import iconUrl from '@/admin/assets/marker-icon.png';
import shadowUrl from '@/admin/assets/marker-shadow.png';
const MapPreview = function () {
	const [mapState, setMapState] = useState(0);

	const initializeMap = function () {
		if (!L) {
			return;
		}

		if (mapState !== 0) {
			return;
		}

		const map = L.map('map-wrapper').setView([51.505, -0.09], 13);
		L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
		}).addTo(map);

		const customIcon = L.icon({
			iconUrl: iconUrl,
			shadowUrl: shadowUrl,
			popupAnchor: [10, 0],
		});

		L.marker([51.5, -0.09], { icon: customIcon }).addTo(map).bindPopup('A pretty CSS3 popup.<br> Easily customizable.');
		L.marker([51.5, -0.99], { icon: customIcon }).addTo(map).bindPopup('A pretty CSS3 popup.<br> Easily customizable.');

		L.polygon([
			[51.509, -0.08],
			[51.503, -0.06],
			[51.51, -0.047],
		])
			.addTo(map)
			.bindPopup('A pretty CSS3 popup.<br> Easily customizable.');

		setMapState(1);
	};

	useEffect(() => {
		initializeMap();
	}, []);
	return (
		<>
			<div id="map-wrapper" style={{ height: '600px' }}></div>
		</>
	);
};
export default MapPreview;
