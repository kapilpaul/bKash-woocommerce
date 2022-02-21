/**
 * Beatutify JSON data
 * @param {*} data
 * @returns
 */
function beautifyJson( data ) {
	return JSON.stringify( data, undefined, 4 );
}

export { beautifyJson };
