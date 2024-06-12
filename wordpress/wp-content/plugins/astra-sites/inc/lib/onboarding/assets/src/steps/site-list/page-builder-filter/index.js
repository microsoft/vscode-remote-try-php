import React, { useState } from 'react';
import { ToggleDropdown } from '@brainstormforce/starter-templates-components';
import { __ } from '@wordpress/i18n';
import { useStateValue } from '../../../store/store';
import { initialState } from '../../../store/reducer';
const { imageDir, isElementorDisabled, isBeaverBuilderDisabled } =
	starterTemplates;

import { useDispatch } from '@wordpress/data';
import Tippy from '@tippyjs/react/headless';
import { motion } from 'framer-motion';
import { ArrowRightIcon } from '@heroicons/react/24/outline';
import { WandIcon } from '../../ui/icons';
import { Button } from '../../../components/index';

const zipPlans = astraSitesVars?.zip_plans;
const sitesRemaining = zipPlans?.plan_data?.remaining;
const aiSitesRemainingCount = sitesRemaining?.ai_sites_count;
const allSitesRemainingCount = sitesRemaining?.all_sites_count;
const PageBuilder = ( { placement = 'bottom-end' } ) => {
	const [ { builder, currentIndex, dismissAINotice }, dispatch ] =
		useStateValue();
	const { setLimitExceedModal } = useDispatch( 'ast-block-templates' );
	const [ show, setShow ] = useState(
		dismissAINotice === 'true' ? false : true
	);

	const dismissAIPopup = () => {
		setShow( false );
		const content = new FormData();
		content.append( 'action', 'astra-sites-dismiss-ai-promotion' );
		content.append( '_ajax_nonce', astraSitesVars._ajax_nonce );
		content.append( 'dismiss_ai_promotion', true );
		fetch( ajaxurl, {
			method: 'post',
			body: content,
		} );
		dispatch( {
			type: 'set',
			dismissAINotice: 'true',
		} );
	};

	const dismissAiPopup = () => {
		setShow( false );
	};

	if ( builder === 'fse' ) {
		return null;
	}

	const isLimitReached =
		( typeof aiSitesRemainingCount === 'number' &&
			aiSitesRemainingCount <= 0 ) ||
		( typeof allSitesRemainingCount === 'number' &&
			allSitesRemainingCount <= 0 );

	const buildersList = [
		{
			id: 'gutenberg',
			title: __( 'Block Editor', 'astra-sites' ),
			image: `${ imageDir }block-editor.svg`,
			extraText: '',
		},
		{
			id: 'elementor',
			title: __( 'Elementor', 'astra-sites' ),
			image: `${ imageDir }elementor.svg`,
			extraText: '',
		},
		{
			id: 'beaver-builder',
			title: __( 'Beaver Builder', 'astra-sites' ),
			image: `${ imageDir }beaver-builder.svg`,
			extraText: '',
		},
		{
			id: 'ai-builder',
			title: __( 'AI Website Builder', 'astra-sites' ),
			image: `${ imageDir }ai-builder.svg`,
			extraText: __( 'Hot!', 'astra-sites' ),
		},
	];

	if ( isElementorDisabled === '1' ) {
		// Find the index of the Elementor builder in the array.
		const indexToRemove = buildersList.findIndex(
			( pageBuilder ) => pageBuilder.id === 'elementor'
		);

		// Remove the Elementor builder if it exists.
		if ( indexToRemove !== -1 ) {
			buildersList.splice( indexToRemove, 1 );
		}
	}

	if ( isBeaverBuilderDisabled === '1' ) {
		// Find the index of the Beaver builder in the array.
		const indexToRemove = buildersList.findIndex(
			( pageBuilder ) => pageBuilder.id === 'beaver-builder'
		);

		// Remove the Beaver builder if it exists.
		if ( indexToRemove !== -1 ) {
			buildersList.splice( indexToRemove, 1 );
		}
	}

	const handleBuildWithAIPress = () => {
		if (
			( typeof aiSitesRemainingCount === 'number' &&
				aiSitesRemainingCount <= 0 ) ||
			( typeof allSitesRemainingCount === 'number' &&
				allSitesRemainingCount <= 0 )
		) {
			setLimitExceedModal( {
				open: true,
			} );
			return;
		}
		const content = new FormData();
		content.append( 'action', 'astra-sites-change-page-builder' );
		content.append( '_ajax_nonce', astraSitesVars._ajax_nonce );
		content.append( 'page_builder', 'ai-builder' );
		fetch( ajaxurl, {
			method: 'post',
			body: content,
		} );
		if ( show ) {
			dismissAIPopup();
		}
		window.location.href =
			astraSitesVars.adminURL + 'themes.php?page=ai-builder';
	};

	return (
		<Tippy
			visible={ show }
			render={ ( attrs ) =>
				currentIndex === 2 && (
					<motion.div
						className="flex flex-col items-start gap-5 max-w-[320px] h-auto bg-white rounded-lg shadow-xl p-4"
						{ ...attrs }
					>
						<div
							className="flex-col flex bg-white text-left relative rounded-xl max-w-[356px]"
							tabIndex="0"
						>
							<WandIcon className="w-[24px] h-[24px] text-accent-st-secondary stroke-2" />
							<div className="mt-4 mb-1 text-heading-text flex gap-2">
								<span className="text-base font-semibold leading-1">
									{ __(
										'Have you tried the new AI WordPress Website Builder?',
										'astra-sites'
									) }
								</span>
								<span className="bg-gradient-1 bg-clip-text text-transparent text-xs font-medium text-center leading-3">
									{ __( 'Hot!', 'astra-sites' ) }
								</span>
							</div>
							<div className="zw-sm-normal text-body-text">
								{ ' ' }
								{ __(
									'Experience the future of website building. We offer AI features powered by ZipWP to help you build your website 10x faster.',
									'astra-sites'
								) }{ ' ' }
							</div>
							<div className="pt-4 mt-auto flex flex-col gap-2 items-center">
								<Button
									className="w-full h-10"
									onClick={ handleBuildWithAIPress }
								>
									<span>Try the New AI Builder</span>{ ' ' }
									<ArrowRightIcon className="w-5 h-5 ml-2" />
								</Button>
								<a
									className="w-fill h-hug !text-zip-app-inactive-icon !text-center !text-sm !font-semibold"
									rel="noreferrer"
									onClick={ dismissAIPopup }
								>
									{ __( 'Dismiss', 'astra-sites' ) }
								</a>
							</div>
						</div>
						{ /* Arrow */ }
						<div
							data-popper-arrow
							className="-top-1 absolute w-2 h-2 bg-inherit before:content-[''] before:w-2 before:h-2 before:bg-inherit before:absolute invisible before:visible before:!rotate-45"
						/>
					</motion.div>
				)
			}
			interactive={ true }
			interactiveBorder={ 20 }
			placement={ placement }
		>
			<div className="st-page-builder-filter">
				<ToggleDropdown
					value={ builder }
					options={ buildersList }
					className="st-page-builder-toggle"
					onClick={ ( event, option ) => {
						if ( 'ai-builder' === option.id ) {
							if ( isLimitReached ) {
								setLimitExceedModal( {
									open: true,
								} );
							}
							return ( window.location = `${ astraSitesVars.adminURL }themes.php?page=ai-builder` );
						}
						dispatch( {
							type: 'set',
							builder: option.id,
							siteSearchTerm: '',
							siteBusinessType: initialState.siteBusinessType,
							selectedMegaMenu: initialState.selectedMegaMenu,
							siteType: '',
							siteOrder: 'popular',
							onMyFavorite: false,
							currentIndex: 2,
						} );

						const pageBuilderOptionId =
							isLimitReached && 'ai-builder' === option.id
								? 'gutenberg'
								: option.id;
						const content = new FormData();
						content.append(
							'action',
							'astra-sites-change-page-builder'
						);
						content.append(
							'_ajax_nonce',
							astraSitesVars._ajax_nonce
						);
						content.append( 'page_builder', pageBuilderOptionId );

						fetch( ajaxurl, {
							method: 'post',
							body: content,
						} );
					} }
					dismissAiPopup={ dismissAiPopup }
				/>
			</div>
		</Tippy>
	);
};

export default PageBuilder;
