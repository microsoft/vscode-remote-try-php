window.onload = () => {
	function addWrapWithOrderTable() {
		var container      = document.querySelector( '#posts-filter' );
		var containerWidth = container.clientWidth;
		var ttsOrderTable  = container.querySelector( '.tts-order-table-container' );
		var first          = ! ttsOrderTable;
		if (first) {
			var table = container.querySelector( 'table' );
			var div   = document.createElement( 'div' );
			div.classList.add( 'tts-order-table-container' );
			container.insertBefore( div, table );
			div.appendChild( table );
			div.style.width = containerWidth + 'px';
		} else {
			ttsOrderTable.style.width = containerWidth + 'px';
		}
	}

	addWrapWithOrderTable();

	window.addEventListener( 'resize', addWrapWithOrderTable );
}
