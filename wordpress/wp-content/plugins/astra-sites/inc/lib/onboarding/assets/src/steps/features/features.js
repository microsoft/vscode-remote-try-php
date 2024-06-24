import { useState } from 'react';
import { __ } from '@wordpress/i18n';
import Button from '../../components/button/button';
import { PreviousStepLink } from '../../components/index';
import { useStateValue } from '../../store/store';
import {
	FunnelIcon,
	HeartIcon,
	PlayCircleIcon,
	SquaresPlusIcon,
	CheckIcon,
	ChatBubbleLeftEllipsisIcon,
	WrenchIcon,
	ArrowLongRightIcon,
} from '@heroicons/react/24/outline';
import { classNames } from '../onboarding-ai/helpers';
import { checkRequiredPlugins } from '../import-site/import-utils';

const ICON_SET = {
	heart: HeartIcon,
	'squares-plus': SquaresPlusIcon,
	funnel: FunnelIcon,
	'play-circle': PlayCircleIcon,
	'live-chat': ChatBubbleLeftEllipsisIcon,
};

const ClassicFeatures = () => {
	const [ { currentIndex }, dispatch ] = useStateValue();
	const storedState = useStateValue();
	const [ siteFeatures, setSiteFeatures ] = useState( [
		{
			title: __( 'Donations', 'astra-sites' ),
			id: 'donations',
			description: __(
				'Collect donations online from your website',
				'astra-sites'
			),
			enabled: false,
			icon: 'heart',
		},
		{
			title: __( 'Automation & Integrations', 'astra-sites' ),
			id: 'automation-integrations',
			description: __( 'Automate your website & tasks', 'astra-sites' ),
			enabled: false,
			icon: 'squares-plus',
		},
		{
			title: __( 'Sales Funnels', 'astra-sites' ),
			id: 'sales-funnels',
			description: __(
				'Boost your sales & maximize your profits',
				'astra-sites'
			),
			enabled: false,
			icon: 'funnel',
		},
		{
			title: __( 'Video Player', 'astra-sites' ),
			id: 'video-player',
			description: __(
				'Showcase your videos on your website',
				'astra-sites'
			),
			enabled: false,
			icon: 'play-circle',
		},
		{
			title: __( 'Free Live Chat', 'astra-sites' ),
			id: 'live-chat',
			description: __(
				'Connect with your website visitors for free',
				'astra-sites'
			),
			enabled: false,
			icon: 'live-chat',
		},
	] );

	const handleToggleFeature = ( featureId ) => () => {
		const updatedFeatures = siteFeatures.map( ( feature ) => {
			if ( feature.id === featureId ) {
				return { ...feature, enabled: ! feature.enabled };
			}
			return feature;
		} );

		setSiteFeatures( updatedFeatures );
	};

	const setNextStep = async () => {
		dispatch( {
			type: 'set',
			currentIndex: currentIndex + 1,
		} );

		storedState[ 0 ].enabledFeatureIds = siteFeatures
			.filter( ( component ) => component.enabled )
			.map( ( component ) => component.id );

		await checkRequiredPlugins( storedState );
	};

	const skipStep = () => {
		dispatch( {
			type: 'set',
			currentIndex: currentIndex + 1,
		} );
	};

	return (
		<div className="grid grid-cols-1 gap-8 auto-rows-auto max-w-[536px] w-full mx-auto">
			<div className="space-y-4">
				<h1 className="text-3xl font-bold text-zip-app-heading">
					{ __( 'Select features', 'astra-sites' ) }
				</h1>
			</div>

			{ /* Feature Cards */ }
			<div className="grid grid-cols-1 lg:grid-cols-1 auto-rows-auto gap-4 w-full bg-background-primary p-8 rounded-md shadow-lg">
				{ siteFeatures.map( ( feature ) => {
					const FeatureIcon = ICON_SET?.[ feature.icon ];
					return (
						<div
							key={ feature.id }
							className={ classNames(
								'relative px-4 py-4 rounded-md border border-solid border-border-tertiary transition-colors duration-150 ease-in-out',
								feature.enabled && 'border-accent-st-secondary'
							) }
						>
							<div className="flex items-start justify-start gap-3">
								<div className="p-0.5 shrink-0">
									{ FeatureIcon && (
										<FeatureIcon className="text-zip-body-text w-7 h-7" />
									) }
									{ ! FeatureIcon && (
										<WrenchIcon className="text-zip-body-text w-7 h-7" />
									) }
								</div>
								<div className="space-y-1 mr-5">
									<p className="p-0 m-0 !text-base !font-semibold !text-zip-app-heading text-left">
										{ feature.title }
									</p>
									<p className="p-0 m-0 !text-sm !font-normal !text-zip-body-text">
										{ feature.description }
									</p>
								</div>
							</div>
							{ /* Check mark */ }
							<span
								className={ classNames(
									'inline-flex absolute top-4 right-4 p-[0.1875rem] border border-solid border-zip-app-inactive-icon rounded',
									feature.enabled &&
										'border-accent-st-secondary bg-accent-st-secondary'
								) }
							>
								<CheckIcon
									className="w-2.5 h-2.5 text-white"
									strokeWidth={ 4 }
								/>
							</span>
							{ /* Click handler overlay */ }
							<div
								className="absolute inset-0 cursor-pointer"
								onClick={ handleToggleFeature( feature.id ) }
							/>
						</div>
					);
				} ) }

				<div className="flex flex-col gap-6 mt-2">
					<Button
						className="w-full flex gap-2 items-center"
						onClick={ setNextStep }
					>
						<span>{ __( 'Continue', 'astra-sites' ) }</span>
						<ArrowLongRightIcon className="w-4 h-4 !fill-none" />
					</Button>
					<a
						className="w-fill h-hug text-zip-body-text no-underline text-base font-normal"
						rel="noreferrer"
						onClick={ skipStep }
					>
						{ __( 'Skip this step', 'astra-sites' ) }
					</a>
				</div>
			</div>
			<PreviousStepLink before>
				{ __( 'Back', 'astra-sites' ) }
			</PreviousStepLink>
		</div>
	);
};

export default ClassicFeatures;
