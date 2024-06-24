import { useSelect, dispatch } from '@wordpress/data';
import { store as spectraStore } from '@Store';
import { isCustomizerPage } from '@Utils/Helpers';

export const useDeviceType = () => {
	const deviceType = useSelect( ( select ) => {
		const getDeviceFromStore = select( 'core/edit-site' )?.__experimentalGetPreviewDeviceType() ||
			select( 'core/edit-post' )?.__experimentalGetPreviewDeviceType() || select( spectraStore )?.getDeviceType();

		return getDeviceFromStore || 'Desktop'
	}, [] );

	return deviceType || '';
};

/**
 * Sets the preview device type for the Gutenberg editor.
 *
 * @param {string} device - The value representing the device type.
 * @param {boolean} updateInCustomizer - Whether to update the device type in the customizer preview.
 */
export const setDeviceType = ( device, updateInCustomizer = true ) => {
    const setPreviewDeviceType = dispatch( 'core/edit-site' )?.__experimentalSetPreviewDeviceType || dispatch( 'core/edit-post' )?.__experimentalSetPreviewDeviceType || dispatch( spectraStore )?.setDeviceType;

    // Verify setPreviewDeviceType is available and setPreviewDeviceType should be function.
    if( ! setPreviewDeviceType || typeof setPreviewDeviceType !== 'function' ){
        return;
    }

	setPreviewDeviceType( device );

    // If we don't want to update the device type in the customizer preview, return.
    if ( ! updateInCustomizer ) {
        return;
    }

    // This code sets the device type in the customizer preview. It's particularly useful when not using a Full Site Editing (FSE) theme.
    setCustomizerPreview( device );
};

/**
 * This function is used to set previewedDevice in customizer if it is customizer page.
 * 
 * @param {string} deviceType deviceType should be string e.g. 'desktop', 'tablet', 'mobile' may be 'Desktop', 'Tablet', 'Mobile'. 
 */
export const setCustomizerPreview = ( deviceType ) => {
    if ( ! isCustomizerPage() ) {
        return;
    }

    // deviceType should be string.
    if ( typeof deviceType !== 'string' ) {
        return;
    }

    const deviceTypeLower = deviceType.toLowerCase();

    // Check deviceType is valid.
    if ( ! [ 'desktop', 'tablet', 'mobile' ].includes( deviceTypeLower ) ) {
        return;
    }

    wp.customize.previewedDevice.set( deviceTypeLower );
}

/**
 * This function is used to set deviceType in customizer get previewedDevice if it is customizer page and set deviceType gutenberg store.
 */
export const setDeviceOnCustomizerAction = () => {
    if ( ! isCustomizerPage() ) {
        return;
    }

    window.wp.customize.bind( 'ready', () => {
        window.wp.customize.previewedDevice.bind( ( device ) => {
            if ( ! device ) {
                return;
            }

            // Check device type only mobile, tablet and desktop.
            if ( ! [ 'mobile', 'tablet', 'desktop' ].includes( device ) ) {
                return;
            }

            const deviceTypeFirstLetterUpper = device.charAt( 0 ).toUpperCase() + device.slice( 1 );
        
            setDeviceType( deviceTypeFirstLetterUpper, false );
        } );
    } );
}