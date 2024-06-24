/**
 * Block Icons
 */

import { createElement as el } from '@wordpress/element';

// This is the color that will be visible on the drag and drop of the blocks. Use this as the primary fill / stroke color.
const iconColor = '#fff';
// This is the color used for non-block icons.
const spectraDarkColor = '#1d2327';

// Negative Space Color needs to be implemented for the property that doesn't use iconColor.
const noColor = 'none';

const UAGB_Block_Icons = {
	// ------------------------.
	// All Spectra Block Icons .
	// ------------------------.

	logo: el(
		'svg',
		{ width: 20, height: 20, viewBox: '0 0 85 85', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M41.7849 0C33.5206 0 25.4419 2.49258 18.5705 7.16254C11.699 11.8325 6.34331 18.4701 3.18071 26.236C0.0181112 34.0018 -0.809377 42.5471 0.802901 50.7913C2.41518 59.0355 6.3948 66.6083 12.2385 72.552C18.0822 78.4958 25.5276 82.5435 33.633 84.1834C41.7385 85.8232 50.1401 84.9816 57.7753 81.7649C65.4105 78.5482 71.9363 73.1008 76.5277 66.1117C81.1191 59.1226 83.5697 50.9057 83.5697 42.5C83.565 31.2298 79.1612 20.4225 71.326 12.4533C63.4908 4.48402 52.8655 0.0048112 41.7849 0ZM57.5278 49.0175C57.5278 49.1264 57.5278 49.2354 57.5278 49.3443V49.6529V49.8526C57.3161 51.0856 56.8673 52.2639 56.207 53.3201L55.8143 53.8829L55.2431 54.5546C55.0482 54.8082 54.8268 55.0396 54.5827 55.2445V55.3353L54.4578 55.4261L54.2079 55.6258C54.1249 55.7007 54.0353 55.7675 53.9402 55.8255L53.6724 56.0071L34.3061 69.0602C34.1479 69.1683 33.9612 69.2253 33.7706 69.2236H33.5743C33.447 69.1967 33.3262 69.1443 33.2189 69.0695C33.1117 68.9947 33.0201 68.899 32.9495 68.7879C32.9495 68.7879 32.5033 67.9891 32.4676 67.9347V67.7531V67.6623V67.5534V67.4263V67.2811C31.7014 65.262 31.6378 63.0371 32.2872 60.9761C32.9366 58.9152 34.2601 57.1424 36.0375 55.9526L45.0691 49.8708C45.2101 49.7754 45.3201 49.6398 45.3853 49.4809C45.4505 49.322 45.4679 49.147 45.4355 48.9781C45.403 48.8091 45.3221 48.6537 45.2029 48.5315C45.0837 48.4093 44.9316 48.3257 44.7657 48.2913L34.1633 46.1128H33.8599H33.7706H33.485H33.1816H33.0567H32.896H32.8068L32.5212 46.0038H32.4319H32.2534L31.9679 45.8586L31.6823 45.7134L31.4145 45.55L31.1468 45.3866C30.3041 44.8521 29.5509 44.1841 28.9156 43.4077L28.6836 43.0809L28.4159 42.6634C27.73 41.6278 27.255 40.4629 27.0189 39.2375C26.7828 38.012 26.7905 36.751 27.0415 35.5286C27.228 34.6884 27.5221 33.8766 27.9161 33.1141C28.0232 32.9144 28.1124 32.7328 28.2195 32.5694L28.3802 32.3153C28.4948 32.1207 28.6199 31.9329 28.755 31.7525L28.9156 31.5528L29.112 31.3167L29.3083 31.0989L29.4333 30.9718L29.6475 30.754L29.8259 30.5906L30.0223 30.4272L30.2365 30.2456H30.3257L30.4685 30.1185H30.5578L30.7898 29.9551L50.049 16.9746C50.1725 16.8912 50.3113 16.8338 50.457 16.806C50.6027 16.7782 50.7524 16.7804 50.8973 16.8126C51.0421 16.8448 51.1791 16.9062 51.3002 16.9933C51.4212 17.0804 51.5238 17.1913 51.6019 17.3195L51.7626 17.5918L51.8875 17.846C51.8875 17.9368 51.9768 18.0094 52.0125 18.082C52.0125 18.082 52.0125 18.082 52.0125 18.191C52.0239 18.2325 52.0239 18.2765 52.0125 18.318L52.1195 18.5904C52.1292 18.6383 52.1292 18.6877 52.1195 18.7356C52.1195 18.7356 52.1195 18.7356 52.1195 18.8445C52.8288 20.8305 52.8625 23.0011 52.2153 25.0089C51.5681 27.0166 50.2774 28.7455 48.5497 29.9188L39.6251 35.9462C39.4842 36.0415 39.3742 36.1772 39.309 36.3361C39.2438 36.495 39.2263 36.6699 39.2588 36.8389C39.2912 37.0078 39.3722 37.1632 39.4914 37.2854C39.6106 37.4077 39.7627 37.4912 39.9286 37.5256L50.4417 39.686C52.5458 40.1281 54.4361 41.2935 55.7948 42.9862C57.1536 44.679 57.8979 46.796 57.9027 48.9812C57.5635 48.4003 57.5278 48.9812 57.5278 48.9812V49.0175Z',
			fill: 'url(#paint0_linear_619_170)',
		} )
	),
	section: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M21 15V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19L3 15M21 15L3 15M21 15V9M3 15L3 9M21 9V5C21 3.89543 20.1046 3 19 3L5 3C3.89543 3 3 3.89543 3 5L3 9M21 9L3 9',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	buttons: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M7 17.5H17M7 6.5H17M5 10H19C20.1046 10 21 9.10457 21 8V5C21 3.89543 20.1046 3 19 3H5C3.89543 3 3 3.89543 3 5V8C3 9.10457 3.89543 10 5 10ZM5 21H19C20.1046 21 21 20.1046 21 19V16C21 14.8954 20.1046 14 19 14H5C3.89543 14 3 14.8954 3 16V19C3 20.1046 3.89543 21 5 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	buttons_child: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M7 11.5H17M5 15H19C20.1046 15 21 14.1046 21 13V10C21 8.89543 20.1046 8 19 8H5C3.89543 8 3 8.89543 3 10V13C3 14.1046 3.89543 15 5 15Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	advanced_heading: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M8 7V12.8333M8 12.8333V17M8 12.8333H16M16 12.8333V7M16 12.8333V17M5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	google_map: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M9 3.00002L4.10557 5.44723C3.428 5.78601 3 6.47854 3 7.23608V20.382C3 21.1254 3.78231 21.6089 4.44721 21.2764L9 19M9 3.00002L15 5.00002M9 3.00002V19M9 19L15 21M15 5.00002L19.5528 2.72362C20.2177 2.39117 21 2.87467 21 3.61805V16.7639C21 17.5215 20.572 18.214 19.8944 18.5528L15 21M15 5.00002V21',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	info_box: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M7 13H17M7 16.5H15.3333M9 8C9 8.55228 8.55228 9 8 9C7.44772 9 7 8.55228 7 8C7 7.44772 7.44772 7 8 7C8.55228 7 9 7.44772 9 8ZM5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	post_carousel: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M0 17C1.10457 17 2 16.1046 2 15V8C2 6.89543 1.10457 6 0 6M24 17C22.8954 17 22 16.1046 22 15V8C22 6.89543 22.8954 6 24 6M7 17H17C18.1046 17 19 16.1046 19 15V8C19 6.89543 18.1046 6 17 6H7C5.89543 6 5 6.89543 5 8V15C5 16.1046 5.89543 17 7 17Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d: 'M8 11H12.5M8 14H16',
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	post_masonry: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M3 14C3 12.8954 3.89543 12 5 12H8C9.10457 12 10 12.8954 10 14V21C10 22.1046 9.10457 23 8 23H5C3.89543 23 3 22.1046 3 21V14Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M14 3C14 1.89543 14.8954 1 16 1H19C20.1046 1 21 1.89543 21 3V10C21 11.1046 20.1046 12 19 12H16C14.8954 12 14 11.1046 14 10V3Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M14 18C14 16.8954 14.8954 16 16 16H19C20.1046 16 21 16.8954 21 18V21C21 22.1046 20.1046 23 19 23H16C14.8954 23 14 22.1046 14 21V18Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M3 3C3 1.89543 3.89543 1 5 1H8C9.10457 1 10 1.89543 10 3V6C10 7.10457 9.10457 8 8 8H5C3.89543 8 3 7.10457 3 6V3Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	post_grid: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M3 16C3 14.8954 3.89543 14 5 14H8C9.10457 14 10 14.8954 10 16V19C10 20.1046 9.10457 21 8 21H5C3.89543 21 3 20.1046 3 19V16Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M14 16C14 14.8954 14.8954 14 16 14H19C20.1046 14 21 14.8954 21 16V19C21 20.1046 20.1046 21 19 21H16C14.8954 21 14 20.1046 14 19V16Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M3 5C3 3.89543 3.89543 3 5 3H8C9.10457 3 10 3.89543 10 5V8C10 9.10457 9.10457 10 8 10H5C3.89543 10 3 9.10457 3 8V5Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M14 5C14 3.89543 14.8954 3 16 3H19C20.1046 3 21 3.89543 21 5V8C21 9.10457 20.1046 10 19 10H16C14.8954 10 14 9.10457 14 8V5Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	testimonial: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M15.5 9.42857C15.5 10.2175 14.903 10.8571 14.1667 10.8571C13.4303 10.8571 12.8333 10.2175 12.8333 9.42857C12.8333 8.63959 13.4303 8 14.1667 8C14.903 8 15.5 8.63959 15.5 9.42857ZM15.5 9.42857C15.5 9.42857 15.5 11.5714 13.5 13M11.1667 9.42857C11.1667 10.2175 10.5697 10.8571 9.83333 10.8571C9.09695 10.8571 8.5 10.2175 8.5 9.42857C8.5 8.63959 9.09695 8 9.83333 8C10.5697 8 11.1667 8.63959 11.1667 9.42857ZM11.1667 9.42857C11.1667 9.42857 11.1667 11.5714 9.16667 13M12 21L14.4142 18.5858C14.7893 18.2107 15.298 18 15.8284 18H19C20.1046 18 21 17.1046 21 16V5C21 3.89543 20.1046 3 19 3H5C3.89543 3 3 3.89543 3 5V16C3 17.1046 3.89543 18 5 18H8.17157C8.70201 18 9.21071 18.2107 9.58579 18.5858L12 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	cf7_styler: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M4.51555 17C6.13007 19.412 8.87958 21 12 21C15.1204 21 17.8699 19.412 19.4845 17M4.51555 17C3.55827 15.5699 3 13.8501 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12C21 13.8501 20.4417 15.5699 19.4845 17M4.51555 17C5.75777 17 7.12889 15 8.43944 13M19.4845 17C18.2422 17 16.8711 15 15.5606 13M8.43944 13C9.75 11 11 9 12 9C13 9 14.25 11 15.5606 13M8.43944 13L9.09522 14.2607C9.47211 14.9852 10.5116 14.9769 10.8768 14.2464L11.3795 13.241C11.6848 12.6305 12.4984 12.4984 12.9811 12.9811L13.4309 13.4309C13.7632 13.7632 14.282 13.8193 14.6776 13.5658L15.5606 13',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	gf_styler: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M17 12V15H7L6.99998 12.9996C6.99998 10.7905 8.79083 9.00001 10.9999 9.00001H17.5M3.33984 8.71466V15.2854C3.33984 16.0317 3.75541 16.7159 4.41768 17.0601L11.0779 20.5208C11.656 20.8212 12.3442 20.8212 12.9223 20.5208L19.5825 17.0601C20.2448 16.7159 20.6604 16.0317 20.6604 15.2854V8.71466C20.6604 7.96832 20.2448 7.28407 19.5825 6.93995L12.9223 3.47918C12.3442 3.1788 11.656 3.1788 11.0779 3.47918L4.41768 6.93995C3.75541 7.28407 3.33984 7.96832 3.33984 8.71466Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	content_timeline: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M12.0001 3V7M12.0001 21V17M12.0001 7H15.0001M12.0001 7V12M15.0001 7C15.0001 8.10457 15.8954 9 17 9H19C20.1046 9 21 8.10457 21 7C21 5.89543 20.1046 5 19 5H17C15.8954 5 15.0001 5.89543 15.0001 7ZM12.0001 12H9M12.0001 12V17M9 12C9 13.1046 8.10457 14 7 14H5C3.89543 14 3 13.1046 3 12C3 10.8954 3.89543 10 5 10H7C8.10457 10 9 10.8954 9 12ZM12.0001 17H15.0001M15.0001 17C15.0001 18.1046 15.8954 19 17 19H19C20.1046 19 21 18.1046 21 17C21 15.8954 20.1046 15 19 15H17C15.8954 15 15.0001 15.8954 15.0001 17Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	content_timeline_child: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M5.99993 3V7V12M5.99993 21V17V12M5.99993 12H9M9 12C9 13.1046 9.89543 14 11 14H16C17.1046 14 18 13.1046 18 12C18 10.8954 17.1046 10 16 10H11C9.89543 10 9 10.8954 9 12Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	call_to_action: el(
		'svg',
		{ width: 40, height: 40, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M16.4545 16.4545L14.6364 21L11 11L21 14.6364L16.4545 16.4545ZM16.4545 16.4545L21 21M9 16.9291C5.60771 16.4439 3 13.5265 3 10C3 6.13401 6.13401 3 10 3C13.5265 3 16.4439 5.60771 16.9291 9M8.5 12.5987C7.6033 12.0799 7 11.1104 7 10C7 8.34315 8.34315 7 10 7C11.1104 7 12.0799 7.6033 12.5987 8.5',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	post_timeline: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M4 3V6M4 6C2.89543 6 2 6.89543 2 8C2 9.10457 2.89543 10 4 10M4 6C5.10457 6 6 6.89543 6 8C6 9.10457 5.10457 10 4 10M4 10V14M4 14C2.89543 14 2 14.8954 2 16C2 17.1046 2.89543 18 4 18M4 14C5.10457 14 6 14.8954 6 16C6 17.1046 5.10457 18 4 18M4 18V21M11 18H19C20.1046 18 21 17.1046 21 16C21 14.8954 20.1046 14 19 14H11C9.89543 14 9 14.8954 9 16C9 17.1046 9.89543 18 11 18ZM11 10H19C20.1046 10 21 9.10457 21 8C21 6.89543 20.1046 6 19 6H11C9.89543 6 9 6.89543 9 8C9 9.10457 9.89543 10 11 10Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	icon_list: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d: 'M11 5H21M11 12H21M11 19H21',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} ),
		el( 'path', {
			d: 'M7 5C7 6.10457 6.10457 7 5 7C3.89543 7 3 6.10457 3 5C3 3.89543 3.89543 3 5 3C6.10457 3 7 3.89543 7 5Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} ),
		el( 'path', {
			d:
				'M7 12C7 13.1046 6.10457 14 5 14C3.89543 14 3 13.1046 3 12C3 10.8954 3.89543 10 5 10C6.10457 10 7 10.8954 7 12Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} ),
		el( 'path', {
			d:
				'M7 19C7 20.1046 6.10457 21 5 21C3.89543 21 3 20.1046 3 19C3 17.8954 3.89543 17 5 17C6.10457 17 7 17.8954 7 19Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	icon_list_child: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M11 5H21M13 11H21M13 16H21M13 20.5H21M7 5C7 6.10457 6.10457 7 5 7C3.89543 7 3 6.10457 3 5C3 3.89543 3.89543 3 5 3C6.10457 3 7 3.89543 7 5Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M9 11C9 11.5523 8.55228 12 8 12C7.44772 12 7 11.5523 7 11C7 10.4477 7.44772 10 8 10C8.55228 10 9 10.4477 9 11Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M9 16C9 16.5523 8.55228 17 8 17C7.44772 17 7 16.5523 7 16C7 15.4477 7.44772 15 8 15C8.55228 15 9 15.4477 9 16Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M9 20.5C9 21.0523 8.55228 21.5 8 21.5C7.44772 21.5 7 21.0523 7 20.5C7 19.9477 7.44772 19.5 8 19.5C8.55228 19.5 9 19.9477 9 20.5Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	team: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M17 20H22V18C22 16.3431 20.6569 15 19 15C18.0444 15 17.1931 15.4468 16.6438 16.1429M17 20H7M17 20V18C17 17.3438 16.8736 16.717 16.6438 16.1429M7 20H2V18C2 16.3431 3.34315 15 5 15C5.95561 15 6.80686 15.4468 7.35625 16.1429M7 20V18C7 17.3438 7.12642 16.717 7.35625 16.1429M7.35625 16.1429C8.0935 14.301 9.89482 13 12 13C14.1052 13 15.9065 14.301 16.6438 16.1429M15 7C15 8.65685 13.6569 10 12 10C10.3431 10 9 8.65685 9 7C9 5.34315 10.3431 4 12 4C13.6569 4 15 5.34315 15 7ZM21 10C21 11.1046 20.1046 12 19 12C17.8954 12 17 11.1046 17 10C17 8.89543 17.8954 8 19 8C20.1046 8 21 8.89543 21 10ZM7 10C7 11.1046 6.10457 12 5 12C3.89543 12 3 11.1046 3 10C3 8.89543 3.89543 8 5 8C6.10457 8 7 8.89543 7 10Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	tabs: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M10 3V6C10 7.10457 10.8954 8 12 8H15.5M15.5 8H21M15.5 8V3M5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	tabs_child: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M12 3V8C12 9.10457 12.8954 10 14 10H16.5H21M5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	social_share: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M9 12C9 13.1046 8.10457 14 7 14C5.89543 14 5 13.1046 5 12C5 10.8954 5.89543 10 7 10C8.10457 10 9 10.8954 9 12Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M19 6C19 7.10457 18.1046 8 17 8C15.8954 8 15 7.10457 15 6C15 4.89543 15.8954 4 17 4C18.1046 4 19 4.89543 19 6Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M19 18C19 19.1046 18.1046 20 17 20C15.8954 20 15 19.1046 15 18C15 16.8954 15.8954 16 17 16C18.1046 16 19 16.8954 19 18Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d: 'M9 10.5L15 7M9 13.5L15 17',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	social_share_child: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M9 10.5L15 7M9 13.5L15 17M21 18C21 19.6569 19.6569 21 18 21C16.3431 21 15 19.6569 15 18C15 16.3431 16.3431 15 18 15C19.6569 15 21 16.3431 21 18ZM9 12C9 13.1046 8.10457 14 7 14C5.89543 14 5 13.1046 5 12C5 10.8954 5.89543 10 7 10C8.10457 10 9 10.8954 9 12ZM19 6C19 7.10457 18.1046 8 17 8C15.8954 8 15 7.10457 15 6C15 4.89543 15.8954 4 17 4C18.1046 4 19 4.89543 19 6Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	restaurant_menu: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M17.7472 5.05554C17.5824 4.84998 17.3481 4.68976 17.074 4.59515C16.8912 4.53203 16.696 4.5 16.5 4.5M16.5 4.5C16.4022 4.5 16.3042 4.50797 16.2074 4.52402C15.9164 4.57225 15.6491 4.6913 15.4393 4.86612C15.2296 5.04093 15.0867 5.26366 15.0288 5.50614C14.9709 5.74861 15.0006 5.99995 15.1142 6.22835C15.2277 6.45676 15.42 6.65199 15.6666 6.78934C15.9133 6.92669 16.2033 7 16.5 7C16.7967 7 17.0867 7.07331 17.3334 7.21066C17.58 7.34802 17.7723 7.54324 17.8858 7.77165C17.9994 8.00005 18.0291 8.25139 17.9712 8.49386C17.9133 8.73634 17.7704 8.95907 17.5607 9.13388C17.3509 9.3087 17.0836 9.42775 16.7926 9.47598C16.6958 9.49203 16.5978 9.5 16.5 9.5M16.5 4.5V4M15.2528 8.94446C15.4176 9.15003 15.6519 9.31024 15.926 9.40485C16.1088 9.46797 16.304 9.5 16.5 9.5M16.5 9.5V10',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} ),
		el( 'path', {
			d:
				'M17.7472 15.0555C17.5824 14.85 17.3481 14.6898 17.074 14.5952C16.8912 14.532 16.696 14.5 16.5 14.5M16.5 14.5C16.4022 14.5 16.3042 14.508 16.2074 14.524C15.9164 14.5723 15.6491 14.6913 15.4393 14.8661C15.2296 15.0409 15.0867 15.2637 15.0288 15.5061C14.9709 15.7486 15.0006 15.9999 15.1142 16.2284C15.2277 16.4568 15.42 16.652 15.6666 16.7893C15.9133 16.9267 16.2033 17 16.5 17C16.7967 17 17.0867 17.0733 17.3334 17.2107C17.58 17.348 17.7723 17.5432 17.8858 17.7716C17.9994 18.0001 18.0291 18.2514 17.9712 18.4939C17.9133 18.7363 17.7704 18.9591 17.5607 19.1339C17.3509 19.3087 17.0836 19.4278 16.7926 19.476C16.6958 19.492 16.5978 19.5 16.5 19.5M16.5 14.5V14M15.2528 18.9445C15.4176 19.15 15.6519 19.3102 15.926 19.4049C16.1088 19.468 16.304 19.5 16.5 19.5M16.5 19.5V20',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} ),
		el( 'path', {
			d: 'M6 5H12M6 15H12M6 8H9M6 18H9',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} ),
		el( 'path', {
			d:
				'M3 3C3 1.89543 3.89543 1 5 1H19C20.1046 1 21 1.89543 21 3V21C21 22.1046 20.1046 23 19 23H5C3.89543 23 3 22.1046 3 21V3Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	restaurant_menu_child: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M20.6208 8.58331C20.3736 8.27496 20.0222 8.03464 19.611 7.89273C19.3367 7.79804 19.0441 7.75 18.75 7.75M18.75 7.75C18.6033 7.75 18.4563 7.76195 18.311 7.78603C17.8746 7.85838 17.4737 8.03695 17.159 8.29918C16.8443 8.5614 16.6301 8.89549 16.5432 9.25921C16.4564 9.62292 16.501 9.99992 16.6713 10.3425C16.8416 10.6851 17.13 10.978 17.5 11.184C17.87 11.39 18.305 11.5 18.75 11.5C19.195 11.5 19.63 11.61 20 11.816C20.37 12.022 20.6584 12.3149 20.8287 12.6575C20.999 13.0001 21.0436 13.3771 20.9568 13.7408C20.8699 14.1045 20.6557 14.4386 20.341 14.7008C20.0263 14.963 19.6254 15.1416 19.189 15.214C19.0437 15.2381 18.8967 15.25 18.75 15.25M18.75 7.75V7M16.8792 14.4167C17.1264 14.725 17.4778 14.9654 17.889 15.1073C18.1633 15.202 18.4559 15.25 18.75 15.25M18.75 15.25V16M3 9.5H12M3 13H7.5M3 3H20.9568M3 21H20.9568',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	blockquote: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M7 7H17M7 11H9M17 11.8571C17 12.3305 16.6418 12.7143 16.2 12.7143C15.7582 12.7143 15.4 12.3305 15.4 11.8571C15.4 11.3838 15.7582 11 16.2 11C16.6418 11 17 11.3838 17 11.8571ZM17 11.8571C17 11.8571 17 13.1429 15.8 14M13.6 11.8571C13.6 12.3305 13.2418 12.7143 12.8 12.7143C12.3582 12.7143 12 12.3305 12 11.8571C12 11.3838 12.3582 11 12.8 11C13.2418 11 13.6 11.3838 13.6 11.8571ZM13.6 11.8571C13.6 11.8571 13.6 13.1429 12.4 14M3 21V5C3 3.89543 3.89543 3 5 3H19C20.1046 3 21 3.89543 21 5V16C21 17.1046 20.1046 18 19 18H7.66667C7.23393 18 6.81286 18.1404 6.46667 18.4L3 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	columns: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M15 3H19C20.1046 3 21 3.89543 21 5V19C21 20.1046 20.1046 21 19 21H15M15 3V21M15 3H9M15 21H9M9 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H9M9 3V21',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	column: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M3 5C3 3.89543 3.89543 3 5 3H10C11.1046 3 12 3.89543 12 5V19C12 20.1046 11.1046 21 10 21H5C3.89543 21 3 20.1046 3 19V5Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} ),
		el( 'path', {
			d: 'M15 3H19C20.1046 3 21 3.89543 21 5V19C21 20.1046 20.1046 21 19 21H15',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeDasharray: 1.4,
		} )
	),
	marketing_button: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d: 'M7 20.9895H17M4 16.9895H20',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} ),
		el( 'path', {
			d:
				'M10 7H19M3 11H21C22.1046 11 23 10.1046 23 9V5C23 3.89543 22.1046 3 21 3H3C1.89543 3 1 3.89543 1 5V9C1 10.1046 1.89543 11 3 11ZM7 7C7 7.55228 6.55228 8 6 8C5.44772 8 5 7.55228 5 7C5 6.44772 5.44772 6 6 6C6.55228 6 7 6.44772 7 7Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	table_of_contents: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M7 16H13M7 12H15.3333M7 8H17M5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	faq: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M9.00006 9C9.50006 8 10.5001 7 12.0001 7C13.5002 7 15.0001 8.5 15.0001 10C15.0001 11.4553 14.0588 12.4399 13.0843 12.8686C13.0248 12.8948 12.9619 12.9116 12.8992 12.9285C12.4001 13.0627 12.0001 13.4804 12.0001 14M12.0001 17H12.0101M21.0001 12C21.0001 16.9706 16.9706 21 12.0001 21C10.5124 21 9.1091 20.6391 7.87286 20C7.19356 19.6488 3.56466 21.5054 3 21C2.43678 20.4959 4.93748 17.6302 4.51561 17C3.55833 15.5699 3.00006 13.8501 3.00006 12C3.00006 7.02944 7.0295 3 12.0001 3C16.9706 3 21.0001 7.02944 21.0001 12Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	faq_child: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M12.0067 10.2C12.34 9.6 13.0067 9 14.0067 9C15.0067 9 16.0067 9.9 16.0067 10.8C16.0067 11.6628 15.394 12.2498 14.7527 12.5118C14.6977 12.5343 14.6397 12.5477 14.5824 12.5632C14.2608 12.6496 14.0067 12.8957 14.0067 13.2M14.0067 15H14.0133M11.0002 15H11.0069M8.00017 15H8.00684M21.0001 12C21.0001 16.9706 16.9706 21 12.0001 21C10.5124 21 9.1091 20.6391 7.87286 20C7.19356 19.6488 3.56466 21.5054 3 21C2.43678 20.4959 4.93748 17.6302 4.51561 17C3.55833 15.5699 3.00006 13.8501 3.00006 12C3.00006 7.02944 7.0295 3 12.0001 3C16.9706 3 21.0001 7.02944 21.0001 12Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	forms: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M7 10H17M7 7H10.5M9 17H15M7 13H17M13.5 7H17M5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	how_to: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M13 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V11M13 3L21 11M13 3V9C13 10.1046 13.8954 11 15 11H21M10 11H17M10 7H13M10 15H17M7 7H7.1M7 11H7.1M7 15H7.1',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	how_to_step: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M14 12C14 13.1046 13.1046 14 12 14C10.8954 14 10 13.1046 10 12C10 10.8954 10.8954 10 12 10C13.1046 10 14 10.8954 14 12Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} ),
		el( 'path', {
			d:
				'M23 12C23 13.1046 22.1046 14 21 14C19.8954 14 19 13.1046 19 12C19 10.8954 19.8954 10 21 10C22.1046 10 23 10.8954 23 12Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} ),
		el( 'path', {
			d:
				'M5 12C5 13.1046 4.10457 14 3 14C1.89543 14 1 13.1046 1 12C1 10.8954 1.89543 10 3 10C4.10457 10 5 10.8954 5 12Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} ),
		el( 'path', {
			d: 'M7.5 12H7.6',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} ),
		el( 'path', {
			d: 'M16.5 12H16.6',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	inline_notice: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M3 21H21M3 3H21M10 12H19M3 17H21C22.1046 17 23 16.1046 23 15V9C23 7.89543 22.1046 7 21 7H3C1.89543 7 1 7.89543 1 9V15C1 16.1046 1.89543 17 3 17ZM7 12C7 12.5523 6.55228 13 6 13C5.44772 13 5 12.5523 5 12C5 11.4477 5.44772 11 6 11C6.55228 11 7 11.4477 7 12Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	wp_search: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M3 21L9 15M7 10C7 13.866 10.134 17 14 17C17.866 17 21 13.866 21 10C21 6.13401 17.866 3 14 3C10.134 3 7 6.13401 7 10Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	taxonomy_list: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M10 16H17M10 12H15M10 8H17M6.98999 8H6.99999M6.98999 12H6.99999M6.98999 16H6.99999M5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	review: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M7 10.5H17M9.5 7H9.51M6.99 7H7M12 7H12.01M7 14H14M12 21L14.4142 18.5858C14.7893 18.2107 15.298 18 15.8284 18H19C20.1046 18 21 17.1046 21 16V5C21 3.89543 20.1046 3 19 3H5C3.89543 3 3 3.89543 3 5V16C3 17.1046 3.89543 18 5 18H8.17157C8.70201 18 9.21071 18.2107 9.58579 18.5858L12 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	lottie: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M7 17C7 17 12 17.5 12 12C12 6.5 17 7 17 7M5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	accept: el(
		'svg',
		{ width: 24, height: 24, role: 'img', viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M20 12H12M4 12L5.66667 14L9 10M3 17H21C22.1046 17 23 16.1046 23 15V9C23 7.89543 22.1046 7 21 7H3C1.89543 7 1 7.89543 1 9V15C1 16.1046 1.89543 17 3 17Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	checkbox: el(
		'svg',
		{ width: 24, height: 24, role: 'img', viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M8 12L10.6667 15L16 9M5.77778 20H18.2222C19.2041 20 20 18.9767 20 17.7143V6.28571C20 5.02335 19.2041 4 18.2222 4H5.77778C4.79594 4 4 5.02335 4 6.28571V17.7143C4 18.9767 4.79594 20 5.77778 20Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	datepicker: el(
		'svg',
		{ width: 24, height: 24, role: 'img', viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M8 7V3M16 7V3M8 13H8.1M11.9 13H12M16 13H16.1M5 21H19C20.1046 21 21 20.1046 21 19V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V19C3 20.1046 3.89543 21 5 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	email: el(
		'svg',
		{ width: 24, height: 24, role: 'img', viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M3 8L10.8906 13.2604C11.5624 13.7083 12.4376 13.7083 13.1094 13.2604L21 8M5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	hidden: el(
		'svg',
		{ width: 24, height: 24, role: 'img', viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M3.00024 3L6.5894 6.58916M21.0002 21L17.4114 17.4112M13.8751 18.8246C13.268 18.9398 12.6414 19 12.0007 19C7.52305 19 3.73275 16.0571 2.4585 12C2.80539 10.8955 3.33875 9.87361 4.02168 8.97118M9.87892 9.87868C10.4218 9.33579 11.1718 9 12.0002 9C13.6571 9 15.0002 10.3431 15.0002 12C15.0002 12.8284 14.6645 13.5784 14.1216 14.1213M9.87892 9.87868L14.1216 14.1213M9.87892 9.87868L6.5894 6.58916M14.1216 14.1213L6.5894 6.58916M14.1216 14.1213L17.4114 17.4112M6.5894 6.58916C8.14922 5.58354 10.0068 5 12.0007 5C16.4783 5 20.2686 7.94291 21.5429 12C20.836 14.2507 19.3548 16.1585 17.4114 17.4112',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	name: el(
		'svg',
		{ width: 24, height: 24, role: 'img', viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M18 9H13M13 12H17M5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19ZM7 14H9C9.55228 14 10 13.5523 10 13V9C10 8.44772 9.55228 8 9 8H7C6.44772 8 6 8.44772 6 9V13C6 13.5523 6.44772 14 7 14Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	phone: el(
		'svg',
		{ width: 24, height: 24, role: 'img', viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M3 5C3 3.89543 3.89543 3 5 3H8.27924C8.70967 3 9.09181 3.27543 9.22792 3.68377L10.7257 8.17721C10.8831 8.64932 10.6694 9.16531 10.2243 9.38787L7.96701 10.5165C9.06925 12.9612 11.0388 14.9308 13.4835 16.033L14.6121 13.7757C14.8347 13.3306 15.3507 13.1169 15.8228 13.2743L20.3162 14.7721C20.7246 14.9082 21 15.2903 21 15.7208V19C21 20.1046 20.1046 21 19 21H18C9.71573 21 3 14.2843 3 6V5Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	radio: el(
		'svg',
		{ width: 24, height: 24, role: 'img', viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M12 7H22M12 17H22M8 7C8 8.65685 6.65685 10 5 10C3.34315 10 2 8.65685 2 7C2 5.34315 3.34315 4 5 4C6.65685 4 8 5.34315 8 7ZM8 17C8 18.6569 6.65685 20 5 20C3.34315 20 2 18.6569 2 17C2 15.3431 3.34315 14 5 14C6.65685 14 8 15.3431 8 17ZM5.5 7C5.5 7.27614 5.27614 7.5 5 7.5C4.72386 7.5 4.5 7.27614 4.5 7C4.5 6.72386 4.72386 6.5 5 6.5C5.27614 6.5 5.5 6.72386 5.5 7Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	select: el(
		'svg',
		{ width: 24, height: 24, role: 'img', viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M5 17H17M5 14H19M4 9H20C21.1046 9 22 8.10457 22 7V5C22 3.89543 21.1046 3 20 3H4C2.89543 3 2 3.89543 2 5V7C2 8.10457 2.89543 9 4 9ZM4 21H20C21.1046 21 22 20.1046 22 19V12C22 10.8954 21.1046 10 20 10H4C2.89543 10 2 10.8954 2 12V19C2 20.1046 2.89543 21 4 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	textarea: el(
		'svg',
		{ width: 24, height: 24, role: 'img', viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M15.5 16L18 13.5M18.5 16.5L18.6 16.4M5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	toggle: el(
		'svg',
		{ width: 24, height: 24, role: 'img', viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M2 12C2 8.68629 4.68629 6 8 6H16C19.3137 6 22 8.68629 22 12V12C22 15.3137 19.3137 18 16 18H8C4.68629 18 2 15.3137 2 12V12Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M20 12C20 14.2091 18.2091 16 16 16C13.7909 16 12 14.2091 12 12C12 9.79086 13.7909 8 16 8C18.2091 8 20 9.79086 20 12Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	url: el(
		'svg',
		{ width: 24, height: 24, role: 'img', viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M13.8284 10.1716C12.2663 8.60948 9.73367 8.60948 8.17157 10.1716L4.17157 14.1716C2.60948 15.7337 2.60948 18.2663 4.17157 19.8284C5.73367 21.3905 8.26633 21.3905 9.82843 19.8284L10.93 18.7269M10.1716 13.8284C11.7337 15.3905 14.2663 15.3905 15.8284 13.8284L19.8284 9.82843C21.3905 8.26633 21.3905 5.73367 19.8284 4.17157C18.2663 2.60948 15.7337 2.60948 14.1716 4.17157L13.072 5.27118',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	star_rating: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M11.103 7.81696C11.4698 7.07371 12.5297 7.07371 12.8965 7.81696L13.8243 9.69697C13.97 9.99211 14.2516 10.1967 14.5773 10.244L16.652 10.5455C17.4722 10.6647 17.7997 11.6726 17.2062 12.2512L15.7049 13.7146C15.4692 13.9443 15.3617 14.2753 15.4173 14.5997L15.7717 16.666C15.9118 17.4829 15.0544 18.1059 14.3208 17.7202L12.4651 16.7446C12.1738 16.5915 11.8257 16.5915 11.5344 16.7446L9.67874 17.7202C8.94511 18.1059 8.08768 17.4829 8.22779 16.666L8.58219 14.5997C8.63783 14.2753 8.53028 13.9443 8.29459 13.7146L6.79332 12.2512C6.1998 11.6726 6.52731 10.6647 7.34753 10.5455L9.42225 10.244C9.74796 10.1967 10.0295 9.99211 10.1752 9.69697L11.103 7.81696Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M18.4424 14.106L20.1846 14.8943C21.0283 15.2761 21.9613 14.5667 21.7842 13.6782L21.6155 12.8313C21.5377 12.4411 21.6806 12.0403 21.99 11.7808L22.6034 11.2662C23.3499 10.6399 22.9822 9.44941 22.0043 9.32712L20.8958 9.18848C20.5373 9.14365 20.2219 8.93603 20.0458 8.62895L19.4377 7.56859C19.003 6.81047 17.8817 6.81047 17.447 7.56859L16.559 9.11719M5.55765 14.106L3.8154 14.8943C2.97172 15.2761 2.0387 14.5667 2.21577 13.6782L2.38454 12.8313C2.46229 12.4411 2.3194 12.0403 2.01002 11.7808L1.39664 11.2662C0.650069 10.6399 1.01784 9.44941 1.99569 9.32712L3.1042 9.18848C3.46273 9.14365 3.77814 8.93603 3.95423 8.62895L4.56227 7.56859C4.997 6.81047 6.11829 6.81047 6.55302 7.56859L7.44104 9.11719',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} )
	),
	container: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M3 16C3 14.8954 3.89543 14 5 14H8C9.10457 14 10 14.8954 10 16V19C10 20.1046 9.10457 21 8 21H5C3.89543 21 3 20.1046 3 19V16Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M14 16C14 14.8954 14.8954 14 16 14H19C20.1046 14 21 14.8954 21 16V19C21 20.1046 20.1046 21 19 21H16C14.8954 21 14 20.1046 14 19V16Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} ),
		el( 'path', {
			d:
				'M3 5C3 3.89543 3.89543 3 5 3H19C20.1046 3 21 3.89543 21 5V8C21 9.10457 20.1046 10 19 10H5C3.89543 10 3 9.10457 3 8V5Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	image: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M3 17L7.41995 12.58C8.26284 11.7372 9.65125 11.8141 10.3959 12.7449L11.789 14.4863C12.4639 15.3298 13.6866 15.4851 14.5508 14.8369L15.6123 14.0408C16.4086 13.4436 17.5228 13.5228 18.2265 14.2265L21 17M17 8C17 8.55228 16.5523 9 16 9C15.4477 9 15 8.55228 15 8C15 7.44772 15.4477 7 16 7C16.5523 7 17 7.44772 17 8ZM5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	image_gallery: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M3 8.5V5C3 3.89543 3.89543 3 5 3H8.5C9.60457 3 10.5 3.89543 10.5 5V8.5M3 8.5C3 9.60457 3.89543 10.5 5 10.5H8.5C9.60457 10.5 10.5 9.60457 10.5 8.5M3 8.5L4.94679 6.87767C5.58153 6.34873 6.47618 6.26455 7.19844 6.6658L10.5 8.5M13.5 8.5V5C13.5 3.89543 14.3954 3 15.5 3H19C20.1046 3 21 3.89543 21 5V8.5M13.5 8.5C13.5 9.60457 14.3954 10.5 15.5 10.5H19C20.1046 10.5 21 9.60457 21 8.5M13.5 8.5L15.4468 6.87767C16.0815 6.34873 16.9762 6.26455 17.6984 6.6658L21 8.5M3 19V15.5C3 14.3954 3.89543 13.5 5 13.5H8.5C9.60457 13.5 10.5 14.3954 10.5 15.5V19M3 19C3 20.1046 3.89543 21 5 21H8.5C9.60457 21 10.5 20.1046 10.5 19M3 19L4.94679 17.3777C5.58153 16.8487 6.47618 16.7645 7.19844 17.1658L10.5 19M13.5 19V15.5C13.5 14.3954 14.3954 13.5 15.5 13.5H19C20.1046 13.5 21 14.3954 21 15.5V19M13.5 19C13.5 20.1046 14.3954 21 15.5 21H19C20.1046 21 21 20.1046 21 19M13.5 19L15.4468 17.3777C16.0815 16.8487 16.9762 16.7645 17.6984 17.1658L21 19',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	counter: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M1 10.5L3 9.5V16.5M5.5 11C5.66667 10.5 6.3 9.5 7.5 9.5C9 9.5 9.5 10.5 9.5 11C9.5 13.0707 7 15 5.5 16.5H9.7M11.5 10.5C11.6667 10.1667 12.3 9.2 13.5 9.2C14.7 9.2 15.7 10 15.7 11C15.7 12 14.2 13 13 13C14 13 16.2469 13 16 15C15.8742 16.0188 14.8107 16.7 13.5 16.7C13 16.7 12 16.5 11.5 15.5M20.5 18V6M20.5 6L18 8M20.5 6L23 8',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	countdown: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M12 5.39514V3.16507M16.2269 6.58948L17.8848 5.09796M9.69775 2.60654H14.3022M11.8872 13.4553H16.6079M20.0052 13.3886C20.0052 17.8099 16.4211 21.394 11.9999 21.394C7.57873 21.394 3.99463 17.8099 3.99463 13.3886C3.99463 8.96743 7.57873 5.38333 11.9999 5.38333C16.4211 5.38333 20.0052 8.96743 20.0052 13.3886ZM12.327 13.3886C12.327 13.2081 12.1807 13.0618 12.0002 13.0618C11.8197 13.0618 11.6733 13.2081 11.6733 13.3886C11.6733 13.5692 11.8197 13.7155 12.0002 13.7155C12.1807 13.7155 12.327 13.5692 12.327 13.3886Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	modal: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'rect', {
			x: 3,
			y: 7.30975,
			width: 15.033,
			height: 11.7712,
			rx: 2,
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d: 'M6.86572 11.6992H14.1675',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d: 'M6.86572 14.6918H14.1675',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d:
				'M19.0982 4.51694L20.1633 5.58197M20.1633 5.58197L21.2283 6.647M20.1633 5.58197L19.0982 6.647M20.1633 5.58197L21.2283 4.51694',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	slider: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 26 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'rect', {
			x: 5.4502,
			y: 4.98621,
			width: 15.0978,
			height: 11.2249,
			rx: 2,
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d: 'M9.19922 9.10217H16.501',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d: 'M9.19922 12.0948H16.501',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d: 'M15.9987 19.0138H16.0054M12.9922 19.0138H12.9989M9.99219 19.0138H9.99885',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d: 'M2.49658 9.69873L1.49658 10.5987L2.49658 11.4987',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d: 'M23.5034 11.4987L24.5034 10.5987L23.5034 9.69872',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	slider_child: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 26 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'rect', {
			x: 5.4502,
			y: 6.38751,
			width: 15.0978,
			height: 11.2249,
			rx: 2,
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d: 'M9.19922 10.5035H16.501',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d: 'M9.19922 13.4962H16.501',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d: 'M2.49658 11.1L1.49658 12.0001L2.49658 12.9',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d: 'M23.5034 12.9L24.5034 12L23.5034 11.1',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),

	// ----------------------------.
	// All Spectra Pro Block Icons .
	// ----------------------------.

	login: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M12 16L16 12M16 12L12 8M16 12L4 12M7 16V17C7 18.6569 8.34315 20 10 20H17C18.6569 20 20 18.6569 20 17V7C20 5.34315 18.6569 4 17 4H10C8.34315 4 7 5.34315 7 7V8',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	register: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M14 21H4C4 17.134 7.13401 14 11 14C12.0736 14 13.0907 14.2417 14 14.6736M17 15V18M17 18V21M17 18H14M17 18H20M15 7C15 9.20914 13.2091 11 11 11C8.79086 11 7 9.20914 7 7C7 4.79086 8.79086 3 11 3C13.2091 3 15 4.79086 15 7Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	password: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M15 7C16.1046 7 17 7.89543 17 9M21 9C21 12.3137 18.3137 15 15 15C14.3938 15 13.8087 14.9101 13.2571 14.7429L11 17H9V19H7V21H4C3.44772 21 3 20.5523 3 20V17.4142C3 17.149 3.10536 16.8946 3.29289 16.7071L9.25707 10.7429C9.08989 10.1914 9 9.60617 9 9C9 5.68629 11.6863 3 15 3C18.3137 3 21 5.68629 21 9Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	rePassword: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M13 9C13 7.9 13.8954 7 15 7C16.1046 7 17 7.89543 17 9C17 10.1 16.1 11 15 11M15 11V10M15 11L16 11.5M21 9C21 12.3137 18.3137 15 15 15C14.3938 15 13.8087 14.9101 13.2571 14.7429L11 17H9V19H7V21H4C3.44772 21 3 20.5523 3 20V17.4142C3 17.149 3.10536 16.8946 3.29289 16.7071L9.25707 10.7429C9.08989 10.1914 9 9.60617 9 9C9 5.68629 11.6863 3 15 3C18.3137 3 21 5.68629 21 9Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	instagram_feed: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M17 6.5H17.1M8.14286 21H15.8571C18.6975 21 21 18.6975 21 15.8571V8.14286C21 5.30254 18.6975 3 15.8571 3H8.14286C5.30254 3 3 5.30254 3 8.14286V15.8571C3 18.6975 5.30254 21 8.14286 21ZM16 12C16 14.2091 14.2091 16 12 16C9.79086 16 8 14.2091 8 12C8 9.79086 9.79086 8 12 8C14.2091 8 16 9.79086 16 12Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	loop_builder: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 25 24', fill: 'none', className: 'uagb-editor-icons' },
		el(
			'g',
			null,
			el( 'path', {
				d: 'M3.35981 8.80578C5.11039 7.05892 7.94864 7.05892 9.69922 8.80578L12.8689 11.9687L9.69922 15.1317C7.94864 16.8785 5.11039 16.8785 3.35981 15.1317C1.60923 13.3848 1.60923 10.5526 3.35981 8.80578Z',
				fill: noColor,
				stroke: iconColor,
				strokeWidth: 1.4,
			} ),
			el( 'path', {
				d: 'M22.2987 15.1942C20.5481 16.9411 17.7099 16.9411 15.9593 15.1942L12.7896 12.0313L15.9593 8.86831C17.7099 7.12146 20.5481 7.12146 22.2987 8.86831C24.0493 10.6152 24.0493 13.4474 22.2987 15.1942Z',
				fill: noColor,
				stroke: iconColor,
				strokeWidth: 1.4,
			} ),
		),
	),
	loop_wrapper: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 25 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d: 'M7.21733 14.2448C5.93905 14.2448 5.29991 14.2448 4.82713 13.9634C4.54096 13.7931 4.30185 13.554 4.13151 13.2678C3.8501 12.795 3.8501 12.1559 3.8501 10.8776V7.02072C3.8501 5.1351 3.8501 4.19229 4.43588 3.60651C5.02167 3.02072 5.96448 3.02072 7.8501 3.02072H11.707C12.9852 3.02072 13.6244 3.02072 14.0972 3.30214C14.3833 3.47248 14.6224 3.71158 14.7928 3.99775C15.0742 4.47053 15.0742 5.10967 15.0742 6.38795M14.584 20.9793H17.8081C19.6937 20.9793 20.6365 20.9793 21.2223 20.3935C21.8081 19.8077 21.8081 18.8649 21.8081 16.9793V13.7552C21.8081 11.8696 21.8081 10.9267 21.2223 10.341C20.6365 9.75517 19.6937 9.75517 17.8081 9.75517H14.584C12.6984 9.75517 11.7556 9.75517 11.1698 10.341C10.584 10.9267 10.584 11.8696 10.584 13.7552V16.9793C10.584 18.8649 10.584 19.8077 11.1698 20.3935C11.7556 20.9793 12.6984 20.9793 14.584 20.9793Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} ),
	),
	popup_builder: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 25 25', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d: 'M7.18382 7.82229C7.1591 7.79676 6.49721 7.78045 5.55331 7.77579C4.45592 7.77038 3.57129 8.66533 3.57129 9.76272V20.3833C3.57129 21.4879 4.46672 22.3833 5.57129 22.3833H15.7129C16.8175 22.3833 17.7129 21.4879 17.7129 20.3833V17.8467M11.5659 7.36682H17.0951C17.6474 7.36682 18.0951 7.81454 18.0951 8.36682V13.8366M7.62451 5.35669V15.8467C7.62451 16.9513 8.51994 17.8467 9.62451 17.8467H20.0361C21.1407 17.8467 22.0361 16.9513 22.0361 15.8467V5.35669C22.0361 4.25212 21.1407 3.35669 20.0361 3.35669H9.62451C8.51994 3.35669 7.62451 4.25212 7.62451 5.35669Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
		} ),
	),

	// ------------------------.
	// All Miscellaneous Icons .
	// ------------------------.

	post_title: el(
		'svg',
		{ width: 20, height: 20 },
		el( 'path', {
			fill: spectraDarkColor,
			d:
				'M19.31 0h-18.619c-0.381 0-0.691 0.309-0.691 0.691v18.619c0 0.382 0.309 0.691 0.691 0.691h18.619c0.382 0 0.691-0.309 0.691-0.691v-18.619c0-0.381-0.309-0.691-0.69-0.691v0zM18.62 6.206h-4.825v-4.825h4.825v4.825zM18.62 12.413h-4.825v-4.825h4.825v4.825zM1.381 7.588h4.825v4.825h-4.825v-4.825zM7.588 7.588h4.825v4.825h-4.825v-4.825zM12.413 1.381v4.825h-4.825v-4.825h4.825zM6.206 1.381v4.825h-4.825v-4.825h4.825zM1.381 13.794h4.825v4.826h-4.825v-4.826zM7.588 18.62v-4.826h4.825v4.826h-4.825zM13.794 18.62v-4.826h4.825v4.826h-4.825z',
		} )
	),
	quote_1: el(
		'svg',
		{ width: 20, height: 20, className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M15.581 10.226h-15.162c-0.233 0-0.419 0.37-0.419 0.826 0 0.458 0.186 0.828 0.419 0.828h15.161c0.233 0 0.419-0.37 0.419-0.828 0.001-0.455-0.186-0.826-0.418-0.826v0z',
		} ),
		el( 'path', {
			d:
				'M15.581 14.285h-15.162c-0.233 0-0.419 0.373-0.419 0.827 0 0.458 0.186 0.826 0.419 0.826h15.161c0.233 0 0.419-0.369 0.419-0.826 0.001-0.454-0.186-0.827-0.418-0.827v0z',
		} ),
		el( 'path', {
			d:
				'M15.581 18.346h-15.162c-0.233 0-0.419 0.37-0.419 0.826 0 0.459 0.186 0.828 0.419 0.828h15.161c0.233 0 0.419-0.369 0.419-0.828 0.001-0.455-0.186-0.826-0.418-0.826v0z',
		} ),
		el( 'path', {
			d:
				'M9.126 0.595c-0.46 0.465-0.974 1.35-0.835 3.042 0.081 1.319 0.666 3.29 3.048 5.216 0.112 0.090 0.241 0.136 0.38 0.136 0.183 0 0.362-0.086 0.487-0.251 0.214-0.283 0.164-0.683-0.113-0.902-1.935-1.566-2.458-3.105-2.551-4.154 0.274 0.156 0.582 0.258 0.913 0.258 1.045 0 1.89-0.886 1.89-1.972 0-1.088-0.846-1.966-1.89-1.966-0.233 0-0.451 0.062-0.657 0.143l0.004-0.011-0.218 0.101-0.018 0.011-0.007 0.006-0.299 0.214-0.134 0.131z',
		} ),
		el( 'path', {
			d:
				'M4.517 0.595c-0.465 0.465-0.974 1.35-0.841 3.042 0.085 1.319 0.671 3.29 3.049 5.216 0.116 0.090 0.245 0.136 0.383 0.136 0.178 0 0.366-0.086 0.487-0.251 0.214-0.283 0.165-0.683-0.108-0.902-1.939-1.566-2.467-3.105-2.56-4.154 0.278 0.156 0.584 0.258 0.92 0.258 1.046 0 1.885-0.886 1.885-1.972 0-1.088-0.845-1.966-1.885-1.966-0.236 0-0.447 0.062-0.657 0.143l0.010-0.011-0.218 0.101-0.022 0.011-0.009 0.006-0.305 0.214-0.129 0.13z',
		} )
	),
	quote_2: el(
		'svg',
		{ width: 20, height: 20, className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M4.599 6.686c-0.39 0.397-0.822 1.149-0.705 2.586 0.068 1.123 0.561 2.799 2.561 4.434 0.096 0.080 0.205 0.115 0.321 0.115 0.153 0 0.306-0.071 0.409-0.214 0.181-0.239 0.135-0.58-0.095-0.766-1.626-1.332-2.066-2.639-2.144-3.531 0.231 0.132 0.488 0.218 0.769 0.218 0.874 0 1.587-0.753 1.587-1.677s-0.711-1.672-1.587-1.672c-0.196 0-0.38 0.054-0.552 0.121l0.003-0.010-0.184 0.085-0.016 0.010-0.006 0.006-0.252 0.181-0.109 0.114z',
		} ),
		el( 'path', {
			d:
				'M0.725 6.686c-0.389 0.397-0.821 1.149-0.706 2.586 0.068 1.123 0.562 2.799 2.56 4.434 0.094 0.077 0.204 0.114 0.322 0.114 0.151 0 0.31-0.073 0.409-0.213 0.177-0.239 0.136-0.582-0.090-0.767-1.63-1.332-2.072-2.639-2.149-3.531 0.23 0.132 0.486 0.218 0.772 0.218 0.879 0 1.583-0.753 1.583-1.677s-0.71-1.672-1.583-1.672c-0.199 0-0.378 0.054-0.554 0.121l0.008-0.010-0.184 0.085-0.018 0.010-0.009 0.006-0.253 0.182-0.108 0.114z',
		} ),
		el( 'path', {
			d:
				'M19.25 6.929h-10.041c-0.414 0-0.75-0.336-0.75-0.75s0.336-0.75 0.75-0.75h10.041c0.414 0 0.75 0.336 0.75 0.75s-0.336 0.75-0.75 0.75z',
		} ),
		el( 'path', {
			d:
				'M19.25 10.75h-10.041c-0.414 0-0.75-0.336-0.75-0.75s0.336-0.75 0.75-0.75h10.041c0.414 0 0.75 0.336 0.75 0.75s-0.336 0.75-0.75 0.75z',
		} ),
		el( 'path', {
			d:
				'M19.25 14.571h-10.041c-0.414 0-0.75-0.336-0.75-0.75s0.336-0.75 0.75-0.75h10.041c0.414 0 0.75 0.336 0.75 0.75s-0.336 0.75-0.75 0.75z',
		} )
	),
	quote_inline_icon: el(
		'svg',
		{ width: 20, height: 20, viewBox: '0 0 32 32' },
		el( 'path', {
			d:
				'M7.031 14c3.866 0 7 3.134 7 7s-3.134 7-7 7-7-3.134-7-7l-0.031-1c0-7.732 6.268-14 14-14v4c-2.671 0-5.182 1.040-7.071 2.929-0.364 0.364-0.695 0.751-0.995 1.157 0.357-0.056 0.724-0.086 1.097-0.086zM25.031 14c3.866 0 7 3.134 7 7s-3.134 7-7 7-7-3.134-7-7l-0.031-1c0-7.732 6.268-14 14-14v4c-2.671 0-5.182 1.040-7.071 2.929-0.364 0.364-0.695 0.751-0.995 1.157 0.358-0.056 0.724-0.086 1.097-0.086z',
		} )
	),
	quote_tweet_icon: el(
		'svg',
		{ width: 20, height: 20, viewBox: '0 0 512 512' },
		el( 'path', {
			d:
				'M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z',
		} )
	),
	at_the_rate: el(
		'svg',
		{ width: 30, height: 30, className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M5.605 17.2c0-2.131 0.499-4.074 1.499-5.829 1-1.754 2.395-3.136 4.19-4.15 1.794-1.013 3.81-1.52 6.046-1.52 2.712 0 4.901 0.773 6.562 2.323 1.662 1.549 2.493 3.589 2.493 6.118 0 2.051-0.542 3.786-1.626 5.202-1.088 1.418-2.304 2.125-3.656 2.125-0.781 0-1.355-0.232-1.717-0.696-0.365-0.462-0.533-1.037-0.506-1.726-1.006 1.614-2.378 2.422-4.11 2.422-1.39 0-2.507-0.539-3.347-1.619-0.842-1.077-1.142-2.442-0.904-4.088 0.237-1.65 0.946-2.982 2.125-4.002 1.179-1.021 2.509-1.53 3.992-1.53s2.638 0.518 3.467 1.558c0.829 1.040 1.122 2.322 0.883 3.843l-0.494 3.2c-0.094 0.739 0.165 1.11 0.77 1.11 0.77 0 1.502-0.56 2.205-1.678 0.701-1.12 1.054-2.493 1.054-4.122 0-2.106-0.656-3.787-1.966-5.046-1.312-1.258-3.133-1.886-5.462-1.886-2.794 0-5.098 0.96-6.91 2.88-1.814 1.92-2.722 4.29-2.722 7.109 0 2.382 0.738 4.227 2.214 5.533 1.477 1.302 3.459 1.989 5.947 2.056l-0.376 1.509c-2.862-0.069-5.184-0.899-6.971-2.494-1.787-1.594-2.68-3.794-2.68-6.602zM19.15 15.85c0.17-1.15-0.014-2.118-0.558-2.899-0.542-0.781-1.28-1.173-2.214-1.173-0.933 0-1.79 0.392-2.571 1.173s-1.259 1.749-1.43 2.899c-0.171 1.152 0.013 2.122 0.557 2.91 0.542 0.787 1.282 1.181 2.214 1.181s1.79-0.394 2.573-1.181c0.781-0.789 1.256-1.758 1.43-2.91z',
		} )
	),
	top_align: el(
		'svg',
		{ width: 20, height: 20, className: 'uagb-editor-icons' },
		el( 'path', {
			d: 'M1.5 0.438v2.125h17v-2.125h-17zM5.75 8.938h3.188v10.625h2.125v-10.625h3.187l-4.25-4.25-4.25 4.25z',
		} )
	),
	middle_align: el(
		'svg',
		{ width: 20, height: 20, className: 'uagb-editor-icons' },
		el( 'path', { d: 'M18.5 11.063v-2.125h-17v2.125h17z' } ),
		el( 'path', {
			d: 'M12.707 3.519l-2.707-2.707-2.707 2.707h2.030v4.368h1.354v-4.368h2.030z',
		} ),
		el( 'path', {
			d: 'M7.293 16.48l2.707 2.707 2.707-2.707h-2.030v-4.368h-1.354v4.368h-2.030z',
		} )
	),
	bottom_align: el(
		'svg',
		{ width: 20, height: 20, className: 'uagb-editor-icons' },
		el( 'path', {
			d: 'M18.5 20v-2.125h-17v2.125h17zM14.25 11.5h-3.187v-10.625h-2.125v10.625h-3.188l4.25 4.25 4.25-4.25z',
		} )
	),
	carousel_left: el(
		'svg',
		{ width: 20, height: 20, viewBox: '0 0 256 512', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z',
		} )
	),
	carousel_right: el(
		'svg',
		{ width: 20, height: 20, viewBox: '0 0 256 512', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z',
		} )
	),
	top_margin: el(
		'svg',
		{ width: 20, height: 20, className: 'uagb-editor-icons' },
		el( 'path', {
			fill: '#999',
			d:
				'M17 1c1.103 0 2 0.897 2 2v14c0 1.103-0.897 2-2 2h-14c-1.103 0-2-0.897-2-2v-14c0-1.103 0.897-2 2-2h14zM17 0h-14c-1.657 0-3 1.343-3 3v14c0 1.657 1.343 3 3 3h14c1.657 0 3-1.343 3-3v-14c0-1.657-1.343-3-3-3v0z',
		} ),
		el( 'path', {
			fill: '#575E67',
			d: 'M17 0.984h-14c-1.103 0-2 0.897-2 2v0.797h18v-0.797c0-1.103-0.897-2-2-2z',
		} )
	),
	bottom_margin: el(
		'svg',
		{ width: 20, height: 20, className: 'uagb-editor-icons' },
		el( 'path', {
			fill: '#999',
			d:
				'M17 1c1.103 0 2 0.897 2 2v14c0 1.103-0.897 2-2 2h-14c-1.103 0-2-0.897-2-2v-14c0-1.103 0.897-2 2-2h14zM17 0h-14c-1.657 0-3 1.343-3 3v14c0 1.656 1.343 3 3 3h14c1.657 0 3-1.343 3-3v-14c0-1.657-1.343-3-3-3v0z',
		} ),
		el( 'path', {
			fill: '#575E67',
			d: 'M3 19.016h14c1.103 0 2-0.896 2-2v-0.797h-18v0.797c0 1.103 0.897 2 2 2z',
		} )
	),
	left_margin: el(
		'svg',
		{ width: 20, height: 20, className: 'uagb-editor-icons' },
		el( 'path', {
			fill: '#999',
			d:
				'M17 0.999c1.103 0 2 0.897 2 2v14c0 1.103-0.897 2-2 2h-14c-1.103 0-2-0.897-2-2v-14c0-1.103 0.897-2 2-2h14zM17-0.001h-14c-1.656 0-3 1.343-3 3v14c0 1.657 1.343 3 3 3h14c1.657 0 3-1.343 3-3v-14c0-1.657-1.343-3-3-3v0z',
		} ),
		el( 'path', {
			fill: '#575E67',
			d: 'M0.984 2.999v14c0 1.103 0.896 2 2 2h0.797v-18h-0.797c-1.104 0-2 0.897-2 2z',
		} )
	),
	right_margin: el(
		'svg',
		{ width: 20, height: 20, className: 'uagb-editor-icons' },
		el( 'path', {
			fill: '#999',
			d:
				'M17 0.999c1.103 0 2 0.897 2 2v14c0 1.103-0.897 2-2 2h-14c-1.103 0-2-0.897-2-2v-14c0-1.103 0.897-2 2-2h14zM17-0.001h-14c-1.657 0-3 1.343-3 3v14c0 1.657 1.343 3 3 3h14c1.656 0 3-1.343 3-3v-14c0-1.657-1.343-3-3-3v0z',
		} ),
		el( 'path', {
			fill: '#575E67',
			d: 'M19.015 16.999v-14c0-1.103-0.896-2-2-2h-0.797v18h0.797c1.104 0 2-0.896 2-2z',
		} )
	),
	vertical_spacing: el(
		'svg',
		{ width: 20, height: 20, className: 'uagb-editor-icons' },
		el( 'path', {
			fill: '#999',
			d:
				'M17 0.999c1.103 0 2 0.897 2 2v14c0 1.103-0.897 2-2 2h-14c-1.103 0-2-0.897-2-2v-14c0-1.103 0.897-2 2-2h14zM17-0.001h-14c-1.657 0-3 1.343-3 3v14c0 1.657 1.343 3 3 3h14c1.657 0 3-1.343 3-3v-14c0-1.656-1.343-3-3-3v0z',
		} ),
		el( 'path', {
			fill: '#575E67',
			d: 'M17 0.983h-14c-1.103 0-2 0.896-2 2v0.797h18v-0.797c0-1.103-0.896-2-2-2z',
		} ),
		el( 'path', {
			fill: '#575E67',
			d: 'M3 19.031h14c1.103 0 2-0.896 2-2v-0.797h-18v0.797c0 1.104 0.896 2 2 2z',
		} )
	),
	horizontal_spacing: el(
		'svg',
		{ width: 20, height: 20, className: 'uagb-editor-icons' },
		el( 'path', {
			fill: '#999',
			d:
				'M17 0.999c1.103 0 2 0.897 2 2v14c0 1.103-0.897 2-2 2h-14c-1.103 0-2-0.897-2-2v-14c0-1.103 0.897-2 2-2h14zM17-0.001h-14c-1.657 0-3 1.343-3 3v14c0 1.657 1.343 3 3 3h14c1.656 0 3-1.343 3-3v-14c0-1.657-1.343-3-3-3v0z',
		} ),
		el( 'path', {
			fill: '#575E67',
			d: 'M19.016 16.999v-14c0-1.103-0.896-2-2-2h-0.797v18h0.797c1.103 0 2-0.896 2-2z',
		} ),
		el( 'path', {
			fill: '#575E67',
			d: 'M0.968 2.999v14c0 1.103 0.896 2 2 2h0.797v-18h-0.797c-1.104 0-2 0.897-2 2z',
		} )
	),
	form1: el(
		'svg',
		{ width: 20, height: 20, role: 'img', viewBox: '0 0 58 58', className: 'uagb-editor-icons' },
		el( 'path', {
			fill: spectraDarkColor,
			d:
				'M41.5,2.1H7.8C4,2.1,1,5.2,1,8.9v40.5c0,3.7,3,6.8,6.8,6.8h18.6v-3.4H7.8c-1.9,0-3.4-1.5-3.4-3.4V8.9c0-1.9,1.5-3.4,3.4-3.4h33.8c1.9,0,3.4,1.5,3.4,3.4v19.8l3.4-3.4V8.9C48.3,5.2,45.2,2.1,41.5,2.1z',
		} ),
		el( 'path', { fill: spectraDarkColor, d: 'M38.1,15.6h-27v-3.4h27V15.6z' } ),
		el( 'path', { fill: spectraDarkColor, d: 'M38.1,23h-27v-3.4h27V23z' } ),
		el( 'path', { fill: spectraDarkColor, d: 'M35.2,29.6H13.5v-2.7h21.7V29.6z' } ),
		el( 'path', { fill: spectraDarkColor, d: 'M35.2,35.4H13.5v-2.7h21.7V35.4z' } ),
		el( 'path', { fill: spectraDarkColor, d: 'M35.2,27.8v6.3h-2.7v-6.3H35.2z' } ),
		el( 'path', { fill: spectraDarkColor, d: 'M16.2,27.9v6.3h-2.7v-6.3H16.2z' } ),
		el( 'path', {
			fill: spectraDarkColor,
			d:
				'M43.4,31.9c-0.7,0.4-1.2,1-1.4,1.8c-0.9-0.3-1.9-0.1-2.7,0.3c-0.7,0.4-1.2,1-1.4,1.8c-0.9-0.3-1.9-0.1-2.7,0.3c-0.4,0.4-0.8,0.7-1.1,1.3L31,34c-1.1-1.3-3-1.5-4.4-0.7c-0.8,0.6-1.4,1.4-1.5,2.4c-0.1,1,0.1,2,0.8,2.8l4,4.5c-0.7,0.3-1.2,0.6-1.8,1.2c-0.4,0.5-0.9,1.2-1,1.9c0,0.3,0.1,0.6,0.3,0.9l5.8,6.5c4.3,4.8,11.6,5.5,16.5,1.6c0.2-0.1,0.3-0.2,0.4-0.4c0.1-0.1,0.3-0.2,0.3-0.3c5-4.7,5.2-12.9,0.4-18.3l-3-3.4C46.7,31.4,44.8,31.1,43.4,31.9z M49.1,37.5c4,4.5,3.8,11.2-0.3,15.2c-0.3,0.2-0.4,0.4-0.7,0.6c-3.8,3.1-9.9,2.4-13.3-1.4l-5.3-6c0-0.2,0.1-0.2,0.2-0.3c0.3-0.3,0.7-0.5,1.3-0.6c0.5,0,0.9,0.2,1.2,0.6l4.1,4.6c0.4,0.5,1.1,0.5,1.6,0.1s0.5-1.1,0.1-1.6l-4.1-4.6l-1.1-1.3l-5.2-5.9c-0.2-0.3-0.4-0.6-0.3-1c0-0.3,0.3-0.5,0.5-0.8c0.4-0.2,1.1-0.1,1.5,0.3l5.2,5.8l1.5,1.7c0.4,0.5,1.1,0.5,1.6,0.1c0.5-0.4,0.5-1.1,0.1-1.6l-1.5-1.7c-0.2-0.3-0.4-0.6-0.3-1c0-0.3,0.2-0.6,0.5-0.8c0.5-0.3,1.1-0.1,1.5,0.3l0.7,0.8l0.7,0.8c0.4,0.5,1.1,0.5,1.6,0.1c0.5-0.4,0.5-1.1,0.1-1.6l-0.7-0.8c-0.2-0.3-0.4-0.6-0.3-1c0-0.3,0.2-0.6,0.5-0.8c0.5-0.3,1.1-0.1,1.5,0.3l0.7,0.8l0.7,0.8c0.4,0.5,1.1,0.5,1.6,0.1c0.5-0.4,0.5-1.1,0.1-1.6l-0.7-0.8c-0.2-0.3-0.4-0.6-0.3-1c0-0.3,0.2-0.6,0.5-0.8l0,0c0.5-0.3,1.1-0.1,1.5,0.3L49.1,37.5L49.1,37.5z',
		} )
	),
	form2: el(
		'svg',
		{ width: 20, height: 20, role: 'img', viewBox: '0 0 58 58', className: 'uagb-editor-icons' },
		el( 'path', {
			fill: spectraDarkColor,
			d:
				'M44.4,5.6H10.9c-4.1,0-7.4,3.3-7.4,7.4v33.5c0,4.1,3.3,7.4,7.4,7.4h15.4l3.7-1.9l-3.7-1.9H10.9c-2.1,0-3.7-1.7-3.7-3.7V13.1c0-2.1,1.7-3.7,3.7-3.7h33.5c2.1,0,3.7,1.7,3.7,3.7v30l0.1-0.1c0.4-2.3,1.7-4.4,3.6-5.7V13.1C51.9,9,48.5,5.6,44.4,5.6z',
		} ),
		el( 'path', { fill: spectraDarkColor, d: 'M14.7,19.6h26.1v-3.7H14.7V19.6z' } ),
		el( 'path', { fill: spectraDarkColor, d: 'M14.7,27h26.1v-3.7H14.7V27z' } ),
		el( 'path', { fill: spectraDarkColor, d: 'M14.7,34.5h26.1v-3.7H14.7V34.5z' } ),
		el( 'path', {
			fill: spectraDarkColor,
			d:
				'M56.2,45l0-3.7c0-1-0.4-2-1.1-2.6c-0.7-0.7-1.6-1.1-2.6-1.1l-22.6,0.2c-1,0-4.6,0.5-5.3,1.1l-7.1,4.6l7.2,4.4c0,0,4.2,1,5.3,1l22.6-0.2C54.5,48.7,56.2,47,56.2,45z M29.9,47.1c-0.3,0-1.2-0.2-2.2-0.4l-0.1-6.8c0.8-0.1,1.7-0.2,2.2-0.2l17-0.2l0.1,7.4L29.9,47.1z',
		} )
	),
	form3: el(
		'svg',
		{ width: 20, height: 20, role: 'img', viewBox: '0 0 58 58', className: 'uagb-editor-icons' },
		el( 'path', { fill: spectraDarkColor, d: 'M41.5,46.1h-27v-3.4h27V46.1z' } ),
		el( 'path', {
			fill: spectraDarkColor,
			d:
				'M20.7,26.3l10.1,10.1l21.2-21.2L41.8,5.1L20.7,26.3z M44.2,12.8c0.7,0.7,0.7,1.8,0,2.5L32,27.6c-0.3,0.4-0.8,0.5-1.3,0.5c-0.5,0-0.9-0.2-1.3-0.5c-0.7-0.7-0.7-1.8,0-2.5l12.3-12.3C42.4,12.1,43.5,12.1,44.2,12.8z',
		} ),
		el( 'path', {
			fill: spectraDarkColor,
			d: 'M56,5.8l-4.8-4.8c-1.4-1.4-3.9-1.4-5.3,0l-1.6,1.6l10.1,10.1l1.5-1.5C57.4,9.7,57.4,7.3,56,5.8z',
		} ),
		el( 'path', {
			fill: spectraDarkColor,
			d:
				'M46.9,56.3H4.6c-1,0-1.8-0.8-1.8-1.8V9.7c0-1,0.8-1.8,1.8-1.8H33c1,0,1.8,0.8,1.8,1.8c0,1-0.8,1.8-1.8,1.8H6.5v41.1h40.4c1.4,0,2.6-1.2,2.6-2.6V26.9c0-1,0.8-1.8,1.8-1.8c1,0,1.8,0.8,1.8,1.8V50C53.2,53.5,50.4,56.3,46.9,56.3z',
		} )
	),
	close: el(
		'svg',
		{ width: 8, height: 8, viewBox: '0 0 6 6', className: 'uagb-editor-icons' },
		el( 'path', {
			fill: spectraDarkColor,
			d:
				'M5.91683 0.5875L5.32933 0L3.00016 2.32917L0.670996 0L0.0834961 0.5875L2.41266 2.91667L0.0834961 5.24583L0.670996 5.83333L3.00016 3.50417L5.32933 5.83333L5.91683 5.24583L3.58766 2.91667L5.91683 0.5875Z',
		} )
	),
	add: el(
		'svg',
		{ width: 8, height: 8, viewBox: '0 0 8 8', className: 'uagb-editor-icons' },
		el( 'path', {
			fill: spectraDarkColor,
			d:
				'M4.35613 0.231675L3.52528 0.231675V3.52561L0.231343 3.52561V4.35647L3.52528 4.35647V7.65041L4.35613 7.65041L4.35613 4.35647H7.65007V3.52561H4.35613L4.35613 0.231675Z',
		} )
	),
	video_placeholder: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M14.7519 11.1679L11.5547 9.03647C10.8901 8.59343 10 9.06982 10 9.86852V14.1315C10 14.9302 10.8901 15.4066 11.5547 14.9635L14.7519 12.8321C15.3457 12.4362 15.3457 11.5638 14.7519 11.1679Z',
			stroke: spectraDarkColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d:
				'M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z',
			stroke: spectraDarkColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	gallery_placeholder: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M6 14.6667L9.68329 10.9834C10.3857 10.281 11.5427 10.345 12.1632 11.1207L13.3242 12.5719C13.8866 13.2749 14.9055 13.4042 15.6257 12.8641L16.5103 12.2006C17.1738 11.703 18.1023 11.769 18.6888 12.3554L21 14.6667M6 6H4.66667C3.74619 6 3 6.74619 3 7.66667V19.3333C3 20.2538 3.74619 21 4.66667 21H16.3333C17.2538 21 18 20.2538 18 19.3333V18M17.6667 7.16667C17.6667 7.6269 17.2936 8 16.8333 8C16.3731 8 16 7.6269 16 7.16667C16 6.70643 16.3731 6.33333 16.8333 6.33333C17.2936 6.33333 17.6667 6.70643 17.6667 7.16667ZM7.66667 18H19.3333C20.2538 18 21 17.2538 21 16.3333V4.66667C21 3.74619 20.2538 3 19.3333 3H7.66667C6.74619 3 6 3.74619 6 4.66667V16.3333C6 17.2538 6.74619 18 7.66667 18Z',
			fill: noColor,
			stroke: spectraDarkColor,
			strokeWidth: 1.4,
		} )
	),
	bg_color: el(
		'svg',
		{ width: 11, height: 10, viewBox: '0 0 11 10', className: 'uagb-editor-icons' },
		el( 'path', {
			fill: spectraDarkColor,
			d:
				'M10.6927 1.08247C10.6927 1.08247 10.8502 0.615805 10.5119 0.289139C10.2027 -0.0141947 9.80023 0.149139 9.80023 0.149139C9.4444 0.324139 6.44023 2.17331 5.32606 3.39831C4.8244 3.95831 4.12439 5.60914 4.69023 6.20997C5.2269 6.78164 7.00023 6.11081 7.4844 5.62664C8.68606 4.42497 10.5236 1.44414 10.6927 1.08247ZM0.816895 9.29581C2.19939 8.38581 1.66856 7.30664 2.70106 6.58914C3.24356 6.20997 3.99606 6.22747 4.49773 6.75831C4.86523 7.14914 4.9644 8.25747 4.4044 8.77664C3.48856 9.62247 2.07106 9.68081 0.816895 9.29581Z',
		} )
	),
	bg_gradient: el(
		'svg',
		{ width: 12, height: 12, viewBox: '0 0 10 10', className: 'uagb-editor-icons' },
		el( 'path', {
			fill: spectraDarkColor,
			fillRule: 'evenodd',
			clipRule: 'evenodd',
			d:
				'M1.11111 1.11111V8.88889H8.88889V1.11111H1.11111ZM0.555556 0C0.248731 0 0 0.248731 0 0.555556V9.44444C0 9.75127 0.248731 10 0.555556 10H9.44444C9.75127 10 10 9.75127 10 9.44444V0.555556C10 0.248731 9.75127 0 9.44444 0H0.555556Z',
		} ),
		el( 'path', {
			fill: spectraDarkColor,
			d: 'M1.66667 1.66667H7.77778L1.66667 7.77778V1.66667Z',
		} )
	),
	bg_image: el(
		'svg',
		{ width: 14, height: 14, viewBox: '0 0 14 14', className: 'uagb-editor-icons' },
		el( 'path', {
			fill: spectraDarkColor,
			d:
				'M1.5752 0.699951H12.4252C12.9082 0.699951 13.3002 1.09195 13.3002 1.57495V12.425C13.3002 12.908 12.9082 13.3 12.4252 13.3H1.5752C1.0922 13.3 0.700195 12.908 0.700195 12.425V1.57495C0.700195 1.09195 1.0922 0.699951 1.5752 0.699951ZM11.9002 11.9V2.09995H2.1002V11.9H11.9002ZM7.0002 4.19995C7.0002 3.42995 6.3702 2.79995 5.6002 2.79995C4.8302 2.79995 4.2002 3.42995 4.2002 4.19995C4.2002 4.96995 4.8302 5.59995 5.6002 5.59995C6.3702 5.59995 7.0002 4.96995 7.0002 4.19995ZM9.1002 7.69995C9.1002 7.69995 9.1002 3.49995 11.2002 3.49995V10.5C11.2002 10.885 10.8852 11.2 10.5002 11.2H3.5002C3.1152 11.2 2.8002 10.885 2.8002 10.5V5.59995C4.2002 5.59995 4.9002 8.39995 4.9002 8.39995C4.9002 8.39995 5.6002 6.29995 7.0002 6.29995C8.4002 6.29995 9.1002 7.69995 9.1002 7.69995Z',
		} )
	),
	bg_video: el(
		'svg',
		{ width: 14, height: 8, viewBox: '0 0 14 8', className: 'uagb-editor-icons' },
		el( 'path', {
			fill: spectraDarkColor,
			d:
				'M8.4002 6.1V1.9C8.4002 1.13 7.7702 0.5 7.0002 0.5H2.1002C1.3302 0.5 0.700195 1.13 0.700195 1.9V6.1C0.700195 6.87 1.3302 7.5 2.1002 7.5H7.0002C7.7702 7.5 8.4002 6.87 8.4002 6.1ZM9.1002 4.35L13.3002 7.5V0.5L9.1002 3.65V4.35Z',
		} )
	),
	dynamic_content: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'red', className: 'uagb-editor-icons' },
		el( 'path', {
			stoke: spectraDarkColor,
			strokeWidth: 1.4,
			d:
				'M4 7V17C4 19.2091 7.58172 21 12 21C16.4183 21 20 19.2091 20 17V7M4 7C4 9.20914 7.58172 11 12 11C16.4183 11 20 9.20914 20 7M4 7C4 4.79086 7.58172 3 12 3C16.4183 3 20 4.79086 20 7M20 12C20 14.2091 16.4183 16 12 16C7.58172 16 4 14.2091 4 12',
		} )
	),
	separator: el(
		'svg',
		{ width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none' , className: 'uagb-editor-icons' },
		el( 'path', {
			stokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
			fill: noColor,
			stroke: iconColor,
			d: 'M4 12H20M14 17L12 19L10 17M14 7L12 5L10 7',
        } ),
	),	
	icon: el(
		'svg',
		{ width: 24, height: 25, viewBox: '0 0 24 25', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M21 12.6987C21 17.6693 16.9706 21.6987 12 21.6987C7.02944 21.6987 3 17.6693 3 12.6987C3 7.72817 7.02944 3.69873 12 3.69873C16.9706 3.69873 21 7.72817 21 12.6987Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d:
				'M11.3217 9.12032C11.5991 8.5582 12.4007 8.5582 12.6781 9.12032L13.3798 10.5422C13.49 10.7654 13.7029 10.9201 13.9493 10.9559L15.5184 11.1839C16.1387 11.2741 16.3864 12.0364 15.9375 12.4739L14.8021 13.5807C14.6239 13.7544 14.5425 14.0048 14.5846 14.2501L14.8526 15.8129C14.9586 16.4307 14.3101 16.9019 13.7553 16.6102L12.3518 15.8723C12.1315 15.7565 11.8683 15.7565 11.6479 15.8723L10.2445 16.6102C9.68964 16.9019 9.04116 16.4307 9.14712 15.8129L9.41516 14.2501C9.45724 14.0048 9.3759 13.7544 9.19765 13.5807L8.06223 12.4739C7.61335 12.0364 7.86105 11.2741 8.48138 11.1839L10.0505 10.9559C10.2968 10.9201 10.5098 10.7654 10.6199 10.5422L11.3217 9.12032Z',
			fill: noColor,
			stroke: iconColor,
			strokeWidth: 1.4,
		} )
	),
	pencilIcon: el(
		'svg',
		{ width: 20, height: 20, viewBox: '0 0 20 20', fill: 'none', className: 'uagb-editor-icons' },
		el( 'path', {
			d:
				'M10 18.5H18M12.5 4L16 7M3.5 13L13.3595 2.79619C14.4211 1.73461 16.1422 1.7346 17.2038 2.79619C18.2654 3.85777 18.2654 5.57894 17.2038 6.64052L7 16.5L2 18L3.5 13Z',
			stroke: '#94A3B8',
			strokeWidth: 1.8,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	),
	trashIcon: el(
		'svg',
		{ width: 20, height: 20, xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 448 512', className: 'trash-font' },
		el( 'path', {
			d: 'M32 464a48 48 0 0 0 48 48h288a48 48 0 0 0 48-48V128H32zm272-256a16 16 0 0 1 32 0v224a16 16 0 0 1-32 0zm-96 0a16 16 0 0 1 32 0v224a16 16 0 0 1-32 0zm-96 0a16 16 0 0 1 32 0v224a16 16 0 0 1-32 0zM432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.72 23.72 0 0 0-21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0-16-16z',
		} )
	),
};

export const renderLegacyBlockEditorIcon = ( blockName ) => (
	<>
		{ UAGB_Block_Icons[ blockName ] }
		<div className="spectra__legacy-icon--block-inserter-label">Legacy</div>
	</>
);

export default UAGB_Block_Icons;