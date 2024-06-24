import { select, dispatch, subscribe } from '@wordpress/data';
import { createBlock, parse, serialize } from '@wordpress/blocks';
import isInvalid from './isInvalid';

// Flag to Detect if At Least One Block was Recovered.
let recoveryDone = false;

// Create Recovery CSS to Hide All Errornous Blocks.
const createRecoveryCSS = () => {
	const recoveryCSS = document.createElement( 'style' );
	recoveryCSS.setAttribute( 'id', 'uagb-recovery-styles' );
	recoveryCSS.innerHTML = '.has-warning[data-type^="uagb/"] { opacity: 0 !important; }';
	document.body.appendChild( recoveryCSS );
};

// Destroy the Recovery CSS to Restore the Editor to its Original State.
const destroyRecoveryCSS = () => {
	const recoveryCSS = document.querySelector( '#uagb-recovery-styles' );
	if ( recoveryCSS ) {
		document.body.removeChild( recoveryCSS );
	}
};

// Start Block Recovery for all Spectra Blocks.
const initBlockRecovery = ( blocks ) => {
	const curBlocks = [ ...blocks ];
	let isRecovered = false;

	const recoverInnerBlocks = ( innerBlocks ) => {
		innerBlocks.forEach( ( block ) => {
			if ( isInvalid( block ) ) {
				isRecovered = true;
				const newBlock = recoverBlock( block );
				for ( const key in newBlock ) {
					block[ key ] = newBlock[ key ];
				}
			}

			if ( block.innerBlocks.length ) {
				recoverInnerBlocks( block.innerBlocks );
			}
		} );
	};

	recoverInnerBlocks( curBlocks );
	return [ curBlocks, isRecovered ];
};

// Create Replacement Blocks Based on the Fixed Variant.
const recoverBlocks = ( allBlocks ) =>
	allBlocks.map( ( block ) => {
		const curBlock = block;

		if ( 'core/block' === block.name ) {
			const {
				attributes: { ref },
			} = block;
			const reusableBlockPosts = select( 'core' ).getEntityRecords( 'postType', 'wp_block' );

			let reusableBlockPost = null;

			if ( reusableBlockPosts ) {
				reusableBlockPosts?.forEach( ( post ) => {
					if ( ref === post?.id ) {
						reusableBlockPost = post?.content?.raw;
					}
				} );
			}

			if ( null === reusableBlockPost ) {
				return curBlock;
			}

			const parsedBlocks = parse( reusableBlockPost ) || [];

			const [ recoveredBlocks, isRecovered ] = initBlockRecovery( parsedBlocks );

			if ( isRecovered ) {
				recoveryDone = true;
				return {
					blocks: recoveredBlocks,
					isReusable: true,
					ref,
				};
			}
		}

		if ( curBlock.innerBlocks && curBlock.innerBlocks.length ) {
			const newInnerBlocks = recoverBlocks( curBlock.innerBlocks );
			if ( newInnerBlocks.some( ( innerBlock ) => innerBlock.recovered ) ) {
				curBlock.innerBlocks = newInnerBlocks;
				curBlock.replacedClientId = curBlock.clientId;
				curBlock.recovered = true;
			}
		}
		if ( isInvalid( curBlock ) ) {
			recoveryDone = true;
			const newBlock = recoverBlock( curBlock );
			newBlock.replacedClientId = curBlock.clientId;
			newBlock.recovered = true;
			return newBlock;
		}

		return curBlock;
	} );

// Recover Current Block.
const recoverBlock = ( { name, attributes, innerBlocks } ) => createBlock( name, attributes, innerBlocks );

// Start with the Automatic Block Recovery Process.
const autoBlockRecovery = () => {
	createRecoveryCSS();
	setTimeout( () => {
		const unsubscribe = subscribe( () => {
			if ( select( 'core' ).getEntityRecords( 'postType', 'wp_block' ) !== null ) {
				unsubscribe();
				const recoveredBlocks = recoverBlocks( select( 'core/block-editor' ).getBlocks() );
				recoveredBlocks.forEach( ( block ) => {
					if ( block.isReusable && block.ref ) {
						dispatch( 'core' )
							.editEntityRecord( 'postType', 'wp_block', block.ref, {
								content: serialize( block.blocks ),
							} )
							.then();
					}

					if ( block.recovered && block.replacedClientId ) {
						dispatch( 'core/block-editor' ).replaceBlock( block.replacedClientId, block );
					}
				} );
				if ( recoveryDone ) {
					//eslint-disable-next-line no-console
					console.log(
						'%cSpectra Auto Recovery Enabled: All Spectra Blocks on this page have been recovered!',
						'border-radius: 6px; width: 100%; margin: 16px 0; padding: 16px; background-color: #007CBA; color: #fff; font-weight: bold; text-shadow: 2px 2px 2px #0063A1;'
					);
				}
				destroyRecoveryCSS();
			}
		} );
	}, 0 );
};

export default autoBlockRecovery;
