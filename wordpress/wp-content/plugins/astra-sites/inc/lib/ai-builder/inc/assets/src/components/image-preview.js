import { motion } from 'framer-motion';
import { CheckIcon, XMarkIcon } from '@heroicons/react/24/outline';
import { memo } from '@wordpress/element';
import { classNames } from '../helpers';
import Tile from './tile';

const VARIANTS = {
	default: 'default',
	selection: 'selection',
};

const ImagePreview = ( {
	image,
	isSelected,
	onClick,
	variant = 'default',
	...props
} ) => {
	const handleSelection = ( imageItem ) => ( event ) => {
		event?.preventDefault();
		event?.stopPropagation();

		if ( variant === VARIANTS.selection && isSelected ) {
			return;
		}

		onClick( imageItem );
	};

	const handleRemoveSelection = ( imageItem ) => ( event ) => {
		event?.preventDefault();
		event?.stopPropagation();

		onClick( imageItem );
	};

	const renderSelectionIcon = () => {
		if ( ! isSelected ) {
			return null;
		}

		if ( variant === VARIANTS.selection ) {
			return (
				<div
					onClick={ handleRemoveSelection( image ) }
					className="flex items-center justify-center absolute top-2 right-2 p-1 bg-white rounded-full border border-solid border-zip-dark-theme-border cursor-pointer"
				>
					<XMarkIcon className="w-4 h-4 text-zip-app-heading" />
				</div>
			);
		}

		return (
			<div className="inline-flex absolute top-2 right-2 p-1 bg-outline-color rounded-full pointer-events-none">
				<CheckIcon className="w-4 h-4 text-white" />
			</div>
		);
	};

	return (
		<motion.div
			key={ image.id }
			initial={ { opacity: 0 } }
			animate={ { opacity: 1 } }
			transition={ {
				duration: 0.15,
			} }
			exit={ { opacity: 0 } }
			{ ...props }
		>
			<Tile
				className={ classNames(
					'flex relative overflow-hidden rounded-lg border-2 border-solid border-transparent',
					variant === VARIANTS.default && 'cursor-pointer',
					variant === VARIANTS.default &&
						isSelected &&
						'border-outline-color'
				) }
				onClick={ handleSelection( image ) }
			>
				<img
					className="inline-block w-full h-fit relative aspect-[12/8] bg-background-secondary"
					src={ image.optimized_url }
					alt={ image?.description ?? '' }
					loading="lazy"
					onLoad={ ( event ) => {
						event.target.classList.remove( 'aspect-[12/8]' );
					} }
				/>
				{ renderSelectionIcon() }
			</Tile>
			{ image?.author_name && (
				<a
					href={ image?.author_url }
					target="_blank"
					className="block w-11/12 mt-1 mx-1 text-[0.625rem] font-normal leading-3 !text-secondary-text no-underline"
					rel="noreferrer"
				>
					by { image.author_name } via{ ' ' }
					{ image.engine
						? image.engine.charAt( 0 ).toUpperCase() +
						  image.engine.slice( 1 )
						: 'Default' }
				</a>
			) }
		</motion.div>
	);
};

export default memo( ImagePreview, ( prevProps, nextProp ) => {
	return (
		String( prevProps.image.id ) === String( nextProp.image.id ) &&
		prevProps.isSelected === nextProp.isSelected &&
		prevProps.onClick === nextProp.onClick &&
		prevProps.variant === nextProp.variant
	);
} );
