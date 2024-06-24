import React, { useEffect, useLayoutEffect } from 'react';
import ICONS from '../../../icons';
import { useStateValue } from '../../store/store';

const DefaultStep = ( { preview, content, controls, actions, stepName } ) => {
	const [ { showSidebar, currentIndex }, dispatch ] = useStateValue();

	const toggleSidebar = () => {
		dispatch( {
			type: 'set',
			showSidebar: ! showSidebar,
		} );
	};

	useEffect( () => {
		const contentArea = document.querySelector( '.step-content' );
		contentArea.addEventListener( 'scroll', ( event ) => {
			setTimeout( () => {
				if ( event.target.classList.length === 1 ) {
					localStorage.setItem(
						'st-scroll-position',
						event.target.scrollTop
					);
				}
			}, 200 );
		} );
	}, [] );

	useLayoutEffect( () => {
		const contentArea = document.querySelector( '.step-content' );
		const scrollPosition = localStorage.getItem( 'st-scroll-position' );

		if (
			!! scrollPosition &&
			scrollPosition > 100 &&
			currentIndex === 2 &&
			contentArea.classList.length === 1
		) {
			contentArea.scrollTo( 0, scrollPosition );
		}
	} );

	return (
		<div
			className={ `step-row
				${ preview ? '' : 'step-full-width' }
				${ showSidebar ? 'show-sidebar' : 'hide-sidebar' }
			` }
		>
			<div className={ `step-col ${ preview ? 'step-col-left' : '' } ` }>
				<div className="toggle-sidebar-wrap" onClick={ toggleSidebar }>
					{ showSidebar && ICONS.angleLeft }
					{ ! showSidebar && ICONS.angleRight }
				</div>
				<div
					className={ `step-content ${ stepName || '' }` }
					style={ {
						padding: currentIndex === 1 ? '5% 6% 6% 6%' : '',
					} }
				>
					<div
						className={ `content-wrapper
					${ currentIndex === 3 ? 'flex flex-col items-start h-full' : '' }
					` }
					>
						{ content && content }
						{ controls && (
							<div
								className={ `step-controls
							${ currentIndex === 3 ? 'flex flex-col items-start h-full w-full' : '' }
							` }
								/* eslint-disable-next-line jsx-a11y/tabindex-no-positive -- This is a used for keyboard navigation support. */
								tabIndex="1"
							>
								{ controls }
							</div>
						) }
					</div>
				</div>

				{ actions && currentIndex !== 0 && (
					/* eslint-disable-next-line jsx-a11y/tabindex-no-positive -- This is a used for keyboard navigation support. */
					<div className="step-actions" tabIndex="1">
						{ actions }
					</div>
				) }
			</div>

			{ preview && (
				<div className="step-col step-col-right">{ preview }</div>
			) }
		</div>
	);
};

export default DefaultStep;
