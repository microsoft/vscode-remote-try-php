import { classNames } from '../utils/helpers';

const Snackbar = ( { type, icon, message, ctaLink, ctaText, rounded = 4 } ) => {
	const typeClassName = {
		warning: {
			text: 'text-credit-warning',
			icon: 'text-credit-warning',
			background: 'bg-credit-warning/5',
		},
		error: {
			text: 'text-credit-danger',
			icon: 'text-credit-danger',
			background: 'bg-credit-danger/5',
		},
		info: {
			text: 'text-body-text',
			icon: 'text-accent-st',
			background: 'bg-background-secondary',
		},
	};

	const borderRadiusClassName = {
		4: 'rounded',
		6: 'rounded-md',
		8: 'rounded-lg',
	};

	return (
		<div
			className={ classNames(
				'pl-3 pr-4 py-3',
				typeClassName[ type ]?.background,
				borderRadiusClassName[ rounded ]
			) }
		>
			<div className="flex items-center gap-2">
				<div
					className={ classNames(
						'flex items-center',
						typeClassName[ type ]?.icon
					) }
				>
					{ !! icon && icon }
				</div>
				<div className="flex-1 flex justify-between items-center">
					<p
						className={ classNames(
							'text-sm m-0',
							typeClassName[ type ]?.text
						) }
					>
						{ !! message && message }
					</p>
					<p className="text-sm m-0">
						{ !! ctaLink && (
							<a
								href={ ctaLink }
								target="_blank"
								className="whitespace-nowrap font-normal !text-nav-active"
								rel="noreferrer"
							>
								{ !! ctaText && ctaText }
							</a>
						) }
					</p>
				</div>
			</div>
		</div>
	);
};

export default Snackbar;
