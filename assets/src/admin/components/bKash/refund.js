import React, { useState, useEffect, useRef } from 'react';
import AsyncSelect from 'react-select/async';
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { API } from '../../../constants';
import { toast } from 'react-toastify';
import '../../styles/react-toastify.scss';

const Refund = () => {
	const [ isSubmitted, setIsSubmitted ] = useState( false );
	const [ refundTrxId, setRefundTrxId ] = useState( '' );
	const [ refundAmount, setRefundAmount ] = useState( 0 );
	const [ refundReason, setRefundReason ] = useState( '' );
	const [ wcCreateRefund, setWcCreateRefund ] = useState( true );
	const [ invalidAmount, setInvalidAmount ] = useState( false );
	const [ selectedValue, setSelectedValue ] = useState( { trx_id: '', amount: 0 } ); // eslint-disable-line camelcase

	/**
	 * Handle async select click.
	 * @param {*} value
	 */
	const handleChange = ( value ) => {
		setSelectedValue( value );
		setRefundTrxId( value?.trx_id );
		setRefundAmount( value?.amount );
	};

	/**
	 * Load all transaction options.
	 *
	 * @param {*} inputValue
	 * @returns
	 */
	const loadOptions = ( inputValue ) => {
		let url = API.v1.transactionSearch + inputValue;

		return apiFetch( {
			path: url,
			parse: false
		} ).then( ( res ) => res.json() );
	};

	/**
	 * Handle change amount
	 * @param {*} amount
	 *
	 * @return void
	 */
	const handleChangeAmount = ( amount ) => {
		let selectedRefundAmount = parseFloat( selectedValue?.amount );

		if ( 1 > amount || amount > selectedRefundAmount ) {
			setInvalidAmount( true );
		} else {
			setInvalidAmount( false );
		}

		setRefundAmount( amount );
	};

	/**
	 * Handle Submit of Refund
	 */
	const handleSubmit = () => {
		setIsSubmitted( true );

		apiFetch( {
			path: API.v1.refund,
			method: 'POST',
			data: {
				order_number: selectedValue.order_number,
				amount: refundAmount,
				wc_create_refund: wcCreateRefund,
				refund_reason: refundReason
			}
		} )
			.then( ( resp ) => {
				setIsSubmitted( false );

				setSelectedValue( { trx_id: '', amount: 0 } );
				setRefundTrxId( '' );
				setRefundReason( '' );
				setRefundAmount( 0 );
				setWcCreateRefund( true );

				toast.success( __( 'Refund Successfully!', 'dc-bkash' ) );
			} )
			.catch( ( err ) => {
				setIsSubmitted( false );
				toast.error( err.data.status + ' : ' + err.message );
			} );
	};

	return (
		<>
			<div className="refund-container__form">
				<div className="search-order">
					<div className="form-group">
						<label>{ __( 'Search Order ID', 'dc-bkash' ) }</label>

						<AsyncSelect
							cacheOptions
							defaultOptions
							value={ selectedValue }
							getOptionLabel={ ( e ) => e.order_number }
							getOptionValue={ ( e ) => e.id }
							loadOptions={ loadOptions }
							onChange={ handleChange }
						/>

						<span className="help">{ __( 'You may type your order number or transaction ID here.', 'dc-bkash' ) }</span>

						{ '1' === selectedValue.refund_status ? (
							<>
								<span className='help warning'>
									{ __( `This order is refunded once. Refund amount was ${ selectedValue.refund_amount }`, 'dc-bkash' ) }
								</span>
								<span className='help warning'>
									{ __( 'A merchant can do refund only once for a transaction, it can be a full refund or partial amount refund.', 'dc-bkash' ) }
								</span>
							</>
						) : '' }

					</div>
				</div>

				<div className="form-group">
					<label>{ __( 'Trx ID', 'dc-bkash' ) }</label>
					<input
						type="text"
						className="form-control"
						defaultValue={ refundTrxId }
						readOnly
					/>
				</div>

				<div className="form-group">
					<label>{ __( 'Amount', 'dc-bkash' ) }</label>
					<input
						type="number"
						value={ refundAmount }
						step={ 0.01 }
						className={ `form-control ${ invalidAmount ? 'danger' : '' }` }
						onChange={ ( e ) => handleChangeAmount( parseFloat( e.target.value ) ) }
						readOnly={ '1' === selectedValue.refund_status ?? false }
					/>
					<span className="help">{ __( `You can only put value only between 1 to ${ selectedValue?.amount }`, 'dc-bkash' ) }</span>
				</div>

				<div className="form-group">
					<label>{ __( 'Reason', 'dc-bkash' ) }</label>
					<input
						type="text"
						value={ refundReason }
						className="form-control"
						onChange={ ( e ) => setRefundReason( e.target.value ) }
						readOnly={ '1' === selectedValue.refund_status ?? false }
					/>
				</div>

				<div className="form-group">
					<label>
						<input
							name="isGoing"
							type="checkbox"
							checked={ wcCreateRefund }
							onChange={ ( e ) => setWcCreateRefund( ! wcCreateRefund ) }
						/>

						{ __( 'Create refund on WooCommerce?', 'dc-bkash' ) }
					</label>
				</div>

				<Button
					type="submit"
					isBusy={ isSubmitted }
					disabled={ ! refundAmount || invalidAmount || isSubmitted || '1' === selectedValue.refund_status }
					className="dc_bkash_save_btn"
					isPrimary={ true }
					onClick={ () => handleSubmit() }
				>
					{ isSubmitted ?
						__( 'Submitting', 'dc-bkash' ) :
						__( 'Submit', 'dc-bkash' ) }
				</Button>
			</div>
		</>
	);
};

export default Refund;
