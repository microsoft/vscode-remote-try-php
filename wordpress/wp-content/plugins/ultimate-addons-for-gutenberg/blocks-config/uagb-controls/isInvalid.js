const isInvalid = ( block ) => {
	const { name, isValid, validationIssues } = block;

	if ( ! name || ! name.match( /^uagb\// ) ) {
		return false;
	}

	if ( isValid || ! validationIssues.length ) {
		return false;
	}

	return true;
};

export default isInvalid;
