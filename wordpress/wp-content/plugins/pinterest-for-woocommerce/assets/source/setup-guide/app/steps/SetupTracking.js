/**
 * External dependencies
 */
import { sprintf, __ } from '@wordpress/i18n';
import {
	useEffect,
	useState,
	useCallback,
	createInterpolateElement,
} from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { getNewPath } from '@woocommerce/navigation';
import { recordEvent } from '@woocommerce/tracks';
import {
	Button,
	Card,
	CardBody,
	SelectControl,
	CheckboxControl,
	Spinner,
	__experimentalText as Text, // eslint-disable-line @wordpress/no-unsafe-wp-apis --- _experimentalText unlikely to change/disappear and also used by WC Core
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import AdsCreditsPromo from './components/AdsCreditsPromo';
import StepHeader from '../components/StepHeader';
import StepOverview from '../components/StepOverview';
import ThirdPartyTagsNotice from '../components/ThirdPartyTagsNotice';
import {
	useSettingsSelect,
	useSettingsDispatch,
	useCreateNotice,
} from '../helpers/effects';
import connectAdvertiser from '../helpers/connect-advertiser';
import documentationLinkProps from '../helpers/documentation-link-props';

/**
 * Tracking setup component.
 *
 * To be used in onboarding stepper.
 *
 * @fires wcadmin_pfw_documentation_link_click with `{ link_id: 'ad-guidelines', context: 'wizard'|'settings' }`
 * @fires wcadmin_pfw_documentation_link_click with `{ link_id: 'ad-data-terms', context: 'wizard'|'settings' }`
 * @fires wcadmin_pfw_documentation_link_click with `{ link_id: 'ad-terms-of-service', context: 'wizard'|'settings' }`
 * @fires wcadmin_pfw_documentation_link_click with `{ link_id: 'install-tag', context: 'wizard'|'settings' }`
 * @fires wcadmin_pfw_documentation_link_click with `{ link_id: 'automatic-enhanced-match', context: 'wizard'|'settings' }`
 *
 * @fires wcadmin_pfw_setup with `{ target: 'complete', trigger: 'setup-tracking-complete' }` when "Complete setup" button is clicked.
 * @fires wcadmin_pfw_setup with `{ target: 'fetch-tags' | 'fetch-advertisers', trigger: 'setup-tracking-try-again' }` when "Try again" button is clicked.
 *
 * @param {Object} props React props.
 * @param {string} [props.view='settings'] `'wizard'|'settings'` Kind of view to render.
 * @return {JSX.Element} rendered component
 */
const SetupTracking = ( { view = 'settings' } ) => {
	const [ isSaving, setIsSaving ] = useState( false );
	const [ isFetching, setIsFetching ] = useState( false );
	const [ status, setStatus ] = useState( 'idle' );
	const [ termsAgreed, setTermsAgreed ] = useState( false );
	const [ advertisersList, setAdvertisersList ] = useState();
	const [ tagsList, setTagsList ] = useState();
	const appSettings = useSettingsSelect();
	const setAppSettings = useSettingsDispatch( view === 'wizard' );
	const createNotice = useCreateNotice();

	useEffect( () => {
		if (
			! isFetching &&
			undefined !== appSettings &&
			undefined === advertisersList &&
			status !== 'error'
		) {
			fetchAdvertisers();
		}

		if (
			advertisersList &&
			tagsList &&
			appSettings?.tracking_advertiser &&
			appSettings?.tracking_tag
		) {
			setStatus( 'success' );
		}
	}, [
		appSettings,
		advertisersList,
		status,
		fetchAdvertisers,
		isFetching,
		tagsList,
	] );

	const fetchAdvertisers = useCallback( async () => {
		setIsFetching( true );

		try {
			setAdvertisersList();

			const results = await apiFetch( {
				path:
					wcSettings.pinterest_for_woocommerce.apiRoute +
					'/tagowners/?terms_agreed=' +
					termsAgreed,
				method: 'GET',
			} );

			setAdvertisersList( results.advertisers );

			if ( results.advertisers.length > 0 ) {
				if ( ! appSettings?.tracking_advertiser ) {
					handleOptionChange(
						'tracking_advertiser',
						results.advertisers[ 0 ].id
					);
				} else {
					fetchTags( appSettings?.tracking_advertiser );
				}
				setTermsAgreed( true );
			} else {
				setStatus( 'notAgreed' );
			}
		} catch ( error ) {
			setStatus( 'error' );
			createNotice(
				'error',
				error.message ||
					__(
						'Couldn’t retrieve your advertisers.',
						'pinterest-for-woocommerce'
					)
			);
		}

		setIsFetching( false );
	}, [
		fetchTags,
		appSettings,
		createNotice,
		handleOptionChange,
		termsAgreed,
		setTermsAgreed,
	] );

	const fetchTags = useCallback(
		async ( advertiserId ) => {
			setIsFetching( true );

			try {
				setTagsList();

				const results = await apiFetch( {
					path:
						wcSettings.pinterest_for_woocommerce.apiRoute +
						'/tags/?advrtsr_id=' +
						advertiserId,
					method: 'GET',
				} );

				setTagsList( results );

				if ( Object.keys( results ).length > 0 ) {
					if (
						! appSettings?.tracking_tag ||
						typeof results[ appSettings?.tracking_tag ] ===
							'undefined'
					) {
						const tagsKeys = Object.keys( results );
						handleOptionChange(
							'tracking_tag',
							results[ tagsKeys[ 0 ] ].id
						);
					}
				} else {
					setStatus( 'error' );
				}

				if ( appSettings?.tracking_tag ) {
					setStatus( 'success' );
				}
			} catch ( error ) {
				setStatus( 'error' );
				createNotice(
					'error',
					error.message ||
						__(
							'Couldn’t retrieve your tags.',
							'pinterest-for-woocommerce'
						)
				);
			}

			setIsFetching( false );
		},
		[
			appSettings,
			createNotice,
			setIsFetching,
			setStatus,
			handleOptionChange,
		]
	);

	const handleOptionChange = useCallback(
		async ( name, value ) => {
			if ( name === 'tracking_advertiser' ) {
				fetchTags( value );
			}

			if (
				appSettings?.tracking_advertiser &&
				appSettings?.tracking_tag
			) {
				setStatus( 'success' );
			} else {
				setStatus( 'idle' );
			}

			await saveOptions( name, value );
		},
		[ fetchTags, setStatus, appSettings, saveOptions ]
	);

	const saveOptions = useCallback(
		async ( name, value ) => {
			setIsSaving( true );

			try {
				await setAppSettings( {
					[ name ]: value ?? ! appSettings[ name ],
				} );
			} catch ( error ) {
				createNotice(
					'error',
					__(
						'There was a problem saving your settings.',
						'pinterest-for-woocommerce'
					)
				);
			}

			setIsSaving( false );
		},
		[ appSettings, setIsSaving, setAppSettings, createNotice ]
	);

	const handleTryAgain = () => {
		setStatus( 'idle' );

		if ( appSettings.tracking_advertiser ) {
			recordEvent( 'pfw_setup', {
				target: 'fetch-tags',
				trigger: 'setup-tracking-try-again',
			} );
			fetchTags( appSettings.tracking_advertiser );
		} else {
			recordEvent( 'pfw_setup', {
				target: 'fetch-advertisers',
				trigger: 'setup-tracking-try-again',
			} );
			fetchAdvertisers();
		}
	};

	const StepButton = () => {
		if ( status === 'idle' ) {
			return '';
		}

		const buttonLabels = {
			notAgreed: __( 'Continue', 'pinterest-for-woocommerce' ),
			error: __( 'Try Again', 'pinterest-for-woocommerce' ),
			success: __( 'Complete Setup', 'pinterest-for-woocommerce' ),
		};

		return (
			<Button
				isPrimary
				disabled={ isSaving || ( ! termsAgreed && status !== 'error' ) }
				onClick={
					status === 'success' ? handleCompleteSetup : handleTryAgain
				}
			>
				{ buttonLabels[ status ] }
			</Button>
		);
	};

	const handleCompleteSetup = async () => {
		try {
			const result = await connectAdvertiser(
				appSettings.tracking_advertiser,
				appSettings.tracking_tag,
				true
			);

			if ( result ) {
				createNotice(
					'success',
					__(
						'The advertiser was connected successfully.',
						'pinterest-for-woocommerce'
					)
				);
			}

			recordEvent( 'pfw_setup', {
				target: 'complete',
				trigger: 'setup-tracking-complete',
			} );

			// Force reload WC admin page to initiate the relevant dependencies of the Dashboard page.
			const path = getNewPath( {}, '/pinterest/settings', {} );

			window.location = new URL( wcSettings.adminUrl + path );
		} catch ( error ) {
			createNotice(
				'error',
				__(
					'There was a problem connecting the advertiser.',
					'pinterest-for-woocommerce'
				)
			);
		}
	};

	return (
		<div className="woocommerce-setup-guide__setup-tracking">
			{ view === 'wizard' && (
				<StepHeader
					title={ __(
						'Track conversions with the Pinterest tag',
						'pinterest-for-woocommerce'
					) }
					subtitle={ __( 'Step Three', 'pinterest-for-woocommerce' ) }
				/>
			) }

			<div className="woocommerce-setup-guide__step-columns">
				<div className="woocommerce-setup-guide__step-column">
					<StepOverview
						title={
							view === 'wizard'
								? __(
										'Select your advertiser and tag',
										'pinterest-for-woocommerce'
								  )
								: __(
										'Track conversions with the Pinterest tag',
										'pinterest-for-woocommerce'
								  )
						}
						description={
							<>
								{ createInterpolateElement(
									__(
										'The <linkTag>Pinterest tag</linkTag> is a piece of JavaScript code you put on your website to gather conversion insights and build audiences to target based on actions people have taken on your site.',
										'pinterest-for-woocommerce'
									),
									{
										linkTag: (
											<Button
												isLink
												{ ...documentationLinkProps( {
													href:
														wcSettings
															.pinterest_for_woocommerce
															.pinterestLinks
															.installTag,
													linkId: 'install-tag',
													context: view,
												} ) }
											></Button>
										),
									}
								) }
								<br />
								<br />
								{ createInterpolateElement(
									__(
										'Using conversion tags means you agree to our <linkGuidelines>Ad Guidelines</linkGuidelines> and <linkTerms>Ad Data Terms</linkTerms>.',
										'pinterest-for-woocommerce'
									),
									{
										linkGuidelines: (
											<Button
												isLink
												{ ...documentationLinkProps( {
													href:
														wcSettings
															.pinterest_for_woocommerce
															.pinterestLinks
															.adGuidelines,
													linkId: 'ad-guidelines',
													context: view,
												} ) }
											></Button>
										),
										linkTerms: (
											<Button
												isLink
												{ ...documentationLinkProps( {
													href:
														wcSettings
															.pinterest_for_woocommerce
															.pinterestLinks
															.adDataTerms,
													linkId: 'ad-data-terms',
													context: view,
												} ) }
											></Button>
										),
									}
								) }
								<br />
								<br />
								{ createInterpolateElement(
									__(
										'<linkAem>Automatic Enhanced Match</linkAem> is enabled by default to match more of your website visitors and conversions to people on Pinterest. You can manage this in Settings.',
										'pinterest-for-woocommerce'
									),
									{
										linkAem: (
											<Button
												isLink
												{ ...documentationLinkProps( {
													href:
														wcSettings
															.pinterest_for_woocommerce
															.pinterestLinks
															.automaticEnhancedMatch,
													linkId:
														'automatic-enhanced-match',
													context: view,
												} ) }
											></Button>
										),
									}
								) }
							</>
						}
					/>
					{ view === 'wizard' && <AdsCreditsPromo /> }
				</div>
				<div className="woocommerce-setup-guide__step-column">
					<Card>
						{ undefined !== appSettings &&
						Object.keys( appSettings ).length > 0 &&
						undefined !== advertisersList ? (
							<CardBody size="large">
								<ThirdPartyTagsNotice />
								{ advertisersList.length > 0 ? (
									<>
										<SelectControl
											label={ __(
												'Advertiser',
												'pinterest-for-woocommerce'
											) }
											labelPosition="top"
											value={
												appSettings.tracking_advertiser
											}
											onChange={ ( selectedAdvertiser ) =>
												handleOptionChange(
													'tracking_advertiser',
													selectedAdvertiser
												)
											}
											options={ advertisersList.map(
												( item ) => ( {
													label: sprintf(
														'%1$s (%2$d)',
														item.name,
														item.id
													),
													value: item.id,
												} )
											) }
											help={ __(
												'Select the advertiser for which you would like to install a tracking snippet.',
												'pinterest-for-woocommerce'
											) }
										/>

										{ undefined !==
											appSettings.tracking_advertiser &&
											( undefined !== tagsList ? (
												Object.keys( tagsList ).length >
													0 && (
													<>
														<SelectControl
															label={ __(
																'Tracking Tag',
																'pinterest-for-woocommerce'
															) }
															labelPosition="top"
															value={
																appSettings.tracking_tag
															}
															onChange={ (
																selectedTag
															) =>
																handleOptionChange(
																	'tracking_tag',
																	selectedTag
																)
															}
															options={ Object.values(
																tagsList
															).map(
																( item ) => ( {
																	label: sprintf(
																		'%1$s (%2$d)',
																		item.name,
																		item.id
																	),
																	value:
																		item.id,
																} )
															) }
															help={ __(
																'Select the tracking tag to use.',
																'pinterest-for-woocommerce'
															) }
														/>
													</>
												)
											) : (
												<Spinner />
											) ) }
									</>
								) : (
									<>
										<Text
											variant="body"
											className="text-margin"
										>
											{ __(
												'In order to proceed you need to read and accept the contents of the Pinterest Advertising Agreement.',
												'pinterest-for-woocommerce'
											) }
										</Text>

										<CheckboxControl
											label={ createInterpolateElement(
												__(
													'I accept the <link>Pinterest Advertising Agreement</link>',
													'pinterest-for-woocommerce'
												),
												{
													link: (
														<Button
															isLink
															{ ...documentationLinkProps(
																{
																	href:
																		wcSettings
																			.pinterest_for_woocommerce
																			.countryTos
																			.terms_url,
																	linkId:
																		'ad-terms-of-service',
																	context: view,
																}
															) }
														></Button>
													),
												}
											) }
											checked={ termsAgreed }
											className="woocommerce-setup-guide__checkbox-group"
											onChange={ ( agreed ) =>
												setTermsAgreed(
													agreed
														? wcSettings
																.pinterest_for_woocommerce
																.countryTos
																.tos_id
														: false
												)
											}
										/>
									</>
								) }
							</CardBody>
						) : (
							<CardBody size="large">
								{ status === 'error' ? (
									<Text
										variant="body"
										className="errorMessage"
									>
										{ __(
											'An error occurred while attempting to fetch Advertisers & Tags from Pinterest. Please try again.',
											'pinterest-for-woocommerce'
										) }
									</Text>
								) : (
									<Spinner />
								) }
							</CardBody>
						) }
					</Card>

					{ view === 'wizard' && (
						<div className="woocommerce-setup-guide__footer-button">
							<StepButton />
						</div>
					) }
				</div>
			</div>
		</div>
	);
};

export default SetupTracking;
