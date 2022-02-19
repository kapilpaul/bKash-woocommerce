import React, { useState, useEffect, useRef } from 'react';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { toast } from 'react-toastify';
import Loader from '../components/loader';
import '../styles/react-toastify.scss';

const Refund = () => {
	const [isFetching, setIsFetching] = useState(true);
	const [awaitingSearch, setAwaitingSearch] = useState(false);
	const searchInput = useRef(null);

	/**
	 * Send Request by api fetch
	 */
	const sendRequest = (path) => {
		apiFetch({
			path: path,
			parse: false,
		})
			.then((resp) => {
				setIsFetching(false);

				resp.json().then((body) => {
					setTransactions(body);
				});
			})
			.catch((err) => {
				setIsFetching(false);
				toast.error(err.data.status + ' : ' + err.message);
			});
	};

	useEffect(() => {
		setIsFetching(false);
	}, []);

	if (isFetching) {
		return <Loader />;
	}

	/**
	 * Search
	 * @param {*} search
	 */
	const handleSearch = (search) => {
		if ('' === search) {
			fetchTransactions();
			return;
		}

		if (!awaitingSearch) {
			setTimeout(() => {
				let searchText = searchInput.current.value;
				setAwaitingSearch(false);
				doSearch(searchText);
			}, 1000);
		}

		setAwaitingSearch(true);
	};

	/**
	 * Do search
	 *
	 * @param {*} searchText
	 */
	const doSearch = (searchText) => {
		let url = '/dc-bkash/v1/transactions/?search=' + searchText;

		sendRequest(url);
	};

	return (
		<div className="dokan_admin_settings_container">
			<div className="title-section">
				<h2>{__('Refund', 'dc-bkash')}</h2>

				<div className="search-transaction">
					<input
						type="text"
						className="widefat"
						name="search"
						id="search"
						placeholder={__('Search', 'dc-bkash')}
						onChange={(event) => handleSearch(event.target.value)}
						ref={searchInput}
					/>
				</div>
			</div>

			<div className="all-transactions"></div>
		</div>
	);
};

export default Refund;
