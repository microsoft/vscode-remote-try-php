import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { DefaultStep } from '../../../components';
import { sendPostMessage } from '../utils/helpers';
import SiteSkeleton from '../../../components/site-preview/site-skeleton';
import { CustomizeAiSteps } from './customize-ai-steps';

const CustomizeAiSite = () => {
	const [ { currentCustomizeIndex, siteLogo }, dispatch ] = [ {}, () => {} ]; // Remove this line.

	const currentStepObject = CustomizeAiSteps[ currentCustomizeIndex ];
	let CurrentStepContent;
	let CurrentStepControls;

	if ( typeof currentStepObject !== 'undefined' ) {
		CurrentStepContent = currentStepObject.content;
		CurrentStepControls = currentStepObject.controls;
	}

	useEffect( () => {
		const previousIndex = parseInt( currentCustomizeIndex ) - 1;
		const nextIndex = parseInt( currentCustomizeIndex ) + 1;

		if ( nextIndex > 0 && nextIndex < CustomizeAiSteps.length ) {
			document.body.classList.remove(
				CustomizeAiSteps[ nextIndex ].class
			);
		}

		if ( previousIndex >= 0 ) {
			document.body.classList.remove(
				CustomizeAiSteps[ previousIndex ].class
			);
		}

		document.body.classList.add(
			CustomizeAiSteps[ currentCustomizeIndex ].class
		);
	} );

	const setNextStep = () => {
		if ( CustomizeAiSteps.length - 1 === currentCustomizeIndex ) {
			return null;
		}

		dispatch( {
			type: 'set',
			currentCustomizeIndex: currentCustomizeIndex + 1,
		} );
	};

	const setPreviousStep = () => {
		if ( 0 === currentCustomizeIndex ) {
			return null;
		}
		dispatch( {
			type: 'set',
			currentCustomizeIndex: currentCustomizeIndex - 1,
		} );
	};

	const [ previewUrl, setPreviewUrl ] = useState( '' );
	const [ loading, setLoading ] = useState( true );

	useEffect( () => {
		setPreviewUrl( aiBuilderVars.siteURL );
	}, [] );

	useEffect( () => {
		if ( loading !== false ) {
			return;
		}

		sendPostMessage( {
			param: 'cleanStorage',
			data: siteLogo,
		} );
	}, [ loading ] );

	const handleIframeLoading = () => {
		setLoading( false );
	};

	return (
		<DefaultStep
			stepName={ CustomizeAiSteps[ currentCustomizeIndex ].class }
			content={
				<CurrentStepContent
					customizeStep={ true }
					onNextClick={ setNextStep }
					onPreviousClick={ setPreviousStep }
				/>
			}
			controls={
				CurrentStepControls && (
					<CurrentStepControls
						customizeStep={ true }
						onNextClick={ setNextStep }
						onPreviousClick={ setPreviousStep }
					/>
				)
			}
			actions={ null }
			preview={
				<>
					{ loading ? <SiteSkeleton /> : null }
					{ previewUrl !== '' && (
						<iframe
							id="astra-starter-templates-preview"
							title={ __( 'Website Preview', 'ai-builder' ) }
							height="100%"
							width="100%"
							src={ previewUrl }
							onLoad={ handleIframeLoading }
						/>
					) }
				</>
			}
		/>
	);
};

export default CustomizeAiSite;
