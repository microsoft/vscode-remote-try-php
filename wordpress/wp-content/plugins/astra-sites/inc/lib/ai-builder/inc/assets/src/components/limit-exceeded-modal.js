import { useSelect, useDispatch } from '@wordpress/data';
import { renderToString } from 'react-dom/server';
import { ExclamationTriangleColorfulIcon } from '../ui/icons';
import { STORE_KEY } from '../store';
import Modal from './modal';
import ModalTitle from './modal-title';
import Button from './button';
import { __, sprintf } from '@wordpress/i18n';

const LimitExceedModal = ( { onOpenChange, openTarget = '_blank' } ) => {
	const { setLimitExceedModal } = useDispatch( STORE_KEY );

	const { limitExceedModal } = useSelect( ( select ) => {
		const { getLimitExceedModalInfo } = select( STORE_KEY );

		return {
			limitExceedModal: getLimitExceedModalInfo(),
		};
	} );

	const planName = (
		<span className="zw-base-semibold text-app-heading capitalize">
			{ aiBuilderVars?.zip_plans?.active_plan?.slug }
		</span>
	);

	const teamName = (
		<span className="zw-base-semibold text-app-heading">
			{ aiBuilderVars?.zip_plans?.team?.name }
		</span>
	);

	const teamPlanInfo = (
		<span
			dangerouslySetInnerHTML={ {
				__html: sprintf(
					/* translators: %1$s: team name, %2$s: plan name */
					__(
						'Your current active organization is %1$s, which is on the %2$s plan.',
						'ai-builder'
					),
					renderToString( teamName ),
					renderToString( planName )
				),
			} }
		/>
	);

	const dailyLimit = (
		<span className="zw-base-semibold text-app-heading ">
			{ sprintf(
				/* translators: %s: daily limit */
				__( '%s AI sites', 'ai-builder' ),
				aiBuilderVars?.zip_plans?.plan_data?.limit?.ai_sites_count_daily
			) }
		</span>
	);

	const aiSitesCount =
		aiBuilderVars?.zip_plans?.plan_data?.remaining?.ai_sites_count_daily;

	const displayMessage = (
		<span
			dangerouslySetInnerHTML={ {
				__html:
					typeof aiSitesCount === 'number' && aiSitesCount <= 0
						? `
				<br />
					${ sprintf(
						/* translators: %s: daily limit */
						__(
							'This plan allows you to generate %s per day, and you have reached this limit.',
							'ai-builder'
						),
						renderToString( dailyLimit )
					) }
					<br />
					<br />
					${ __(
						'To create more AI websites, you will need to either upgrade your plan or wait until the limit resets.',
						'ai-builder'
					) }
				`
						: `
				${ sprintf(
					/* translators: %s: plan name */
					__(
						'You have reached the maximum number of sites allowed to be created on %s plan.',
						'ai-builder'
					),
					renderToString( planName )
				) }
				<br />
				<br />
				${ sprintf(
					/* translators: %s: team name */
					__(
						'Please upgrade the plan for %s in order to create more sites.',
						'ai-builder'
					),
					renderToString( teamName )
				) }
				`,
			} }
		/>
	);

	return (
		<Modal
			open={ limitExceedModal.open }
			setOpen={ ( toggle ) => {
				if ( typeof onOpenChange === 'function' ) {
					onOpenChange( toggle );
				}

				setLimitExceedModal( {
					...limitExceedModal,
					open: toggle,
				} );
			} }
			width={ 464 }
			height="200"
			overflowHidden={ false }
		>
			<ModalTitle>
				<ExclamationTriangleColorfulIcon className="w-6 h-6" />
				<span>{ __( 'Limit reached', 'ai-builder' ) }</span>
			</ModalTitle>
			<div className="space-y-8">
				<div className="text-app-text text-base leading-6">
					<div>
						{ teamPlanInfo }
						{ displayMessage }
					</div>
				</div>
				<Button
					variant="primary"
					size="base"
					className="w-full"
					onClick={ () => {
						setLimitExceedModal( {
							...limitExceedModal,
							open: false,
						} );
						if ( typeof window === 'undefined' ) {
							return;
						}
						window.open(
							'https://app.zipwp.com/founders-deal',
							openTarget
						);
					} }
				>
					{ __( 'Unlock Full Power', 'ai-builder' ) }
				</Button>
			</div>
		</Modal>
	);
};

export default LimitExceedModal;
