import React, { useEffect, useState } from 'react';
import { PhotoIcon, TrashIcon } from '@heroicons/react/24/outline';
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { RangeControl } from '@wordpress/components';
import { MediaUpload } from '@wordpress/media-utils';
import { useStateValue } from '../../../store/store';
import { getDataUri } from '../../../utils/functions';
import { initialState } from '../../../store/reducer';
import ToggleSwitch from './toggle-switch';
import { sendPostMessage } from '../utils/helpers';

const SiteLogo = () => {
	const replaceMediaUpload = () => MediaUpload;
	const [ { aiSiteLogo }, dispatch ] = useStateValue();
	const [ showTitle, setShowTitle ] = useState( true ),
		toggleTitle = () => setShowTitle( ( prev ) => ! prev );

	addFilter(
		'editor.MediaUpload',
		'core/edit-post/components/media-upload/replace-media-upload',
		replaceMediaUpload
	);

	const onSelectImage = ( media ) => {
		const mediaData = {
			id: media.id,
			url: media.url,
			width: aiSiteLogo.width,
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

	const dispatchPostMessage = ( action, data ) => {
		sendPostMessage(
			{
				param: action,
				data,
			},
			'astra-starter-templates-preview'
		);
	};

	const updateValues = ( data ) => {
		dispatch( {
			type: 'set',
			aiSiteLogo: data,
		} );

		dispatchPostMessage( 'siteLogo', data );
	};

	const removeImage = () => {
		updateValues( initialState.aiSiteLogo );
	};

	const onWidthChange = ( width ) => {
		const newLogoOptions = {
			...aiSiteLogo,
			width,
		};

		dispatch( {
			type: 'set',
			aiSiteLogo: newLogoOptions,
		} );

		dispatchPostMessage( 'siteLogo', newLogoOptions );
	};

	const handleOnChangeToggleTitle = () => {
		dispatchPostMessage( 'siteTitle', ! showTitle );
		toggleTitle();
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
		<>
			<h5 className="!text-zip-dark-theme-heading !text-sm !font-semibold !mb-1">
				{ __( 'Site Logo', 'astra-sites' ) }
			</h5>
			<MediaUpload
				onSelect={ ( media ) => onSelectImage( media ) }
				allowedTypes={ [ 'image' ] }
				value={ aiSiteLogo.id }
				render={ ( { open } ) => (
					<div className="space-y-3">
						{ !! aiSiteLogo.url && (
							<div className="w-full py-2.5 px-3 flex items-start justify-start gap-3 rounded-md border border-solid border-zip-dark-theme-border bg-transparent">
								<div className="w-full flex items-center justify-between">
									<div className="flex items-center justify-center rounded-sm bg-zip-dark-theme-border p-1">
										<img
											className="w-8 h-8 object-contain"
											alt={ __(
												'Site Logo',
												'astra-sites'
											) }
											src={ aiSiteLogo.url }
										/>
									</div>
									<div className="flex items-center justify-end gap-4">
										<button
											onClick={ open }
											className="inline-flex border-0 focus:outline-none bg-transparent text-sm font-normal text-zip-dark-theme-body cursor-pointer"
										>
											{ __( 'Change', 'astra-sites' ) }
										</button>
										<button
											onClick={ removeImage }
											className="inline-flex border-0 focus:outline-none bg-transparent cursor-pointer"
										>
											<TrashIcon className="h-5 w-5 text-alert-error" />
										</button>
									</div>
								</div>
							</div>
						) }

						{ ! aiSiteLogo.url && (
							<button
								className="w-full py-2.5 px-3 flex items-start justify-start gap-3 rounded-md border border-solid border-zip-dark-theme-border bg-transparent cursor-pointer"
								onClick={ open }
							>
								<PhotoIcon className="h-5 w-5 text-zip-app-inactive-icon" />
								<div className="space-y-5">
									<p className="text-start !text-white !text-sm !font-normal !leading-5 !m-0">
										{ __(
											'Upload File Here',
											'astra-sites'
										) }
									</p>
								</div>
							</button>
						) }

						{ aiSiteLogo.url && (
							<div className="flex items-center justify-between gap-2">
								<span className="text-sm font-normal text-zip-dark-theme-body">
									{ __( 'Show site title', 'astra-sites' ) }
								</span>
								<ToggleSwitch
									value={ showTitle }
									onChange={ handleOnChangeToggleTitle }
									requiredClass={
										showTitle
											? 'bg-accent-st'
											: 'bg-zip-dark-theme-border'
									}
								/>
							</div>
						) }

						{ aiSiteLogo.url && (
							<>
								<div className="flex items-center justify-between gap-2">
									<div className="flex-1 text-sm font-normal text-zip-dark-theme-body">
										{ __( 'Logo width', 'astra-sites' ) }
									</div>
									<div className="w-20 [&_.components\-base\-control\_\_field]:mb-0">
										<RangeControl
											className="[&_.components\-range\-control\_\_thumb-wrapper]:border [&_.components\-range\-control\_\_thumb-wrapper]:border-solid [&_.components\-range\-control\_\_thumb-wrapper]:border-white [&_.components\-range\-control\_\_thumb-wrapper]:w-[14px] [&_.components\-range\-control\_\_thumb-wrapper]:h-[14px] [&_.components\-range\-control\_\_thumb-wrapper]:mt-2"
											value={ aiSiteLogo.width }
											min={ 50 }
											max={ 250 }
											step={ 1 }
											onChange={ ( width ) => {
												onWidthChange( width );
											} }
											trackColor="#3D4592"
											color="#3D4592"
											railColor="#FFFFFF"
											disabled={
												'' !== aiSiteLogo.url
													? false
													: true
											}
											withInputField={ false }
										/>
									</div>
									<div className="w-16 flex items-center justify-center gap-1 px-2 py-1 pointer-events-none">
										<span className="text-sm font-normal text-zip-dark-theme-body">
											{ aiSiteLogo.width }
										</span>
										<span className="text-sm font-normal text-zip-app-inactive-icon">
											px
										</span>
									</div>
								</div>
								<hr className="my-6 border-b-0 border-t border-zip-dark-theme-border w-full" />
							</>
						) }
					</div>
				) }
			/>
		</>
	);
};

export default SiteLogo;
