import React, { useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { RangeControl } from '@wordpress/components';
import { MediaUpload } from '@wordpress/media-utils';
import Button from '../button/button';
import { useStateValue } from '../../store/store';
import { sendPostMessage, getDataUri } from '../../utils/functions';
import { initialState } from '../../store/reducer';

import './style.scss';
import ICONS from '../../../icons';

const MediaUploader = () => {
	const replaceMediaUpload = () => MediaUpload;
	const [ { siteLogo }, dispatch ] = useStateValue();

	addFilter(
		'editor.MediaUpload',
		'core/edit-post/components/media-upload/replace-media-upload',
		replaceMediaUpload
	);

	const onSelectImage = ( media ) => {
		const mediaData = {
			id: media.id,
			url: media.url,
			width: siteLogo.width,
		};

		if ( window.location.protocol === 'http:' ) {
			getDataUri( media.url, function ( data ) {
				mediaData.dataUri = data;
				updateValues( mediaData );
			} );
		} else {
			updateValues( mediaData );
		}
	};

	const updateValues = ( data ) => {
		dispatch( {
			type: 'set',
			siteLogo: data,
		} );

		sendPostMessage( {
			param: 'siteLogo',
			data,
		} );
	};

	const removeImage = () => {
		updateValues( initialState.siteLogo );
	};

	const onWidthChange = ( width ) => {
		const newLogoOptions = {
			...siteLogo,
			width,
		};

		dispatch( {
			type: 'set',
			siteLogo: newLogoOptions,
		} );

		sendPostMessage( {
			param: 'siteLogo',
			data: newLogoOptions,
		} );
	};

	const resetLogoWidth = ( event ) => {
		if ( ! siteLogo.url ) {
			return;
		}
		event.stopPropagation();
		onWidthChange( initialState.siteLogo.width );
	};

	useEffect( () => {
		if ( !! astraSitesVars.isRTLEnabled ) {
			const rangeControl = document.querySelector(
				'.components-range-control__wrapper'
			);
			if ( rangeControl === null ) {
				return;
			}

			// Range control slider styling for RTL.
			const currentValue = rangeControl.children[ 3 ].style.left;
			rangeControl.children[ 3 ].style.marginRight = '-10px';
			rangeControl.children[ 3 ].style.removeProperty( 'margin-left' );
			rangeControl.children[ 3 ].style.right = currentValue;
			rangeControl.children[ 4 ].style.removeProperty( 'transform' );
			rangeControl.children[ 4 ].style.removeProperty( 'left' );
			rangeControl.children[ 4 ].style.right = currentValue;
			rangeControl.children[ 4 ].style.transform = 'translateX(50%)';
		}
	} );

	return (
		<MediaUpload
			onSelect={ ( media ) => onSelectImage( media ) }
			allowedTypes={ [ 'image' ] }
			value={ siteLogo.id }
			render={ ( { open } ) => (
				<>
					{ '' !== siteLogo.url && undefined !== siteLogo.url ? (
						<div className="ist-logo-wrapper">
							<div
								className="ist-remove-logo"
								onClick={ removeImage }
								title={ __( 'Remove Logo', 'astra-sites' ) }
							>
								<svg
									xmlns="http://www.w3.org/2000/svg"
									width="8"
									height="8"
									viewBox="0 0 8 8"
									fill="#333333"
								>
									<path
										d="M8 0.7L7.3 0L4 3.3L0.7 0L0 0.7L3.3 4L0 7.3L0.7 8L4 4.7L7.3 8L8 7.3L4.7 4L8 0.7Z"
										fill="#333333"
									/>
								</svg>
							</div>
							<div
								className="ist-selected-image"
								onClick={ open }
							>
								<div
									onClick={ open }
									className="ist-logo-preview-wrap"
								>
									<img
										alt={ __( 'Site Logo', 'astra-sites' ) }
										src={ siteLogo.url }
									/>
									<div
										className="ist-change-logo"
										onClick={ open }
									>
										{ __( 'Change Logo', 'astra-sites' ) }
									</div>
								</div>
							</div>
						</div>
					) : (
						''
					) }

					{ '' === siteLogo.url ? (
						<div>
							<Button
								className="ist-button ist-button-outline ist-logo-button"
								onClick={ open }
							>
								<h5>
									{ __( 'Upload File Here', 'astra-sites' ) }
								</h5>
								<p>
									{ __(
										'Suggested Dimensions: 180x60 pixels',
										'astra-sites'
									) }
								</p>
							</Button>
						</div>
					) : (
						''
					) }
					{ '' === siteLogo.url && (
						<div className="logo-skip-info">
							<h5>
								{ __(
									'Don’t have a logo? No problem!',
									'astra-sites'
								) }
							</h5>
							<p>
								{ __(
									'You can upload it later',
									'astra-sites'
								) }
							</p>
						</div>
					) }
					<div className="astra-sites-ai-logo-wrap">
						<div className="label">
							{ __( 'Logo Width', 'astra-sites' ) }
						</div>
						<div
							className={ `control-reset ${
								siteLogo.width === initialState.siteLogo.width
									? 'disabled'
									: ''
							}` }
							onClick={ resetLogoWidth }
						>
							{ ICONS.reset }
						</div>
					</div>
					<div className="astra-sites-ai-rangecontrol-wrap">
						<RangeControl
							value={ siteLogo.width }
							min={ 0 }
							max={ 500 }
							step={ 1 }
							onChange={ ( width ) => {
								onWidthChange( width );
							} }
							trackColor="#4361ee"
							disabled={ '' !== siteLogo.url ? false : true }
						/>
					</div>
				</>
			) }
		/>
	);
};

export default MediaUploader;
