import { DocumentDuplicateIcon } from '@heroicons/react/24/outline';
import React from 'react';
import toast from 'react-hot-toast';
import { classNames, copyToClipboard } from '../utils/helpers';
import { toastBody } from '../helpers/index';
import { ExternalLinkIcon } from '../../ui/icons';
import { __ } from '@wordpress/i18n';

const SiteLoginCredentials = ( {
	url,
	wp_user,
	wp_password,
	variant = 'dark',
	className = '',
	hideTitle = false,
	hideCopyIcon = false,
} ) => {
	const bgClassName = {
		dark: 'bg-dark-app-container',
		light: 'bg-white',
	};
	const textClassName = {
		dark: {
			heading: 'text-dark-app-heading',
			text: 'text-dark-app-text',
		},
		light: {
			heading: 'text-app-heading',
			text: 'text-app-text',
		},
	};
	const borderClassName = {
		dark: 'border-dark-app-border',
		light: 'border-app-border-hover',
	};

	const copyLoginCredentials = ( event ) => {
		copyToClipboard(
			`URL: ${ url }/wp-admin\nUsername: ${ wp_user }\nPassword: ${ wp_password }`
		);

		const copyTextNode =
			event.target.closest( 'div#zw-copy-info' ).firstChild;

		if ( ! copyTextNode ) {
			return;
		}

		copyTextNode.innerText = 'Copied!';
		setTimeout( () => {
			copyTextNode.innerText = 'Copy';
		}, 3000 );
	};

	return (
		<div
			className={ classNames(
				'h-full w-full px-8 py-6 bg-dark-app-container border border-dark-app-border rounded-md flex flex-col gap-3 flex-1',
				bgClassName[ variant ],
				borderClassName[ variant ],
				className
			) }
		>
			<div className="flex items-center gap-2">
				{ ! hideTitle && (
					<h4
						className={ classNames(
							'text-dark-app-heading',
							textClassName[ variant ]?.heading
						) }
					>
						{ __( 'Login credentials:', 'astra-sites' ) }
					</h4>
				) }
				{ ! hideCopyIcon && (
					<div
						id="zw-copy-info"
						className={ classNames(
							'flex items-center justify-center gap-1.5 py-1 pl-2 pr-1.5 h-6 w-fit rounded border border-dark-app-border bg-dark-app-container cursor-pointer',
							bgClassName[ variant ],
							borderClassName[ variant ]
						) }
						onClick={ copyLoginCredentials }
					>
						<p
							className={ classNames(
								'zw-xs-normal text-dark-app-text',
								textClassName[ variant ]?.text
							) }
						>
							{ __( 'Copy', 'astra-sites' ) }
						</p>
						<DocumentDuplicateIcon className="w-4 h-4 text-app-inactive-icon" />
					</div>
				) }
			</div>
			<div className="flex items-center gap-2">
				<p
					className={ classNames(
						'zw-base-semibold text-dark-app-heading',
						textClassName[ variant ]?.heading
					) }
				>
					{ __( 'URL:', 'astra-sites' ) }
				</p>
				<a
					href={ url }
					className="group overflow-ellipsis whitespace-nowrap text-app-heading overflow-hidden flex items-center gap-2"
					target="_blank"
					rel="noreferrer"
				>
					<p
						className={ classNames(
							'group-hover:underline truncate zw-base-normal text-dark-app-text',
							textClassName[ variant ]?.text
						) }
					>
						{ url }
					</p>

					<ExternalLinkIcon className="text-app-inactive-icon shrink-0" />
				</a>
			</div>
			<div className="flex items-center gap-2">
				<p
					className={ classNames(
						'zw-base-semibold text-dark-app-heading',
						textClassName[ variant ]?.heading
					) }
				>
					{ __( 'Username:', 'astra-sites' ) }
				</p>
				<div className="flex items-center gap-2">
					<p
						className={ classNames(
							'group-hover:underline zw-base-normal text-dark-app-text',
							textClassName[ variant ]?.text
						) }
					>
						{ wp_user }
					</p>
					<DocumentDuplicateIcon
						className="w-4 h-4 text-app-inactive-icon cursor-pointer"
						onClick={ () => {
							copyToClipboard( wp_user );
							toast.success(
								toastBody( {
									message: 'Username copied to clipboard',
								} )
							);
						} }
					/>
				</div>
			</div>
			<div className="flex items-center gap-2">
				<p
					className={ classNames(
						'zw-base-semibold text-dark-app-heading',
						textClassName[ variant ]?.heading
					) }
				>
					{ __( 'Password:', 'astra-sites' ) }
				</p>
				<div className="flex items-center gap-2">
					<p
						className={ classNames(
							'group-hover:underline zw-base-normal text-dark-app-text',
							textClassName[ variant ]?.text
						) }
					>
						{ wp_password }
					</p>
					<DocumentDuplicateIcon
						className="w-4 h-4 text-app-inactive-icon cursor-pointer"
						onClick={ () => {
							copyToClipboard( wp_password );
							toast.success(
								toastBody( {
									message: 'Password copied to clipboard',
								} )
							);
						} }
					/>
				</div>
			</div>
		</div>
	);
};

export default SiteLoginCredentials;
