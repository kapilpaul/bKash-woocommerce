import React, { useState, useEffect, useRef } from 'react';
import AsyncSelect from 'react-select/async';
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

const Refund = () => {
	const [ isFetching, setIsFetching ] = useState( true );
	const [ awaitingSearch, setAwaitingSearch ] = useState( false );
	const [ selectedValue, setSelectedValue ] = useState( { trx_id: '' } ); // eslint-disable-line camelcase

	// handle input change event
	const handleInputChange = ( value ) => {
		setValue( value );
	};

	const handleChange = ( value ) => {
		setSelectedValue( value );
	};

	const loadOptions = ( inputValue ) => {
		let url = '/dc-bkash/v1/transactions/?search=' + inputValue;

		return apiFetch( {
			path: url,
			parse: false
		} ).then( ( res ) => res.json() );
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
					</div>
				</div>

				<div className="form-group">
					<label>{ __( 'Trx ID', 'dc-bkash' ) }</label>
					<input
						type="text"
						className="form-control"
						value={ selectedValue?.trx_id }
						readOnly
					/>
				</div>

				<div className="form-group">
					<label>{ __( 'Amount', 'dc-bkash' ) }</label>
					<input type="number" step={ 0.01 } className="form-control" />
				</div>

				<Button
					type="submit"

					// isBusy={isGenerating}
					// disabled={isGenerating}
					className="dc_bkash_save_btn"
					isPrimary={ true }

					// onClick={() => setIsGenerating(true)}
				>
					{ /* {isGenerating */ }
					{ /* ? __('Generating', 'dc-bkash') */ }
					{ /* : __('Generate', 'dc-bkash')} */ }
					Submit
				</Button>
			</div>
		</>
	);
};

export default Refund;
