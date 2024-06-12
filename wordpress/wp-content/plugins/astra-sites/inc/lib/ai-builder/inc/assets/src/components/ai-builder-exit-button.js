import { memo } from '@wordpress/element';
import ExitConfirmationPopover from './exit-confirmation-popover';

const AIBuilderExitButton = () => {
	const handleClosePopup = () => {
		// window.location.href = `${ aiBuilderVars.adminUrl }themes.php?page=starter-templates`;
		window.location.href = `${ aiBuilderVars.adminUrl }`;
	};

	return <ExitConfirmationPopover onExit={ handleClosePopup } />;
};

export default memo( AIBuilderExitButton );
