import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { Spinner, Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

function Transactions() {
  return (
    <div className="dokan_admin_settings_container">
      <h2>{__('Transactions', 'dc-bkash')}</h2>

      <div className="all-transactions">
        <table className="transactions">
          <thead>
            <tr>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Job Title</th>
              <th>Twitter</th>
            </tr>
          </thead>

          <tbody>
            <tr>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
            </tr>


            <tr>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
            </tr>


            <tr>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
            </tr>


            <tr>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
            </tr>


            <tr>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
            </tr>


            <tr>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
            </tr>


            <tr>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
            </tr>


            <tr>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
              <td>hello</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default Transactions;
