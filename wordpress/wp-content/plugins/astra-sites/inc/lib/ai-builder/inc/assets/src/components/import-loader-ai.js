import { useEffect } from '@wordpress/element';
import { decodeEntities } from '@wordpress/html-entities';
import { getTotalTime } from '../utils/functions';
import { useDispatch, useSelect } from '@wordpress/data';
import { STORE_KEY } from '../store';

const ImportLoaderAi = ( { onClickNext } ) => {
	const { importPercent, builder, importStatus, importError } = useSelect(
		( select ) => {
			const { getImportSiteProgressData } = select( STORE_KEY );
			return {
				...getImportSiteProgressData(),
			};
		},
		[]
	);
	const { updateImportAiSiteData: dispatch } = useDispatch( STORE_KEY );

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
		<div className="ist-import-progress ist-ai mt-0">
			<div className="ist-import-progress-info my-0"></div>
			<div className="ist-import-progress-info my-0">
				<div className={ `ist-import-progress-info-text` }>
					<span className="import-status-string">
						<p className="text-sm font-normal">
							{ importStatus + decodeEntities( '&nbsp;' ) }
						</p>
					</span>
					<div className="import-done-section"></div>
				</div>
			</div>
		</div>
	);
};

export default ImportLoaderAi;
