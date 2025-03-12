if (!window.ecap) {
	window.ecap = {
		async ajax(action = '', params = {}) {
			try {
				if (!action) {
					throw new Error('Localized variable not accessible or not defined');
				}

				if (!easyCoverageAreaMapsAjax || !easyCoverageAreaMapsAjax.ajax_url || !easyCoverageAreaMapsAjax.nonce) {
					throw new Error('Localized variable not accessible or not defined');
				}

				let requestBody = new FormData();
				requestBody.append('action', action);
				requestBody.append('security', easyCoverageAreaMapsAjax.nonce);

				if (params) {
					for (const key in params) {
						if (Object.hasOwnProperty.call(params, key)) {
							requestBody.append(key, params[key]);
						}
					}
				}

				const response = await fetch(easyCoverageAreaMapsAjax.ajax_url, { method: 'POST', credentials: 'same-origin', body: requestBody });
				if (!response.ok) {
					throw new Error(response.statusText ? response.statusText : 'Invalid response');
				}

				const responseJSON = await response.json();
				if (!responseJSON.success) {
					throw new Error(responseJSON.data && responseJSON.data.message ? responseJSON.data.message : 'Invalid response');
				}

				return responseJSON.data ? responseJSON.data : null;
			} catch (error) {
				throw new Error(error.message ? error.message : 'An error ocurred');
			}
		},
		async getRegionStatuses() {
			try {
				let response = await this.ajax('ecap_get_region_statuses');
				if (!response || !response.statuses) {
					return [];
				}

				return response.statuses;
			} catch (error) {
				throw new Error(error.message ? error.message : 'An error ocurred');
			}
		},
		async getMapRegions(mapId = null) {
			try {
				if (!mapId) {
					return [];
				}

				let response = await this.ajax('ecap_get_map_regions', { post_id: mapId });
				if (!response || !response.regions) {
					return [];
				}

				return response.regions;
			} catch (error) {
				throw new Error(error.message ? error.message : 'An error ocurred');
			}
		},
		guid() {
			const w = () => {
				return Math.floor((1 + Math.random()) * 0x10000)
					.toString(16)
					.substring(1);
			};
			return `${w()}${w()}-${w()}-${w()}-${w()}-${w()}${w()}${w()}`;
		},
	};
}

