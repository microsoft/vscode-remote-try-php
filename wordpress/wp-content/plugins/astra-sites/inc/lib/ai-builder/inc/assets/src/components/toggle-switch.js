import { Switch } from '@headlessui/react';
import { memo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { classNames } from '../helpers';

const ToggleSwitch = ( { onChange, value, variant = 'dark' } ) => {
	const switchClassNames = {
		dark: {
			wrapper: [
				'w-10',
				value ? 'bg-accent-st' : 'bg-zip-dark-theme-border',
			],
			background: [ value ? 'bg-accent-st' : 'bg-zip-dark-theme-border' ],
			switch: [ 'size-5', value ? 'translate-x-5' : 'translate-x-0' ],
		},
		light: {
			wrapper: [ 'w-9', value ? 'bg-accent-st' : 'bg-border-tertiary' ],
			background: [ value ? 'bg-accent-st' : 'bg-border-tertiary' ],
			switch: [
				'size-4',
				value ? 'translate-x-[1.15rem]' : 'translate-x-[0.15rem]',
			],
		},
	};
	return (
		<Switch
			checked={ value }
			onChange={ onChange }
			className={ classNames(
				'group relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer items-center justify-center rounded-full focus:outline-none border-0 bg-transparent',
				switchClassNames[ variant ].wrapper
			) }
		>
			<span className="sr-only">
				{ __( 'Use setting', 'ai-builder' ) }
			</span>
			<span
				aria-hidden="true"
				className="pointer-events-none absolute h-full w-full rounded-md bg-transparent"
			/>
			<span
				aria-hidden="true"
				className={ classNames(
					'pointer-events-none absolute mx-auto h-4 w-9 rounded-full transition-colors duration-150 ease-out',
					switchClassNames[ variant ].background
				) }
			/>
			<span
				aria-hidden="true"
				className={ classNames(
					'pointer-events-none absolute size-5 left-0 inline-block transform rounded-full bg-white shadow transition-transform duration-150 ease-out',
					switchClassNames[ variant ].switch
				) }
			/>
		</Switch>
	);
};

export default memo( ToggleSwitch );
