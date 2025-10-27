import React from 'react';

function Header() {
	return (
		<div className="bkash_header_container">
			<div className="header_logo">
				<img src={getLogo()} alt="" />
			</div>
		</div>
	);
}

/**
 * get bkash logo for header
 */
function getLogo() {
	return window.dc_bkash_admin.asset_url + '/images/bkash_logo.png';
}

export default Header;
