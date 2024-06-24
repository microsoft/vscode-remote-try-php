import React, { useState } from 'react';
import Tooltip from '../onboarding-ai/components/tooltip';
import { __ } from '@wordpress/i18n';
import { PreviousStepLink, DefaultStep } from '../../components/index';
import ICONS from '../../../icons';
import { useStateValue } from '../../store/store';
import { checkRequiredPlugins } from '../../steps/import-site/import-utils';
import SurveyForm from './survey';
import AdvancedSettings from './advanced-settings';
import './style.scss';
const { phpVersion, analytics } = starterTemplates;

const Survey = () => {
	const storedState = useStateValue();
	const [
		{
			currentIndex,
			builder,
			requiredPlugins,
			analyticsFlag,
			shownRequirementOnce,
			pluginInstallationAttempts,
			fileSystemPermissions,
		},
		dispatch,
	] = storedState;

	const notInstalled = requiredPlugins?.required_plugins?.notinstalled;
	const notActivated = requiredPlugins?.required_plugins?.inactive;
	const allPuginList = [];
	if ( notInstalled.length > 0 ) {
		notInstalled.map( ( plugin ) => {
			return allPuginList.push( {
				plugin,
				action: __( 'Install & Activate', 'astra-sites' ),
			} );
		} );
	}

	if ( notActivated.length > 0 ) {
		notActivated.map( ( plugin ) => {
			return allPuginList.push( {
				plugin,
				action: __( 'Activate', 'astra-sites' ),
			} );
		} );
	}

	const manualPluginInstallation = () => {
		return (
			<form className="install-plugins-form" onSubmit={ recheckPlugins }>
				<h1>{ __( 'Required plugins missing', 'astra-sites' ) }</h1>
				<p>
					{ __(
						'Some required plugins could not be able to be installed/activated due to some limitations coming from this website’s hosting service provider.',
						'astra-sites'
					) }
				</p>
				<p>
					{ __(
						'We request you to please install/update the following plugins to proceed.',
						'astra-sites'
					) }
				</p>
				<h5>{ __( 'Required plugins -', 'astra-sites' ) }</h5>
				<ul className="manual-required-plugins-list">
					{ allPuginList.map( ( value, index ) => {
						return (
							<li key={ index }>
								{ value.plugin.name }
								&nbsp;-&nbsp;
								<a
									href={ value.plugin?.action }
									target="_blank"
									rel="noreferrer"
								>
									{ value.action }
								</a>
							</li>
						);
					} ) }
				</ul>
				<button
					type="submit"
					className="submit-survey-btn button-text d-flex-center-align"
				>
					{ __( 'Start Importing', 'astra-sites' ) }
					{ ICONS.arrowRight }
				</button>
			</form>
		);
	};

	const thirtPartyPlugins =
		requiredPlugins !== null
			? requiredPlugins.third_party_required_plugins
			: [];
	const isThirtPartyPlugins = thirtPartyPlugins.length > 0;

	const [ skipPlugins, setSkipPlugins ] = useState( isThirtPartyPlugins );

	const compatibilities = astraSitesVars.compatibilities;
	const requirementsErrors = compatibilities.errors;
	let requirementWarning = compatibilities.warnings;

	if (
		requiredPlugins &&
		requiredPlugins.update_avilable_plugins.length > 0
	) {
		const updatePluginsList = [];
		requiredPlugins.update_avilable_plugins.map( ( plugin ) => {
			return updatePluginsList.push( plugin.name );
		} );

		const output = [ '<ul>' ];
		updatePluginsList.forEach( function ( item ) {
			output.push( '<li>' + item + '</li>' );
		} );
		output.push( '</ul>' );

		const tooltipString =
			astraSitesVars.compatibilities_data[ 'update-available' ];
		tooltipString.tooltip = tooltipString.tooltip.replace(
			'##LIST##',
			output.join( '' )
		);

		requirementWarning = {
			...requirementWarning,
			'update-available': tooltipString,
		};
	}

	let requirementsFlag;
	if ( shownRequirementOnce === true ) {
		requirementsFlag = false;
	} else {
		requirementsFlag =
			Object.keys( requirementsErrors ).length > 0 ||
			Object.keys( requirementWarning ).length > 0;
	}

	const [ showRequirementCheck, setShowRequirementCheck ] =
		useState( requirementsFlag );

	const [ formDetails, setFormDetails ] = useState( {
		first_name: '',
		email: '',
		wp_user_type: '',
		build_website_for: '',
		opt_in: true,
	} );

	const updateFormDetails = ( field, value ) => {
		setFormDetails( ( prevState ) => ( {
			...prevState,
			[ field ]: value,
		} ) );
	};

	const setStartFlag = () => {
		const content = new FormData();
		content.append( 'action', 'astra-sites-set_start_flag' );
		content.append( '_ajax_nonce', astraSitesVars._ajax_nonce );
		content.append( 'template_type', 'classic' );

		fetch( ajaxurl, {
			method: 'post',
			body: content,
		} );
	};

	const handleSurveyFormSubmit = ( e ) => {
		e.preventDefault();

		setStartFlag();

		setTimeout( () => {
			dispatch( {
				type: 'set',
				currentIndex: currentIndex + 1,
			} );
		}, 500 );

		if ( analytics !== 'yes' ) {
			// Send data to analytics.
			const answer = analyticsFlag ? 'yes' : 'no';
			const optinAnswer = new FormData();
			optinAnswer.append( 'action', 'astra-sites-update-analytics' );
			optinAnswer.append( '_ajax_nonce', astraSitesVars._ajax_nonce );
			optinAnswer.append( 'data', answer );

			fetch( ajaxurl, {
				method: 'post',
				body: optinAnswer,
			} )
				.then( ( response ) => response.json() )
				.then( ( response ) => {
					if ( response.success ) {
						starterTemplates.analytics = answer;
					}
				} );
		}

		if ( astraSitesVars.subscribed === 'yes' ) {
			dispatch( {
				type: 'set',
				user_subscribed: true,
			} );
			return;
		}

		if ( ! formDetails.opt_in && ! formDetails.email ) {
			return;
		}

		const subscriptionFields = {
			EMAIL: formDetails.email,
			FIRSTNAME: formDetails.first_name,
			PAGE_BUILDER: builder,
			WP_USER_TYPE: formDetails.wp_user_type,
			BUILD_WEBSITE_FOR: formDetails.build_website_for,
			OPT_IN: formDetails.opt_in,
		};

		const content = new FormData();
		content.append( 'action', 'astra-sites-update-subscription' );
		content.append( '_ajax_nonce', astraSitesVars._ajax_nonce );
		content.append( 'data', JSON.stringify( subscriptionFields ) );

		fetch( ajaxurl, {
			method: 'post',
			body: content,
		} )
			.then( ( response ) => response.json() )
			.then( () => {
				dispatch( {
					type: 'set',
					user_subscribed: true,
				} );
			} );
	};

	const handlePluginFormSubmit = ( e ) => {
		e.preventDefault();
		setSkipPlugins( false );
	};

	const recheckPlugins = ( e ) => {
		e.preventDefault();
		checkRequiredPlugins( storedState );
	};

	const surveyForm = () => {
		return (
			<form className="survey-form" onSubmit={ handleSurveyFormSubmit }>
				<h1>{ __( 'Okay, just one last step…', 'astra-sites' ) }</h1>
				{ astraSitesVars.subscribed !== 'yes' && (
					<SurveyForm updateFormDetails={ updateFormDetails } />
				) }
				{ <AdvancedSettings /> }
				<button
					type="submit"
					className="submit-survey-btn button-text d-flex-center-align"
				>
					{ __( 'Submit & Build My Website', 'astra-sites' ) }
					{ ICONS.arrowRight }
				</button>
				<p className="subscription-agreement-text text-center mt-4">
					By clicking { `"Submit & Build My Website"` }, you agree to
					our{ ' ' }
					<a
						className="st-link"
						href="https://store.brainstormforce.com/terms-and-conditions/"
						target="_blank"
						rel="noreferrer"
					>
						Terms
					</a>{ ' ' }
					and{ ' ' }
					<a
						className="st-link"
						href="https://store.brainstormforce.com/privacy-policy/"
						target="_blank"
						rel="noreferrer"
					>
						Privacy Policy
					</a>
					.
				</p>
			</form>
		);
	};

	const thirdPartyPluginList = () => {
		return (
			<form
				className="required-plugins-form"
				onSubmit={ handlePluginFormSubmit }
			>
				<h1>{ __( 'Required plugins missing', 'astra-sites' ) }</h1>
				<p>
					{ __(
						"This starter template requires premium plugins. As these are third party premium plugins, you'll need to purchase, install and activate them first.",
						'astra-sites'
					) }
				</p>
				<h5>{ __( 'Required plugins -', 'astra-sites' ) }</h5>
				<ul className="third-party-required-plugins-list">
					{ thirtPartyPlugins.map( ( plugin, index ) => {
						return (
							<li
								data-slug={ plugin.slug }
								data-init={ plugin.init }
								data-name={ plugin.name }
								key={ index }
							>
								<a
									href={ plugin.link }
									target="_blank"
									rel="noreferrer"
								>
									{ plugin.name }
								</a>
							</li>
						);
					} ) }
				</ul>
				<button
					type="submit"
					className="submit-survey-btn button-text d-flex-center-align"
				>
					{ __( 'Skip & Start Importing', 'astra-sites' ) }
					{ ICONS.arrowRight }
				</button>
			</form>
		);
	};

	const handleRequirementCheck = () => {
		setShowRequirementCheck( false );
		dispatch( {
			type: 'set',
			shownRequirementOnce: true,
		} );
	};

	const hardRequirement = () => {
		return (
			<div className="requirement-check-wrap">
				<h1>{ __( "We're Almost There!", 'astra-sites' ) }</h1>

				<p>
					{ __(
						'The Starter Template you are trying to import requires a few plugins to be installed and activated. Your current PHP version does not match the minimum requirement for these plugins.',
						'astra-sites'
					) }
				</p>

				<p className="current-php-version">
					<strong>{ `Current PHP version: ${ phpVersion }` }</strong>
				</p>

				<ul className="requirement-check-list">
					{ Object.values( requiredPlugins.incompatible_plugins ).map(
						( value, index ) => {
							return (
								<li key={ index }>
									<div className="requirement-list-item">
										{ `${ value.name } - PHP Version: ${ value.min_php_version } or higher` }
									</div>
								</li>
							);
						}
					) }
				</ul>
			</div>
		);
	};

	const optionalRequirement = () => {
		return (
			<div className="requirement-check-wrap">
				<h1>{ __( "We're Almost There!", 'astra-sites' ) }</h1>

				<p>
					{ __(
						"You're close to importing the template. To complete the process, please clear the following conditions.",
						'astra-sites'
					) }
				</p>

				<ul className="requirement-check-list">
					{ Object.keys( requirementsErrors ).length > 0 &&
						Object.values( requirementsErrors ).map(
							( value, index ) => {
								return (
									<li key={ index }>
										<div className="requirement-list-item">
											{ value.title }
											<Tooltip
												interactive={ true }
												content={
													<span
														dangerouslySetInnerHTML={ {
															__html: value.tooltip,
														} }
													/>
												}
											>
												{ ICONS.questionMark }
											</Tooltip>
										</div>
									</li>
								);
							}
						) }
					{ Object.keys( requirementWarning ).length > 0 &&
						Object.values( requirementWarning ).map(
							( value, index ) => {
								return (
									<li key={ index }>
										<div className="requirement-list-item">
											{ value.title }
											<Tooltip
												interactive={ true }
												content={
													<span
														dangerouslySetInnerHTML={ {
															__html: value.tooltip,
														} }
													/>
												}
											>
												{ ICONS.questionMark }
											</Tooltip>
										</div>
									</li>
								);
							}
						) }
				</ul>
				<button
					className="submit-survey-btn button-text d-flex-center-align"
					onClick={ handleRequirementCheck }
					disabled={
						Object.keys( requirementsErrors ).length > 0
							? true
							: false
					}
				>
					{ __( 'Skip & Continue', 'astra-sites' ) }
					{ ICONS.arrowRight }
				</button>
			</div>
		);
	};

	const fileSystemPermissionRequirement = () => {
		const {
			is_readable: isReadable,
			is_writable: isWritable,
			is_wp_filesystem: isFilesystem,
		} = fileSystemPermissions.permissions;

		return (
			<div className="requirement-check-wrap">
				<h1>{ __( "We're Almost There!", 'astra-sites' ) }</h1>

				<p>
					{ __(
						'The import process was interrupted due to the lack of file-system permissions from your host. It is required to import images, XML files, and more required for the template to work.',
						'astra-sites'
					) }
				</p>

				<p className="current-file-system-permissions">
					<strong>
						{ __(
							'Current file-system permissions:',
							'astra-sites'
						) }
					</strong>
				</p>

				<ul className="requirement-check-list">
					<li>
						<div className="requirement-list-item">
							{ __( 'Read Permissions:', 'astra-sites' ) }
							<span
								className={ `dashicons ${
									isReadable
										? 'dashicons-yes'
										: 'dashicons-no'
								}` }
							/>
						</div>
					</li>
					<li>
						<div className="requirement-list-item">
							{ __( 'Write Permissions:', 'astra-sites' ) }
							<span
								className={ `dashicons ${
									isWritable
										? 'dashicons-yes'
										: 'dashicons-no'
								}` }
							/>
						</div>
					</li>
					<li>
						<div className="requirement-list-item">
							{ __(
								'WP_Filesystem Permissions:',
								'astra-sites'
							) }
							<span
								className={ `dashicons ${
									isFilesystem
										? 'dashicons-yes'
										: 'dashicons-no'
								}` }
							/>
						</div>
					</li>
				</ul>

				<p>
					{ __(
						'Please contact the hosting service provider to help you update the permissions so that you can successfully import a complete template.',
						'astra-sites'
					) }
				</p>
			</div>
		);
	};

	let defaultStepContent = surveyForm();

	if ( pluginInstallationAttempts > 2 && allPuginList.length > 0 ) {
		// If plugin installation fails more than 3 times.
		defaultStepContent = manualPluginInstallation();
	} else if (
		fileSystemPermissions !== null &&
		! Object.values( fileSystemPermissions.permissions ).every(
			( value ) => value === true
		)
	) {
		defaultStepContent = fileSystemPermissionRequirement();
	} else if (
		null !== requiredPlugins &&
		Object.keys( requiredPlugins.incompatible_plugins ).length > 0
	) {
		defaultStepContent = hardRequirement();
	} else if ( showRequirementCheck ) {
		defaultStepContent = optionalRequirement();
	} else if ( skipPlugins ) {
		defaultStepContent = thirdPartyPluginList();
	}

	return (
		<DefaultStep
			content={
				<>
					<div className="survey-container">
						{ ' ' }
						{ defaultStepContent }{ ' ' }
					</div>
					<PreviousStepLink before>
						{ __( 'Back', 'astra-sites' ) }
					</PreviousStepLink>
				</>
			}
		/>
	);
};

export default Survey;
