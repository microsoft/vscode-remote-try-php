import React, { useEffect, useMemo, useRef, useState } from 'react';
import { CircularProgressBar } from '@tomickigrzegorz/react-circular-progress-bar';
import { __, sprintf } from '@wordpress/i18n';
import { withDispatch, useSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import apiFetch from '@wordpress/api-fetch';
import ImportLoaderAi from '../../components/import-steps/import-loader-ai';
import { useStateValue } from '../../store/store';
import sseImport from '../import-site/sse-import';
import {
	installAstra,
	divideIntoChunks,
	checkRequiredPlugins,
	checkFileSystemPermissions,
	generateAnalyticsLead,
	getAiDemo,
	setSiteLogo,
	setColorPalettes,
	setSiteTitle,
	saveTypography,
	setSiteLanguage,
} from '../import-site/import-utils';
const { reportError } = starterTemplates;
let sendReportFlag = reportError;
const successMessageDelay = 8000; // 8 seconds delay for fully assets load.
import { STORE_KEY } from '../../steps/onboarding-ai/store';
import ErrorModel from './error-model';
import '../import-site/style.scss';

const { imageDir } = starterTemplates;

const RANDOM_FINAL_FINISHING_MESSAGES = [
	__( 'Double-checking for grammar and spelling errors…', 'astra-sites' ),
	__( 'Finalizing setup and configurations…', 'astra-sites' ),
	__( `Crossing the t's and dotting the i's…`, 'astra-sites' ),
	__( 'Reviewing for any last-minute tweaks…', 'astra-sites' ),
	__( 'Almost there! Just a few more finishing touches…', 'astra-sites' ),
	__( 'Your website is almost ready.', 'astra-sites' ),
	__( "It's taking longer than usual. Please bear with us!", 'astra-sites' ),
];

function* getMessage() {
	let msgIndx = 0;
	while ( true ) {
		yield RANDOM_FINAL_FINISHING_MESSAGES[
			msgIndx++ % RANDOM_FINAL_FINISHING_MESSAGES.length
		];
	}
}

const ImportAiSite = ( { onClickNext } ) => {
	const storedState = useStateValue();

	// const [ progressPercentage, setProgressPercentage ] = useState( 0 );
	const [ showProgressBar, setShowProgressBar ] = useState( true );
	const [ isReadyForImport, setIsReadyForImport ] = useState( false );

	const {
		websiteInfo,
		aiStepData: {
			businessName,
			selectedTemplate,
			selectedImages,
			siteLanguage,
			siteLanguageList,
		},
	} = useSelect( ( select ) => {
		const { getWebsiteInfo, getAIStepData } = select( STORE_KEY );
		return {
			websiteInfo: getWebsiteInfo(),
			aiStepData: getAIStepData(),
		};
	}, [] );

	const [
		{
			// importStart, // not used
			importEnd,
			importPercent,
			templateResponse,
			reset,
			themeStatus,
			importError,
			customizerImportFlag,
			widgetImportFlag,
			contentImportFlag,
			themeActivateFlag,
			requiredPluginsDone,
			requiredPlugins,
			notInstalledList,
			notActivatedList,
			tryAgainCount,
			xmlImportDone,
			templateId,
			builder,
			pluginInstallationAttempts,
			importErrorMessages,
		},
		dispatch,
	] = storedState;

	const percentage = useRef( importPercent );
	const randomMessage = useMemo( getMessage, [] );

	/**
	 *
	 * @param {string} primary   Primary text for the error.
	 * @param {string} secondary Secondary text for the error.
	 * @param {string} text      Text received from the AJAX call.
	 * @param {string} code      Error code received from the AJAX call.
	 * @param {string} solution  Solution provided for the current error.
	 * @param {string} stack
	 */
	const report = (
		primary = '',
		secondary = '',
		text = '',
		code = '',
		solution = '',
		stack = ''
	) => {
		dispatch( {
			type: 'set',
			importError: true,
			importErrorMessages: {
				primaryText: primary,
				secondaryText: secondary,
				errorCode: code,
				errorText:
					typeof text === 'string' ? text : JSON.stringify( text ),
				solutionText: solution,
				tryAgain: true,
			},
		} );

		localStorage.removeItem( 'st-import-start' );
		localStorage.removeItem( 'st-import-end' );

		sendErrorReport(
			primary,
			secondary,
			text,
			code,
			solution,
			stack,
			tryAgainCount
		);
	};

	const sendErrorReport = (
		primary = '',
		secondary = '',
		text = '',
		code = '',
		solution = '',
		stack = ''
	) => {
		if ( tryAgainCount >= 2 ) {
			generateAnalyticsLead( tryAgainCount, false, templateId, builder );
		}
		if ( ! sendReportFlag ) {
			return;
		}
		const reportErr = new FormData();
		reportErr.append( 'action', 'astra-sites-report_error' );
		reportErr.append( '_ajax_nonce', astraSitesVars._ajax_nonce );
		reportErr.append(
			'error',
			JSON.stringify( {
				primaryText: primary,
				secondaryText: secondary,
				errorCode: code,
				errorText: text,
				solutionText: solution,
				tryAgain: true,
				stack,
				tryAgainCount,
			} )
		);
		reportErr.append( 'id', templateResponse?.id );
		reportErr.append( 'plugins', JSON.stringify( requiredPlugins ) );
		fetch( ajaxurl, {
			method: 'post',
			body: reportErr,
		} );
	};

	const customizeWebsite = async () => {
		const [ { aiSiteLogo, aiActiveTypography, aiActivePallette } ] =
			storedState;
		const languageItem = siteLanguageList.find(
			( item ) => item.code === siteLanguage
		);
		await setSiteLogo( aiSiteLogo );
		await setColorPalettes( JSON.stringify( aiActivePallette ) );
		await setSiteTitle( businessName );
		await saveTypography( aiActiveTypography );
		await setSiteLanguage( languageItem?.[ 'wordpress-code' ] ?? 'en_US' );
	};

	const { stepsData } = useSelect( ( select ) => {
		const { getAIStepData } = select( STORE_KEY );

		return {
			stepsData: getAIStepData(),
		};
	}, [] );

	/**
	 * Start Import Part 1.
	 */
	const importPart1 = async () => {
		let resetStatus = false;
		// const cfStatus = false;
		// const formsStatus = false;
		let customizerStatus = false;
		let spectraStatus = false;
		let sureCartStatus = false;
		let imageDownloadStatus = false;

		resetStatus = await resetOldSite();

		if ( resetStatus ) {
			imageDownloadStatus = await downloadImages();
		}

		if ( imageDownloadStatus ) {
			customizerStatus = await importCustomizerJson();
		}

		if ( customizerStatus ) {
			spectraStatus = await importSpectraSettings();
		}

		if ( spectraStatus ) {
			sureCartStatus = await importSureCartSettings();
		}

		if ( sureCartStatus ) {
			await importSiteContent();
		}
	};

	/**
	 * Start Import Part 2.
	 */
	const importPart2 = async () => {
		let optionsStatus = false;
		let widgetStatus = false;
		let finalStepStatus = false;
		let migrationStatus = false;

		optionsStatus = await importSiteOptions();

		if ( optionsStatus ) {
			widgetStatus = await importWidgets();
		}

		if ( widgetStatus ) {
			finalStepStatus = await importDone();
		}

		if ( finalStepStatus ) {
			migrationStatus = await waitForFullMigration();
		}

		if ( migrationStatus ) {
			generateAnalyticsLead( tryAgainCount, true, templateId, builder );
		}
	};

	/**
	 * Install Required plugins.
	 */
	const installRequiredPlugins = () => {
		// Install Bulk.
		if ( notInstalledList.length <= 0 ) {
			dispatch( {
				type: 'set',
				requiredPluginsDone: true,
			} );
			return;
		}

		percentage.current += 2;
		dispatch( {
			type: 'set',
			importStatus: __( 'Installing Required Plugins.', 'astra-sites' ),
			importPercent: percentage.current,
		} );

		const copiedList = [ ...notInstalledList ];

		notInstalledList.forEach( ( plugin ) => {
			wp.updates.queue.push( {
				action: 'install-plugin', // Required action.
				data: {
					slug: plugin.slug,
					init: plugin.init,
					name: plugin.name,
					clear_destination: true,
					ajax_nonce: astraSitesVars._ajax_nonce,
					success() {
						dispatch( {
							type: 'set',
							importStatus: sprintf(
								// translators: Plugin Name.
								__(
									'%1$s plugin installed successfully.',
									'astra-sites'
								),
								plugin.name
							),
						} );

						const inactiveList = [ ...notActivatedList ];
						inactiveList.push( plugin );

						dispatch( {
							type: 'set',
							notActivatedList: inactiveList,
						} );
						const notInstalledPluginList = copiedList;
						notInstalledPluginList.forEach(
							( singlePlugin, index ) => {
								if ( singlePlugin.slug === plugin.slug ) {
									notInstalledPluginList.splice( index, 1 );
								}
							}
						);
						dispatch( {
							type: 'set',
							notInstalledList: notInstalledPluginList,
						} );
					},
					error( err ) {
						dispatch( {
							type: 'set',
							pluginInstallationAttempts:
								pluginInstallationAttempts + 1,
						} );
						let errText = err;
						if ( err && undefined !== err.errorMessage ) {
							errText = err.errorMessage;
							if ( undefined !== err.errorCode ) {
								errText = err.errorCode + ': ' + errText;
							}
						}
						report(
							sprintf(
								// translators: Plugin Name.
								__(
									'Could not install the plugin - %s',
									'astra-sites'
								),
								plugin.name
							),
							'',
							errText,
							'',
							'',
							err
						);
					},
				},
			} );
		} );

		// Required to set queue.
		wp.updates.queueChecker();
	};

	/**
	 * Activate Plugin
	 *
	 * @param {Object} plugin
	 */
	const activatePlugin = ( plugin ) => {
		percentage.current += 2;
		dispatch( {
			type: 'set',
			importStatus: sprintf(
				// translators: Plugin Name.
				__( 'Activating %1$s plugin.', 'astra-sites' ),
				plugin.name
			),
			importPercent: percentage.current,
		} );

		const activatePluginOptions = new FormData();
		activatePluginOptions.append(
			'action',
			'astra-sites-required_plugin_activate'
		);
		activatePluginOptions.append( 'init', plugin.init );
		activatePluginOptions.append(
			'_ajax_nonce',
			astraSitesVars._ajax_nonce
		);
		fetch( ajaxurl, {
			method: 'post',
			body: activatePluginOptions,
		} )
			.then( ( response ) => response.text() )
			.then( ( text ) => {
				let cloneResponse = [];
				let errorReported = false;
				try {
					const response = JSON.parse( text );
					cloneResponse = response;
					if ( response.success ) {
						const notActivatedPluginList = [ ...notActivatedList ];
						notActivatedPluginList.forEach(
							( singlePlugin, index ) => {
								if ( singlePlugin.slug === plugin.slug ) {
									notActivatedPluginList.splice( index, 1 );
								}
							}
						);
						dispatch( {
							type: 'set',
							notActivatedList: notActivatedPluginList,
						} );
						percentage.current += 2;
						dispatch( {
							type: 'set',
							importStatus: sprintf(
								// translators: Plugin Name.
								__( '%1$s activated.', 'astra-sites' ),
								plugin.name
							),
							importPercent: percentage.current,
						} );
					}
				} catch ( error ) {
					report(
						sprintf(
							// translators: Plugin name.
							__(
								`JSON_Error: Could not activate the required plugin - %1$s.`,
								'astra-sites'
							),
							plugin.name
						),
						'',
						error,
						'',
						sprintf(
							// translators: Support article URL.
							__(
								'<a href="%1$s">Read article</a> to resolve the issue and continue importing template.',
								'astra-sites'
							),
							'https://wpastra.com/docs/enable-debugging-in-wordpress/#how-to-use-debugging'
						),
						text
					);

					errorReported = true;
				}

				if ( ! cloneResponse.success && errorReported === false ) {
					throw cloneResponse;
				}
			} )
			.catch( ( error ) => {
				dispatch( {
					type: 'set',
					pluginInstallationAttempts: pluginInstallationAttempts + 1,
				} );
				report(
					sprintf(
						// translators: Plugin name.
						__(
							`Could not activate the required plugin - %1$s.`,
							'astra-sites'
						),
						plugin.name
					),
					'',
					error?.data?.message,
					'',
					sprintf(
						// translators: Support article URL.
						__(
							'<a href="%1$s">Read article</a> to resolve the issue and continue importing template.',
							'astra-sites'
						),
						'https://wpastra.com/docs/enable-debugging-in-wordpress/#how-to-use-debugging'
					),
					error
				);
			} );
	};

	/**
	 * 1. Reset.
	 * The following steps are covered here.
	 * 		1. Settings backup file store.
	 * 		2. Reset Customizer
	 * 		3. Reset Site Options
	 * 		4. Reset Widgets
	 * 		5. Reset Forms and Terms
	 * 		6. Reset all posts
	 */
	const resetOldSite = async () => {
		if ( ! reset ) {
			return true;
		}
		percentage.current += 2;
		dispatch( {
			type: 'set',
			importStatus: __( 'Resetting site.', 'astra-sites' ),
			importPercent: percentage.current,
		} );

		let backupFileStatus = false;
		let resetCustomizerStatus = false;
		let resetWidgetStatus = false;
		let resetOptionsStatus = false;
		let reseteTermsStatus = false;
		let resetPostsStatus = false;

		/**
		 * Settings backup file store.
		 */
		backupFileStatus = await performSettingsBackup();

		/**
		 * Reset Customizer.
		 */
		if ( backupFileStatus ) {
			resetCustomizerStatus = await performResetCustomizer();
		}

		/**
		 * Reset Site Options.
		 */
		if ( resetCustomizerStatus ) {
			resetOptionsStatus = await performResetSiteOptions();
		}

		/**
		 * Reset Widgets.
		 */
		if ( resetOptionsStatus ) {
			resetWidgetStatus = await performResetWidget();
		}

		/**
		 * Reset Terms, Forms.
		 */
		if ( resetWidgetStatus ) {
			reseteTermsStatus = await performResetTermsAndForms();
		}

		/**
		 * Reset Posts.
		 */
		if ( reseteTermsStatus ) {
			resetPostsStatus = await performResetPosts();
		}

		if (
			! (
				resetCustomizerStatus &&
				resetOptionsStatus &&
				resetWidgetStatus &&
				reseteTermsStatus &&
				resetPostsStatus
			)
		) {
			return false;
		}

		percentage.current += 10;
		dispatch( {
			type: 'set',
			importPercent: percentage.current >= 50 ? 50 : percentage.current,
			importStatus: __( 'Reset for old website is done.', 'astra-sites' ),
		} );

		return true;
	};

	/**
	 * Reset a chunk of posts.
	 *
	 * @param {Object} chunk
	 */
	const performPostsReset = async ( chunk ) => {
		const data = new FormData();
		data.append( 'action', 'astra-sites-get_deleted_post_ids' );
		data.append( '_ajax_nonce', astraSitesVars._ajax_nonce );

		dispatch( {
			type: 'set',
			importStatus: __( `Resetting posts.`, 'astra-sites' ),
		} );

		const formOption = new FormData();
		formOption.append( 'action', 'astra-sites-reset_posts' );
		formOption.append( 'ids', JSON.stringify( chunk ) );
		formOption.append( '_ajax_nonce', astraSitesVars._ajax_nonce );

		await fetch( ajaxurl, {
			method: 'post',
			body: formOption,
		} )
			.then( ( resp ) => resp.text() )
			.then( ( text ) => {
				let cloneData = [];
				let errorReported = false;
				try {
					const result = JSON.parse( text );
					cloneData = result;
					if ( result.success ) {
						percentage.current += 2;
						dispatch( {
							type: 'set',
							importPercent:
								percentage.current >= 50
									? 50
									: percentage.current,
						} );
					} else {
						throw result;
					}
				} catch ( error ) {
					report(
						__( 'Resetting posts failed.', 'astra-sites' ),
						'',
						error,
						'',
						'',
						text
					);

					errorReported = true;
					return false;
				}

				if ( ! cloneData.success && errorReported === false ) {
					throw cloneData.data;
				}
			} )
			.catch( ( error ) => {
				report(
					__( 'Resetting posts failed.', 'astra-sites' ),
					'',
					error?.message,
					'',
					'',
					error
				);
				return false;
			} );
		return true;
	};

	/**
	 * 1.0 Perform Settings backup file stored.
	 */
	const performSettingsBackup = async () => {
		dispatch( {
			type: 'set',
			importStatus: __( 'Taking settings backup.', 'astra-sites' ),
		} );

		const customizerContent = new FormData();
		customizerContent.append( 'action', 'astra-sites-backup_settings' );
		customizerContent.append( '_ajax_nonce', astraSitesVars._ajax_nonce );

		const status = await fetch( ajaxurl, {
			method: 'post',
			body: customizerContent,
		} )
			.then( ( response ) => response.text() )
			.then( ( text ) => {
				const response = JSON.parse( text );
				if ( response.success ) {
					percentage.current += 2;
					dispatch( {
						type: 'set',
						importPercent: percentage.current,
					} );
					return true;
				}
				throw response.data;
			} )
			.catch( ( error ) => {
				report(
					__( 'Taking settings backup failed.', 'astra-sites' ),
					'',
					error?.message,
					'',
					'',
					error
				);
				return false;
			} );
		return status;
	};

	/**
	 * 1.1 Perform Reset for Customizer.
	 */
	const performResetCustomizer = async () => {
		dispatch( {
			type: 'set',
			importStatus: __( 'Resetting customizer.', 'astra-sites' ),
		} );

		const customizerContent = new FormData();
		customizerContent.append(
			'action',
			'astra-sites-reset_customizer_data'
		);
		customizerContent.append( '_ajax_nonce', astraSitesVars._ajax_nonce );

		const status = await fetch( ajaxurl, {
			method: 'post',
			body: customizerContent,
		} )
			.then( ( response ) => response.text() )
			.then( ( text ) => {
				try {
					const response = JSON.parse( text );
					if ( response.success ) {
						percentage.current += 2;
						dispatch( {
							type: 'set',
							importPercent: percentage.current,
						} );
						return true;
					}
					throw response.data;
				} catch ( error ) {
					report(
						__( 'Resetting customizer failed.', 'astra-sites' ),
						'',
						error?.message,
						'',
						'',
						text
					);

					return false;
				}
			} )
			.catch( ( error ) => {
				report(
					__( 'Resetting customizer failed.', 'astra-sites' ),
					'',
					error?.message,
					'',
					'',
					error
				);
				return false;
			} );
		return status;
	};

	/**
	 * 1.2 Perform reset Site options
	 */
	const performResetSiteOptions = async () => {
		dispatch( {
			type: 'set',
			importStatus: __( 'Resetting site options.', 'astra-sites' ),
		} );

		const siteOptions = new FormData();
		siteOptions.append( 'action', 'astra-sites-reset_site_options' );
		siteOptions.append( '_ajax_nonce', astraSitesVars._ajax_nonce );

		const status = await fetch( ajaxurl, {
			method: 'post',
			body: siteOptions,
		} )
			.then( ( response ) => response.text() )
			.then( ( text ) => {
				try {
					const data = JSON.parse( text );
					if ( data.success ) {
						percentage.current += 2;
						dispatch( {
							type: 'set',
							importPercent: percentage.current,
						} );
						return true;
					}
					throw data.data;
				} catch ( error ) {
					report(
						__( 'Resetting site options Failed.', 'astra-sites' ),
						'',
						error?.message,
						'',
						'',
						text
					);
					return false;
				}
			} )
			.catch( ( error ) => {
				report(
					__( 'Resetting site options Failed.', 'astra-sites' ),
					'',
					error?.message,
					'',
					'',
					error
				);
				return false;
			} );
		return status;
	};

	/**
	 * 1.3 Perform Reset for Widgets
	 */
	const performResetWidget = async () => {
		const widgets = new FormData();
		widgets.append( 'action', 'astra-sites-reset_widgets_data' );
		widgets.append( '_ajax_nonce', astraSitesVars._ajax_nonce );

		dispatch( {
			type: 'set',
			importStatus: __( 'Resetting widgets.', 'astra-sites' ),
		} );
		const status = await fetch( ajaxurl, {
			method: 'post',
			body: widgets,
		} )
			.then( ( response ) => response.text() )
			.then( ( text ) => {
				try {
					const response = JSON.parse( text );
					if ( response.success ) {
						percentage.current += 2;
						dispatch( {
							type: 'set',
							importPercent: percentage.current,
						} );
						return true;
					}
					throw response.data;
				} catch ( error ) {
					report(
						__(
							'Resetting widgets JSON parse failed.',
							'astra-sites'
						),
						'',
						error,
						'',
						'',
						text
					);
					return false;
				}
			} )
			.catch( ( error ) => {
				report(
					__( 'Resetting widgets failed.', 'astra-sites' ),
					'',
					error,
					'',
					'',
					error
				);
				return false;
			} );
		return status;
	};

	/**
	 * 1.4 Reset Terms and Forms.
	 */
	const performResetTermsAndForms = async () => {
		const formOption = new FormData();
		formOption.append( 'action', 'astra-sites-reset_terms_and_forms' );
		formOption.append( '_ajax_nonce', astraSitesVars._ajax_nonce );

		dispatch( {
			type: 'set',
			importStatus: __( 'Resetting terms and forms.', 'astra-sites' ),
		} );

		const status = await fetch( ajaxurl, {
			method: 'post',
			body: formOption,
		} )
			.then( ( response ) => response.text() )
			.then( ( text ) => {
				try {
					const response = JSON.parse( text );
					if ( response.success ) {
						percentage.current += 2;
						dispatch( {
							type: 'set',
							importPercent: percentage.current,
						} );
						return true;
					}
					throw response.data;
				} catch ( error ) {
					report(
						__(
							'Resetting terms and forms failed.',
							'astra-sites'
						),
						'',
						error,
						'',
						'',
						text
					);
					return false;
				}
			} )
			.catch( ( error ) => {
				report(
					__( 'Resetting terms and forms failed.', 'astra-sites' ),
					'',
					error?.message,
					'',
					'',
					error
				);
				return false;
			} );
		return status;
	};

	/**
	 * 1.5 Reset Posts.
	 */
	const performResetPosts = async () => {
		const data = new FormData();
		data.append( 'action', 'astra-sites-get_deleted_post_ids' );
		data.append( '_ajax_nonce', astraSitesVars._ajax_nonce );

		dispatch( {
			type: 'set',
			importStatus: __( 'Gathering posts for deletions.', 'astra-sites' ),
		} );

		let err = '';

		const status = await fetch( ajaxurl, {
			method: 'post',
			body: data,
		} )
			.then( ( response ) => response.json() )
			.then( async ( response ) => {
				if ( response.success ) {
					const chunkArray = divideIntoChunks( 10, response.data );
					if ( chunkArray.length > 0 ) {
						for (
							let index = 0;
							index < chunkArray.length;
							index++
						) {
							await performPostsReset( chunkArray[ index ] );
						}
					}
					return true;
				}
				err = response;
				return false;
			} );

		if ( status ) {
			dispatch( {
				type: 'set',
				importStatus: __( 'Resetting posts done.', 'astra-sites' ),
			} );
		} else {
			report( __( 'Resetting posts failed.', 'astra-sites' ), '', err );
		}
		return status;
	};

	const importCustomizerJson = async () => {
		if ( ! customizerImportFlag ) {
			percentage.current += 5;
			dispatch( {
				type: 'set',
				importPercent:
					percentage.current >= 65 ? 65 : percentage.current,
			} );
			return true;
		}
		dispatch( {
			type: 'set',
			importStatus: __( 'Importing forms.', 'astra-sites' ),
		} );

		const forms = new FormData();
		forms.append( 'action', 'astra-sites-import_customizer_settings' );
		forms.append( '_ajax_nonce', astraSitesVars._ajax_nonce );

		const status = await fetch( ajaxurl, {
			method: 'post',
			body: forms,
		} )
			.then( ( response ) => response.text() )
			.then( ( text ) => {
				try {
					const data = JSON.parse( text );
					if ( data.success ) {
						percentage.current += 5;
						dispatch( {
							type: 'set',
							importPercent:
								percentage.current >= 65
									? 65
									: percentage.current,
						} );
						return true;
					}
					throw data.data;
				} catch ( error ) {
					report(
						__(
							'Importing Customizer failed due to parse JSON error.',
							'astra-sites'
						),
						'',
						error,
						'',
						'',
						text
					);
					return false;
				}
			} )
			.catch( ( error ) => {
				report(
					__( 'Importing Customizer Failed.', 'astra-sites' ),
					'',
					error
				);
				return false;
			} );

		return status;
	};

	const downloadImages = async () => {
		for ( let index = 0; index < selectedImages.length; index++ ) {
			const formData = new FormData();
			formData.append( 'action', 'astra-sites-download-image' );
			formData.append( '_ajax_nonce', astraSitesVars._ajax_nonce );
			formData.append( 'index', index );
			try {
				dispatch( {
					type: 'set',
					importStatus: sprintf(
						//translators: %s: Image number.
						__( 'Downloading Image %s', 'astra-sites' ),
						index + 1
					),
				} );

				const response = await fetch( ajaxurl, {
					method: 'POST',
					body: formData,
				} );

				const data = await response.json();

				if ( ! data.success ) {
					report(
						__( 'Downloading images failed.', 'astra-sites' ),
						'',
						''
					);
				}
			} catch ( error ) {
				report(
					__( 'Downloading images failed.', 'astra-sites' ),
					'',
					error
				);
			}
		}

		return true;
	};

	/**
	 * 5. Import Site Content XML.
	 */
	const importSiteContent = async () => {
		if ( ! contentImportFlag ) {
			percentage.current += 20;
			dispatch( {
				type: 'set',
				importPercent:
					percentage.current >= 78 ? 78 : percentage.current,
				xmlImportDone: true,
			} );
			return true;
		}

		dispatch( {
			type: 'set',
			importStatus: __( 'Importing Site Content.', 'astra-sites' ),
		} );

		const wxr = await apiFetch( {
			path: 'zipwp/v1/wxr',
			method: 'POST',
			data: {
				template: selectedTemplate,
				business_name: businessName,
			},
		} );
		if ( wxr.success ) {
			importXML( wxr.data );
		}

		return true;
	};

	/**
	 * 6. Import Spectra Settings.
	 */
	const importSpectraSettings = async () => {
		const spectraSettings =
			templateResponse[ 'astra-site-spectra-options' ] || '';

		if ( '' === spectraSettings || 'null' === spectraSettings ) {
			return true;
		}

		dispatch( {
			type: 'set',
			importStatus: __( 'Importing Spectra Settings.', 'astra-sites' ),
		} );

		const spectra = new FormData();
		spectra.append( 'action', 'astra-sites-import-spectra-settings' );
		spectra.append( '_ajax_nonce', astraSitesVars._ajax_nonce );

		const status = await fetch( ajaxurl, {
			method: 'post',
			body: spectra,
		} )
			.then( ( response ) => response.text() )
			.then( ( text ) => {
				try {
					const data = JSON.parse( text );
					if ( data.success ) {
						percentage.current =
							percentage.current < 70
								? 70
								: percentage.current + 2;
						dispatch( {
							type: 'set',
							importPercent:
								percentage.current >= 70
									? 70
									: percentage.current,
						} );
						return true;
					}
					throw data.data;
				} catch ( error ) {
					report(
						__(
							'Importing Spectra Settings failed due to parse JSON error.',
							'astra-sites'
						),
						'',
						error,
						'',
						'',
						text
					);
					return false;
				}
			} )
			.catch( ( error ) => {
				report(
					__( 'Importing Spectra Settings Failed.', 'astra-sites' ),
					'',
					error
				);
				return false;
			} );
		return status;
	};

	/**
	 * 7. Import Surecart Settings.
	 */
	const importSureCartSettings = async () => {
		const sourceID =
			templateResponse?.[ 'astra-site-surecart-settings' ]?.id || '';
		const sourceCurrency =
			templateResponse?.[ 'astra-site-surecart-settings' ]?.currency ||
			'usd';
		if ( '' === sourceID || 'null' === sourceID ) {
			return true;
		}
		const surecart = new FormData();
		surecart.append( 'action', 'astra-sites-import-surecart-settings' );
		surecart.append( 'source_id', sourceID );
		surecart.append( 'source_currency', sourceCurrency );
		surecart.append( '_ajax_nonce', astraSitesVars._ajax_nonce );

		const status = await fetch( ajaxurl, {
			method: 'post',
			body: surecart,
		} )
			.then( ( response ) => response.text() )
			.then( ( text ) => {
				try {
					const data = JSON.parse( text );
					if ( data.success ) {
						percentage.current =
							percentage.current < 75
								? 75
								: percentage.current + 2;
						dispatch( {
							type: 'set',
							importPercent:
								percentage.current >= 75
									? 75
									: percentage.current,
						} );
						return true;
					}
					throw data.data;
				} catch ( error ) {
					report(
						__(
							'Importing Surecart Settings failed.',
							'astra-sites'
						),
						'',
						error,
						'',
						'',
						text
					);
					return false;
				}
			} )
			.catch( ( error ) => {
				report(
					__( 'Importing Surecart Settings Failed.', 'astra-sites' ),
					'',
					error
				);
				return false;
			} );
		return status;
	};

	/**
	 * Imports XML using EventSource.
	 *
	 * @param {JSON} data JSON object for all the content in XML
	 */
	const importXML = ( data ) => {
		// Import XML though Event Source.
		sseImport.data = data;
		sseImport.render( dispatch, percentage.current );

		const evtSource = new EventSource( sseImport.data.url );
		evtSource.onmessage = ( message ) => {
			const eventData = JSON.parse( message.data );
			switch ( eventData.action ) {
				case 'updateDelta':
					sseImport.updateDelta( eventData.type, eventData.delta );
					break;

				case 'complete':
					if ( false === eventData.error ) {
						evtSource.close();
						dispatch( {
							type: 'set',
							xmlImportDone: true,
						} );
					} else {
						report(
							astraSitesVars.xml_import_interrupted_primary,
							'',
							astraSitesVars.xml_import_interrupted_error,
							'',
							astraSitesVars.xml_import_interrupted_secondary
						);
					}
					break;
			}
		};

		evtSource.onerror = ( error ) => {
			if ( ! ( error && error?.isTrusted ) ) {
				evtSource.close();
				report(
					__(
						'Importing Site Content Failed. - Import Process Interrupted',
						'astra-sites'
					),
					'',
					error
				);
			}
		};

		evtSource.addEventListener( 'log', function ( message ) {
			const eventLogData = JSON.parse( message.data );
			let importMessage = eventLogData.message || '';
			if ( importMessage && 'info' === eventLogData.level ) {
				importMessage = importMessage.replace( /"/g, function () {
					return '';
				} );
			}

			dispatch( {
				type: 'set',
				importStatus: sprintf(
					// translators: Response importMessage
					__( 'Importing - %1$s', 'astra-sites' ),
					importMessage
				),
			} );
		} );
	};

	/**
	 * 6. Import Site Option table values.
	 */
	const importSiteOptions = async () => {
		dispatch( {
			type: 'set',
			importStatus: __( 'Importing Site Options.', 'astra-sites' ),
		} );

		const siteOptions = new FormData();
		siteOptions.append( 'action', 'astra-sites-import_options' );
		siteOptions.append( '_ajax_nonce', astraSitesVars._ajax_nonce );

		const status = await fetch( ajaxurl, {
			method: 'post',
			body: siteOptions,
		} )
			.then( ( response ) => response.text() )
			.then( ( text ) => {
				try {
					const data = JSON.parse( text );
					if ( data.success ) {
						percentage.current = 80;
						dispatch( {
							type: 'set',
							importPercent: percentage.current,
						} );
						return true;
					}
					throw data.data;
				} catch ( error ) {
					report(
						__(
							'Importing Site Options failed due to parse JSON error.',
							'astra-sites'
						),
						'',
						error,
						'',
						'',
						text
					);
					return false;
				}
			} )
			.catch( ( error ) => {
				report(
					__( 'Importing Site Options Failed.', 'astra-sites' ),
					'',
					error
				);
				return false;
			} );

		return status;
	};

	/**
	 * 7. Import Site Widgets.
	 */
	const importWidgets = async () => {
		if ( ! widgetImportFlag ) {
			percentage.current += 3;
			dispatch( {
				type: 'set',
				importPercent:
					percentage.current >= 83 ? 83 : percentage.current,
			} );
			return true;
		}
		dispatch( {
			type: 'set',
			importStatus: __( 'Importing Widgets.', 'astra-sites' ),
		} );

		const widgetsData = templateResponse[ 'astra-site-widgets-data' ] || '';

		const widgets = new FormData();
		widgets.append( 'action', 'astra-sites-import_widgets' );
		widgets.append( 'widgets_data', widgetsData );
		widgets.append( '_ajax_nonce', astraSitesVars._ajax_nonce );

		const status = await fetch( ajaxurl, {
			method: 'post',
			body: widgets,
		} )
			.then( ( response ) => response.text() )
			.then( ( text ) => {
				try {
					const data = JSON.parse( text );
					if ( data.success ) {
						percentage.current += 2;
						dispatch( {
							type: 'set',
							importPercent:
								percentage.current >= 85
									? 85
									: percentage.current,
						} );
						return true;
					}
					throw data.data;
				} catch ( error ) {
					report(
						__(
							'Importing Widgets failed due to parse JSON error.',
							'astra-sites'
						),
						'',
						error,
						'',
						'',
						text
					);
					return false;
				}
			} )
			.catch( ( error ) => {
				report(
					__( 'Importing Widgets Failed.', 'astra-sites' ),
					'',
					error
				);
				return false;
			} );
		return status;
	};

	/**
	 * 9. Final setup - Invoking Batch process.
	 */
	const importDone = async () => {
		dispatch( {
			type: 'set',
			importStatus: __( 'Final finishing.', 'astra-sites' ),
		} );

		const finalSteps = new FormData();
		finalSteps.append( 'action', 'astra-sites-import_end' );
		finalSteps.append( '_ajax_nonce', astraSitesVars._ajax_nonce );

		const status = await fetch( ajaxurl, {
			method: 'post',
			body: finalSteps,
		} )
			.then( ( response ) => response.text() )
			.then( ( text ) => {
				try {
					const data = JSON.parse( text );
					if ( data.success ) {
						localStorage.setItem( 'st-import-end', +new Date() );
						setTimeout( function () {
							percentage.current =
								percentage.current < 90
									? 90
									: percentage.current;
							dispatch( {
								type: 'set',
								importPercent:
									percentage.current >= 90
										? 90
										: percentage.current,
							} );
						}, successMessageDelay );

						return true;
					}
					throw data.data;
				} catch ( error ) {
					report(
						__(
							'Final finishing failed due to parse JSON error.',
							'astra-sites'
						),
						'',
						error,
						'',
						'',
						text
					);
					setTimeout( function () {
						percentage.current =
							percentage.current > 90
								? 90
								: percentage.current + 1;
						dispatch( {
							type: 'set',
							importPercent: percentage.current,
						} );
					}, successMessageDelay );

					localStorage.setItem( 'st-import-end', +new Date() );
					return false;
				}
			} )
			.catch( ( error ) => {
				report(
					__( 'Final finishing Failed.', 'astra-sites' ),
					'',
					error
				);
				return false;
			} );

		return status;
	};

	const waitForFullMigration = async () => {
		try {
			const randomToken = ( Math.random() * 200 )?.toString(); // to avoid response caching
			const response = await apiFetch( {
				path: `zipwp/v1/migration-status?uuid=${ websiteInfo.uuid }&token=${ randomToken }`,
				method: 'GET',
				headers: {
					'X-WP-Nonce': astraSitesVars.rest_api_nonce,
					_ajax_nonce: astraSitesVars._ajax_nonce,
				},
			} );

			if ( response?.data?.data === 'yes' ) {
				// Save customizations.
				await customizeWebsite();

				dispatch( {
					type: 'set',
					importPercent: 100,
					importEnd: true,
				} );
				setShowProgressBar( false );
				return true;
			} else if ( response?.data?.data === 'no' ) {
				percentage.current += 2;
				dispatch( {
					type: 'set',
					importPercent:
						percentage.current >= 98 ? 98 : percentage.current,
					importStatus: randomMessage.next()?.value,
				} );
				setTimeout( () => {
					waitForFullMigration();
				}, 10000 );
			}
		} catch ( error ) {
			percentage.current += 2;
			dispatch( {
				type: 'set',
				importPercent:
					percentage.current >= 98 ? 98 : percentage.current,
				importStatus: randomMessage.next()?.value,
			} );
			setTimeout( () => {
				waitForFullMigration();
			}, 10000 );
		}
	};

	const preventRefresh = ( event ) => {
		if ( importPercent < 100 ) {
			event.returnValue = __(
				'Are you sure you want to cancel the site import process?',
				'astra-sites'
			);
			return event;
		}
	};

	useEffect( () => {
		window.addEventListener( 'beforeunload', preventRefresh ); // eslint-disable-line
		return () => {
			window.removeEventListener( 'beforeunload', preventRefresh ); // eslint-disable-line
		};
	}, [ importPercent ] ); // Add importPercent as a dependency.

	// Add a useEffect to remove the event listener when importPercent is 100%.
	useEffect( () => {
		if ( importPercent === 100 ) {
			window.removeEventListener( 'beforeunload', preventRefresh );
		}
	}, [ importPercent ] );

	/**
	 * When try again button is clicked:
	 * There is a possibility that few/all the required plugins list is already installed.
	 * We cre-check the status of the required plugins here.
	 */
	useEffect( () => {
		if ( tryAgainCount > 0 ) {
			dispatch( {
				type: 'set',
				importPercent: 0,
				importStatus: __( 'Retrying Import.', 'astra-sites' ),
			} );
			handleImport();
		}
	}, [ tryAgainCount ] );

	const setStartFlag = async () => {
		const content = new FormData();
		content.append( 'action', 'astra-sites-set_start_flag' );
		content.append( '_ajax_nonce', astraSitesVars._ajax_nonce );
		content.append( 'uuid', websiteInfo.uuid );

		await fetch( ajaxurl, {
			method: 'post',
			body: content,
		} );
	};

	const handleImport = async () => {
		if ( ! importError ) {
			localStorage.setItem( 'st-import-start', +new Date() );

			dispatch( {
				type: 'set',
				importStart: true,
				importPercent: 0,
				importStatus: __(
					'Preparing your site for import…',
					'astra-sites'
				),
			} );

			percentage.current += 2;

			dispatch( {
				type: 'set',
				importStart: true,
				importPercent: percentage.current,
				importStatus: __(
					'Preparing your site for import…',
					'astra-sites'
				),
			} );

			await setStartFlag();
			setIsReadyForImport( true );
		}
	};

	const handleImportStart = async () => {
		// Get the import data from the AI site.
		await getAiDemo( stepsData, storedState, websiteInfo );
		await checkRequiredPlugins( storedState );
		checkFileSystemPermissions( storedState );

		percentage.current += 3;

		dispatch( {
			type: 'set',
			importPercent: percentage.current,
			importStatus: __( 'Starting Import.', 'astra-sites' ),
		} );

		if ( themeActivateFlag && false === themeStatus ) {
			installAstra( storedState );
		} else {
			dispatch( {
				type: 'set',
				themeStatus: true,
			} );
		}
		sendReportFlag = false;
	};

	const tryAainCallback = () => {
		dispatch( {
			type: 'set',
			// Reset errors.
			importErrorMessages: {},
			importErrorResponse: [],
			importError: false,
			// Try again count.
			tryAgainCount: tryAgainCount + 1,
			// Reset import flags.
			xmlImportDone: false,
			resetData: [],
			importStart: false,
			importEnd: false,
			importPercent: 0,
			requiredPluginsDone: false,
			themeStatus: false,
			notInstalledList: [],
			notActivatedList: [],
		} );
	};

	/**
	 * Start the pre import process.
	 * 		1. Install Astra Theme
	 * 		2. Install Required Plugins.
	 */
	useEffect( () => {
		handleImport();
	}, [] );

	useEffect( () => {
		if ( isReadyForImport ) {
			handleImportStart();
			setIsReadyForImport( false );
		}
	}, [ isReadyForImport ] );

	/**
	 * Start the process only when:
	 * 		1. Required plugins are installed and activated.
	 * 		2. Astra Theme is installed
	 */
	useEffect( () => {
		if ( requiredPluginsDone && themeStatus ) {
			sendReportFlag = reportError;
			importPart1();
		}
	}, [ requiredPluginsDone, themeStatus ] );

	useEffect( () => {
		if ( themeStatus ) {
			installRequiredPlugins();
		}
	}, [ themeStatus, tryAgainCount ] );

	/**
	 * Start Part 2 of the import once the XML is imported sucessfully.
	 */
	useEffect( () => {
		if ( xmlImportDone ) {
			importPart2();
		}
	}, [ xmlImportDone ] );

	// This checks if all the required plugins are installed and activated.
	useEffect( () => {
		if ( ! requiredPlugins ) {
			return;
		}
		if ( notActivatedList.length <= 0 && notInstalledList.length <= 0 ) {
			dispatch( {
				type: 'set',
				requiredPluginsDone: true,
			} );
		}
	}, [
		notActivatedList.length,
		notInstalledList.length,
		requiredPlugins,
		tryAgainCount,
	] );

	// Whenever a plugin is installed, this code sends an activation request.
	useEffect( () => {
		if ( ! requiredPlugins ) {
			return;
		}
		// Installed all required plugins.
		if ( notActivatedList.length > 0 ) {
			activatePlugin( notActivatedList[ 0 ] );
		}
	}, [ notActivatedList.length, requiredPlugins ] );

	// Confirmation before leaving the page.
	useEffect( () => {
		const handleBeforeUnload = () => importPercent < 100;
		window.onbeforeunload = handleBeforeUnload;

		return () => {
			window.onbeforeunload = null;
		};
	}, [ importPercent ] );

	return (
		<>
			<div className="flex flex-1 flex-col items-center justify-center w-full gap-y-4 pb-10">
				<div className="flex items-center justify-center gap-x-6">
					{ showProgressBar && ! importError && (
						<CircularProgressBar
							colorCircle="#3d45921a"
							colorSlice={ importError ? '#EF4444' : '#3D4592' }
							percent={ importPercent }
							round
							speed={
								importError || status === 'retrying' ? 0 : 15 //eslint-disable-line
							}
							fontColor="#0F172A"
							fontSize="18px"
							fontWeight={ 700 }
							size={ 72 }
						/>
					) }
					{ importError && (
						<ErrorModel
							error={ importErrorMessages }
							websiteInfo={ websiteInfo }
							tryAgainCallback={ tryAainCallback }
						/>
					) }
					<div className="flex flex-col">
						{ ! importEnd && ! importError && (
							<h4>
								{ __(
									'We are importing your website…',
									'astra-sites'
								) }
							</h4>
						) }
						{ ! importError && (
							<div className="zw-sm-normal text-app-text w-[350px]">
								<ImportLoaderAi onClickNext={ onClickNext } />
							</div>
						) }
					</div>
				</div>
				{ ! importError && (
					<>
						<div className="relative flex items-center justify-center px-10 py-6 h-120 w-120 bg-loading-website-grid-texture">
							<img
								className="w-[30rem] h-[20.875rem]"
								src={ `${ imageDir }/build-with-ai/migrate.svg` }
								alt={ __( 'Migrating', 'astra-sites' ) }
							/>
						</div>
					</>
				) }
			</div>
		</>
	);
};

export default compose(
	withDispatch( ( dispatch ) => {
		const { setNextAIStep, setPreviousAIStep } = dispatch( STORE_KEY );

		return {
			onClickNext: setNextAIStep,
			onClickPrevious: setPreviousAIStep,
		};
	} )
)( ImportAiSite );
