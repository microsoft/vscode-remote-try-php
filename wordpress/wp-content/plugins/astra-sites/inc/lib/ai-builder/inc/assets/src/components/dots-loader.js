import { motion } from 'framer-motion';
import { memo } from '@wordpress/element';
import { classNames } from '../helpers';

const dotVariants = {
	hidden: { scale: 0 },
	visible: { scale: 1 },
	exit: { scale: 0 },
};

const dotTransition = ( itemIndx ) => ( {
	duration: 0.5,
	repeat: Infinity,
	repeatType: 'reverse',
	delay: itemIndx * 0.2,
} );

const DotsLoading = ( { className } ) => {
	return (
		<div className="w-5 h-5 flex items-center justify-center gap-0.5">
			{ [ ...Array( 3 ) ].map( ( _, i ) => (
				<motion.div
					key={ i }
					className={ classNames(
						'w-1 h-1 bg-accent-st rounded-full !shrink-0',
						className
					) }
					variants={ dotVariants }
					initial="hidden"
					animate="visible"
					exit="exit"
					transition={ dotTransition( i ) }
				/>
			) ) }
		</div>
	);
};

export default memo( DotsLoading );
