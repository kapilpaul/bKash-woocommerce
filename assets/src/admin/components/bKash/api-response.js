import apiFetch from '@wordpress/api-fetch';
import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React, { useEffect, useState } from 'react';
import { beautifyJson } from '../../utils/helper';

function ApiResponse({ path, callback = false }) {
	const [loading, setLoading] = useState(true);
	const [apiTitle, setApiTitle] = useState('');
	const [responseData, setResponseData] = useState({});
	const [requestParamsData, setRequestParamsData] = useState({});
	const [requestUrl, setRequestUrl] = useState('');

	useEffect(() => {
		apiFetch({
			path: path,
		})
			.then((resp) => {
				setLoading(false);
				setApiTitle(resp.title);
				setResponseData(resp.data);
				setRequestParamsData(resp.request_params);
				setRequestUrl(resp.request_url);

				if (callback) {
					callback(resp);
				}
			})
			.catch((err) => {});
	}, []);

	return (
		<div className="grant-token-container">
			{loading && <Spinner />}

			{!loading && (
				<>
					<p className="strong">
						{__('API Title: ', 'dc-bkash')}
						{apiTitle}
					</p>
					<p className="strong">
						{__('API URL: ', 'dc-bkash')}
						<a href={requestUrl} target="_blank">
							{requestUrl}
						</a>
					</p>

					<p className="strong">Request Body:</p>

					<pre>{beautifyJson(requestParamsData)}</pre>

					<p className="strong">API Response:</p>
					<pre>{beautifyJson(responseData)}</pre>
				</>
			)}
		</div>
	);
}

export default ApiResponse;
