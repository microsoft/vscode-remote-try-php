const getGBSEditorStyles = ( globalBlockStyles, globalBlockStyleId ) => {
    if ( ! globalBlockStyles || ! globalBlockStyleId ) {
        return '';
    }
    let editorStyles = '';
    for ( const style of globalBlockStyles ) {
        if ( style?.value === globalBlockStyleId ) {
            editorStyles = style?.editorStyles
            break;
        }
    }
    return editorStyles;
};

export default getGBSEditorStyles;