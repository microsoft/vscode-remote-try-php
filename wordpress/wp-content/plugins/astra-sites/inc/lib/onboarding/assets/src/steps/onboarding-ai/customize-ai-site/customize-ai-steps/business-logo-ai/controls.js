import React, { useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import Button from '../../../../../components/button/button';
import MediaUploader from '../../../../../components/media-uploader';
import { useStateValue } from '../../../../../store/store';
import { sendPostMessage } from '../../../../../utils/functions';
import { classNames } from '../../../helpers';
import { STORE_KEY } from '../../../store';

const BusinessLogoControls = () => {
	const [ { siteLogo, currentCustomizeIndex }, dispatch ] = useStateValue();
	const nextStep = () => {
		dispatch( {
			type: 'set',
			currentCustomizeIndex: currentCustomizeIndex + 1,
		} );
	};

	const { businessName } = useSelect( ( select ) => {
		const { getAIStepData } = select( STORE_KEY );
		return getAIStepData();
	} );

	useEffect( () => {
		sendPostMessage( {
			param: 'siteTitle',
			data: businessName,
		} );
	}, [] );

	return (
		<>
			<MediaUploader />
			<Button
				className={ classNames( `ist-button ist-next-step` ) }
				onClick={ nextStep }
				after
			>
				{ '' !== siteLogo.url
					? __( 'Continue', 'astra-sites' )
					: __( 'Skip & Continue', 'astra-sites' ) }
			</Button>
		</>
	);
};

export default BusinessLogoControls;
