import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import ApiResponse from '../../components/bKash/api-response';
import dcBkash from '../../utils/bkash';

function DocDataContainer() {
  const [paymentID, setPaymentID] = useState('');
  const [createPaymentData, setCreatePaymentData] = useState({});
  const [validatePin, setValidatePin] = useState(false);

  const handlePaymentID = (response) => {
    setCreatePaymentData(response.data);
    setPaymentID(response.data.paymentID);

    dcBkash.initBkash(
      response.data.merchantInvoiceNumber,
      response.data.amount,
      response.data,
      handleBkashAfterValidatePin
    );
  };

  const handleBkashAfterValidatePin = (success) => {
    if (success) {
      setValidatePin(true);
    }
  };

  const executePayment = () => {
    if (validatePin) {
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
