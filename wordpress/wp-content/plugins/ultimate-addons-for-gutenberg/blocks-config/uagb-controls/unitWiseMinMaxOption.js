import { useDeviceType } from '@Controls/getPreviewType';
export const limitMax = ( unitVal, props, isResponsiveMinMax ) => {
	let max = 0;
	if ( isResponsiveMinMax ) {
		const deviceType = useDeviceType(); // eslint-disable-line react-hooks/rules-of-hooks
		const responsiveUnit = unitVal ? unitVal : props.data[ deviceType.toLowerCase() ].value;
		max =
			responsiveUnit && props.data[ deviceType.toLowerCase() ]?.max
				? props.data[ deviceType.toLowerCase() ].max
				: props.max;
	} else {
		max = unitVal && props.limitMax ? props.limitMax[ unitVal ] : props.max;
	}

	return max;
};

export const limitMin = ( unitVal, props, isResponsiveMinMax ) => {
	let min = 0;
	if ( isResponsiveMinMax ) {
		const deviceType = useDeviceType(); // eslint-disable-line react-hooks/rules-of-hooks
		const responsiveUnit = unitVal ? unitVal : props.data[ deviceType.toLowerCase() ].value;
		min =
			responsiveUnit && props.data[ deviceType.toLowerCase() ]?.min
				? props.data[ deviceType.toLowerCase() ].min
				: props.min;
	} else {
		min = unitVal && props.limitMin ? props.limitMin[ unitVal ] : props.min;
	}
	return min;
};
