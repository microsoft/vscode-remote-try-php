export default function sendPostMessage( data ) {
	const frame = document.getElementById( 'astra-starter-templates-preview' );

	frame.contentWindow.postMessage(
		{
			call: 'starterTemplatePreviewDispatch',
			value: data,
		},
		'*'
	);
}
