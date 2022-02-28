/**
 * API calls URL.
 *
 * @since 2.1.0
 *
 * @returns
 */

const v1 = '/dc-bkash/v1';

const API = {
	v1: {
		settings: v1 + '/settings',
		transactions: v1 + '/transactions',
		transactionSearch: v1 + '/transactions/?search=',
		getToken: v1 + '/payment/get-token',
		createPayment: v1 + '/payment/create-payment',
		queryPayment: v1 + '/payment/query-payment/',
		executePayment: v1 + '/payment/execute-payment/',
		docSearchPayment: v1 + '/payment/search-payment/',
		docRefundPayment: v1 + '/payment/refund-payment/',
		refund: v1 + '/transactions/refund',
		searchTransaction: v1 + '/payment/search-transaction/',
		upgrade: v1 + '/upgrade'
	}
};

export { API };
