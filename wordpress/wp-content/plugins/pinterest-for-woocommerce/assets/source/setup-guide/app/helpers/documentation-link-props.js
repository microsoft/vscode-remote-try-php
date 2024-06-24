/**
 * External dependencies
 */
import { recordEvent } from '@woocommerce/tracks';

/**
 * Clicking on an external documentation link.
 *
 * @event wcadmin_pfw_documentation_link_click
 *
 * @property {string} link_id Identifier of the link.
 * @property {string} context `'settings' | 'welcome-section' | 'wizard'` In which context the link was placed?
 * @property {string} href Href to which the user was navigated to.
 */

/**
 * Clicking on the link inside the notice.
 *
 * @event wcadmin_pfw_get_started_notice_link_click
 *
 * @property {string} link_id Identifier of the link.
 * @property {string} context What action was initiated.
 * @property {string} href Href to which the user was navigated to.
 *
 *
 */

/**
 * Creates properties for an external documentation link.
 * May take any other props to be extended and forwarded to a link element (`<a>`, `<Button isLink>`).
 *
 * Sets `target="_blank" rel="noopener"` and `onClick` handler that fires track event.
 *
 * Please be careful not to overwrite the `onClick` handler coincidently.
 *
 *
 * @fires wcadmin_pfw_documentation_link_click on click, with given `linkId` and `context`.
 * @param {Object} props React props.
 * @param {string} props.href Href to used by link and in track event.
 * @param {string} props.linkId Forwarded to {@link wcadmin_pfw_documentation_link_click}
 * @param {string} props.context Forwarded to {@link wcadmin_pfw_documentation_link_click}
 * @param {string} [props.target='_blank']
 * @param {string} [props.rel='noopener']
 * @param {Function} [props.onClick] onClick event handler to be decorated with firing Track event.
 * @param {string} [props.eventName='pfw_documentation_link_click'] The name of the event to be recorded
 * @param {...import('react').AnchorHTMLAttributes} props.props
 * @return {{href: string, target: string, rel: string, onClick: Function, props}} Documentation link props.
 */
function documentationLinkProps( {
	href,
	linkId,
	context,
	target = '_blank',
	rel = 'noopener',
	onClick,
	eventName = 'pfw_documentation_link_click',
	...props
} ) {
	return {
		href,
		target,
		rel,
		...props,
		onClick: ( event ) => {
			if ( onClick ) {
				onClick( event );
			}
			if ( ! event.defaultPrevented ) {
				recordEvent( eventName, {
					link_id: linkId,
					context,
					href,
				} );
			}
		},
	};
}
export default documentationLinkProps;
