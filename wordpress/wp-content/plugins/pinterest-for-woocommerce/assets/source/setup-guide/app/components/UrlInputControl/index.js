/**
 * External dependencies
 */
import classnames from 'classnames';
import { Icon, link as linkIcon } from '@wordpress/icons';
import { __experimentalInputControl as InputControl } from '@wordpress/components'; // eslint-disable-line @wordpress/no-unsafe-wp-apis

/**
 * Internal dependencies
 */
import './style.scss';

/**
 * Renders a <InputControl> with a link icon prefix.
 *
 * @param {Object} props React props.
 * @param {string} [props.className] Additional CSS class name to be appended.
 * @param {...*} [props.restProps] Props to be forwarded to the InputControl.
 *
 * @see module:@wordpress/components#__experimentalInputControl
 */
export default function UrlInputControl( { className, ...restProps } ) {
	return (
		<InputControl
			className={ classnames( 'pfw-url-input-control', className ) }
			prefix={ <Icon icon={ linkIcon } size={ 24 } /> }
			{ ...restProps }
		/>
	);
}
