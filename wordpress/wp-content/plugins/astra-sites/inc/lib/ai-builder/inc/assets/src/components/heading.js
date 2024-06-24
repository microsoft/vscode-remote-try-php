import { classNames } from '../helpers';

const Heading = ( { heading, subHeading, className } ) => {
	return (
		<div className={ classNames( 'space-y-3', className ) }>
			{ !! heading && (
				<div className="text-[2rem] font-semibold leading-[140%]">
					{ heading }
				</div>
			) }
			{ !! subHeading && (
				<p className="text-zip-body-text text-base font-normal leading-6">
					{ subHeading }
				</p>
			) }
		</div>
	);
};

export default Heading;
