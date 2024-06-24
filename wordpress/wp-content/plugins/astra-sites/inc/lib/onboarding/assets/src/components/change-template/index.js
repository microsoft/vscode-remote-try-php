import React from 'react';
import { __ } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';
import { useStateValue } from '../../store/store';
import './style.scss';
import ICONS from '../../../icons';
import { sendPostMessage } from '../../utils/functions';
import { XMarkIcon } from '@heroicons/react/24/outline';

const ChangeTemplate = () => {
	const [
		{
			selectedTemplateName,
			currentIndex,
			licenseStatus,
			selectedTemplateType,
		},
		dispatch,
	] = useStateValue();

	const goToShowcase = () => {
		sendPostMessage( {
			param: 'clearPreviewAssets',
			data: {},
		} );

		setTimeout( () => {
			dispatch( {
				type: 'set',
				currentIndex: currentIndex - 1,
				currentCustomizeIndex: 0,
			} );
		}, 300 );
	};
	return (
		<div className="change-template-wrap w-full">
			<div className="template-name">
				<p className="label">
					{ __( 'Selected Template:', 'astra-sites' ) }
				</p>
				<div className="flex gap-2 items-center">
					<h5>{ decodeEntities( selectedTemplateName ) }</h5>
					{ ! licenseStatus && 'free' !== selectedTemplateType && (
						<span>{ ICONS.premiumIcon }</span>
					) }
				</div>
			</div>
			<div className="change-btn-wrap" onClick={ goToShowcase }>
				<XMarkIcon className="w-6 h-6 text-zip-body-text" />
			</div>
		</div>
	);
};
export default ChangeTemplate;
