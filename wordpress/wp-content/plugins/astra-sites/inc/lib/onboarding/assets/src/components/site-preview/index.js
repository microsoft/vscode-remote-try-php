import React, { memo, useEffect, useState } from 'react';
import { sendPostMessage } from '../../utils/functions';
import { useStateValue } from '../../store/store';
import { prependHTTPS } from '../../utils/prepend-https';
import { stripSlashes } from '../../utils/strip-slashes';
import { addTrailingSlash } from '../../utils/add-trailing-slash';
import SiteSkeleton from './site-skeleton';
import { classNames } from '../../steps/onboarding-ai/helpers';

const SitePreview = () => {
	const [ { templateResponse, siteLogo } ] = useStateValue();
	const [ previewUrl, setPreviewUrl ] = useState( '' );
	const [ loading, setLoading ] = useState( true );

	useEffect( () => {
		const url = templateResponse
			? templateResponse[ 'astra-site-url' ]
			: '';

		if ( url !== '' ) {
			setPreviewUrl(
				addTrailingSlash( prependHTTPS( stripSlashes( url ) ) )
			);
		}
	}, [ templateResponse ] );

	useEffect( () => {
		if ( loading !== false ) {
			return;
		}

		sendPostMessage( {
			param: 'cleanStorage',
			data: siteLogo,
		} );
	}, [ loading ] );

	const handleIframeLoading = () => {
		setLoading( false );
	};

	const renderBrowserFrame = () => (
		<div
			className={ classNames(
				'flex items-center justify-start py-3 px-4 bg-browser-bar shadow-sm rounded-t-lg mx-auto h-[44px] z-[1] relative',
				'w-full mx-0'
			) }
		>
			<div className="flex gap-2 py-[3px] w-20">
				<div className="w-[14px] h-[14px] border border-solid border-border-primary rounded-full" />
				<div className="w-[14px] h-[14px] border border-solid border-border-primary rounded-full" />
				<div className="w-[14px] h-[14px] border border-solid border-border-primary rounded-full" />
			</div>
		</div>
	);

	return (
		<>
			{ loading ? <SiteSkeleton /> : null }
			{ previewUrl !== '' && (
				<div className="w-full h-full p-8">
					<div className="h-full relative overflow-hidden shadow-template-preview w-full mx-0">
						{ renderBrowserFrame() }
						<iframe
							id="astra-starter-templates-preview"
							title="Website Preview"
							height="100%"
							width="100%"
							src={ previewUrl }
							onLoad={ handleIframeLoading }
						/>
					</div>
				</div>
			) }
		</>
	);
};

export default memo( SitePreview );
