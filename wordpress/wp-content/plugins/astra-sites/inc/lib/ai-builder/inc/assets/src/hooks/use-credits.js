import { useEffect } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { STORE_KEY } from '../store';
import { getColorClass, formatNumber } from '../utils/helpers';

const useCredits = () => {
	const { setCreditsDetails } = useDispatch( STORE_KEY );
	const {
		percentage,
		total,
		used,
		status,
		free_user: isFreeUser,
	} = useSelect( ( select ) => {
		const { getCreditsDetails } = select( STORE_KEY );
		return getCreditsDetails();
	}, [] );

	const getUserCredits = async () => {
		try {
			const response = await apiFetch( {
				path: 'zipwp/v1/get-credits',
				method: 'GET',
				headers: {
					'content-type': 'application/json',
					'X-WP-Nonce': aiBuilderVars.rest_api_nonce,
				},
			} );
			if ( response.success ) {
				setCreditsDetails( response?.data?.data );
			} else {
				//  Handle error.
			}
		} catch ( error ) {
			// Handle error.
		}
	};

	useEffect( () => {
		if ( status ) {
			return;
		}
		getUserCredits();
	}, [ status ] );

	const creditsColorClassName = getColorClass( percentage );
	const remaining = total - used;
	const remainingPercentage = ( remaining / total ) * 100;

	/**
	 * Determines the balance status based on the percentage value.
	 *
	 * @param {number} percentageValue - The percentage value to determine the balance status.
	 * @return {{normal: boolean, warning: boolean, danger: boolean}} - An object containing the balance status.
	 */
	const getBalanceStatus = ( percentageValue ) => {
		const result = {
			normal: false,
			warning: false,
			danger: false,
		};
		if ( percentageValue <= 10 ) {
			result.danger = true;
		} else if ( percentageValue <= 20 ) {
			result.warning = true;
		} else {
			result.normal = true;
		}

		return result;
	};

	return {
		percentage,
		remainingPercentage,
		total,
		used,
		remaining,
		status,
		creditsColorClassName,
		formatNumber,
		setCreditsDetails,
		currentBalanceStatus: getBalanceStatus( remainingPercentage ),
		getBalanceStatus,
		isFreeUser,
	};
};

export default useCredits;
