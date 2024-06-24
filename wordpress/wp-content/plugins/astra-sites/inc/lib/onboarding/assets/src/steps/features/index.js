import React from 'react';
import { DefaultStep } from '../../components/index';
import ClassicFeatures from './features';

const FeaturesStep = () => {
	return (
		<DefaultStep
			content={
				<div className="features-container">{ ClassicFeatures() }</div>
			}
		/>
	);
};

export default FeaturesStep;
