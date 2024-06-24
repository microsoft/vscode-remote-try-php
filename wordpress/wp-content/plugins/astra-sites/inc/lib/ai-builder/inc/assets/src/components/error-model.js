import { ArrowRightIcon } from '@heroicons/react/24/outline';
import { __ } from '@wordpress/i18n';
import { getSupportLink } from '../utils/functions';
import Button from '../components/button';

const ErrorModel = ( {
	error,
	websiteInfo,
	tryAgainCallback,
	renderHeader,
} ) => {
	return (
		<div className="relative grid grid-cols-1 grid-rows-1 place-items-center py-5 md:py-0 px-5 md:px-10 bg-app-light-background ">
			<div className="w-full max-w-[32.5rem] p-8 my-10 md:my-0 rounded-lg space-y-6 shadow-xl bg-white">
				{ renderHeader ? (
					renderHeader
				) : (
					<div className="space-y-4">
						<h2>
							{ __(
								'Oops.. Something went wrong',
								'ai-builder'
							) }{ ' ' }
							😕
						</h2>
						<div className="text-base !font-semibold leading-6 !mt-5">
							{ __( 'What happened?', 'ai-builder' ) }
						</div>
						<div className="text-app-text text-base font-normal leading-6">
							{ __(
								'Importing site content has failed. The import process was interrupted.',
								'ai-builder'
							) }
						</div>
						<div className="text-app-text text-base !font-semibold leading-6">
							{ __(
								'Additional technical information from console:',
								'ai-builder'
							) }
						</div>
						<div className="text-app-text text-base font-normal leading-6 bg-gray-100 p-4 max-h-[250px] max-w-full overflow-auto">
							<p>{ error.primaryText }</p>
							<p>{ error.errorText }</p>
						</div>
					</div>
				) }
				<div className="items-center gap-3 justify-center mt-4">
					<Button
						onClick={ () => {
							tryAgainCallback();
						} }
						variant="primary"
						size="l"
						className="w-full min-h-[48px] mt-3"
					>
						<div className="flex items-center justify-center gap-2">
							{ __( 'Click here to try again', 'ai-builder' ) }
							<ArrowRightIcon className="w-5 h-5" />
						</div>
					</Button>
					<Button
						onClick={ () => {
							const content = new FormData();
							content.append(
								'action',
								'astra-sites-change-page-builder'
							);
							content.append(
								'_ajax_nonce',
								aiBuilderVars._ajax_nonce
							);
							content.append( 'page_builder', 'gutenberg' );

							fetch( ajaxurl, {
								method: 'post',
								body: content,
							} );
							window.location.href = aiBuilderVars.dashboard_url;
						} }
						variant="white"
						size="l"
						className="w-full min-h-[48px] mt-3"
					>
						<div className="flex items-center justify-center gap-2">
							{ __( 'Exit to Dashboard', 'ai-builder' ) }
						</div>
					</Button>
					<a
						href={ getSupportLink(
							websiteInfo?.uuid,
							error?.errorText ?? ''
						) }
						className="group flex items-center justify-center mt-6 text-base"
						target="_blank"
						rel="noopener noreferrer"
					>
						{ __( 'Contact Support', 'ai-builder' ) }
					</a>
				</div>
			</div>
		</div>
	);
};

export default ErrorModel;
