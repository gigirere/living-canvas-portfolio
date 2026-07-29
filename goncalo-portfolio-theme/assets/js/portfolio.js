/**
 * Portfolio card canvas — progressive enhancement.
 *
 * Cards are server-rendered semantic HTML that stacks cleanly on its own. This
 * script turns that stack into a draggable canvas on EVERY screen size (mobile
 * mirrors desktop) with:
 *   - drag to move, corner handle to resize
 *   - layers icon to send a card to back, header swatch to bring it to front
 *   - a single show/hide toggle for all cards (reveals the "thinking" notes)
 *   - the GG wordmark: links home, and resets the canvas when already on it
 *
 * Desktop geometry mirrors the design: cards are concentric (centred on both
 * axes) at their authored size. Mobile: full-bleed width, staggered tops.
 */
( function () {
	'use strict';

	var MOBILE_MAX = 1023;

	function init() {
		var stage = document.querySelector( '.pf-stage' );
		var controls = document.querySelector( '[data-pf-controls]' );

		var cards = stage
			? Array.prototype.slice.call( stage.querySelectorAll( '[data-pf-card]' ) )
			: [];

		// No canvas on this page (normal page/post): hide the canvas-only
		// controls and leave GG as a plain link home.
		if ( ! stage || ! cards.length ) {
			if ( controls ) {
				var sw = controls.querySelector( '[data-pf-swatches]' );
				var tg = controls.querySelector( '[data-pf-toggle]' );
				if ( sw ) { sw.style.display = 'none'; }
				if ( tg ) { tg.style.display = 'none'; }
			}
			return;
		}

		var swatchWrap = controls && controls.querySelector( '[data-pf-swatches]' );
		var toggleBtn = controls && controls.querySelector( '[data-pf-toggle]' );
		var resetBtn = controls && controls.querySelector( '[data-pf-reset]' );

		var hidden = false;

		// Per-card base state = the reset target, captured from DOM order and
		// the authored inline size.
		var state = cards.map( function ( card, i ) {
			return {
				card: card,
				index: i,
				baseZ: ( i + 1 ) * 10,
				z: ( i + 1 ) * 10,
				baseW: parseInt( card.getAttribute( 'data-w' ), 10 ) || card.offsetWidth,
				baseH: parseInt( card.getAttribute( 'data-h' ), 10 ) || card.offsetHeight,
				// Set once the visitor drags or resizes this card, so a window
				// resize no longer snaps it back to its default position.
				moved: false,
			};
		} );

		function isMobile() {
			return window.innerWidth <= MOBILE_MAX;
		}

		function zValues() {
			return state.map( function ( s ) { return s.z; } );
		}

		function findState( card ) {
			for ( var i = 0; i < state.length; i++ ) {
				if ( state[ i ].card === card ) { return state[ i ]; }
			}
			return null;
		}

		function bringToFront( card ) {
			var s = findState( card );
			if ( ! s ) { return; }
			s.z = Math.max.apply( null, zValues() ) + 1;
			card.style.zIndex = s.z;
		}

		function sendToBack( card ) {
			var s = findState( card );
			if ( ! s ) { return; }
			s.z = Math.min.apply( null, zValues() ) - 1;
			card.style.zIndex = s.z;
		}

		/**
		 * Place a card at its default position/size for the current breakpoint.
		 * Desktop: concentric, authored size. Mobile: full-bleed, staggered.
		 */
		function placeCard( s ) {
			var card = s.card;
			var stageW = stage.clientWidth;

			if ( isMobile() ) {
				var gutter = 11;
				var w = Math.min( s.baseW, stageW - gutter * 2 );
				var h = Math.min( s.baseH, Math.round( window.innerHeight * 0.72 ) );
				card.style.width = w + 'px';
				card.style.height = h + 'px';
				card.style.left = Math.round( ( stageW - w ) / 2 ) + 'px';
				card.style.top = 84 + s.index * 64 + 'px';
			} else {
				card.style.width = s.baseW + 'px';
				card.style.height = s.baseH + 'px';
				card.style.left = Math.round( ( stageW - s.baseW ) / 2 ) + 'px';
				card.style.top =
					Math.max( 0, Math.round( ( stage.clientHeight - s.baseH ) / 2 ) ) + 'px';
			}
			card.style.zIndex = s.z;
		}

		function clearInline() {
			cards.forEach( function ( card ) {
				card.style.left = '';
				card.style.top = '';
				card.style.width = '';
				card.style.height = '';
				card.style.zIndex = '';
			} );
		}

		// The canvas is active at every size; only a no-JS visit stays stacked.
		// `force` re-places every card (used by Reset); otherwise cards the
		// visitor has dragged or resized keep their position.
		function layout( force ) {
			stage.classList.add( 'is-canvas' );
			state.forEach( function ( s ) {
				if ( force || ! s.moved ) {
					placeCard( s );
				}
			} );
		}

		// ---- swatches: one per card, coloured by the card -------------------
		function buildSwatches() {
			if ( ! swatchWrap ) { return; }
			swatchWrap.innerHTML = '';
			// Front-most card first, mirroring the design's swatch order.
			state.slice().reverse().forEach( function ( s ) {
				var btn = document.createElement( 'button' );
				btn.type = 'button';
				btn.className = 'pf-swatch';
				btn.style.backgroundColor = s.card.getAttribute( 'data-bg' ) || '#ccc';
				var label = s.card.getAttribute( 'data-label' ) || '';
				btn.title = label ? 'Bring "' + label + '" to front' : 'Bring card to front';
				btn.setAttribute( 'aria-label', btn.title );
				btn.addEventListener( 'click', function () {
					// Bringing a card forward while hidden also reveals them.
					if ( hidden ) { setHidden( false ); }
					bringToFront( s.card );
				} );
				swatchWrap.appendChild( btn );
			} );
		}

		// ---- show / hide all cards -----------------------------------------
		function setHidden( next ) {
			hidden = next;
			stage.classList.toggle( 'cards-hidden', hidden );
			if ( controls ) { controls.classList.toggle( 'cards-hidden', hidden ); }
			if ( toggleBtn ) {
				var label = hidden ? 'Show cards' : 'Hide cards';
				toggleBtn.setAttribute( 'aria-pressed', hidden ? 'true' : 'false' );
				toggleBtn.setAttribute( 'aria-label', label );
				toggleBtn.setAttribute( 'title', label );
			}
		}
		if ( toggleBtn ) {
			toggleBtn.addEventListener( 'click', function () {
				setHidden( ! hidden );
			} );
		}

		// ---- reset ----------------------------------------------------------
		function reset() {
			state.forEach( function ( s ) {
				s.z = s.baseZ;
				s.moved = false;
			} );
			setHidden( false );
			layout( true );
		}
		if ( resetBtn ) {
			resetBtn.addEventListener( 'click', function ( e ) {
				// On the canvas GG resets instead of navigating home.
				e.preventDefault();
				reset();
			} );
		}

		// ---- per-card controls: drag, send-to-back, resize -----------------
		cards.forEach( function ( card ) {
			var handle = card.querySelector( '[data-pf-drag]' );
			var backBtn = card.querySelector( '[data-pf-back]' );
			var resizeHandle = card.querySelector( '[data-pf-resize]' );

			if ( backBtn ) {
				backBtn.addEventListener( 'click', function () {
					sendToBack( card );
				} );
			}

			if ( handle ) {
				var drag = null;
				handle.addEventListener( 'pointerdown', function ( e ) {
					e.preventDefault();
					bringToFront( card );
					drag = {
						px: e.clientX,
						py: e.clientY,
						left: parseInt( card.style.left, 10 ) || 0,
						top: parseInt( card.style.top, 10 ) || 0,
					};
					handle.setPointerCapture( e.pointerId );
				} );
				handle.addEventListener( 'pointermove', function ( e ) {
					if ( ! drag ) { return; }
					var s = findState( card );
					if ( s ) { s.moved = true; }
					card.style.left = drag.left + ( e.clientX - drag.px ) + 'px';
					card.style.top = drag.top + ( e.clientY - drag.py ) + 'px';
				} );
				var endDrag = function ( e ) {
					if ( ! drag ) { return; }
					drag = null;
					try { handle.releasePointerCapture( e.pointerId ); } catch ( err ) {}
				};
				handle.addEventListener( 'pointerup', endDrag );
				handle.addEventListener( 'pointercancel', endDrag );
			}

			if ( resizeHandle ) {
				var rs = null;
				resizeHandle.addEventListener( 'pointerdown', function ( e ) {
					e.preventDefault();
					e.stopPropagation();
					bringToFront( card );
					rs = { px: e.clientX, py: e.clientY, w: card.offsetWidth, h: card.offsetHeight };
					resizeHandle.setPointerCapture( e.pointerId );
				} );
				resizeHandle.addEventListener( 'pointermove', function ( e ) {
					if ( ! rs ) { return; }
					var s = findState( card );
					if ( s ) { s.moved = true; }
					card.style.width = Math.max( 180, rs.w + ( e.clientX - rs.px ) ) + 'px';
					card.style.height = Math.max( 120, rs.h + ( e.clientY - rs.py ) ) + 'px';
				} );
				var endResize = function ( e ) {
					if ( ! rs ) { return; }
					rs = null;
					try { resizeHandle.releasePointerCapture( e.pointerId ); } catch ( err ) {}
				};
				resizeHandle.addEventListener( 'pointerup', endResize );
				resizeHandle.addEventListener( 'pointercancel', endResize );
			}
		} );

		// ---- keep the default layout centred as the window changes ---------
		var raf;
		window.addEventListener( 'resize', function () {
			window.cancelAnimationFrame( raf );
			// Re-places untouched cards only; dragged/resized ones stay put.
			raf = window.requestAnimationFrame( function () { layout(); } );
		} );

		buildSwatches();
		layout();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
