import GlobalBlockStyles from '@Components/global-block-link';

const renderGBSSettings = ( styling, setAttributes, attributes ) => {
    if ( ! uagb_blocks_info?.spectra_pro_status || 'enabled' !== uagb_blocks_info?.uag_enable_gbs_extension ) {
        return null;
    }

    return (
        <GlobalBlockStyles
            { ...{ setAttributes, styling, attributes  } }
        />
    )
};

export default renderGBSSettings;