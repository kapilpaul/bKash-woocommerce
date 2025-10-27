import React from 'react';

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
	const renderByType = (field) => {
		let type = field.type;

		switch (type) {
			case 'text':
			case 'password':
				return (
					<input
						type={type}
						className="widefat"
						value={value}
						onChange={(e) =>
							handleChange(e.target.value, section_id, id)
						}
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
							onChange={(e) =>
								handleChange(e.target.value, section_id, id)
							}
						/>
						<label htmlFor={id}>{field.title}</label>
					</>
				);

			case 'select':
				// eslint-disable-next-line no-case-declarations
				let options = Object.entries(field.options);

				return (
					<>
						<select
							className="widefat"
							value={value}
							onChange={(e) =>
								handleChange(e.target.value, section_id, id)
							}
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
							onChange={(e) =>
								handleChange(e.target.value, section_id, id)
							}
							value={value || field?.default}
						></textarea>
					</>
				);

			default:
				return '';
		}
	};

	/**
	 * decide wheather to show or hide this field
	 */
	const showThisField = () => {
		let options, optionValue;

		//get deceisions by checking the conditions
		let decisions = field.show_if.map((item) => {
			options = allSettings?.[section_id]?.[item.key];
			optionValue = options?.value ? options?.value : options?.default;

			return is_matched(item, optionValue);
		});

		//checking wheather all deceisions are true or not
		return decisions.every((item) => {
			return true === item;
		});
	};

	/** if show_if exists and do not match with the condition we are returning it */
	if (field?.show_if && !showThisField()) {
		return false;
	}

	value = '' === value ? field?.default : value;

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

/**
 * if condition matched with the case and option value is same as item value
 *
 * @param {*} item
 * @param {*} optionValue
 */
function is_matched(item, optionValue) {
	switch (item?.condition) {
		case 'equal':
			if (optionValue === item.value) {
				return true;
			}
	}

	return false;
}

export default Fields;
