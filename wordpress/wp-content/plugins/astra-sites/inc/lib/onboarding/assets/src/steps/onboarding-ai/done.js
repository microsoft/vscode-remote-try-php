import { ArrowRightIcon } from '@heroicons/react/24/outline';
import { sprintf, __ } from '@wordpress/i18n';
import { useCallback, useEffect } from 'react';
import Button from './components/button';
import Confetti from './components/Confetti';
import { removeLocalStorageItem } from './helpers';
import { USER_KEYWORD } from './select-template';
import { saveGutenbergAsDefaultBuilder } from '../../utils/functions';

const BuildDone = () => {
	const handleClickVisitDashboard = () => {
		window.open( starterTemplates.adminUrl, '_self' );
	};

	const handleClickSeeWebsite = () => {
		window.open( starterTemplates.siteUrl, '_blank' );
	};

	const removeSavedState = useCallback( () => {
		removeLocalStorageItem( 'ai-onboarding-details' );
		removeLocalStorageItem( 'starter-templates-onboarding' );
		removeLocalStorageItem( 'create-site-payload' );
		removeLocalStorageItem( USER_KEYWORD );
	}, [] );

	// Remove onboarding details from local storage.
	useEffect( () => {
		removeSavedState();
		saveGutenbergAsDefaultBuilder();
		window.addEventListener( 'beforeunload', removeSavedState );

		return () => {
			window.removeEventListener( 'beforeunload', removeSavedState );
		};
	}, [] );

	return (
		<div className="w-screen h-screen overflow-y-hidden">
			<div className="relative grid grid-cols-1 grid-rows-1 place-items-center min-h-screen py-5 md:py-0 px-5 md:px-10 bg-app-light-background ">
				<div className="w-full max-w-[32.5rem] p-8 my-10 md:my-0 rounded-lg space-y-6 shadow-xl bg-white">
					<span className="flex items-center justify-center gap-3 text-2xl">
						<span>🎉</span>
						<span>🥳</span>
					</span>
					<div className="space-y-3 text-center">
						<h1
							dangerouslySetInnerHTML={ {
								__html: sprintf(
									/* translators: %s: line break */
									__(
										`Woohoo, your website %1$s is ready!`,
										'astra-sites'
									),
									'<br />'
								),
							} }
						/>
						<p className="text-app-text text-base text-center font-normal leading-6">
							{ __(
								'You did it! Your brand new website is all set to shine online.',
								'astra-sites'
							) }
						</p>
					</div>
					<div className="w-full flex flex-col justify-center items-center gap-5 flex-wrap md:flex-nowrap">
						<Button
							onClick={ handleClickSeeWebsite }
							variant="primary"
							size="l"
							className="w-full min-w-fit min-h-[48px]"
						>
							<span>
								{ __( 'See Your Website', 'astra-sites' ) }
							</span>
						</Button>
						<Button
							onClick={ handleClickVisitDashboard }
							variant="blank"
							size="l"
							className="w-full min-w-fit py-0 text-accent-st"
						>
							<span>
								{ __( 'Visit Dashboard', 'astra-sites' ) }
							</span>
							<ArrowRightIcon className="w-5 h-5" />
						</Button>
					</div>
				</div>
				<Confetti />
			</div>
		</div>
	);
};

export default BuildDone;
