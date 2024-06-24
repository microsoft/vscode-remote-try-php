import React, { useEffect } from 'react';
import { ArrowRightIcon, ArrowLongLeftIcon } from '@heroicons/react/24/outline';
import { Tooltip } from '@brainstormforce/starter-templates-components';
import { __ } from '@wordpress/i18n';
import { useDispatch } from '@wordpress/data';
import { Button } from '../../../components/index';
import { useStateValue } from '../../../store/store';
import { STORE_KEY } from '../../onboarding-ai/store';
import Logo from '../../../components/logo';
import ICONS from '../../../../icons';
import PageBuilder from '../../site-list/page-builder-filter';
import { Graphics } from './graphics';
import '../../site-list/header/style.scss';

const { adminUrl } = starterTemplates;

const GetStarted = () => {
	const [ , dispatch ] = useStateValue();
	const { setLimitExceedModal } = useDispatch( STORE_KEY );

	const zipPlans = astraSitesVars?.zip_plans;
	const sitesRemaining = zipPlans?.plan_data?.remaining;
	const aiSitesRemainingCount = sitesRemaining?.ai_sites_count;
	const allSitesRemainingCount = sitesRemaining?.all_sites_count;

	useEffect( () => {
		const urlParams = new URLSearchParams( window.location.search );

		const token = urlParams.get( 'token' );
		if ( token ) {
			if (
				( typeof aiSitesRemainingCount === 'number' &&
					aiSitesRemainingCount <= 0 ) ||
				( typeof allSitesRemainingCount === 'number' &&
					allSitesRemainingCount <= 0 )
			) {
				setLimitExceedModal( {
					open: true,
				} );
			} else {
				dispatch( {
					type: 'set',
					currentIndex: 1,
				} );
			}
		}
	}, [] );

	return (
		<div className="flex-1 w-full bg-st-background-secondary">
			{
				<div className="step-header bg-white">
					{
						<div className="row">
							<div className="col">
								<Logo />
							</div>
							<div className="right-col">
								<PageBuilder />
								<div className="col exit-link">
									<a href={ adminUrl }>
										<Tooltip
											content={ __(
												'Exit to Dashboard',
												'astra-sites'
											) }
										>
											{ ICONS.remove }
										</Tooltip>
									</a>
								</div>
							</div>
						</div>
					}

					<canvas
						id="ist-bashcanvas"
						width={ window.innerWidth }
						height={ window.innerHeight }
					/>
				</div>
			}
			<div className="flex w-full mt-7 md:mt-14 lg:mt-28">
				<div className="gap-10 lg:gap-16 flex-wrap lg:flex-nowrap h-full flex items-center justify-center w-full px-8 lg:px-10">
					<div className="flex flex-col items-start justify-center gap-6 order-2 lg:order-1 h-full">
						<h1 className="font-bold">
							{ __(
								'Building a website has never been this easy!',
								'astra-sites'
							) }
						</h1>
						<p className=" m-0 !text-zip-body-text !text-xl !font-normal">
							{ __(
								'Here is how the AI Website Builder works:',
								'astra-sites'
							) }
						</p>
						<ul className="list-decimal ml-6 my-0 !text-zip-body-text !text-xl font-normal">
							<li className="text-start">
								{ __(
									'Create a free account on ZipWP platform.',
									'astra-sites'
								) }
							</li>
							<li className="text-start">
								{ __(
									'Describe your dream website in your own words.',
									'astra-sites'
								) }
							</li>
							<li className="text-start">
								{ __(
									'Watch as AI crafts your WordPress website instantly.',
									'astra-sites'
								) }
							</li>
							<li className="text-start">
								{ __(
									'Refine the website with an easy drag & drop builder.',
									'astra-sites'
								) }
							</li>
							<li className="text-start">
								{ __( 'Launch.', 'astra-sites' ) }
							</li>
						</ul>

						<div className="gap-6 mt-4 mb-10 flex flex-col items-start justify-start">
							<Button
								variant="primary"
								hasSuffixIcon
								onClick={ () => {
									const url =
										wpApiSettings?.zipwp_auth?.screen_url +
										'?type=token&redirect_url=' +
										wpApiSettings?.zipwp_auth
											?.redirect_url +
										'&ask=/register' +
										( wpApiSettings?.zipwp_auth?.partner_id
											? '&aff=' +
											  wpApiSettings?.zipwp_auth
													?.partner_id
											: '' );

									window.location.href = url;
								} }
							>
								<span className="mr-2">{ `Let's Get Started. It's Free` }</span>
								<ArrowRightIcon className="w-5 h-5" />
							</Button>
							<button
								className="flex items-center justify-start gap-2 w-auto p-0 m-0 focus:outline-none bg-transparent border-0 cursor-pointer !text-zip-body-text"
								onClick={ () => {
									dispatch( {
										type: 'set',
										currentIndex: 0,
										builder: 'gutenberg',
									} );
									const content = new FormData();
									content.append(
										'action',
										'astra-sites-change-page-builder'
									);
									content.append(
										'_ajax_nonce',
										astraSitesVars._ajax_nonce
									);
									content.append(
										'page_builder',
										'gutenberg'
									);

									fetch( ajaxurl, {
										method: 'post',
										body: content,
									} );
								} }
							>
								<ArrowLongLeftIcon className="w-5 h-5 text-zip-body-text" />
								<span>{ __( 'Back', 'astra-sites' ) }</span>
							</button>
						</div>
					</div>
					<div className="self-center scale-[0.8] md:scale-100 order-1 lg:order-2">
						<Graphics />
					</div>
				</div>
			</div>
		</div>
	);
};

export default GetStarted;
