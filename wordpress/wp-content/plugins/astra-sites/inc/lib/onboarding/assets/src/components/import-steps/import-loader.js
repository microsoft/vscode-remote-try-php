import React from 'react';
import confetti from 'canvas-confetti';
import { __, sprintf } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';
import { useStateValue } from '../../store/store';
import { Button } from '../../components';
import ICONS from '../../../icons';
import { getTotalTime } from '../../utils/functions';
const { siteUrl } = starterTemplates;

const ImportLoader = () => {
	const [ { importPercent, importStatus, builder, confettiDone }, dispatch ] =
		useStateValue();
	const doneClass = 100 === importPercent ? 'import-done' : '';
	let percentClass = '';
	let stepText = '';

	if ( importPercent <= 25 ) {
		percentClass = 'import-1';
		stepText = __(
			'1. Installing required theme, plugins, forms, etc',
			'astra-sites'
		);
	}
	if ( importPercent > 25 && importPercent <= 50 ) {
		percentClass = 'import-2';
		stepText = __( '2. Importing pages, menus, posts, etc', 'astra-sites' );
	}
	if ( importPercent > 50 && importPercent <= 75 ) {
		percentClass = 'import-3';
		stepText = __(
			'3. Setting up customizer settings and the site settings',
			'astra-sites'
		);
	}
	if ( importPercent > 75 && importPercent <= 100 ) {
		percentClass = 'import-4';
		stepText = __( '4. Finalizing last few settings', 'astra-sites' );
	}

	const start = localStorage.getItem( 'st-import-start' );
	const end = localStorage.getItem( 'st-import-end' );
	const diff = end - start;
	const unixTimeInSeconds = Math.floor( diff / 1000 );

	const totalTime = start && end ? getTotalTime( unixTimeInSeconds ) : 0;
	const typeOfTime = totalTime > 1 ? 'minutes' : 'seconds';

	let timeTaken = totalTime;

	let descMessage;
	let tweetMessage;
	const themeName = builder !== 'fse' ? '@AstraWP' : '@WPSpectra';
	const threshold = 5; // Max 5 mins threshold.

	if ( timeTaken > 0 && timeTaken <= threshold ) {
		timeTaken = timeTaken < 1 ? timeTaken.split( '.' )[ 1 ] : timeTaken;

		descMessage = sprintf(
			//translators: %1$s Time taken %2$s Time Type %3$s Website Url.
			__(
				`Your Website is ready and it took just %1$s %2$s to build.`,
				'astra-sites'
			),
			timeTaken,
			typeOfTime
		);
		tweetMessage = sprintf(
			//translators: %1$s Time taken %2$s Time Type %3$s theme name.
			__(
				`I just built my website in %1$s %2$s with Starter Templates by %3$s. Can’t believe how easy it was!!! 😍`,
				'astra-sites'
			),
			timeTaken,
			typeOfTime,
			themeName
		);
	} else {
		descMessage = __(
			'Your Website is ready. It took just 25 seconds to build it.',
			'astra-sites'
		);
		tweetMessage = sprintf(
			//translators: %1$s theme name.
			__(
				`I just built my website with Starter Templates by %1$s in minutes. Can't believe how easy it is! 😍`,
				'astra-sites'
			),
			themeName
		);
	}

	const istConfetti = confetti.create(
		document.getElementById( 'ist-bashcanvas' ),
		{ resize: true }
	);

	if ( ! confettiDone && importPercent === 100 ) {
		document.body.classList.add( 'step-import-site-done' );
		setTimeout( function () {
			istConfetti( {
				particleCount: 250,
				origin: { x: 1, y: 1.4 },
				gravity: 0.4,
				spread: 80,
				ticks: 300,
				angle: 120,
				startVelocity: 100,
				colors: [
					'#0e6ef1',
					'#f5b800',
					'#ff344c',
					'#98e027',
					'#9900f1',
				],
			} );
		}, 100 );

		setTimeout( function () {
			istConfetti( {
				particleCount: 250,
				origin: { x: 0, y: 1.4 },
				gravity: 0.4,
				spread: 80,
				ticks: 300,
				angle: 60,
				startVelocity: 100,
				colors: [
					'#0e6ef1',
					'#f5b800',
					'#ff344c',
					'#98e027',
					'#9900f1',
				],
			} );
			dispatch( {
				type: 'set',
				confettiDone: true,
			} );
		}, 500 );
	}

	return (
		<div className="ist-import-progress">
			<div className="ist-import-progress-info">
				<div
					className={ `ist-import-progress-info-text ${ doneClass }` }
				>
					<span className="ist-import-text-inner">{ stepText }</span>
					<span className="ist-import-done-inner">
						{ descMessage }
					</span>
				</div>
				<div className="ist-import-progress-info-precent">
					{ importPercent > 100 ? 90 : importPercent }%
				</div>
			</div>
			<div className="ist-import-progress-bar-wrap">
				<div className="ist-import-progress-bar-bg">
					<div
						className={ `ist-import-progress-bar ${ doneClass } ${ percentClass }` }
					/>
				</div>
				<div className="import-progress-gap">
					<span />
					<span />
					<span />
				</div>
			</div>
			<div className="ist-import-progress-info">
				<div
					className={ `ist-import-progress-info-text ${ doneClass }` }
				>
					<span className="import-status-string">
						<p>{ importStatus + decodeEntities( '&nbsp;' ) }</p>
					</span>
					<div className="import-done-section">
						<div className="tweet-import-success">
							<p className="tweet-text">{ tweetMessage }</p>
							<a
								href={ `https://twitter.com/intent/tweet?text=${ tweetMessage }` }
								target="_blank"
								className="twitter-btn-wrap"
								rel="noreferrer"
							>
								<p className="tweet-btn">
									{ __( 'Tweet this', 'astra-sites' ) }
								</p>
								{ ICONS.twitter }
							</a>
						</div>
						<div className="import-done-text">
							<Button
								className="view-website-btn import-done-button"
								after
								onClick={ () => {
									window.open( siteUrl, '_blank' );
								} }
							>
								{ __( 'View Your Website', 'astra-sites' ) }
							</Button>
							<a
								className="view-dashboard-link"
								href={ astraSitesVars.dashboard_url }
							>
								{ __( 'Visit Dashboard', 'astra-sites' ) }
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	);
};

export default ImportLoader;
