import { __ } from '@wordpress/i18n';
const sseImport = {
	complete: {
		posts: 0,
		media: 0,
		users: 0,
		comments: 0,
		terms: 0,
	},

	updateDelta( type, delta ) {
		this.complete[ type ] += delta;

		const self = this;
		requestAnimationFrame( function () {
			self.render();
		} );
	},

	updateProgress( type, complete, total, dispatch, percentage ) {
		const text = complete + '/' + total;

		if ( 'undefined' !== type && 'undefined' !== text ) {
			total = parseInt( total );
			if ( 0 === total || isNaN( total ) ) {
				total = 1;
			}

			const percent = parseInt( complete ) / total;
			const progressBar = percent * 100;

			if ( progressBar <= 100 ) {
				if ( 'function' === typeof dispatch ) {
					dispatch( {
						type: 'set',
						importStatus: __( 'Importing Content…', 'astra-sites' ),
					} );
					percentage += 5;
					dispatch( {
						type: 'set',
						importPercent: percentage >= 90 ? 90 : percentage,
					} );
				}
			}
		}
	},

	render( dispatch, percentage ) {
		const types = Object.keys( this.complete );
		let complete = 0;
		let total = 0;

		for ( let i = types.length - 1; i >= 0; i-- ) {
			const type = types[ i ];
			this.updateProgress(
				type,
				this.complete[ type ],
				this.data.count[ type ],
				dispatch,
				percentage
			);

			complete += this.complete[ type ];
			total += this.data.count[ type ];
		}

		this.updateProgress( 'total', complete, total, dispatch, percentage );
	},
};

export default sseImport;
