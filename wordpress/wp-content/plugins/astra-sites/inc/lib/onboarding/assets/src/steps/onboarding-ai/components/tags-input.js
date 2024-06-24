import { XMarkIcon } from '@heroicons/react/24/outline';
import { useState, useEffect, useRef } from '@wordpress/element';
import { classNames } from '../helpers';

const TagsInput = ( {
	name = '',
	placeholder = '',
	value = '',
	onChange,
	validationPattern = '',
	delimiters = [ ',', '\n', ';', ' ' ],
	className = '',
	tokenClassName = '',
	filterDuplicates = true,
	maxLength = 80,
	maxTokens = 0, // 0 means unlimited.
} ) => {
	const [ tags, setTags ] = useState( [] );
	const delimitersRegex = new RegExp( delimiters.join( '|' ), 'g' );
	const inputRef = useRef( null );
	const autoSizer = useRef( null );

	const addNewTag = ( tagValue ) => {
		setTags( filterDuplicateValues( [ ...tags, tagValue ] ) );
	};

	const handleDeleteTag = ( index ) => {
		const newTags = [ ...tags ];
		newTags.splice( index, 1 );
		setTags( newTags );

		if ( typeof onChange === 'function' ) {
			onChange( newTags );
		}

		if ( maxTokens > 0 && tags.length >= maxTokens ) {
			setTimeout( () => {
				if ( ! inputRef.current ) {
					return;
				}
				inputRef.current.focus();
			}, 50 );
		}
	};

	const filterDuplicateValues = ( val ) => {
		return filterDuplicates
			? [ ...new Set( val ) ]?.filter( Boolean )
			: val;
	};

	const handleAddTag = ( event ) => {
		if ( maxTokens > 0 && tags.length >= maxTokens ) {
			return;
		}
		const tag = event.target.value.trim();
		if (
			! tag ||
			( tag && validationPattern && ! validationPattern.test( tag ) ) ||
			tag.length > maxLength
		) {
			return;
		}
		addNewTag( tag );
		event.target.value = '';
		handleAutoSizeInput( '' );

		if ( typeof onChange === 'function' ) {
			onChange( filterDuplicateValues( [ ...tags, tag ] ) );
		}
	};

	const handleKeyDown = ( event ) => {
		// If press Enter add tag.
		if ( event.key === 'Enter' ) {
			event.preventDefault();
			handleAddTag( event );
		}

		// If press Backspace and input is empty, remove tag.
		if ( event.key === 'Backspace' && ! event.target.value ) {
			handleDeleteTag( tags.length - 1 );
		}

		// If press delimiter, add tag.
		if ( delimiters.includes( event.key ) ) {
			event.preventDefault();
			handleAddTag( event );
		}
	};

	const handlePaste = ( event ) => {
		event.preventDefault();
		const clipboardData = event.clipboardData || window.clipboardData;
		const pastedText = clipboardData.getData( 'text' );

		if ( ! pastedText ) {
			return;
		}

		if ( maxTokens > 0 && tags.length >= maxTokens ) {
			return;
		}

		const newTags = pastedText
			.split( delimitersRegex )
			.filter( Boolean )
			.filter(
				( tag ) => ! validationPattern || validationPattern.test( tag )
			);
		setTags( filterDuplicateValues( [ ...tags, ...newTags ] ) );

		if ( typeof onChange === 'function' ) {
			onChange( filterDuplicateValues( [ ...tags, ...newTags ] ) );
		}
	};

	const handleClickInputContainer = ( event ) => {
		if ( event.target === inputRef.current ) {
			return;
		}
		if ( ! inputRef.current ) {
			return;
		}
		inputRef.current.focus();
	};

	const handleAutoSizeInput = ( input ) => {
		if ( input === '' ) {
			autoSizer.current.innerHTML = placeholder;
			return;
		}
		autoSizer.current.innerText = input;
	};

	const handleOnChangeInput = ( event ) => {
		handleAutoSizeInput( event.target.value );
	};

	useEffect( () => {
		if ( ! value ) {
			setTags( [] );
			return;
		}
		let tagValues = [];
		if ( typeof value === 'string' ) {
			tagValues = value.split( delimitersRegex ).filter( Boolean );
		} else if ( Array.isArray( value ) ) {
			tagValues = [ ...value ];
		}

		// Slice tags if maxTokens is set.
		if ( maxTokens > 0 ) {
			tagValues = tagValues.slice( 0, maxTokens );
		}

		if (
			tags.length > 0 &&
			tags.every( ( tag ) => tagValues.includes( tag ) )
		) {
			return;
		}
		setTags(
			filterDuplicateValues ? [ ...new Set( tagValues ) ] : tagValues
		);
	}, [ value ] ); // eslint-disable-line

	return (
		<div
			className={ classNames(
				'w-full min-h-[48px] flex items-center flex-wrap gap-2.5 rounded-md px-3 py-2.5 border border-border-primary border-solid ring-1 ring-transparent focus-within:ring-1 focus-within:ring-accent-st focus-within:outline-none placeholder:text-secondary-text text-app-text shadow-sm',
				className
			) }
			onMouseDown={ ( event ) => {
				if ( event.target.tagName !== 'INPUT' ) {
					event.preventDefault();
				}
			} }
			role="button"
			tabIndex={ 0 }
			onClick={ handleClickInputContainer }
			onKeyDown={ ( event ) => {
				if ( event.key === 'Enter' || event.key === ' ' ) {
					handleClickInputContainer();
				}
			} }
		>
			{ tags.map( ( tag, index ) => (
				<div
					key={ index }
					className={ classNames(
						'tag-item max-w-max w-full flex items-center gap-1 pl-2.5 pr-1 py-0.5 bg-alert-info-bg shadow-sm rounded',
						tags.length - 1 === index && 'mr-1.5',
						tokenClassName
					) }
				>
					<span className="truncate text-sm font-medium leading-5 text-app-active-icon">
						{ tag }
					</span>
					<div
						onClick={ () => handleDeleteTag( index ) }
						className="flex-shrink-0 w-4 h-4 text-nav-inactive hover:text-nav-active bg-transparent border-0 cursor-pointer"
					>
						<XMarkIcon />
					</div>
				</div>
			) ) }
			{ ( tags.length < maxTokens || maxTokens === 0 ) && (
				<span className="relative overflow-hidden flex items-center">
					<span
						ref={ autoSizer }
						className="text-base font-normal leading-6 invisible inline whitespace-pre"
					>
						{ placeholder }
					</span>
					<input
						ref={ inputRef }
						type="text"
						name={ name }
						placeholder={ placeholder }
						onKeyDown={ handleKeyDown }
						onPaste={ handlePaste }
						onBlur={ handleAddTag }
						onChange={ handleOnChangeInput }
						className="absolute left-0 !min-w-[50px] !w-full !p-0 !text-base !font-normal !leading-6 !focus:outline-none !border-0 focus:!border-0 focus:!ring-0 !h-6 !bg-transparent"
						autoComplete="off"
						maxLength={ maxLength }
					/>
				</span>
			) }
		</div>
	);
};

export default TagsInput;
