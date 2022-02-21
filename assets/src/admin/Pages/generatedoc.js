import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { Spinner, Button } from '@wordpress/components';
import DocDataContainer from './Doc/doc-container';
import dcBkash from '../utils/bkash';

function GenerateDoc() {
	const [ isGenerating, setIsGenerating ] = useState( false );

	const GenerateDocContainer = () => {
		if ( ! isGenerating ) {
			return false;
		}

		return <DocDataContainer />;
	};

	useEffect( () => {

		//initialize bkash scripts
		if ( 'undefined' === typeof bKash ) {
			dcBkash.init();
		}
	}, [] );

	return (
		<div className="dokan_admin_settings_container">
			<h2>{ __( 'Generate Doc', 'dc-bkash' ) }</h2>

			<div className="generate_help_text_container">
				<h4>
					{ __(
						'You may generate API Request/Response doc from here.',
						'dc-bkash'
					) }
				</h4>

				{ '1' === dc_bkash_admin.all_credentials_filled ? (
					<div className="generate-content-actions">
						<p>
							{ __(
								'In case, if you need sandbox mobile number and OTP then you may use the below number.',
								'dc-bkash'
							) }
						</p>

						<div className="sandbox_number_details">
							<p>
								<span>bKash Number</span> : 01770618575
							</p>
							<p>
								<span>OTP</span> : 123456
							</p>
							<p>
								<span>PIN</span> : 12121
							</p>
						</div>

						<Button
							type="submit"
							isBusy={ isGenerating }
							disabled={ isGenerating }
							className="dc_bkash_save_btn"
							isPrimary={ true }
							onClick={ () => setIsGenerating( true ) }
						>
							{ isGenerating ?
								__( 'Generating', 'dc-bkash' ) :
								__( 'Generate', 'dc-bkash' ) }
						</Button>

						<Button
							type="submit"
							className="dc_bkash_save_btn"
							isPrimary={ true }
							disabled={ ! isGenerating }
							onClick={ () => {
								setIsGenerating( false );
								window.print();
							} }
						>
							{ __( 'Download', 'dc-bkash' ) }
						</Button>
					</div>
				) : (
					<div className="generate-content-actions">
						<p>
							{ __(
								'Before generate the doc, you must have to add sandbox keys in settings.',
								'dc-bkash'
							) }
						</p>
					</div>
				) }
			</div>

			{ GenerateDocContainer() }
		</div>
	);
}

export default GenerateDoc;
