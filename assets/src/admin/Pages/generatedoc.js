import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { Spinner, Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

function GenerateDoc() {
  const [isGenerating, setIsGenerating] = useState(false);

  const GenerateDocContainer = () => {
    if (!isGenerating) {
      return false;
    }

    return (
      <div className='generator-container-area'>
        <p>{ 
          `API Title : Grant Token
          API URL : https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/token/grant
          Request Body :
           headers:
           {
           username: testdemo,
           password: test%#de23@msdao
           },
           body params:{
           app_key: 5nej5keguopj928ekcj3dne8p,
           app_secret: 1honf6u1c56mqcivtc9ffl960slp4v2756jle5925nbooa46ch62
           }`
        }</p>
      </div>
    );
  }

  return (
    <div className="dokan_admin_settings_container">
      <h2>{__('Generate Doc', dc_bkash_admin.text_domain)}</h2>

      <div className="generate_help_text_container">
        <h4>
          {__(
            'You may generate API Request/Response doc from here.',
            dc_bkash_admin.text_domain
          )}
        </h4>

        <p>
          {__(
            'In case, if you need sandbox mobile number and OTP then you may use the follow number.',
            dc_bkash_admin.text_domain
          )}
        </p>

        <div className="sandbox_number_details">
          <p>
            <span>bKash Number</span> : 00000000000
          </p>
          <p>
            <span>OTP</span> : 123456
          </p>
          <p>
            <span>PIN</span> : 12121
          </p>
        </div>

        <Button
          type="submit"
          isBusy={isGenerating}
          disabled={isGenerating}
          className="dc_bkash_save_btn"
          isPrimary={true}
          onClick={() => setIsGenerating(true)}
        >
          {isGenerating
            ? __('Generating', dc_bkash_admin.text_domain)
            : __('Generate', dc_bkash_admin.text_domain)}
        </Button>
      </div>{/* .generate_help_text_container */}


      { GenerateDocContainer() }

    </div>
  );
}

export default GenerateDoc;
