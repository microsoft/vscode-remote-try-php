import { createRoot } from '@wordpress/element';
import App from './app';

const root = createRoot( document.getElementById( 'ai-builder-root' ) );
root.render( <App /> );
