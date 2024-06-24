import { memo, useMemo } from '@wordpress/element';
import {
	ComputerDesktopIcon,
	DevicePhoneMobileIcon,
	DeviceTabletIcon,
} from '@heroicons/react/24/outline';
import { motion, LayoutGroup } from 'framer-motion';
import { classNames } from '../helpers';

export const RESPONSIVE_MODES = {
	desktop: {
		name: 'desktop',
		width: '100%',
		icon: ComputerDesktopIcon,
	},
	tablet: {
		name: 'tablet',
		width: '768px',
		icon: DeviceTabletIcon,
	},
	mobile: {
		name: 'mobile',
		width: '375px',
		icon: DevicePhoneMobileIcon,
	},
};

const ResponsiveButtons = ( { value, onChange } ) => {
	const selected =
		! value || ! value?.name ? RESPONSIVE_MODES.desktop : value;

	const handleChange = ( modeValue ) => () => {
		if ( typeof onChange !== 'function' ) {
			return;
		}

		onChange( modeValue );
	};

	const devicesArray = useMemo( () => Object.values( RESPONSIVE_MODES ) ),
		totalDevices = devicesArray.length - 1;
	return (
		<div className="isolate inline-flex rounded-md shadow-sm border border-solid border-zip-dark-theme-border divide-solid divide-x divide-zip-dark-theme-border">
			<LayoutGroup id="responsive-buttons">
				{ devicesArray.map( ( mode, indx ) => (
					<button
						key={ mode.name }
						type="button"
						className={ classNames(
							indx === 0 && 'rounded-l-md',
							indx === totalDevices && 'rounded-r-md',
							indx !== 0 && '-ml-px',
							'relative w-[2.25rem] h-[2.25rem] flex items-center bg-zip-dark-theme-bg p-2 text-sm font-semibold text-zip-dark-theme-icon-active focus:outline-none focus-visible:outline-none border-0 shadow-sm cursor-pointer active:outline-none z-auto transition-colors ease-out duration-[250ms]',
							selected?.name === mode.name &&
								'text-zip-dark-theme-heading bg-zip-dark-theme-bg cursor-default z-[1]',
							selected?.name === mode.name &&
								indx <= totalDevices &&
								'!border-transparent'
						) }
						onClick={ handleChange( mode ) }
					>
						<mode.icon className="!shrink-0 w-5 h-5 z-10 absolute inset-2" />
						{ mode.name === selected?.name && (
							<motion.span
								className="bg-zip-dark-theme-content-background rounded absolute inset-0 z-0"
								layoutId="active-mode"
								layoutDependency={ mode }
								transition={ {
									layout: {
										duration: 0.25,
										ease: 'easeOut',
									},
								} }
							/>
						) }
					</button>
				) ) }
			</LayoutGroup>
		</div>
	);
};

export default memo( ResponsiveButtons );
