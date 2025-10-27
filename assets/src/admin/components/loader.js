import { __ } from '@wordpress/i18n';
import React from 'react';
import '../styles/react-toastify.scss';

const Loader = () => {
	return (
		<div className="loader">
			<img
				src="https://res.cloudinary.com/d-coders/image/upload/v1592201998/wp-plugins/bkash.gif"
				alt="bkash-loader"
			/>
			<p>{__('Loading...', 'dc-bkash')}</p>
		</div>
	);
};

export default Loader;
