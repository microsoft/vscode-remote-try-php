const uagb_deactivated_blocks = uagb_deactivate_blocks.deactivated_blocks;
// If we are recieving an object, let's convert it into an array.
if ( uagb_deactivated_blocks.length ) {
	if ( typeof wp.blocks.unregisterBlockType !== 'undefined' ) {
		for ( const block_index in uagb_deactivated_blocks ) {
			const blockName = uagb_deactivated_blocks[block_index];
			if ( 'uagb/masonry-gallery' === blockName ) {
				continue;
			}

            // Check if the block is registered before attempting to unregister it
            if ( wp.blocks.getBlockType( blockName ) ) {
                wp.blocks.unregisterBlockType( blockName );
            }
		}
	}
}
