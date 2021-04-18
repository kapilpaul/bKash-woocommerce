import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import '../styles/react-toastify.css';

const Loader = () => {
    return (
        <div className="loader">
          <img src="https://res.cloudinary.com/d-coders/image/upload/v1592201998/wp-plugins/bkash.gif" alt="bkash-loader" />
          <p>{ __( 'Loading...', 'dc-bkash' ) }</p>
        </div>
      );
};

export default Loader;