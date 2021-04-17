import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { Spinner, Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

function Transactions() {
  return (
    <div className="dokan_admin_settings_container">
      <h2>{__('Transactions', 'dc-bkash')}</h2>

      <div className="all-transactions">
        <table className="table table-bordered border-primary transactions">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">First</th>
              <th scope="col">Last</th>
              <th scope="col">Handle</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row">1</th>
              <td>Mark</td>
              <td>Otto</td>
              <td>@mdo</td>
            </tr>
            <tr>
              <th scope="row">2</th>
              <td>Jacob</td>
              <td>Thornton</td>
              <td>@fat</td>
            </tr>
            <tr>
              <th scope="row">3</th>
              <td colSpan="2">Larry the Bird</td>
              <td>@twitter</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default Transactions;
