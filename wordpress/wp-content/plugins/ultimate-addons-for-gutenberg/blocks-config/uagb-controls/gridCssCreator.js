/**
 * Creates a CSS grid layout string from a given grid object.
 *
 * @param {Array} gridObject - An array of objects, each representing a grid column or row. 
 * Each object can have a 'default' property set to 'custom' or 'minmax', and corresponding properties:
 * - 'custom': An object with 'value' and 'unit' properties.
 * - 'minmax': An object with 'min' and 'max' properties, each having 'value' and 'unit'.
 *
 * @return {string} A CSS grid layout string. For example: "1fr 1fr 1fr 1fr".
 */
export const gridCssCreator = ( gridObject ) => {
    let gridCss = '';
    gridObject.forEach( ( grid ) => {
        // Add space if the column is not the last column.
        if( gridCss ){
            gridCss = gridCss + ' ';
        }

        let createCss = '';
        if( 'custom' === grid?.default && ( grid?.custom?.value || 0 === grid?.custom?.value ) ){
            createCss = `minmax(1px, ${ grid.custom.value }${ grid.custom.unit })`;
        } else if( 'minmax' === grid.default ){
            createCss = `minmax(${ grid.min.value }${ grid.min.unit }, ${ grid.max.value }${ grid.max.unit })`;
        }else if( 'auto' === grid.default ){
            createCss = 'minmax(1px, auto)';
        }

        gridCss += createCss + ' ';
    } );

    // Grid css will as a result look like: "1fr 1fr 1fr 1fr"
    return gridCss;
}

/**
 * Creates a CSS object for a grid layout from given attributes and device type.
 *
 * @param {Object} attr - This object contains 
 * @param {string} [deviceType='Desktop'] - Device type for the layout. Ex: Desktop, Tablet, Mobile.
 *
 * `attr` properties include `gridColumn<deviceType>`, `gridRow<deviceType>`, `gridAlignItems<deviceType>`, 
 * `gridJustifyItems<deviceType>`, `gridAlignContent<deviceType>`, `gridJustifyContent<deviceType>`.
 *
 * @return {Object} CSS object for styling a grid layout.
 */
const gridCssObject = ( attr, deviceType = 'Desktop' ) => {
    const grid_css = {};
    
    // Check attribute is not empty and should be array.
    if( attr[ 'gridColumn' + deviceType ]?.length ){
        grid_css[ 'grid-template-columns' ] = gridCssCreator( attr[ 'gridColumn' + deviceType ] );
    }

    if( attr[ 'gridRow' + deviceType ]?.length ){
        grid_css[ 'grid-template-rows' ] = gridCssCreator( attr[ 'gridRow' + deviceType ] );
    }

    if( attr[ 'gridAlignItems' + deviceType ] ){
        grid_css[ 'align-items' ] = attr[ 'gridAlignItems' + deviceType ];
    }

    if( attr[ 'gridJustifyItems' + deviceType ] ){
        grid_css[ 'justify-items' ] = attr[ 'gridJustifyItems' + deviceType ];
    }

    if( attr[ 'gridAlignContent' + deviceType ] ){
        grid_css[ 'align-content' ] = attr[ 'gridAlignContent' + deviceType ];
    }

    if( attr[ 'gridJustifyContent' + deviceType ] ){
        grid_css[ 'justify-content' ] = attr[ 'gridJustifyContent' + deviceType ];
    }
    
    return grid_css;
}

export default gridCssObject;