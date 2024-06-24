import React from 'react';
import { __ } from '@wordpress/i18n';
import PreviousStepLink from '../util/previous-step-link/index';
import NextStepLink from '../util/next-step-link/index';
import { Row, Col, Progress } from '../../ui/style';

import { useStateValue } from '../../store/store';

const Customizer = ( { preview, controls } ) => {
	const [ { currentIndex, stepsLength } ] = useStateValue();

	return (
		<Row className="row">
			<Col className="col left">
				<Progress
					value={ currentIndex + 1 }
					min="0"
					max={ stepsLength }
				/>
				{ controls }

				<div className="ist-action-links">
					<div className="ist-action-links-left">
						<PreviousStepLink before="dashicons-arrow-left-alt">
							{ __( 'Back', 'astra-sites' ) }
						</PreviousStepLink>
					</div>
					<div className="ist-action-links-right">
						<NextStepLink>
							{ __( 'Skip', 'astra-sites' ) }
						</NextStepLink>
					</div>
				</div>
			</Col>
			<Col className="col right">{ preview }</Col>
		</Row>
	);
};

export default Customizer;
