
import { setCustomizerPreview } from '@Utils/customizer-preview-device';
import { dispatch } from '@wordpress/data';

/**
 * Sets the preview device type for the Gutenberg editor.
 *
 * @param {string} device - The value representing the device type.
 */
const setDeviceType = ( device ) => {
    const setPreviewDeviceType = dispatch( 'core/edit-site' )?.__experimentalSetPreviewDeviceType || dispatch( 'core/edit-post' )?.__experimentalSetPreviewDeviceType;

    // Verify setPreviewDeviceType is available and setPreviewDeviceType should be function.
    if( ! setPreviewDeviceType || typeof setPreviewDeviceType !== 'function' ){
        return;
    }

	setPreviewDeviceType( device );
    // This code sets the device type in the customizer preview. It's particularly useful when not using a Full Site Editing (FSE) theme.
    setCustomizerPreview( device );
};

// Export the function.
export default setDeviceType;