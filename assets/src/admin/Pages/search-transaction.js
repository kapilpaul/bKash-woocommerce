import React, { useState, useEffect, useRef } from 'react';
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { Link, useLocation } from 'react-router-dom';
import { toast } from 'react-toastify';
import { API } from '../../constants';
import '../styles/react-toastify.scss';

const SearchTransaction = () => {

	const [ isSubmitted, setIsSubmitted ] = useState( false );
	const [ transactionID, setTransactionID ] = useState( '' );
	const [ transactionData, setTransactionData ] = useState( {} );

	const { search } = useLocation();

	useEffect( () => {
		setTransactionID( search.replace( '?trx_id=', '' ) );
	}, [ transactionID ] );

	/**
	 * If API keys not set then show this.
	 *
	 * @returns string
	 */
	const searchTransactionIsInactive = () => {
		return (
			<p>
				{ __(
					'Before search transaction, you must have to add API keys in ',
					'dc-bkash'
				) }
				<Link to="/settings">Settings</Link>.
			</p>
		);
	};

	const handleSearch = () => {
		setIsSubmitted( true );
		setTransactionData( {} );

		let searchPath = API.v1.searchTransaction + transactionID;

		apiFetch( {
			path: searchPath
		} )
			.then( ( resp ) => {
				setIsSubmitted( false );
				setTransactionID( '' );
				setTransactionData( resp );
			} )
			.catch( ( err ) => {
				setIsSubmitted( false );

				if ( 'object' === typeof err.message ) {
					toast.error( err.message.errorCode + ' : ' + err.message.errorMessage );
					return;
				}

				toast.error( err.data.status + ' : ' + err.message );
			} );
	};

	const searchTransactionContainer = () => {
		return (
			<>
				<div className="search-transaction-container__form">
					<div className="form-group">
						<label>{ __( 'Transaction ID', 'dc-bkash' ) }</label>
						<input
							type="text"
							className="form-control"
							value={ transactionID }
							onChange={ ( e ) => setTransactionID( e.target.value ) }
						/>
					</div>

					<Button
						type="submit"
						isBusy={ isSubmitted }
						disabled={ isSubmitted || '' === transactionID }
						className="dc_bkash_save_btn"
						isPrimary={ true }
						onClick={ () => handleSearch() }
					>
						{ isSubmitted ?
							__( 'Searching', 'dc-bkash' ) :
							__( 'Search', 'dc-bkash' ) }
					</Button>
				</div>

				{ 0 === Object.keys( transactionData ).length ? '' : (
					<div className="transaction-deatils">
						<h3>{ __( 'Transaction Details', 'dc-bkash' ) }</h3>

						<table className="table table-bordered border-primary transactions">
							<thead>
								<tr>
									<th scope="col">{ __( 'Title', 'dc-bkash' ) }</th>
									<th scope="col">{ __( 'Data', 'dc-bkash' ) }</th>
								</tr>
							</thead>

							<tbody>
								{ Object.entries( transactionData ).map( ( [ key, item ], index ) => {
									return (
										<tr key={ index }>
											<td>{ key }</td>
											<td>{ item }</td>
										</tr>
									);
								} ) }
							</tbody>
						</table>
					</div>
				) }
			</>
		);
	};

	return (
		<div className="dokan_admin_settings_container">
			<div className="title-section">
				<h2>{ __( 'Search Transaction ', 'dc-bkash' ) }</h2>
			</div>

			<div className="generic-container search-transaction-container">
				{ '1' === dc_bkash_admin.all_credentials_filled ? (
					searchTransactionContainer()
				) : (
					searchTransactionIsInactive()
				) }
			</div>
		</div>
	);
};

export default SearchTransaction;
