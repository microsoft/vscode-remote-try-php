import { useEffect } from '@wordpress/element';
import addBlockEditorDynamicStyles from '@Controls/addBlockEditorDynamicStyles';
import { useDeviceType } from '@Controls/getPreviewType';

const AddStaticStyles = ( ChildComponent )=> {
	const WrapWithStyle = ( props ) => {
		useEffect( () => {
			addBlockEditorDynamicStyles();
		}, [] );

		return <ChildComponent { ...props } deviceType={ useDeviceType() } />
	}

    return WrapWithStyle;
}
export default AddStaticStyles;