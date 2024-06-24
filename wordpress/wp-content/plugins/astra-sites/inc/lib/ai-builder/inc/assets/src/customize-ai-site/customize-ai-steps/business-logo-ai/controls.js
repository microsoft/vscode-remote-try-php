import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import Button from '../../../../../components/button/button';
import MediaUploader from '../../../../../components/media-uploader';
import { sendPostMessage } from '../../../utils/functions';
import { classNames } from '../../../helpers';
import { STORE_KEY } from '../../../store/index';

const BusinessLogoControls = () => {
	const [ { siteLogo, currentCustomizeIndex }, dispatch ] = [ {}, () => {} ]; // Remove this line.
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
					? __( 'Continue', 'ai-builder' )
					: __( 'Skip & Continue', 'ai-builder' ) }
			</Button>
		</>
	);
};

export default BusinessLogoControls;
