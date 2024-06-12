import React from 'react';
import Lottie from 'react-lottie-player';
import { __ } from '@wordpress/i18n';
import DefaultStep from '../../components/default-step/index';
import './style.scss';
import { PreviousStepLink } from '../../components';
import lottieJson from '../../../images/website-building.json';

const SurveyLoading = () => {
	return (
		<DefaultStep
			content={
				<div className="survey-loading-container">
					<div className="loading-container">
						<Lottie
							loop
							animationData={ lottieJson }
							play
							style={ {
								height: 500,
								margin: '-70px auto -90px auto',
							} }
						/>
					</div>
				</div>
			}
			actions={
				<>
					<PreviousStepLink before>
						{ __( 'Back', 'astra-sites' ) }
					</PreviousStepLink>
				</>
			}
		/>
	);
};

export default SurveyLoading;
