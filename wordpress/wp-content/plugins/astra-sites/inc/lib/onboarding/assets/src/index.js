import React from 'react';
import { createRoot } from 'react-dom';
import reducer, { initialState } from './store/reducer';
import { StateProvider } from './store/store';
import App from './app';
import { Toaster } from 'react-hot-toast';

const root = createRoot(
	document.getElementById( 'starter-templates-ai-root' )
);
root.render(
	<StateProvider reducer={ reducer } initialState={ initialState }>
		<App />
		<Toaster position="top-right" reverseOrder={ false } gutter={ 8 } />
	</StateProvider>
);
