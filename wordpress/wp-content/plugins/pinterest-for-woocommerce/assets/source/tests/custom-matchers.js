expect.extend( {
	/**
	 * Custom matcher to check the presence and optionally the value of a search query param in the given location.
	 *
	 * @param {Location} receivedLocation Location to be checked, for example `window.location`.
	 * @param {string} param Parameter to check the presence of.
	 * @param {string} [value] Value of the parameter to be checked.
	 * @return {{message: string, pass: boolean}} Jest compatible matcher output.
	 */
	toContainURLSearchParam( receivedLocation, param, value ) {
		const hasValue = arguments.length === 3;
		// Sanitize the query params.
		const params = new URLSearchParams( receivedLocation.search );

		let message = `Expected location ${ this.utils.printReceived(
			receivedLocation.toString()
		) }`;
		let pass = true;
		// Colorize the param name.
		const expectedParam = this.utils.printExpected( param );
		const receivedParam = this.utils.printReceived( param );
		// Check the presence only.
		if ( ! hasValue ) {
			if ( params.has( param ) ) {
				message += ` not to have param '${ receivedParam }'`;
			} else {
				message += ` to have param '${ expectedParam }'`;
				pass = false;
			}

			return { message: () => message, pass };
		}

		// Check the value.
		const actualValue = params.get( param );
		// Colorize the values.
		const expectedValue = this.utils.printExpected( value );
		const receivedValue = this.utils.printReceived( actualValue );
		// We check the strict equality directly,
		// we may consider using module:expect/build/matchers.equals to support `extand.any()` & co.
		if ( actualValue === value ) {
			message += `not to have param '${ receivedParam }' of '${ receivedValue }'`;
		} else {
			message += `to have param '${ expectedParam }' of '${ expectedValue }', but got '${ receivedValue }'`;
			pass = false;
		}
		return { message: () => message, pass };
	},
} );
