import { useCallback, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import Dropdown from './dropdown';
import { BoltIcon } from '@heroicons/react/24/outline';
import Button from './button';
import { classNames, formatNumber } from '../helpers';
import useCredits from '../hooks/use-credits';
import ConfirmationPopup from './confirmation-popup';

const HeaderCreditStatus = () => {
	const { remaining, currentBalanceStatus } = useCredits();
	const [ showRevokePopup, setShowRevokePopup ] = useState( false );

	const handleClickGetMoreCredits = ( close ) => () => {
		window.open( aiBuilderVars.get_more_credits_url, '_blank' );
		if ( typeof close !== 'function' ) {
			return;
		}
		close();
	};

	const handleClickRevokeAccess = ( event ) => {
		event.preventDefault();
		setShowRevokePopup( true );
	};

	const revokeAccessPopup = {
		revokeText: __( 'Revoke Access', 'ai-builder' ),
		moreCreditText: __( 'Get More Credits', 'ai-builder' ),
		moreCreditdesc: __(
			'Credits are used to personalize templates with AI.',
			'ai-builder'
		),
	};

	const handleConfirmRevokeAccess = useCallback( async () => {
		try {
			const response = await apiFetch( {
				path: '/gutenberg-templates/v1/revoke-access',
				method: 'POST',
				headers: {
					'X-WP-Nonce': aiBuilderVars.rest_api_nonce,
					'content-type': 'application/json',
				},
			} );
			if ( response.success ) {
				window.location.reload();
			}
		} catch ( error ) {
			// TODO: Handle error
		} finally {
			//setShowRevokePopup( false );
		}
	}, [] );

	return (
		<>
			<Dropdown
				trigger={ ( { open: active } ) => (
					<button
						className={ classNames(
							'min-h-[36px] flex items-center justify-center group text-sm leading-[21px] font-normal text-body-text border border-solid border-border-primary focus:outline-none bg-transparent pl-3 rounded cursor-pointer mt-5 mr-5',
							active && 'bg-background-secondary',
							currentBalanceStatus.danger &&
								'bg-credit-danger/5 text-credit-danger border-credit-danger/5',
							currentBalanceStatus.warning &&
								'bg-credit-warning/5 text-credit-warning border-credit-warning/5'
						) }
					>
						<span>{ formatNumber( remaining ) }</span>
						<span className="p-2 flex items-center justify-center">
							<BoltIcon
								className={ classNames(
									'w-5 h-5 text-nav-inactive group-active:text-nav-active transition-colors duration-150 ease-in-out',
									currentBalanceStatus.danger &&
										'text-credit-danger',
									currentBalanceStatus.warning &&
										'text-credit-warning'
								) }
							/>
						</span>
					</button>
				) }
				placement="right"
				width={ 'w-64' }
				contentClassName="border border-solid border-border-primary pt-3 pb-4 px-4 bg-white"
				mainClassName="!absolute top-0 right-0"
			>
				<Dropdown.Item>
					{ ( { close } ) => (
						<div
							className="w-full space-y-4"
							onClick={ ( event ) => {
								event.stopPropagation();
								event.preventDefault();
							} }
							aria-hidden="true"
						>
							<div className="w-full space-y-2">
								<div className="flex items-center justify-between w-">
									<span className="text-sm font-medium text-heading-text">
										{
											typeof remaining === 'number' &&
											! isNaN( remaining )
												? `${ remaining
														.toString()
														.replace(
															/\B(?=(\d{3})+(?!\d))/g,
															','
														) } Credits in Your Account`
												: 'Authentication Problem.' // Or any other fallback message
										}
									</span>
								</div>

								{ /* <div className="w-full h-1 bg-border-primary rounded-sm">
									<div className={ classNames( 'h-full bg-accent-spectra rounded-sm', currentBalanceStatus.warning && 'bg-credit-warning', currentBalanceStatus.danger && 'bg-credit-danger' ) } style={ { width: `${ percentage }%` } } />
								</div> */ }
							</div>
							<p className="m-0 text-border-secondary text-sm font-normal leading-5">
								{ revokeAccessPopup.moreCreditdesc }
							</p>
							<Button
								className="w-full bg-background-tertiary text-accent-spectra"
								variant="blank"
								onClick={ handleClickGetMoreCredits( close ) }
								isSmall
							>
								{ revokeAccessPopup.moreCreditText }
							</Button>
							<Button
								className="w-full bg-background-primary text-credit-danger py-1 mt-2 h-fit"
								variant="blank"
								onClick={ handleClickRevokeAccess }
								isSmall
							>
								{ revokeAccessPopup.revokeText }
							</Button>
						</div>
					) }
				</Dropdown.Item>
			</Dropdown>
			<ConfirmationPopup
				open={ showRevokePopup }
				setOpen={ setShowRevokePopup }
				title={ __( 'Revoke Access', 'ai-builder' ) }
				description={ `${ __(
					'Are you sure you wish to revoke the authorization token?',
					'ai-builder'
				) }\n${ __(
					'You will need to re-authorize Zip to use it again.',
					'ai-builder'
				) }` }
				confirmBtnTitle={ __( 'Revoke', 'ai-builder' ) }
				cancelBtnTitle={ __( 'Cancel', 'ai-builder' ) }
				onClickCancel={ () => setShowRevokePopup( false ) }
				onClickConfirm={ handleConfirmRevokeAccess }
			/>
		</>
	);
};

export default HeaderCreditStatus;
