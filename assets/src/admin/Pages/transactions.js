import React, { useState, useEffect, useRef } from 'react';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { toast } from 'react-toastify';
import ReactPaginate from 'react-paginate';
import { NavLink } from 'react-router-dom';
import Loader from '../components/loader';
import { API } from '../../constants';
import '../styles/react-toastify.scss';

const Transactions = () => {
	const [ isFetching, setIsFetching ] = useState( true );
	const [ transactions, setTransactions ] = useState( [] );
	const [ pageCount, setPageCount ] = useState( 1 );
	const [ currentPage, setCurrentPage ] = useState( 1 );
	const [ awaitingSearch, setAwaitingSearch ] = useState( false );
	const searchInput = useRef( null );

	/**
   * Fetch transactions data
   *
   * @param {*} pageNumber
   */
	const fetchTransactions = ( pageNumber = 1 ) => {
		let url = API.v1.transactions + '?per_page=20&page=' + pageNumber;

		sendRequest( url );
	};

	/**
   * Send Request by api fetch
   */
	const sendRequest = ( path ) => {
		apiFetch( {
			path: path,
			parse: false
		} )
			.then( ( resp ) => {
				setIsFetching( false );

				resp.json().then( body => {
					setTransactions( body );
				} );

				setPageCount( resp.headers.get( 'X-WP-TotalPages' ) );
			} )
			.catch( ( err ) => {
				setIsFetching( false );
				toast.error( err.data.status + ' : ' + err.message );
			} );
	};

	useEffect( () => {
		setIsFetching( true );

		fetchTransactions();
	}, [] );

	if ( isFetching ) {
		return ( <Loader /> );
	}

	/**
   * Get verification Label
   */
	const getVerificationLabel = ( status ) => {
		if ( '1' === status ) {
			return <p className='success-label'>Verified</p>;
		}

		return <p className='error-label'>Pending</p>;
	};

	/**
   * Get verification Button
   */
	const getVerificationButton = ( status, paymentID ) => {
		if ( '1' === status ) {
			return (
				<div className="verify-btn-container verified"></div>
			);
		}

		return (
			<div className="verify-btn-container">
				<button className="btn verify-btn" onClick={ ( event ) => handleVerify( event, paymentID ) }>{ __( 'Verify', 'dc-bkash' ) }</button>
			</div>
		);
	};

	/**
   * Handle Reverification
   * @param {*} event
   * @param {*} paymentID
   */
	const handleVerify = ( event, paymentID ) => {

		//add processing class
		event.target.parentNode.classList.add( 'processing' );

		apiFetch( {
			path: API.v1.queryPayment + paymentID
		} )
			.then( ( resp ) => {
				event.target.parentNode.classList.remove( 'processing' );

				toast.success( __( 'Successfully verified!', 'dc-bkash' ) );

				fetchTransactions( currentPage );
			} )
			.catch( ( err ) => {
				event.target.parentNode.classList.remove( 'processing' );

				toast.error( __( 'Problem in verification!', 'dc-bkash' ) );
			} );
	};

	/**
   * Handle page change
   * @param {*} data
   */
	const handlePageChange = ( data ) => {
		let selectedPage = data.selected + 1;

		setCurrentPage( selectedPage );

		fetchTransactions( selectedPage );
	};

	/**
   * Search
   * @param {*} search
   */
	const handleSearch = ( search ) => {
		if ( '' === search ) {
			fetchTransactions();
			return;
		}

		if ( ! awaitingSearch ) {
			setTimeout( () => {
				let searchText = searchInput.current.value;
				setAwaitingSearch( false );
				doSearch( searchText );
			}, 1000 );
		}

		setAwaitingSearch( true );
	};

	/**
   * Do search
   *
   * @param {*} searchText
   */
	const doSearch = ( searchText ) => {
		let url = API.v1.transactionSearch + searchText;

		sendRequest( url );
	};

	return (
		<div className="dokan_admin_settings_container">
			<div className="title-section">
				<h2>{ __( 'Transactions', 'dc-bkash' ) }</h2>

				<div className="search-transaction">
					<input
						type="text"
						className="widefat"
						name="search"
						id="search"
						placeholder={ __( 'Search', 'dc-bkash' ) }
						onChange={ ( event ) => handleSearch( event.target.value ) }
						ref={ searchInput }
					/>
				</div>
			</div>

			<div className="all-transactions">
				<table className="table table-bordered border-primary transactions">
					<thead>
						<tr>
							<th scope="col">{ __( 'Order Number', 'dc-bkash' ) }</th>
							<th scope="col">{ __( 'Amount', 'dc-bkash' ) }</th>
							<th scope="col">{ __( 'Payment ID', 'dc-bkash' ) }</th>
							<th scope="col">{ __( 'Trx ID', 'dc-bkash' ) }</th>
							<th scope="col">{ __( 'Invoice No', 'dc-bkash' ) }</th>
							<th scope="col">{ __( 'Trx Status', 'dc-bkash' ) }</th>
							<th scope="col">{ __( 'Verification Status', 'dc-bkash' ) }</th>
							<th scope="col">{ __( 'Payment Time', 'dc-bkash' ) }</th>
							<th scope="col">{ __( 'Refund', 'dc-bkash' ) }</th>
							<th scope="col">{ __( 'Refund Amount', 'dc-bkash' ) }</th>
							<th scope="col">{ __( 'Refund Charge', 'dc-bkash' ) }</th>
							<th scope="col">{ __( 'Refund ID', 'dc-bkash' ) }</th>
							<th scope="col">{ __( 'Refund Reason', 'dc-bkash' ) }</th>
							<th scope="col">{ __( 'Action', 'dc-bkash' ) }</th>
						</tr>
					</thead>
					<tbody>
						{ transactions.map( ( transaction, i ) => {
							return (
								<tr key={ i }>
									<td><a href={ transaction.order_url }>{ transaction.order_number }</a></td>
									<td>
										{ '1' === transaction.refund_status ? (
											<del>{ transaction.amount }</del>
										) : (
											transaction.amount
										) }
									</td>
									<td>{ transaction.payment_id }</td>
									<td>{ <NavLink to={ {
										pathname: '/search-transaction',
										search: 'trx_id=' + transaction.trx_id
									} }>{ transaction.trx_id }</NavLink> }</td>
									<td>{ transaction.invoice_number }</td>
									<td>{ transaction.transaction_status }</td>
									<td>{ getVerificationLabel( transaction.verification_status ) }</td>
									<td>{ transaction.created_at }</td>
									<td>{ '1' === transaction.refund_status ? __( 'Refunded', 'dc-bkash' ) : '' }</td>
									<td>{ '0' !== transaction.refund_amount ? transaction.refund_amount : '' }</td>
									<td>{ transaction.refund_charge ?? '' }</td>
									<td>{ <NavLink to={ {
										pathname: '/search-transaction',
										search: 'trx_id=' + transaction.refund_id
									} }>{ transaction.refund_id }</NavLink> }
									</td>
									<td>{ transaction.refund_reason }</td>
									<td>{ getVerificationButton( transaction.verification_status, transaction.payment_id ) }</td>
								</tr>
							);
						} ) }

					</tbody>
				</table>

				<div id="react-paginate">
					<ReactPaginate
						previousLabel={ 'Previous' }
						nextLabel={ 'Next' }
						breakLabel={ '...' }
						breakClassName={ 'break-me' }
						pageCount={ pageCount }
						marginPagesDisplayed={ 2 }
						pageRangeDisplayed={ 2 }
						containerClassName={ 'pagination' }
						activeClassName={ 'active' }
						onPageChange={ handlePageChange }
					/>
				</div>
			</div>
		</div>
	);
};

export default Transactions;
