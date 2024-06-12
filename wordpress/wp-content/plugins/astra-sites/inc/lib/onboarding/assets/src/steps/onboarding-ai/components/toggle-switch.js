import { Switch } from '@headlessui/react';
import { memo } from 'react';
import { classNames } from '../helpers';
import { __ } from '@wordpress/i18n';

const ToggleSwitch = ( { onChange, value, requiredClass } ) => {
	return (
		<Switch
			checked={ value }
			onChange={ onChange }
			className="group relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer items-center justify-center rounded-full focus:outline-none border-0 bg-transparent"
		>
			<span className="sr-only">
				{ __( 'Use setting', 'astra-sites' ) }
			</span>
			<span
				aria-hidden="true"
				className="pointer-events-none absolute h-full w-full rounded-md bg-transparent"
			/>
			<span
				aria-hidden="true"
				className={ classNames(
					requiredClass,
					'pointer-events-none absolute mx-auto h-4 w-9 rounded-full transition-colors duration-200 ease-in-out'
				) }
			/>
			<span
				aria-hidden="true"
				className={ classNames(
					value ? 'translate-x-5' : 'translate-x-0',
					'pointer-events-none absolute left-0 inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform duration-200 ease-in-out'
				) }
			/>
		</Switch>
	);
};

export default memo( ToggleSwitch );
