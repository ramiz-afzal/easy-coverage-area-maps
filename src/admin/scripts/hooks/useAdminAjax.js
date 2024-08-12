import { useEffect, useState } from 'react';

export default function useAdminAjax(action = '', params = {}) {
	const [data, setData] = useState(null);
	const [isPending, setIsPending] = useState(false);
	const [isError, setIsError] = useState(false);
	const [errorMsg, setErrorMsg] = useState(null);
	try {
		useEffect(async () => {
			setIsPending(true);

			if (!action) {
				setIsPending(false);
				setIsError(true);
				setErrorMsg("'action' param is required");
			}

			if (!easyCoverageAreaMapsAdminAjax || !easyCoverageAreaMapsAdminAjax.ajax_url || !easyCoverageAreaMapsAdminAjax.nonce) {
				setIsPending(false);
				setIsError(true);
				setErrorMsg('Localized variable not accessible or not defined');
			}

			let requestBody = new FormData();
			requestBody.append('action', action);
			requestBody.append('security', easyCoverageAreaMapsAdminAjax.nonce);

			if (params) {
				for (const key in params) {
					if (Object.hasOwnProperty.call(params, key)) {
						requestBody.append(key, params[key]);
					}
				}
			}

			const response = await fetch(easyCoverageAreaMapsAdminAjax.ajax_url, { method: 'POST', credentials: 'same-origin', body: requestBody });
			if (!response.ok) {
				setIsPending(false);
				setIsError(true);
				setErrorMsg(response.statusText ? response.statusText : 'Invalid response');
			}

			const responseJSON = await response.json();
			if (!responseJSON.success || !responseJSON.data) {
				setIsPending(false);
				setIsError(true);
				setErrorMsg(responseJSON.data.message ? responseJSON.data.message : 'Invalid response');
			}

			setData(responseJSON.data ? responseJSON.data : null);
			setIsPending(false);
		}, []);
	} catch (error) {
		setIsPending(false);
		setIsError(true);
		setErrorMsg(error.message ? error.message : 'An error ocurred');
	}

	return [data, isPending, isError, errorMsg];
}
