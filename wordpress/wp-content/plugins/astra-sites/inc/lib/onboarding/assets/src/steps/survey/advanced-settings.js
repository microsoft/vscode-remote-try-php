import React, { useState } from 'react';
import Tooltip from '../onboarding-ai/components/tooltip';
import { __ } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';
import { useStateValue } from '../../store/store';
import ICONS from '../../../icons';
import { whiteLabelEnabled } from '../../utils/functions';
const { themeStatus, firstImportStatus, analytics } = starterTemplates;

const AdvancedSettings = () => {
	const [ showSection, setShowSection ] = useState( true );
	const [
		{
			reset,
			customizerImportFlag,
			themeActivateFlag,
			widgetImportFlag,
			contentImportFlag,
			requiredPlugins,
			analyticsFlag,
			templateResponse,
		},
		dispatch,
	] = useStateValue();
	const toggleSection = () => {
		setShowSection( ! showSection );
	};

	const updateAnalyticsFlag = () => {
		dispatch( {
			type: 'set',
			analyticsFlag: ! analyticsFlag,
		} );
	};

	const updateCustomizerImportFlag = () => {
		dispatch( {
			type: 'set',
			customizerImportFlag: ! customizerImportFlag,
		} );
	};

	const updateThemeFlag = () => {
		dispatch( {
			type: 'set',
			themeActivateFlag: ! themeActivateFlag,
			customizerImportFlag: ! themeActivateFlag,
		} );
	};

	const updateWidgetImportFlag = () => {
		dispatch( {
			type: 'set',
			widgetImportFlag: ! widgetImportFlag,
		} );
	};

	const updateContentImportFlag = () => {
		dispatch( {
			type: 'set',
			contentImportFlag: ! contentImportFlag,
		} );
	};

	const updateResetValue = () => {
		dispatch( {
			type: 'set',
			reset: ! reset,
		} );
	};

	const notActivePlugins =
		requiredPlugins !== null
			? requiredPlugins.required_plugins?.inactive
			: [];

	const notInstalled =
		requiredPlugins !== null
			? requiredPlugins.required_plugins?.notinstalled
			: [];

	const themeStatusClass =
		'installed-and-active' !== themeStatus ? 'theme-check' : '';

	const isSurecartTemplate = templateResponse?.[
		'astra-site-surecart-settings'
	]
		? true
		: false;

	return (
		<div
			className={ `survey-form-advanced-wrapper ${
				showSection ? 'show-section' : 'hidden-section'
			}` }
		>
			<p
				className="label-text row-label"
				onClick={ toggleSection }
				role="presentation"
			>
				{ __( 'Advanced Options', 'astra-sites' ) }
				<span className="advanced-options-icons">
					{ showSection ? ICONS.angleUP : ICONS.angleDown }
				</span>
			</p>
			<div className="survey-advanced-section">
				<ul>
					{ 'yes' === firstImportStatus && (
						<li>
							<input
								type="checkbox"
								id="reset-site"
								name="reset-site"
								defaultChecked={ reset }
								onChange={ updateResetValue }
							/>
							<label htmlFor="reset-site">
								{ ' ' }
								{ __(
									'Delete Previously imported sites',
									'astra-sites'
								) }
							</label>
							<Tooltip
								content={
									<span>
										{ __(
											'WARNING: Selecting this option will delete all data from the previous import. Choose this option only if this is intended.',
											'astra-sites'
										) }
										<br />
										{ __(
											'Choose this option only if this is intended.You can find the backup to the current customizer settings at /wp-content/uploads astra-sites',
											'astra-sites'
										) }
									</span>
								}
							>
								{ ICONS.questionMark }
							</Tooltip>
						</li>
					) }
					{ 'installed-and-active' !== themeStatus && (
						<li>
							<input
								type="checkbox"
								id="import-theme"
								name="import-theme"
								defaultChecked={ themeActivateFlag }
								onChange={ updateThemeFlag }
							/>
							<label htmlFor="import-theme">
								{ ' ' }
								{ __(
									'Install & Activate Astra Theme',
									'astra-sites'
								) }
							</label>
							<Tooltip
								content={ __(
									'To import the site in the original format, you would need the Astra theme activated. You can import it with any other theme, but the site might lose some of the design settings and look a bit different.',
									'astra-sites'
								) }
							>
								{ ICONS.questionMark }
							</Tooltip>
						</li>
					) }
					{ themeActivateFlag && (
						<li className={ themeStatusClass }>
							<input
								type="checkbox"
								id="import-customizer"
								name="import-customizer"
								defaultChecked={ customizerImportFlag }
								onChange={ updateCustomizerImportFlag }
							/>
							<label htmlFor="import-customizer">
								{ ' ' }
								{ __(
									'Import Customizer Settings',
									'astra-sites'
								) }
							</label>
							<Tooltip
								content={ __(
									'Starter Templates customizer serves global settings that give uniform design to the website. Choosing this option will override your current customizer settings.',
									'astra-sites'
								) }
							>
								{ ICONS.questionMark }
							</Tooltip>
						</li>
					) }
					<li>
						<input
							type="checkbox"
							id="import-widgets"
							name="import-widgets"
							defaultChecked={ widgetImportFlag }
							onChange={ updateWidgetImportFlag }
						/>
						<label htmlFor="import-widgets">
							{ ' ' }
							{ __( 'Import Widgets', 'astra-sites' ) }
						</label>
					</li>
					{ ( notActivePlugins.length > 0 ||
						notInstalled.length > 0 ) && (
						<li>
							<input
								type="checkbox"
								id="import-plugins"
								name="import-plugins"
								defaultChecked={ true }
								disabled
							/>
							<label htmlFor="import-plugins">
								{ ' ' }
								{ __(
									'Install Required Plugins',
									'astra-sites'
								) }
							</label>
							<Tooltip
								content={
									<div>
										<span>
											{ __(
												'Plugins needed to import this template are missing. Required plugins will be installed and activated automatically.',
												'astra-sites'
											) }
										</span>
										<ul>
											{ notActivePlugins.map(
												( plugin, index ) => {
													return (
														<li key={ index }>
															{ decodeEntities(
																`&bull; ${ plugin.name }`
															) }
														</li>
													);
												}
											) }
										</ul>
									</div>
								}
							>
								{ ICONS.questionMark }
							</Tooltip>
						</li>
					) }
					<li>
						<input
							type="checkbox"
							id="import-content"
							name="import-content"
							defaultChecked={ contentImportFlag }
							onChange={ updateContentImportFlag }
						/>
						<label htmlFor="import-content">
							{ ' ' }
							{ __( 'Import Content', 'astra-sites' ) }
						</label>
						<Tooltip
							content={ __(
								'Selecting this option will import dummy pages, posts, images, and menus. If you do not want to import dummy content, please uncheck this option.',
								'astra-sites'
							) }
						>
							{ ICONS.questionMark }
						</Tooltip>
					</li>
					{ ! whiteLabelEnabled() && analytics !== 'yes' && (
						<li>
							<input
								type="checkbox"
								id="analytics-content"
								name="analytics-content"
								defaultChecked={ analyticsFlag }
								onChange={ updateAnalyticsFlag }
							/>
							<label htmlFor="analytics-content">
								{ ' ' }
								{ __(
									'Share Non-Sensitive Data',
									'astra-sites'
								) }
							</label>
							<Tooltip
								interactive={ true }
								content={
									<div>
										{ __(
											'Help our developers build better templates and products for you by sharing anonymous and non-sensitive data about your website.',
											'astra-sites'
										) }{ ' ' }
										<a
											href="https://store.brainstormforce.com/usage-tracking/?utm_source=wp_dashboard&utm_medium=general_settings&utm_campaign=usage_tracking"
											target="_blank"
											rel="noreferrer noopener"
										>
											{ __(
												'Learn More',
												'astra-sites'
											) }
										</a>
									</div>
								}
							>
								{ ICONS.questionMark }
							</Tooltip>
						</li>
					) }
					{ isSurecartTemplate &&
						astraSitesVars.surecart_store_exists && (
							<li>
								<input
									type="checkbox"
									id="surecart-store"
									name="surecart-store"
									defaultChecked={ true }
									disabled
								/>
								<label htmlFor="surecart-store">
									{ ' ' }
									{ __(
										'Replace Existing Surecart Store',
										'astra-sites'
									) }
								</label>
								<Tooltip
									content={ __(
										"Replace the current Surecart store with the imported store's data and settings.",
										'astra-sites'
									) }
								>
									{ ICONS.questionMark }
								</Tooltip>
							</li>
						) }
				</ul>
			</div>
		</div>
	);
};

export default AdvancedSettings;
