import { memo } from '@wordpress/element';
import { useStateValue } from '../../../store/store';
import { saveGutenbergAsDefaultBuilder } from '../../../utils/functions';
import ExitConfirmationPopover from './exit-confirmation-popover';

const AIBuilderExitButton = () => {
	const [ , dispatch ] = useStateValue();

	const handleClosePopup = ( event ) => {
		event?.preventDefault();
		event?.stopPropagation();

		dispatch( {
			type: 'set',
			builder: 'gutenberg',
			currentIndex: 0,
		} );
		saveGutenbergAsDefaultBuilder();
	};

	return <ExitConfirmationPopover onExit={ handleClosePopup } />;
};

export default memo( AIBuilderExitButton );
