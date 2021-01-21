import React, { useState, useEffect } from 'react';

/**
 * render fields based on type
 *
 * @param {*} field
 */
function Fields({
  field,
  id,
  handleChange,
  value = '',
  section_id,
  allSettings,
}) {
  /**
   * render by type
   *
   * @param {*} type
   */
  function renderByType(field) {
    let type = field.type;

    switch (type) {
      case 'text':
      case 'password':
        return (
          <input
            type={type}
            className="widefat"
            value={value}
            onChange={(e) => handleChange(e.target.value, section_id, id)}
          />
        );
      case 'checkbox':
        return (
          <>
            <input
              type="checkbox"
              className="widefat"
              id={id}
              value={value}
              onChange={(e) => handleChange(e.target.value, section_id, id)}
            />
            <label htmlFor={id}>{field.title}</label>
          </>
        );

      case 'select':
        let options = Object.entries(field.options);

        return (
          <>
            <select
              className="widefat"
              value={value}
              onChange={(e) => handleChange(e.target.value, section_id, id)}
            >
              {options.map((item, index) => {
                return (
                  <option key={index} value={item[0]}>
                    {item[1]}
                  </option>
                );
              })}
            </select>
          </>
        );

      case 'textarea':
        return (
          <>
            <textarea
              id={id}
              cols="30"
              rows="10"
              className="widefat"
            ></textarea>
          </>
        );

      default:
        return '';
    }
  }

  /**
   * decide wheather to show or hide this field
   */
  function showThisField() {
    let options = allSettings?.[section_id]?.[field.show_if.key];
    let optionValue;

    optionValue = options?.value ? options?.value : options?.default;

    switch (field?.show_if?.condition) {
      case 'equal':
        if (optionValue === field.show_if.value) {
          return true;
        }
    }

    return false;
  }

  /** if show_if exists and do not match with the condition we are returning it */
  if (field?.show_if && !showThisField()) {
    return false;
  }

  value = value === '' ? field?.default : value;

  return (
    <>
      <p className="label">{field?.title}</p>

      {renderByType(field)}

      {field.description ? (
        <p className="help-text">{field.description}</p>
      ) : (
        ''
      )}
    </>
  );
}

export default Fields;
