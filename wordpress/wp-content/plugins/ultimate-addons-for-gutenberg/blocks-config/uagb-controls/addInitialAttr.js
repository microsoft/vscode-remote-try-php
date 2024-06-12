import { useEffect } from '@wordpress/element';
import { select } from '@wordpress/data';

const headingDescToggleDefault = 'yes' === uagb_blocks_info.uagb_old_user_less_than_2;

const getUniqId = ( blocks ) => blocks
	.reduce( ( result, block ) => {
		if ( block?.attributes?.block_id && block.name.includes( 'uagb' ) ) {
			result.blockIds.push( block.attributes.block_id );
			result.clientIds.push( block.clientId );
		}

		if ( block.innerBlocks ) {
			const { blockIds, clientIds } = getUniqId( block.innerBlocks );
			result.blockIds = [ ...result.blockIds, ...blockIds ];
			result.clientIds = [ ...result.clientIds, ...clientIds ];
		}

		return result;
	}, { blockIds: [], clientIds: [] } );

const checkDuplicate = ( blockIds, block_id, currentIndex ) => {
	const getFiltered =  blockIds.filter( ( el ) => ( el === block_id ) );
	return getFiltered.length > 1 && currentIndex === blockIds.lastIndexOf( block_id )
}

const addInitialAttr = ( ChildComponent ) => {
	const WrappedComponent = ( props ) => {
		const { name, setAttributes, clientId, attributes : { block_id } } = props;

		const listOfParentBlock = [
			'uagb/faq',
			'uagb/buttons',
			'uagb/icon-list',
			'uagb/restaurant-menu',
			'uagb/social-share',
			'uagb/content-timeline',
			'uagb/tabs',
			'uagb/how-to'
		]; // Add all parent block name here who's getting issue in customize preview.

		useEffect( () => {
			if ( uagb_blocks_info.is_customize_preview && ( '0' === block_id || undefined === block_id ) && listOfParentBlock.includes( name ) ) {
				document.addEventListener( `UAG-${name}-${clientId.substr( 0, 8 )}-BlockCustomizeWidgetEditor`, function ( e ) {
					setAttributes( { block_id: e.detail.id, classMigrate: e.detail.classMigrate, childMigrate: e.detail.childMigrate } );
				} );
			}
		}, [] );

		useEffect( () => {
			if ( uagb_blocks_info.is_customize_preview && ( '0' === block_id || undefined === block_id ) && listOfParentBlock.includes( name ) ) {
				const loadCustomEvent = new CustomEvent( `UAG-${name}-${clientId.substr( 0, 8 )}-BlockCustomizeWidgetEditor`, { detail: { id: clientId.substr( 0, 8 ), classMigrate: true, childMigrate: true }, } );
				document.dispatchEvent( loadCustomEvent );
			}
		}, [ props.attributes ] );

		useEffect( () => {
			const listOfClassMigrate = [
				'uagb/advanced-heading',
				'uagb/blockquote',
				'uagb/buttons',
				'uagb/call-to-action',
				'uagb/column',
				'uagb/columns',
				'uagb/icon-list',
				'uagb/marketing-button',
				'uagb/image-gallery',
				'uagb/info-box',
				'uagb/lottie',
				'uagb/restaurant-menu',
				'uagb/section',
				'uagb/social-share',
				'uagb/content-timeline',
				'uagb/table-of-contents',
				'uagb/team',
				'uagb/testimonial',
				'uagb/instagram-feed',
				'uagb/login',
				'uagb/register',
			];

			const listOfChildMigrate = [
				'uagb/buttons',
				'uagb/icon-list',
				'uagb/restaurant-menu',
				'uagb/social-share',
                'uagb/content-timeline',
				'uagb/instagram-feed',
			];

			const listOfIsHtml = [ 'uagb/cf7-styler', 'uagb/gf-styler' ];

			const listOfEditorInnerblocksPreview = [ 'uagb/countdown' ];

			const listOfAllTaxonomyStore = [ 'uagb/post-carousel', 'uagb/post-grid', 'uagb/post-masonry' ];

			const attributeObject = { block_id: clientId.substr( 0, 8 ) };

			if ( listOfAllTaxonomyStore.includes( name ) ) {
				attributeObject.allTaxonomyStore = undefined;
			}

            // editorInnerblocksPreview: This attribute is used to display innerblocks preview for 'Replace with Content' mode.
			if ( listOfEditorInnerblocksPreview.includes( name ) ) {
				attributeObject.editorInnerblocksPreview = false;
			}

			if ( listOfIsHtml.includes( name ) ) {
				attributeObject.isHtml = false;
			}

			if ( listOfChildMigrate.includes( name ) ) {
				attributeObject.childMigrate = true;
			}

			if ( listOfClassMigrate.includes( name ) ) {
				attributeObject.classMigrate = true;
			}

			if ( 'uagb/advanced-heading' === name ) {
				attributeObject.headingDescToggle = headingDescToggleDefault;
			}

			/**
			 * Resolve issue of reusable block.
			 * As of now we are not providing for all block
			 * After tested few blocks we will implement this is all blocks.
			 */
			const REUSABLE_BLOCK_ISSUE_RESOLVED_BLOCKS = [
				'uagb/image-gallery',
			];

			if( ! REUSABLE_BLOCK_ISSUE_RESOLVED_BLOCKS.includes( name ) ){
				const getStore = select( 'core/block-editor' );
				const getAllBlocks = getStore?.getBlocks ? getStore.getBlocks() : null;
				const { blockIds, clientIds } = getAllBlocks ? getUniqId( getAllBlocks ) : { blockIds: [], clientIds: [] };
				if ( 'not_set' === block_id || '0' === block_id || ! block_id || checkDuplicate( blockIds, block_id, clientIds.indexOf( clientId ) ) ) {
					setAttributes( attributeObject );
				}
			}else{
				setAttributes( attributeObject );
			}

		}, [ clientId ] );

		return <ChildComponent { ...props } />;
	};

	return WrappedComponent;
};
export default addInitialAttr;
