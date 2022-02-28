import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import ApiResponse from '../../components/bKash/api-response';
import dcBkash from '../../utils/bkash';
import { toast } from 'react-toastify';
import '../../styles/react-toastify.scss';
import { API } from '../../../constants';
import DuplicateSS from '../../images/duplicate.png';
import ExceedPinSS from '../../images/exceed-pin.png';

function DocDataContainer( { afterComplete } ) {
	const [ paymentID, setPaymentID ] = useState( '' );
	const [ firstPaymentID, setFirstPaymentID ] = useState( '' );
	const [ transactionID, setTransactionID ] = useState( '' );
	const [ amount, setAmount ] = useState( '' );
	const [ createPaymentData, setCreatePaymentData ] = useState( {} );
	const [ validatePin, setValidatePin ] = useState( false );
	const [ duplicateTransactionData, setDuplicateTransactionData ] = useState( {} );
	const [ exceedPinLimit, setExceedPinLimit ] = useState( false );
	const [ duplicateTransactionExecuteFailed, setDuplicateTransactionExecuteFailed ] = useState( false );

	const [ docSteps, setDocSteps ] = useState( {
		getToken: false,
		createPayment: false,
		executePayment: false,
		queryPayment: false,
		searchPayment: false,
		duplicateTransaction: false,
		exceedPinLimit: false,
		refundPayment: false,
		refundStatusApi: false
	} );

	/**
	 * After complete a step update the data. set it to `true`
	 * @param {*} stepKey
	 */
	const updateStep = ( stepKey ) => {
		setDocSteps( {
			...docSteps,
			[ stepKey ]: true
		} );
	};

	/**
	 * Set payment id to state and call bKash init
	 * @param {*} response
	 */
	const handlePaymentID = ( response ) => {
		setCreatePaymentData( response.data );
		setPaymentID( response.data.paymentID );
		setFirstPaymentID( response.data.paymentID );
		setAmount( response.data.amount );

		dcBkash.initBkash(
			response.data.merchantInvoiceNumber,
			response.data.amount,
			response.data,
			handleBkashAfterValidatePin
		);
	};

	/**
	 * Store Trx ID in state
	 * @param response
	 */
	const storeTrxId = ( response ) => {
		setTransactionID( response.data.trxID );
		updateStep( 'executePayment' );
	};

	/**
	 * after validate the pin
	 * @param {*} success
	 */
	const handleBkashAfterValidatePin = ( success ) => {
		if ( success ) {
			setValidatePin( true );
			updateStep( 'createPayment' );
		}
	};

	/**
	 * Initialize the duplicate Transaction
	 *
	 * @param {*} response
	 */
	const initDuplicateTransaction = ( response ) => {
		if ( response ) {
			updateStep( 'searchPayment' );

			let duplicateTransactionPath = API.v1.createPayment + '?amount=' + amount;

			toast.warn( 'Duplicate Transaction Test', {
				position: 'bottom-center',
				autoClose: false,
				hideProgressBar: true,
				closeOnClick: true,
				pauseOnHover: false,
				draggable: false
			} );

			apiFetch( {
				path: duplicateTransactionPath
			} )
				.then( ( resp ) => {
					setCreatePaymentData( resp.data );
					setDuplicateTransactionData( resp.data );
					setPaymentID( resp.data.paymentID );

					dcBkash.initBkash(
						resp.data.merchantInvoiceNumber,
						resp.data.amount,
						resp.data,
						duplicateTransactionExecute
					);
				} )
				.catch( ( err ) => {} );
		}
	};

	/**
	 * Execute Duplicate Transaction
	 * Here, we know the return will be a error.
	 *
	 * @param {*} success
	 */
	const duplicateTransactionExecute = ( success ) => {
		if ( success ) {
			let executePath;

			setPaymentID( ( paymentID ) => {
				executePath = API.v1.executePayment + paymentID;
			} );

			apiFetch( {
				path: executePath
			} )
				.then( ( resp ) => {} )
				.catch( ( err ) => {
					setDuplicateTransactionExecuteFailed( true );

					updateStep( 'duplicateTransaction' );

					toast.dismiss();

					initVerificationLimitExceed();
				} );
		}
	};

	/**
	 * Initialize verification limit exceed
	 */
	const initVerificationLimitExceed = () => {
		toast.warn( 'Exceed Pin Limit. Please enter wrong pin.', {
			position: 'bottom-center',
			autoClose: false,
			hideProgressBar: true,
			closeOnClick: true,
			pauseOnHover: false,
			draggable: false
		} );

		apiFetch( {
			path: API.v1.createPayment
		} )
			.then( ( resp ) => {
				setCreatePaymentData( resp.data );

				dcBkash.initBkash(
					resp.data.merchantInvoiceNumber,
					resp.data.amount,
					resp.data,
					executeExceedPinLimit,
					true
				);
			} )
			.catch( ( err ) => {} );
	};

	/**
	 * After execute pin limit exceed
	 * @param {*} success
	 */
	const executeExceedPinLimit = ( success ) => {
		if ( success ) {
			toast.dismiss();
			updateStep( 'exceedPinLimit' );
			setExceedPinLimit( true );
		}
	};

	/**
	 * Render the data of exceed pin limit
	 *
	 * @returns
	 */
	const renderExceedPinLimit = () => {
		if ( exceedPinLimit ) {
			return (
				<div>
					<p className="strong">{ __( 'Case #2', 'dc-bkash' ) }</p>
					<p className="strong">
						{ __( 'Invoice Number: ', 'dc-bkash' ) }{ ' ' }
						{ createPaymentData.merchantInvoiceNumber }
					</p>
					<p className="strong">
						{ __( 'Time of Transaction: ', 'dc-bkash' ) }
						{ createPaymentData.createTime }
					</p>

					<p className="strong">{ __( 'Screenshot', 'dc-bkash' ) }</p>

					<img
						className="img-full"
						src={ ExceedPinSS }
						alt="error-screenshot"
					/>
				</div>
			);
		}
	};

	/**
	 * Render duplicate transaction data
	 *
	 * @returns
	 */
	const renderDuplicateTransaction = () => {
		if ( duplicateTransactionExecuteFailed ) {
			return (
				<div>
					<h3>{ __( 'Error Message Implimentation', 'dc-bkash' ) }</h3>
					<p className="strong">{ __( 'Case #1', 'dc-bkash' ) }</p>
					<p className="strong">
						{ __( 'Invoice Number: ', 'dc-bkash' ) }{ ' ' }
						{ duplicateTransactionData.merchantInvoiceNumber }
					</p>
					<p className="strong">
						{ __( 'Time of Transaction: ', 'dc-bkash' ) }
						{ duplicateTransactionData.createTime }
					</p>

					<p className="strong">{ __( 'Screenshot', 'dc-bkash' ) }</p>

					<img
						className="img-full"
						src={ DuplicateSS }
						alt="error-screenshot"
					/>
				</div>
			);
		}
	};

	/**
	 * Execute payment info after validate pin
	 * @returns
	 */
	const renderExecutePayment = () => {
		if ( validatePin ) {
			let executePath = API.v1.executePayment + paymentID;

			return (
				<div>
					<ApiResponse path={ executePath } callback={ storeTrxId } />
				</div>
			);
		}
	};

	/**
	 * Render Query Payment
	 * @returns
	 */
	const renderQueryPayment = () => {
		let verifyPath = API.v1.queryPayment + paymentID;

		return (
			<div>
				<ApiResponse path={ verifyPath } callback={ () => {
					updateStep( 'queryPayment' );
				} } />
			</div>
		);

	};

	/**
	 * Render search payment
	 * @returns {boolean}
	 */
	const renderSearchPayment = () => {
		if ( transactionID ) {
			let searchPath = API.v1.docSearchPayment + transactionID;

			return (
				<ApiResponse
					path={ searchPath }
					callback={ initDuplicateTransaction }
				/>
			);
		}
	};

	const renderRefundPayment = ( title, status = false ) => {

		// after exceedPinLimit.
		if ( exceedPinLimit ) {
			let refundPath = API.v1.docRefundPayment + firstPaymentID + '?trx_id=' + transactionID + '&amount=' + amount + '&title=' + title;

			if ( status ) {
				refundPath = API.v1.docRefundPayment + firstPaymentID + '?trx_id=' + transactionID + '&title=' + title;

				return (
					<ApiResponse path={ refundPath } callback={ () => {
						toast.success( __( 'Doc Generation Done.', 'dc-bkash' ) );
						afterComplete();
					} } />
				);
			}

			return (
				<ApiResponse path={ refundPath } callback={ () => {
					updateStep( 'refundPayment' );
				} } />
			);
		}
	};

	return (
		<div className="generator-container-area" id="doc-details">
			<h2>{ __( 'API Request/Response', 'dc-bkash' ) }</h2>

			<ApiResponse
				path={ API.v1.getToken }
				callback={ () => {
					updateStep( 'getToken' );
				} }
			/>

			{ /* after get token start create payment. */ }

			{ docSteps.getToken && (
				<ApiResponse
					path={ API.v1.createPayment }
					callback={ handlePaymentID }
				/>
			) }

			{ docSteps.createPayment && renderExecutePayment() }

			{ docSteps.executePayment && renderQueryPayment() }

			{ docSteps.queryPayment && renderSearchPayment() }

			{ renderDuplicateTransaction() }

			{ renderExceedPinLimit() }

			{ docSteps.exceedPinLimit && renderRefundPayment( 'Refund API' ) }

			{ docSteps.refundPayment && renderRefundPayment( 'Refund Status API', true ) }
		</div>
	);
}

export default DocDataContainer;
