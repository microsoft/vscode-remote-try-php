import React, { useCallback, useEffect, useRef, useState } from 'react';
import ReactCanvasConfetti from 'react-canvas-confetti';

const randomInRange = ( min, max ) => {
	return Math.random() * ( max - min ) + min;
};

const canvasStyles = {
	position: 'fixed',
	pointerEvents: 'none',
	width: '100%',
	height: '100%',
	top: 0,
	left: 0,
};

const getAnimationSettings = ( originXA, originXB ) => {
	return {
		startVelocity: 30,
		spread: 360,
		ticks: 60,
		zIndex: 0,
		particleCount: 150,
		origin: {
			x: randomInRange( originXA, originXB ),
			y: Math.random() - 0.2,
		},
	};
};

export default function Confetti() {
	const refAnimationInstance = useRef( null );
	const [ intervalId, setIntervalId ] = useState();

	const getInstance = useCallback( ( instance ) => {
		refAnimationInstance.current = instance;
	}, [] );

	const nextTickAnimation = useCallback( () => {
		if ( refAnimationInstance.current ) {
			refAnimationInstance.current( getAnimationSettings( 0.1, 0.3 ) );
			refAnimationInstance.current( getAnimationSettings( 0.7, 0.9 ) );
		}
	}, [] );

	const startAnimation = useCallback( () => {
		if ( ! intervalId ) {
			setIntervalId( setInterval( nextTickAnimation, 400 ) );
			// Stop animation after 5 seconds.
			setTimeout( () => {
				pauseAnimation();
			}, 5000 );
		}
	}, [ intervalId, nextTickAnimation ] );

	const pauseAnimation = useCallback( () => {
		clearInterval( intervalId );
		setIntervalId( null );
	}, [ intervalId ] );

	useEffect( () => {
		startAnimation();
	}, [] );

	useEffect( () => {
		return () => {
			clearInterval( intervalId );
		};
	}, [ intervalId ] );

	return (
		<ReactCanvasConfetti
			refConfetti={ getInstance }
			style={ canvasStyles }
			useWorker
			resize
		/>
	);
}
