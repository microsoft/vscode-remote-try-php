import React from 'react';
import { __ } from '@wordpress/i18n';
import Button from '../../../../components/button/button';
import { useStateValue } from '../../../../store/store';
import PreviousStepLink from '../../../../components/util/previous-step-link/index';
import ChooseEcommerce from '../../../../components/choose-ecommerce';
import { ChevronRightIcon } from '@heroicons/react/24/outline';

const EcommerceSelectionsControls = () => {
	const [ { currentCustomizeIndex, currentIndex, templateId }, dispatch ] =
		useStateValue();
	const nextStep = () => {
		dispatch( {
			type: 'set',
			currentCustomizeIndex: currentCustomizeIndex + 1,
		} );
	};

	const lastStep = () => {
		setTimeout( () => {
			dispatch( {
				type: 'set',
				currentIndex: currentIndex - 1,
				currentCustomizeIndex: 0,
			} );
		}, 300 );
	};

	return (
		<>
			<ChooseEcommerce />
			<div className="w-full flex flex-col gap-4 mt-auto">
				<Button
					className={ `w-full flex gap-2 items-center ${
						templateId === 0
							? '!bg-border-tertiary !text-zip-app-inactive-icon'
							: ''
					}` }
					onClick={ nextStep }
					disabled={ templateId !== 0 ? false : true }
				>
					<span>{ __( 'Continue', 'astra-sites' ) }</span>
					<ChevronRightIcon className="w-4 h-4 !fill-none" />
				</Button>

				<PreviousStepLink onClick={ lastStep } before>
					{ __( 'Back', 'astra-sites' ) }
				</PreviousStepLink>
			</div>
		</>
	);
};

export default EcommerceSelectionsControls;
