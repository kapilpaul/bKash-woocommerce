import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { Spinner, Button } from '@wordpress/components';
import DocDataContainer from './Doc/doc-container';

function GenerateDoc() {
  const [isGenerating, setIsGenerating] = useState(false);

  const GenerateDocContainer = () => {
    if (!isGenerating) {
      return false;
    }

    return <DocDataContainer />;
  };

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
      </div>

      {GenerateDocContainer()}
    </div>
  );
}

export default GenerateDoc;
