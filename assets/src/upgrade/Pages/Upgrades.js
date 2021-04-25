import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { toast } from 'react-toastify';
import '../../admin/styles/react-toastify.scss';

/**
 * configure the toast
 */
toast.configure({
  position: 'top-right',
  autoClose: 5000,
  closeOnClick: false,
  pauseOnHover: false,
  draggable: false,
  closeButton: false,
  style: {
    top: '3em',
  },
});

function Upgrades() {
  const [isSubmitted, setIsSubmitted] = useState(false);

  /**
   * Handle update from here.
   */
  const handleUpdate = () => {
    setIsSubmitted(true);

    apiFetch({
      path: '/dc-bkash/v1/upgrade',
      method: 'POST',
      data: {},
    })
      .then((resp) => {
        setIsSubmitted(false);
        toast.success(__('Updated Successfully!', 'dc-bkash'));

        hideNotice();
      })
      .catch((err) => {
        setIsSubmitted(false);
        toast.error(err.data.status + ' : ' + err.message);
      });
  };

  /**
   * Hide notice container
   */
  const hideNotice = () => {
    let noticeContainer = document.querySelector( '.dc-bkash-notice-info' );

    noticeContainer.classList.add('dc-bkash-notice-info-hide');
  }

  return (
    <div id="dc-bkash-upgrade-notice">
      <div id="dc-bkash-upgrade-notice-icon">
        <div id="dc-bkash-upgrade-notice-message">
          <div id="dc-bkash-upgrade-notice-title">
            <p>
              <strong>
                {__('bKash Data Update Required', 'dc-bkash')}
              </strong>
            </p>
          </div>
          <div id="dc-bkash-upgrade-notice-content">
            <p>
              {__(
                'We need to update your install to the latest version',
                'dc-bkash'
              )}
            </p>
          </div>

          <Button
            type="submit"
            className="wc-update-now bg-bkash text-white"
            onClick={() => handleUpdate()}
            isBusy={isSubmitted}
            disabled={isSubmitted}
          >
            {isSubmitted
              ? __('Updating', 'dc-bkash')
              : __('Update', 'dc-bkash')}
          </Button>
        </div>
      </div>
    </div>
  );
}

export default Upgrades;
