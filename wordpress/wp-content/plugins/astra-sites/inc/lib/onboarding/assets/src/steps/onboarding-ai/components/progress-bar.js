export const ProgressBar = ( {
	value,
	color = 'bg-gradient-to-r from-gradient-color-1 via-46.88 via-gradient-color-2 to-gradient-color-3',
	bgColor = 'bg-[#dfdfe7]',
	height = 'h-1',
} ) => {
	const fillerRelativePercentage = ( 100 / value ) * 100;

	return (
		<div
			className="flex items-center"
			role="progressbar"
			aria-valuemin={ 0 }
			aria-valuemax={ 100 }
			aria-valuenow={ value }
		>
			<div
				className={ `flex-1 ${ height } ${ bgColor } rounded-xl overflow-hidden` }
			>
				<div
					className="h-[inherit] rounded-[inherit] overflow-hidden"
					style={ { width: `${ value }%` } }
				>
					<div
						className={ `h-[inherit] ${ color }` }
						style={ {
							width: `${ fillerRelativePercentage }%`,
						} }
					/>
				</div>
			</div>
		</div>
	);
};