// On Page Load
window.addEventListener(
	'DOMContentLoaded',
	async function () {
		/**
		 * Init Radar maps
		 */
		if ((Radar && easyCoverageAreaMapsAjax) || easyCoverageAreaMapsAjax.radar_pk) {
			Radar.initialize(easyCoverageAreaMapsAjax.radar_pk);
			let mapWrappers = this.document.querySelectorAll('.ecap-map');
			if (mapWrappers.length) {
				let regionStatuses = await ecap.getRegionStatuses();
				mapWrappers.forEach(async function (mapWrapper) {
					try {
						let mapId = mapWrapper.dataset.mapId;
						let regions = await ecap.getMapRegions(mapId);
						const map = Radar.ui.map({
							container: mapWrapper,
							style: 'radar-default-v1',
							center: [-73.9911, 40.7342], // NYC
							zoom: 10,
						});

						map.on('load', () => {
							// TODO for debugging
							let currentURL = new URL(window.location.href);
							if (currentURL.searchParams.has('debug')) {
								map.on('click', (e) => {
									const { lng, lat } = e.lngLat;
									console.log('lat', lat);
									console.log('long', lng);
								});
							}

							// if region and statuses data exists
							if (regions && regions.length !== 0 && regionStatuses && regionStatuses.length !== 0) {
								// setup regions
								regions.forEach(async function (region) {
									let coordinates = region.coordinates ? region.coordinates : [];
									if (coordinates.length !== 0) {
										let polygonId = ecap.guid();
										const geojson = {
											type: 'Feature',
											id: polygonId,
											properties: {
												name: region.title,
											},
											geometry: {
												type: 'Polygon',
												coordinates: coordinates,
											},
										};

										let regionsStatus = Array.from(regionStatuses).find((x) => x.ID == region.status);

										let polygonConfig = {
											'fill-opacity': 0.4,
											'border-width': 1,
											'fill-color': regionsStatus && regionsStatus.color ? regionsStatus.color : 'blue',
											'border-color': regionsStatus && regionsStatus.color ? regionsStatus.color : 'blue',
										};

										const feature = await map.addPolygon(geojson, { paint: polygonConfig });

										feature.on('click', ({ feature, originalEvent: event }) => {
											Radar.ui
												.popup({
													text: feature.properties.name,
												})
												.setLngLat([event.lngLat.lng, event.lngLat.lat])
												.addTo(map);
										});
									}
								});

								// fit the map bounds to the features
								map.fitToFeatures({ padding: 40 });
							}

							/**
							 * Helper function
							 * Check if user's selected address exists within one of our defined regions
							 */
							const getAddressRegion = function (latitude, longitude) {
								if (!regions || regions.length == 0) {
									return false;
								}

								const fallbackRegion = {
									title: 'Out of coverage',
									status: {
										title: 'Unavailable',
										color: '#585858',
										desc: 'This address is currently not in our coverage area.',
									},
								};

								let matchedRegion = null;
								/**
								 * https://stackoverflow.com/questions/22521982/check-if-point-is-inside-a-polygon
								 */
								const getIsPointInsidePolygon = (point, vertices) => {
									const x = point[0];
									const y = point[1];

									let inside = false;
									for (let i = 0, j = vertices.length - 1; i < vertices.length; j = i++) {
										const xi = vertices[i][0],
											yi = vertices[i][1];
										const xj = vertices[j][0],
											yj = vertices[j][1];

										const intersect = yi > y != yj > y && x < ((xj - xi) * (y - yi)) / (yj - yi) + xi;
										if (intersect) inside = !inside;
									}

									return inside;
								};

								for (const region of regions) {
									if (!region.coordinates || region.coordinates.length == 0) {
										continue;
									}

									for (const coordinate_group of region.coordinates) {
										let polygon = coordinate_group.map((x) => [parseFloat(x[1]), parseFloat(x[0])]); // have to flip the lat/long here for the algo to work
										matchedRegion = getIsPointInsidePolygon([latitude, longitude], polygon) ? region : null;

										if (matchedRegion !== null) {
											break;
										}
									}

									if (matchedRegion !== null) {
										break;
									}
								}

								if (matchedRegion == null) {
									return fallbackRegion;
								}

								return {
									title: matchedRegion.title,
									status: Array.from(regionStatuses).find((x) => x.ID == matchedRegion.status),
								};
							};

							// Initialize Radar autocomplete
							let autocompleteElement = mapWrapper.parentElement.querySelector('.ecap-autocomplete');
							if (autocompleteElement) {
								const userLocation = Radar.ui.marker();
								Radar.ui.autocomplete({
									container: autocompleteElement,
									countryCode: 'US',
									onSelection: async (address) => {
										const { latitude, longitude, formattedAddress } = address;
										const intersectingRegion = getAddressRegion(latitude, longitude);
										if (!intersectingRegion || !intersectingRegion.status) {
											return;
										}

										// TODO update popup html to reflect intersectingRegion status
										// set geo location
										userLocation.setLngLat([longitude, latitude]);

										// generate HTML
										let html = '';
										html += `<div class="ecap-point-address">`;
										html += `<h3>Address:</h3>`;
										html += `<p>${formattedAddress}</p>`;
										html += '</div>';
										html += `<div class="ecap-region-status" style="border-color: ${intersectingRegion.status.color};">`;
										html += '<div class="ecap-status-content">';
										html += `<h3 class="ecap-status-title">${intersectingRegion.status.title}</h3>`;
										html += `<div class="ecap-status-desc">${intersectingRegion.status.desc}</div>`;
										html += '</div>';
										html += '</div>';

										if (intersectingRegion.status.has_redirect && intersectingRegion.status.has_redirect == 'yes') {
											let redirectURL = null;
											if (intersectingRegion.status.redirect_type == 'page' && intersectingRegion.status.redirect_page) {
												redirectURL = intersectingRegion.status.redirect_page;
											} else if (intersectingRegion.status.redirect_type == 'url' && intersectingRegion.status.redirect_url) {
												redirectURL = intersectingRegion.status.redirect_url;
											}

											if (redirectURL) {
												html += `<div class="ecap-cta-wrap">`;
												html += `<p>Inquire about a connection today</p>`;
												html += `<a class="ecap-cta-button" href="${redirectURL}" role="button">Residential Application</a>`;
												html += `</div>`;
											}
										}

										// append popup
										userLocation.setPopup(Radar.ui.popup({ html: html }));
										userLocation.addTo(map);
										await map.flyTo({ center: [longitude, latitude], zoom: 14 });

										// open popup
										userLocation.togglePopup();
									},
								});
							}
						}); // map onLoad
					} catch (error) {
						console.log(error);
					}
				});

				/**
				 * Setup status markup
				 */
				let statusWrapperElements = this.document.querySelectorAll('.ecap-status-wrapper');
				if (statusWrapperElements.length > 0 && regionStatuses && regionStatuses.length > 0) {
					statusWrapperElements.forEach(function (wrapper) {
						wrapper.innerHTML = '';
						Array.from(regionStatuses).forEach(function (status) {
							let html = '';
							html += `<div class="ecap-region-status" data-status-id="${status.ID}" style="border-color: ${status.color};">`;
							html += '<div class="ecap-status-content">';
							html += `<h3 class="ecap-status-title">${status.title}</h3>`;
							html += `<div class="ecap-status-desc">${status.desc}</div>`;
							html += '</div>';
							html += '</div>';
							wrapper.innerHTML += html;
						});
					});
				}
			}
		}
	},
	false
); // On Page Load
