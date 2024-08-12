import { useState } from 'react';
import useAdminAjax from '../hooks/useAdminAjax';
import { v4 as uuidv4 } from 'uuid';

const MapRegions = function ({ postId }) {
	const [data, isPending, isError, errorMsg] = useAdminAjax('ecap_get_map_regions', { post_id: postId });
	const [regions, setRegions] = useState([
		{
			id: uuidv4(),
			name: 'Region 1',
			coordinates: [
				{ id: uuidv4(), lat: 51.509, long: -0.08 },
				{ id: uuidv4(), lat: 51.503, long: -0.06 },
				{ id: uuidv4(), lat: 51.51, long: -0.047 },
			],
		},
		{
			id: uuidv4(),
			name: 'Region 2',
			coordinates: [
				{ id: uuidv4(), lat: 51.509, long: -0.08 },
				{ id: uuidv4(), lat: 51.503, long: -0.06 },
				{ id: uuidv4(), lat: 51.51, long: -0.047 },
			],
		},
		{
			id: uuidv4(),
			name: 'Region 3',
			coordinates: [
				{ id: uuidv4(), lat: 51.509, long: -0.08 },
				{ id: uuidv4(), lat: 51.503, long: -0.06 },
				{ id: uuidv4(), lat: 51.51, long: -0.047 },
			],
		},
	]);

	const addRegion = function () {
		let newRegion = {
			id: uuidv4(),
			name: '',
			coordinates: [{ lat: null, long: null }],
		};
		let _regions = [...regions, newRegion];
		setRegions(_regions);
	};

	const updateRegionName = function (regionId, newName) {
		let index = regions.findIndex(function (elem) {
			return elem.id == regionId;
		});
		let _region = regions[index];
		_region = { ..._region, name: newName };
		let start = regions.slice(0, index);
		let end = regions.slice(index + 1, regions.length);
		let _regions = [...start, _region, ...end];
		setRegions(_regions);
	};

	const addCoordinateGroup = function (regionId) {
		let index = regions.findIndex(function (elem) {
			return elem.id == regionId;
		});
		let _region = regions[index];
		_region = { ..._region };
		_region.coordinates = [..._region.coordinates, { id: uuidv4(), lat: null, long: null }];
		let start = regions.slice(0, index);
		let end = regions.slice(index + 1, regions.length);
		let _regions = [...start, _region, ...end];
		setRegions(_regions);
	};

	const updateCoordGroupProp = function (regionId, groupId, propType = '', propValue = null) {
		if (!regionId || !groupId || !propType) {
			return groupId;
		}
		let index = regions.findIndex(function (elem) {
			return elem.id == regionId;
		});
		let region = regions[index];

		let _index = region.coordinates.findIndex(function (elem) {
			return elem.id == groupId;
		});
		let coordGroup = region.coordinates[_index];
		if (!coordGroup) {
			return;
		}
		if (!['lat', 'long'].includes(propType)) {
			return;
		}
		let _coordGroup = null;
		if (propType == 'lat') {
			_coordGroup = { ...coordGroup, lat: propValue };
		} else if (propType == 'long') {
			_coordGroup = { ...coordGroup, long: propValue };
		}

		let start = region.coordinates.slice(0, _index);
		let end = region.coordinates.slice(_index + 1, region.coordinates.length);
		let _coordinates = [...start, _coordGroup, ...end];
		let updated = { ...region, coordinates: _coordinates };
		let _regions = [...regions.slice(0, index), updated, ...regions.slice(index + 1, regions.length)];
		setRegions(_regions);
	};

	const updateCoordGroupLat = function (regionId, groupId, value) {
		updateCoordGroupProp(regionId, groupId, 'lat', value);
	};
	const updateCoordGroupLong = function (regionId, groupId, value) {
		updateCoordGroupProp(regionId, groupId, 'long', value);
	};
	return (
		<>
			{isPending ? (
				<p className="ecap_loading_msg">Loading Data...</p>
			) : isError ? (
				<p className="ecap_error_msg">{errorMsg}</p>
			) : !data || !data?.regions ? (
				<p className="ecap_error_msg">Invalid Data</p>
			) : (
				<form className="ecap_regions_form">
					{regions.length && (
						<div className="ecap_regions">
							{regions.map((region, index) => (
								<div className="ecap_accordion closed" key={index}>
									<div className="ecap_accordion_header">
										<h3>{region.name ? region.name : 'N/A'}</h3>
									</div>
									<div className="ecap_accordion_body">
										<div>
											<label htmlFor="ecap_region_name">Name</label>
											<input type="text" value={region.name} onChange={(e) => updateRegionName(region.id, e.target.value)} />
										</div>
										{region.coordinates && (
											<table>
												<thead>
													<th>Latitude</th>
													<th>Longitude</th>
												</thead>
												<tbody>
													{region.coordinates.map((coord, index) => (
														<tr className="ecap_coord_group" key={index}>
															<td>
																<input type="number" value={coord.lat} onChange={(e) => updateCoordGroupLat(region.id, coord.id, e.target.value)} />
															</td>
															<td>
																<input type="number" value={coord.long} onChange={(e) => updateCoordGroupLong(region.id, coord.id, e.target.value)} />
															</td>
														</tr>
													))}
												</tbody>
												<tfoot>
													<td colSpan={2}>
														<div>
															<button className="button" type="button" onClick={() => addCoordinateGroup(region.id)}>
																<span>Add Coordinate</span>
															</button>
														</div>
													</td>
												</tfoot>
											</table>
										)}
									</div>
								</div>
							))}
						</div>
					)}
					<div>
						<button className="button" type="button" onClick={addRegion}>
							<span>Add Region</span>
						</button>
					</div>
				</form>
			)}
		</>
	);
};
export default MapRegions;
