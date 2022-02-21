import React, { useState, useEffect, useRef } from 'react';
import { __ } from '@wordpress/i18n';
import Refund from '../components/bKash/refund';
import '../styles/react-toastify.scss';
import { HashRouter, Link } from 'react-router-dom';

const RefundContainer = () => {

	/**
	 * If API keys not set then show this.
	 *
	 * @returns string
	 */
	const refundIsInactive = () => {
		return (
			<p>
				{ __(
					'Before refund, you must have to add API keys in ',
					'dc-bkash'
				) }
				<Link to="/settings">Settings</Link>.
			</p>
		);
	};

	return (
		<div className="dokan_admin_settings_container">
			<div className="title-section">
				<h2>{ __( 'Refund', 'dc-bkash' ) }</h2>
			</div>

			<div className="generic-container refund-container">
				{ '1' === dc_bkash_admin.all_credentials_filled ? (
					<Refund />
				) : (
					refundIsInactive()
				) }
			</div>
		</div>
	);
};

export default RefundContainer;
