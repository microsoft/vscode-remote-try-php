import React, { useEffect } from 'react';
import { decodeEntities } from '@wordpress/html-entities';
import { useStateValue } from '../../store/store';
import { getTotalTime } from '../../utils/functions';

const ImportLoaderAi = ( { onClickNext } ) => {
	const [ { importPercent, builder, importStatus, importError }, dispatch ] =
		useStateValue();

	useEffect( () => {
		if ( importPercent !== 100 || importError ) {
			return;
		}

		const start = localStorage.getItem( 'st-import-start' );
		const end = localStorage.getItem( 'st-import-end' );
		const diff = end - start;
		const unixTimeInSeconds = Math.floor( diff / 1000 );

		const totalTime = start && end ? getTotalTime( unixTimeInSeconds ) : 0;
		const typeOfTime = totalTime > 1 ? 'minutes' : 'seconds';

		const timeTaken = totalTime;

		const themeName = builder !== 'fse' ? '@AstraWP' : '@WPSpectra';

		dispatch( {
			type: 'set',
			confettiDone: true,
			importTimeTaken: {
				time: timeTaken,
				type: typeOfTime,
				themeName,
			},
		} );
		onClickNext();
	}, [ importPercent, importStatus, importError ] );

	return (
		<div className="ist-import-progress ist-ai" style={ { marginTop: 0 } }>
			<div
				className="ist-import-progress-info"
				style={ {
					marginTop: 0,
					marginBottom: 0,
				} }
			></div>
			<div
				className="ist-import-progress-info"
				style={ {
					marginTop: 0,
					marginBottom: 0,
				} }
			>
				<div className={ `ist-import-progress-info-text` }>
					<span className="import-status-string">
						<p>{ importStatus + decodeEntities( '&nbsp;' ) }</p>
					</span>
					<div className="import-done-section"></div>
				</div>
			</div>
		</div>
	);
};

export default ImportLoaderAi;
