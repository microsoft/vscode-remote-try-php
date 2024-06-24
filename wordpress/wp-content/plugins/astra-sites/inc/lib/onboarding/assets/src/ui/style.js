import styled from 'styled-components';

export const Row = styled.div`
	display: ${ ( props ) => ( props.hidden ? 'none' : 'flex' ) };
	justify-content: space-between;
	${ ( props ) =>
		props.maxHeight &&
		`
		max-height: ${ props.maxHeight }px;
	` }
`;

export const Col = styled.div`
	width: 50%;
	${ ( props ) =>
		props.width &&
		`
        width: ${ props.width };
    ` }
`;

export const Button = styled.button`
	color: var( --st-background-primary );
	border: none;
	margin-top: 1em;
	display: flex;
	flex-direction: row;
	justify-content: center;
	align-items: center;
	position: relative;
	background: var( --st-color-accent );
	border-radius: var( --st-border-radius-6 );
	margin: 0;
	cursor: pointer;
	line-height: 1;
	font-size: 15px;
	font-weight: var( --st-font-weight-bold );
	padding: 15px 28px;
	transition: background 200ms ease-in-out;

	&:hover {
		background: var( --st-color-accent-hover );
	}

	&:focus {
		outline: none;
		background: var( --st-color-accent-hover );
	}

	svg {
		fill: #fff;
	}

	${ ( props ) =>
		props.before &&
		`
        svg {
            margin: 0 12px 0 0;
        }
    ` }

	${ ( props ) =>
		props.after &&
		`
        svg {
			margin: 0 0 0 12px;
        }
    ` }

    ${ ( props ) =>
		props.ml1 &&
		`
        margin-left: 1em;
    ` }

	${ ( props ) =>
		props.type &&
		'primary' === props.type &&
		`
		background: #2271b1;
    ` }

	${ ( props ) =>
		props.type &&
		'secondary' === props.type &&
		`
		background: #f6f7f7;
		color: #2563EB;
		border-color: #2271b1;
		&:hover {
			background: #f6f7f7;
		}
		&:focus {
			background: #f6f7f7;
		}
    ` }
`;

export const Progress = styled.progress`
	width: 100%;
`;

export const Link = styled.span`
	cursor: pointer;
	font-size: var(--st-font-size-xs);
	color: var(--st-color-body);

	svg {
		fill: #757575;
	}

	&: hover,
	&: focus {
		color: #2d4ad1;
		outline: none;

		svg {
			fill: #2d4ad1;
		}
	}

	${ ( props ) =>
		props.$before &&
		`
		svg {
			margin-right: 10px;
		}
	` }

	${ ( props ) =>
		props.$after &&
		`
		svg {
			margin-left: 10px;
		}
	` }
`;
