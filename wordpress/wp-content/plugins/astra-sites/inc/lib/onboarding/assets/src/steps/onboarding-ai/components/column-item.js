import { useEffect, useRef } from 'react';
import { useSelect, useDispatch } from '@wordpress/data';
import { classNames } from '../helpers';
import { STORE_KEY } from '../store';
import { addHttps, sendPostMessage } from '../utils/helpers';
import { useStateValue } from '../../../store/store';
import TemplateInfo from './template-info';
import DotsLoader from './dots-loader';
import { siteLogoDefault } from '../../../store/reducer';
import { __ } from '@wordpress/i18n';

export const ColumnItem = ( { template, isRecommended, position } ) => {
	const [ , dispatch ] = useStateValue();
	const { businessName, selectedImages, templateList } = useSelect(
		( select ) => {
			const { getAIStepData } = select( STORE_KEY );
			return getAIStepData();
		}
	);

	const { setWebsiteSelectedTemplateAIStep } = useDispatch( STORE_KEY );
	const containerRef = useRef( null );
	const loadingSkeleton = useRef( null );

	const url = template.domain + '?preview_demo=yes';

	const handleScaling = () => {
		if ( ! containerRef.current ) {
			return;
		}
		const container = containerRef.current;
		const firstChild = container.firstChild;
		const childWidth = firstChild.offsetWidth;
		const containerWidth = container.offsetWidth;

		const scaleValue = containerWidth / childWidth;

		firstChild.style.transform = `scale(${ scaleValue })`;
		firstChild.style.height = container.offsetHeight / scaleValue + 'px';
	};

	useEffect( () => {
		handleScaling();
	}, [] );

	useEffect( () => {
		window.addEventListener( 'resize', handleScaling );

		return () => {
			window.removeEventListener( 'resize', handleScaling );
		};
	}, [] );

	const handleRemoveLoadingSkeleton = ( uuid ) => {
		if ( ! loadingSkeleton.current ) {
			return;
		}

		if ( 0 === selectedImages.length ) {
			selectedImages.push( astraSitesVars?.placeholder_images[ 0 ] );
			selectedImages.push( astraSitesVars?.placeholder_images[ 1 ] );
		}

		sendPostMessage(
			{
				param: 'images',
				data: {
					...selectedImages,
				},
			},
			uuid
		);

		const templateData = templateList.find(
			( site ) => site.uuid === uuid
		);

		if ( templateData?.content ) {
			sendPostMessage(
				{
					param: 'content',
					data: templateData.content,
					businessName,
				},
				uuid
			);
		}

		setTimeout( () => {
			if ( loadingSkeleton.current ) {
				loadingSkeleton.current.remove();
			}
		}, 1000 );
	};

	const hoverScrollTimeout = useRef( null );

	return (
		<div
			className={ classNames(
				'w-full border border-border-tertiary border-solid'
			) }
		>
			<div
				className={ classNames(
					'w-full relative h-fit bg-zip-app-highlight-bg'
				) }
			>
				<div
					ref={ containerRef }
					key={ template.uuid }
					className="w-full aspect-[164/179] relative overflow-hidden bg-neutral-300"
				>
					<div className="scale-[0.33] w-[1440px] h-full absolute left-0 top-0 origin-top-left">
						<iframe
							title={ template?.domain }
							className="absolute w-[1440px] h-full"
							src={ addHttps( url ) }
							onLoad={ () =>
								handleRemoveLoadingSkeleton( template.uuid )
							}
							frameBorder="0"
							scrolling="no"
							id={ template.uuid }
						/>
					</div>
					{ isRecommended && (
						<div
							className="absolute top-3 right-5 h-6 zw-xs-semibold text-white flex items-center
                        justify-center rounded-3xl bg-outline-color px-3 pointer-events-none"
						>
							Recommended
						</div>
					) }
					<div
						className="absolute inset-0 w-full h-full bg-transparent cursor-pointer"
						onClick={ () => {
							setWebsiteSelectedTemplateAIStep( template.uuid );
							dispatch( {
								type: 'set',
								aiActivePallette: null,
								aiActiveTypography: null,
								aiSiteLogo: siteLogoDefault,
							} );
						} }
						onMouseEnter={ () => {
							hoverScrollTimeout.current = setTimeout( () => {
								sendPostMessage(
									{
										param: 'template-hover',
										data: {
											action: 'scroll-start',
										},
									},
									template.uuid
								);
							}, 300 );
						} }
						onMouseLeave={ () => {
							clearTimeout( hoverScrollTimeout.current );
							sendPostMessage(
								{
									param: 'template-hover',
									data: {
										action: 'scroll-stop',
									},
								},
								template.uuid
							);
						} }
					/>
				</div>
				<div className="relative h-14">
					<TemplateInfo template={ template } position={ position } />
				</div>
				<div
					ref={ loadingSkeleton }
					className="absolute inset-0 flex flex-col bg-white items-center"
				>
					<div className="w-full flex items-center p-4 space-x-5">
						<div
							data-placeholder
							className="h-5 w-10 rounded-full overflow-hidden relative bg-gray-200"
						/>
						<div className="w-full flex justify-between items-center gap-2">
							<div
								data-placeholder
								className="h-5 w-1/3 overflow-hidden relative bg-gray-200 rounded-md"
							/>
							<div
								data-placeholder
								className="h-5 w-1/3 overflow-hidden relative bg-gray-200 rounded-md"
							/>
							<div
								data-placeholder
								className="h-5 w-1/3 overflow-hidden relative bg-gray-200 rounded-md"
							/>
						</div>
					</div>
					<div
						data-placeholder
						className="flex items-center justify-center gap-2 h-52 w-full overflow-hidden relative bg-gray-200"
					>
						<DotsLoader />
						<p className="!text-base !font-normal !text-zip-app-heading select-none">
							{ __( 'Generating preview…', 'astra-sites' ) }
						</p>
					</div>

					<div className="w-full flex flex-col p-4 space-y-2">
						<div
							data-placeholder
							className="flex h-3 w-10/12 overflow-hidden relative bg-gray-200 rounded"
						/>
						<div
							data-placeholder
							className="flex h-3 w-10/12 overflow-hidden relative bg-gray-200 rounded"
						/>
						<div
							data-placeholder
							className="flex h-3 w-1/2 overflow-hidden relative bg-gray-200 rounded"
						/>
					</div>
					<div className="w-full h-px  overflow-hidden relative bg-gray-200 m-4" />
					<div className="flex justify-between items-center p-4 w-full gap-3">
						<div
							data-placeholder
							className="h-14 w-1/3 rounded-md overflow-hidden relative bg-gray-200"
						/>
						<div
							data-placeholder
							className="h-14 w-1/3 rounded-md overflow-hidden relative bg-gray-200"
						/>
						<div
							data-placeholder
							className="h-14 w-1/3 rounded-md overflow-hidden relative bg-gray-200"
						/>
					</div>
					<div className="flex justify-between items-end flex-1 w-full">
						<div
							data-placeholder
							className="h-5 w-full overflow-hidden relative bg-gray-200"
						/>
					</div>
				</div>
			</div>
		</div>
	);
};
