import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import ApiResponse from '../../components/bKash/api-response';
import dcBkash from '../../utils/bkash';
import { toast } from 'react-toastify';
import '../../styles/react-toastify.css';

function DocDataContainer() {
  const [paymentID, setPaymentID, getPaymentID] = useState('');
  const [amount, setAmount] = useState('');
  const [createPaymentData, setCreatePaymentData] = useState({});
  const [validatePin, setValidatePin] = useState(false);
  const [duplicateTransactionData, setDuplicateTransactionData] = useState({});

  /**
   * Set payment id to state and call bKash init
   * @param {*} response
   */
  const handlePaymentID = (response) => {
    setCreatePaymentData(response.data);
    setPaymentID(response.data.paymentID);
    setAmount(response.data.amount);

    dcBkash.initBkash(
      response.data.merchantInvoiceNumber,
      response.data.amount,
      response.data,
      handleBkashAfterValidatePin
    );
  };

  /**
   * after validate the pin
   * @param {*} success
   */
  const handleBkashAfterValidatePin = (success) => {
    if (success) {
      setValidatePin(true);
    }
  };

  /**
   * Execute payment info after validate pin
   * @returns
   */
  const executePayment = () => {
    if (validatePin) {
      let executePath = '/dc-bkash/v1/payment/execute-payment/' + paymentID;
      let verifyPath = '/dc-bkash/v1/payment/query-payment/' + paymentID;
      let searchPath = '/dc-bkash/v1/payment/search-payment/' + paymentID;

      return (
        <div>
          <ApiResponse path={executePath} />
          <ApiResponse path={verifyPath} />
          <ApiResponse path={searchPath} callback={duplicateTransaction} />
        </div>
      );
    }
  };

  const duplicateTransaction = (response) => {
    if (response) {
      let duplicateTransactionPath =
        '/dc-bkash/v1/payment/create-payment?amount=' + amount;

      apiFetch({
        path: duplicateTransactionPath,
      })
        .then((resp) => {
          setCreatePaymentData(resp.data);
          setPaymentID(resp.data.paymentID);
          setDuplicateTransactionData(resp.data);

          dcBkash.initBkash(
            resp.data.merchantInvoiceNumber,
            resp.data.amount,
            resp.data,
            duplicateTransactionExecute
          );
        })
        .catch((err) => {});
    }
  };

  const duplicateTransactionExecute = (success) => {
    if (success) {
      let executePath;

      setPaymentID((paymentID) => {
        executePath = '/dc-bkash/v1/payment/execute-payment/' + paymentID;
      });

      apiFetch({
        path: executePath,
      })
        .then((resp) => {})
        .catch((err) => {
          toast.warn(err.message.errorMessage);

          console.log(duplicateTransactionData, createPaymentData);
        });
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
