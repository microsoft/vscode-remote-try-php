import { twMerge } from 'tailwind-merge';

const { forwardRef } = wp.element;

const Button = (
	{
		variant = '',
		isSmall = false,
		hasSuffixIcon = false,
		hasPrefixIcon = false,
		type = 'button',
		className,
		onClick,
		children,
		disabled = false,
		id = '',
		...props
	},
	ref
) => {
	const buttonSize = isSmall ? 'small' : 'base';
	const variantClassNames = {
		primary:
			'text-white bg-accent-st focus-visible:ring-accent-st border border-solid border-accent-st',
		white: 'text-accent-st bg-white border border-solid border-accent-st focus-visible:ring-accent-st',
		dark: 'text-white border border-white bg-transparent border-solid',
		link: 'underline border-0 bg-transparent',
		blank: 'bg-transparent border-transparent',
		gray: 'bg-transparent border border-solid border-zip-dark-theme-border text-zip-dark-theme-heading',
		'gray-selected': 'bg-zip-dark-theme-border text-white',
		other: '',
		'gradient-border':
			'bg-transparent text-zip-app-heading zw-base-bold gradient-border-cover gradient-border-cover-button',
		gradient:
			'bg-gradient-to-r from-gradient-color-1 via-46.88 via-gradient-color-2 to-gradient-color-3 text-white zw-base-bold',
		'border-secondary':
			'text-app-secondary bg-app-light-background border border-app-secondary shadow-sm',
	};
	const sizeClassNames = {
		base: {
			default: 'px-6 py-3',
			hasPrefixIcon: 'pl-4 pr-6 py-3',
			hasSuffixIcon: 'pl-6 pr-4 py-3',
		},
		medium: {
			default: 'px-4 py-3 h-11',
			hasPrefixIcon: 'pl-4 pr-6 py-3',
			hasSuffixIcon: 'pl-6 pr-4 py-3',
		},
		small: {
			default: 'px-5 py-2 h-[2.625rem]',
			hasPrefixIcon: 'pl-3 pr-5 py-2 h-[2.625rem]',
			hasSuffixIcon: 'pl-5 pr-3 py-2 h-[2.625rem]',
		},
	};
	const typographyClassNames = {
		base: 'sp-text-base font-medium',
		small: 'sp-text-base font-medium',
	};
	const borderRadiusClassNames = {
		base: 'rounded-md',
		small: 'rounded',
	};

	const handleOnClick = ( event ) => {
		if ( !! onClick && typeof onClick === 'function' ) {
			onClick( event );
		}
	};

	return (
		<button
			type={ type }
			className={ twMerge(
				'group flex items-center justify-center gap-2 rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 transition duration-150 ease-in-out cursor-pointer border-0',
				variantClassNames[ variant ],
				! hasPrefixIcon &&
					! hasSuffixIcon &&
					sizeClassNames[ buttonSize ].default,
				hasPrefixIcon && sizeClassNames[ buttonSize ].hasPrefixIcon,
				hasSuffixIcon && sizeClassNames[ buttonSize ].hasSuffixIcon,
				typographyClassNames[ buttonSize ],
				borderRadiusClassNames[ buttonSize ],
				disabled && 'cursor-not-allowed opacity-70',
				className
			) }
			onClick={ handleOnClick }
			ref={ ref }
			disabled={ disabled }
			{ ...( id && { id } ) }
			{ ...props }
		>
			{ children }
		</button>
	);
};

export default forwardRef( Button );
