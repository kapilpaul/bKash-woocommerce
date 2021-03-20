import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import ApiResponse from '../../components/bKash/api-response';

function DocDataContainer() {
  const [paymentID, setPaymentID] = useState('');

  const handlePaymentID = (response) => {
    setPaymentID(response.data.paymentID);
  };

  const executePayment = () => {
    if (paymentID) {
      let path = '/dc-bkash/v1/payment/execute-payment/' + paymentID;
      return <ApiResponse path={path} />;
    }
  };

  return (
    <div className="generator-container-area">
      <ApiResponse path="/dc-bkash/v1/payment/get-token" />
      <ApiResponse
        path="/dc-bkash/v1/payment/create-payment"
        callback={handlePaymentID}
      />

      {executePayment()}
    </div>
  );
}

export default DocDataContainer;
