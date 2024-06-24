import { __ } from '@wordpress/i18n';
import { Tooltip } from '@wordpress/components';
import Logo from './logo';
import PageBuilderDropdown from './page-builder-dropdown';
import { XMarkIcon } from '@heroicons/react/24/outline';

const { adminUrl } = aiBuilderVars;

const Header = () => {
	return (
		<div className="w-full h-[80px] shadow bg-white">
			<div className="h-full flex items-center justify-start">
				<div className="h-full mr-auto py-4 px-6 border-r border-border-primary border-solid">
					<Logo />
				</div>
				<div className="h-full ml-auto max-w-fit inline-flex items-center justify-end">
					<div className="w-fit">
						<PageBuilderDropdown />
					</div>
					<a
						className="h-full inline-block px-[1.875rem] py-[1.625rem] border-l border-border-primary border-solid appearance-none"
						href={ adminUrl }
					>
						<Tooltip
							content={ __( 'Exit to Dashboard', 'ai-builder' ) }
						>
							<XMarkIcon className="w-6 h-6 text-zip-body-text" />
						</Tooltip>
					</a>
				</div>
			</div>
		</div>
	);
};

export default Header;
