import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { Spinner, Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import Fields from '../components/fields';
import { toast } from 'react-toastify';
import '../styles/react-toastify.css';

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

function Settings() {
  const [settings, setSettings] = useState({
    sections: {},
    fields: {
      gateway: {},
      dokan_integration: {},
    },
  });
  const [sections, setSections] = useState([]);
  const [isFetching, setIsFetching] = useState(true);
  const [currentTab, setCurrentTab] = useState('');
  const [isSubmitted, setIsSubmitted] = useState(false);

  /**
   * Handle the change of an input value
   * @param {*} inputVal
   * @param {*} parent_id
   * @param {*} id
   */
  const handleChange = (inputVal, parent_id, id) => {
    setSettings({
      ...settings,
      fields: {
        ...settings.fields,
        [parent_id]: {
          ...settings.fields?.[parent_id],
          [id]: {
            ...settings.fields?.[parent_id]?.[id],
            value: inputVal,
          },
        },
      },
    });
  };

  /**
   * Save the value
   */
  const handleSubmit = () => {
    setIsSubmitted(true);

    apiFetch({
      path: '/dc-bkash/v1/settings',
      method: 'POST',
      data: settings,
    })
      .then((resp) => {
        setIsSubmitted(false);
        setSettings(resp);
        setSections(resp.sections);

        toast.success(__('Saved Successfully!', dc_bkash_admin.text_domain));
      })
      .catch((err) => {
        setIsSubmitted(false);
        toast.error(err.data.status + ' : ' + err.message);
      });
  };

  useEffect(() => {
    setIsFetching(true);

    apiFetch({
      path: '/dc-bkash/v1/settings',
    })
      .then((resp) => {
        setIsFetching(false);
        setSettings(resp);
        setSections(resp.sections);
      })
      .catch((err) => {
        setIsFetching(false);
        toast.error(err.data.status + ' : ' + err.message);
      });
  }, []);

  if (isFetching) {
    return (
      <div>
        <Spinner /> {__('Loading...', dc_bkash_admin.text_domain)}
      </div>
    );
  }

  return (
    <div>
      <h2>{__('Settings', dc_bkash_admin.text_domain)}</h2>

      <div className="dokan_admin_settings_area">
        <div className="admin_settings_sections">
          <ul className="dokan_admin_settings">
            {Object.keys(sections).map((key) => {
              return (
                <li
                  className="active"
                  key={key}
                  onClick={() => setCurrentTab(key)}
                  className={currentTab === key ? 'active' : ''}
                >
                  {sections[key].title}
                </li>
              );
            })}
          </ul>
        </div>

        <div className="admin_settings_fields">
          {Object.keys(settings.fields).map((key, i) => {
            if (key !== currentTab) {
              return;
            }

            return (
              <div key={i} className="single_settings_container">
                <p className="section_title">{sections[key].title}</p>
                {Object.keys(settings?.fields[key]).map((subkey, index) => {
                  return (
                    <div key={index} className="single_settings_field">
                      <Fields
                        field={settings?.fields[key][subkey]}
                        section_id={key}
                        id={subkey}
                        handleChange={handleChange}
                        value={settings?.fields[key][subkey]?.value}
                        allSettings={settings?.fields}
                      />
                    </div>
                  );
                })}

                <Button
                  type="submit"
                  isBusy={isSubmitted}
                  disabled={isSubmitted}
                  className="dc_bkash_save_btn"
                  isPrimary={true}
                  onClick={() => handleSubmit()}
                >
                  {isSubmitted
                    ? __('Saving', dc_bkash_admin.text_domain)
                    : __('Save', dc_bkash_admin.text_domain)}
                </Button>
              </div>
            );
          })}
        </div>
      </div>
    </div>
  );
}

export default Settings;
