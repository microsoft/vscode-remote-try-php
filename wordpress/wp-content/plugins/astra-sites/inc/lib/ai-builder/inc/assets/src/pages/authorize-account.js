import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { ArrowRightIcon, ArrowLongLeftIcon } from '@heroicons/react/24/outline';
import { useDispatch } from '@wordpress/data';
import Button from '../components/button';
import { STORE_KEY } from '../store';
import { Graphics } from '../ui/graphics';
import { useNavigateSteps } from '../router';
import Header from '../components/header';

const GetStarted = () => {
	const { nextStep } = useNavigateSteps();

	const { setLimitExceedModal } = useDispatch( STORE_KEY );

	const authenticated = aiBuilderVars?.zip_token_exists;
	const zipPlans = aiBuilderVars?.zip_plans;
	const sitesRemaining = zipPlans?.plan_data?.remaining;
	const aiSitesRemainingCount = sitesRemaining?.ai_sites_count;
	const allSitesRemainingCount = sitesRemaining?.all_sites_count;

	useEffect( () => {
		const urlParams = new URLSearchParams( window.location.search );

		const token = urlParams.get( 'token' );
		if ( token || authenticated ) {
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
				nextStep();
			}
		}
	}, [] );

	return (
		<div className="flex-1 w-full bg-st-background-secondary">
			<Header />
			<div className="flex w-full mt-7 md:mt-14 lg:mt-28">
				<div className="gap-10 lg:gap-16 flex-wrap lg:flex-nowrap h-full flex items-center justify-center w-full px-8 lg:px-10">
					<div className="flex flex-col items-start justify-center gap-6 order-2 lg:order-1 h-full">
						<h1 className="font-semibold">
							{ __(
								'Building a website has never been this easy!',
								'ai-builder'
							) }
						</h1>
						<p className=" m-0 !text-zip-body-text !text-xl !font-normal">
							{ __(
								'Here is how the AI Website Builder works:',
								'ai-builder'
							) }
						</p>
						<ul className="list-decimal ml-6 my-0 !text-zip-body-text font-normal space-y-1.5">
							<li className="text-start text-xl">
								{ __(
									'Create a free account on ZipWP platform.',
									'ai-builder'
								) }
							</li>
							<li className="text-start text-xl">
								{ __(
									'Describe your dream website in your own words.',
									'ai-builder'
								) }
							</li>
							<li className="text-start text-xl">
								{ __(
									'Watch as AI crafts your WordPress website instantly.',
									'ai-builder'
								) }
							</li>
							<li className="text-start text-xl">
								{ __(
									'Refine the website with an easy drag & drop builder.',
									'ai-builder'
								) }
							</li>
							<li className="text-start text-xl">
								{ __( 'Launch.', 'ai-builder' ) }
							</li>
						</ul>

						<div className="gap-6 mt-4 mb-10 flex flex-col items-start justify-start">
							<Button
								className="bg-accent-st-secondary text-white"
								variant="blank"
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
								<span className="mr-2">
									{ __(
										"Let's Get Started. It's Free",
										'ai-builder'
									) }
								</span>
								<ArrowRightIcon className="w-5 h-5" />
							</Button>
							<button
								className="flex items-center justify-start gap-2 w-auto p-0 m-0 focus:outline-none bg-transparent border-0 cursor-pointer !text-zip-body-text"
								onClick={ () => {
									window.location.href =
										aiBuilderVars.adminUrl +
										'themes.php?page=starter-templates';
								} }
							>
								<ArrowLongLeftIcon className="w-5 h-5 text-zip-body-text" />
								<span>{ __( 'Back', 'ai-builder' ) }</span>
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
