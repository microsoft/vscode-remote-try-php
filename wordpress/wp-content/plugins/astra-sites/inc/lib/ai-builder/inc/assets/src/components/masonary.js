import { memo } from '@wordpress/element';
import { classNames } from '../helpers';

const { useState, useEffect, useRef } = wp.element;

const defaultBreakPoints = [ 350, 500, 750 ];

const Masonry = ( {
	breakPoints = defaultBreakPoints,
	rowClassName = 'gap-x-6',
	columnClassName = 'gap-y-6',
	children,
} ) => {
	const [ columns, setColumns ] = useState( 1 );
	const masonryRef = useRef( null );

	useEffect( () => {
		const getColumns = ( w ) => {
			return (
				breakPoints.reduceRight( ( p, c, i ) => {
					return c < w ? p : i;
				}, breakPoints.length ) + 1
			);
		};

		const onResize = () => {
			const newColumns = getColumns( masonryRef.current.offsetWidth );
			if ( newColumns !== columns ) {
				setColumns( newColumns );
			}
		};
		onResize();
		window.addEventListener( 'resize', onResize );

		return () => {
			window.removeEventListener( 'resize', onResize );
		};
	}, [ columns, breakPoints ] );

	const mapChildren = () => {
		const numC = columns;
		const col = Array.from( { length: numC }, () => [] );
		return children.reduce( ( p, c, i ) => {
			p[ i % numC ].push( c );
			return p;
		}, col );
	};

	return (
		<div
			className={ classNames(
				'flex flex-row justify-center content-stretch m-auto w-full',
				rowClassName
			) }
			ref={ masonryRef }
		>
			{ mapChildren().map( ( col, ci ) => (
				<div
					className={ classNames(
						'flex flex-col justify-start content-stretch flex-auto',
						columnClassName
					) }
					key={ ci }
				>
					{ col.map( ( child, i ) => (
						<div key={ i }>{ child }</div>
					) ) }
				</div>
			) ) }
		</div>
	);
};

export default memo( Masonry );
