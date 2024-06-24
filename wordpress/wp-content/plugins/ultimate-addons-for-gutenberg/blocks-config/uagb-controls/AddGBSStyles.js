import { useEffect, useMemo } from '@wordpress/element';
import { STORE_NAME as storeName } from '@Store/constants';
import { useSelect } from '@wordpress/data';
import getGBSEditorStyles from '@Controls/getGBSEditorStyles';
import AddGBSStylesDom from './AddGBSStylesDom';
import { GBS_RANDOM_NUMBER } from '@Utils/Helpers';
import { blocksAttributes } from '@Attributes/getBlocksDefaultAttributes';
import getAttributeFallback, { getFallbackNumber } from '@Controls/getAttributeFallback';

const AddGBSStyles = ( ChildComponent )=> {
	const WrapWithStyle = ( props ) => {
		const { globalBlockStyles, initialStateFlag } = useSelect( ( select ) => {
			const getStore = select( storeName );
			return {
				globalBlockStyles: getStore.getGlobalBlockStyles(),
				initialStateFlag: getStore.getState()?.initialStateSetFlag,
			}
		}, [] );

		const { 
			attributes,
			attributes : {
				globalBlockStyleId
			},
			setAttributes,
			name,
		} = props;

		const blockName = name.replace( 'uagb/', '' );
		
		const modifiedAttr = { ...attributes };

		const isGBSPresent = useMemo( () => globalBlockStyles?.find( style => {
			return globalBlockStyleId && style?.value === globalBlockStyleId;
		} ), [ globalBlockStyleId, globalBlockStyles ] );

		useEffect( () => {
			if ( uagb_blocks_info?.spectra_pro_status && 'enabled' === uagb_blocks_info?.uag_enable_gbs_extension ) {
				const editorStyles = getGBSEditorStyles( globalBlockStyles, globalBlockStyleId );
				AddGBSStylesDom( globalBlockStyleId, editorStyles );
			}

			// Don't set attribute is extension is not enabled.
			if ( 'disabled' === uagb_blocks_info?.uag_enable_gbs_extension ){
				return;
			}

			if( initialStateFlag && ! isGBSPresent && globalBlockStyleId ){
				const resetObject = { globalBlockStyleId : '', globalBlockStyleName : '' };
				for ( const attributeKey in modifiedAttr ) {
					// Check attribute support GBS style.
					if( ! blocksAttributes?.[ blockName ]?.[ attributeKey ]?.isGBSStyle ){
						continue;
					}

					// Reset gbs number attributes.
					if( GBS_RANDOM_NUMBER === modifiedAttr?.[ attributeKey ] ){
						resetObject[ attributeKey ] = getFallbackNumber( '', attributeKey, blockName );
					}

					// Reset the attributes which are not GBS style attributes.
					if( ! modifiedAttr?.[ attributeKey ] ){
						resetObject[ attributeKey ] = getAttributeFallback( '', attributeKey, blockName );
					}
				}
				setAttributes( resetObject );
			}
		}, [ globalBlockStyles ] );

		// Filter the placeholder attribute.
		if ( globalBlockStyleId && 'disabled' === uagb_blocks_info?.uag_enable_gbs_extension ){
			// If extension is disabled then set attributes as default.
			for ( const objectKey in modifiedAttr ) {
				// Check attribute support GBS style.
				if( ! blocksAttributes?.[ blockName ]?.[ objectKey ]?.isGBSStyle ){
					continue;
				}

				// Reset gbs number attributes.
				if( GBS_RANDOM_NUMBER === modifiedAttr?.[ objectKey ] ){
					modifiedAttr[ objectKey ] = getFallbackNumber( '', objectKey, blockName );
				}

				// Reset the attributes which are not GBS style attributes.
				if( ! modifiedAttr?.[ objectKey ] ){
					modifiedAttr[ objectKey ] = getAttributeFallback( '', objectKey, blockName );
				}
			}
		}else{
			for ( const objectKey in modifiedAttr ) {
				// Replace GBS_RANDOM_NUMBER with empty string.
				if( GBS_RANDOM_NUMBER === modifiedAttr?.[objectKey] ){
					modifiedAttr[objectKey] = '';
				}
			}
		}
		
		const updatedAttributes = {
			...attributes,
			...modifiedAttr
		};

		props = { ...props, ...{ attributes: updatedAttributes, isGBSPresent } };

		return <ChildComponent { ...props }/>
	}

    return WrapWithStyle;
}
export default AddGBSStyles;