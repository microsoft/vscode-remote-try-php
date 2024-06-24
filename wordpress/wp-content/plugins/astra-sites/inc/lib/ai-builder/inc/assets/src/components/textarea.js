const { forwardRef, useMemo } = wp.element;

const TextArea = (
	{
		disabled = false,
		className,
		textAreaClassName = '',
		error,
		register,
		name,
		validations,
		label,
		...props
	},
	ref
) => {
	const { ref: formHookRef, ...registerValidations } = useMemo( () => {
		return typeof register === 'function'
			? register( name, validations )
			: {};
	}, [ name, register, validations ] );

	return (
		<div className={ className }>
			{ label && (
				<label
					htmlFor={ name }
					className="text-base flex font-semibold leading-6 items-center !mb-2"
				>
					{ label }
				</label>
			) }
			<div className="relative">
				<textarea
					ref={ ( node ) => {
						if ( node && typeof formHookRef === 'function' ) {
							formHookRef( node );
						}
						if ( ! ref ) {
							return;
						}
						switch ( typeof ref ) {
							case 'function':
								ref( node );
								break;
							case 'object':
								ref.current = node;
								break;
							default:
								break;
						}
					} }
					name={ name }
					disabled={ disabled }
					className={ `${
						label ? 'mt-2' : ''
					} w-full placeholder:zw-placeholder zw-input text-[16px] rounded-md border outline-none focus:ring-1 focus:ring-accent-st p-4 ${
						error
							? 'shadow-error border-alert-error  focus:border-accent-st '
							: 'shadow-sm border-zip-light-border-primary focus:border-accent-st'
					} ${ textAreaClassName }` }
					{ ...props }
					{ ...registerValidations }
				/>
			</div>

			{ error && (
				<div className="mt-1 text-sm text-alert-error ">
					{ error.message }
				</div>
			) }
		</div>
	);
};

export default forwardRef( TextArea );
