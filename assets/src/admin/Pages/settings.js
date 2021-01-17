import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { Spinner } from '@wordpress/components';

function Settings() {
  const [settings, setSettings] = useState(0);
  const [sections, setSections] = useState([]);
  const [isFetching, setIsFetching] = useState(true);

  useEffect(() => {
    setIsFetching(true);

    let data = {
      action: 'dc_bkash_get_setting',
      nonce: dc_bkash_admin.nonce,
    };

    jQuery
      .post(dc_bkash_admin.ajaxurl, data)
      .done((resp) => {
        if (resp.success) {
          setIsFetching(false);
          setSettings(resp.data);
          setSections(resp.data.sections);
        }
      })
      .error((err) => {
        setIsFetching(false);
      });
  }, []);

  if (isFetching) {
    return (
      <div>
        <Spinner /> {__('Loading posts...')}
      </div>
    );
  }

  return (
    <div>
      <h2>{__('Settings', window.dc_bkash_admin.text_domain)}</h2>

      <div className="dokan_admin_settings_area">
        <div className="admin_settings_sections">
          <ul className="dokan_admin_settings">
            {Object.keys(sections).map((key) => {
              return <li key={key}>{sections[key].title}</li>;
            })}
          </ul>
        </div>

        <div className="admin_settings_fields">
          <p>
            Lorem Ipsum is simply dummy text of the printing and typesetting
            industry. Lorem Ipsum has been the industry's standard dummy text
            ever since the 1500s, when an unknown printer took a galley of type
            and scrambled it to make a type specimen book. It has survived not
            only five centuries, but also the leap into electronic typesetting,
            remaining essentially unchanged. It was popularised in the 1960s
            with the release of Letraset sheets containing Lorem Ipsum passages,
            and more recently with desktop publishing software like Aldus
            PageMaker including versions of Lorem Ipsum.
          </p>
        </div>
      </div>
    </div>
  );
}

export default Settings;
