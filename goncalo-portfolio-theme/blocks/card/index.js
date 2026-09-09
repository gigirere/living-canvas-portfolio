/**
 * "Card" block editor — no build step, uses the wp.* globals registered as
 * script dependencies.
 *
 * The card is a container only: everything inside it is authored with native
 * core blocks via InnerBlocks, so it saves as semantic HTML for SEO and every
 * core block keeps its own toolbar, sidebar and behaviour. Nothing is
 * whitelisted — the single exception is the Card block itself, which cannot be
 * nested (the front-end canvas script positions every `[data-pf-card]` it
 * finds, so a card inside a card would break the layout).
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { InnerBlocks, InspectorControls, useBlockProps, useInnerBlocksProps } = wp.blockEditor;
	const { PanelBody, TextControl, RangeControl, ColorPalette, BaseControl } = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { useSelect } = wp.data;
	const { __ } = wp.i18n;

	const PALETTE = [
		{ name: 'Diary blue', color: '#75D2FE' },
		{ name: 'Diary blue (light)', color: '#86BFF8' },
		{ name: 'Projects green', color: '#A6B99F' },
		{ name: 'Projects green (light)', color: '#B4E091' },
		{ name: 'About yellow', color: '#F0AF00' },
		{ name: 'About orange', color: '#FB9D6E' },
		{ name: 'Paper', color: '#DED8CC' },
	];

	// Blocks that must never appear inside a card. Everything else — core and
	// third-party alike — is allowed, so paste, block transforms and the
	// Patterns tab all behave natively.
	const DENIED = [ 'goncalo/card' ];

	const TEMPLATE = [
		[ 'core/heading', { level: 2, placeholder: __( 'Card title', 'goncalo-portfolio' ) } ],
		[ 'core/paragraph', { placeholder: __( 'Write text…', 'goncalo-portfolio' ) } ],
	];

	/**
	 * Every registered block except the denied ones.
	 *
	 * Derived from the live registry rather than hard-coded, so blocks added by
	 * a future WordPress release or a plugin are available inside cards without
	 * touching this file. Blocks with their own `parent`/`ancestor` rules (for
	 * example core/list-item) stay correctly gated — WordPress checks those in
	 * addition to this list.
	 *
	 * @return {string[]} Allowed block names.
	 */
	function useAllowedBlocks() {
		return useSelect( function ( select ) {
			const blockTypes = select( 'core/blocks' ).getBlockTypes();

			return blockTypes
				.map( function ( blockType ) {
					return blockType.name;
				} )
				.filter( function ( name ) {
					return DENIED.indexOf( name ) === -1;
				} );
		}, [] );
	}

	registerBlockType( 'goncalo/card', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const { label, bgColor, headerColor, width, height } = attributes;
			const headerBg = headerColor || bgColor;
			const allowedBlocks = useAllowedBlocks();

			// The front end gives the card a fixed height and scrolls its
			// content (see .card__scroll in portfolio.css). Mirror that here so
			// authoring a tall gallery or video shows the same clipping the
			// visitor will get.
			const blockProps = useBlockProps( {
				className: 'gp-card-edit',
				style: {
					backgroundColor: bgColor,
					width: width + 'px',
					height: height + 'px',
				},
			} );

			// `is-layout-flow` is what makes WordPress's own layout rules apply
			// to the blocks inside — alignleft/alignright floats, aligncenter,
			// and the blockGap rhythm. Mirrors render.php.
			const innerBlocksProps = useInnerBlocksProps(
				{ className: 'gp-card-edit__content is-layout-flow' },
				{
					allowedBlocks: allowedBlocks,
					template: TEMPLATE,
					templateLock: false,
				}
			);

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Card settings', 'goncalo-portfolio' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Label (eyebrow)', 'goncalo-portfolio' ),
							help: __( 'Small tag at the top, e.g. "visual diary".', 'goncalo-portfolio' ),
							value: label,
							onChange: function ( v ) {
								setAttributes( { label: v } );
							},
						} ),
						el( RangeControl, {
							label: __( 'Width (px)', 'goncalo-portfolio' ),
							value: width,
							min: 220,
							max: 900,
							onChange: function ( v ) {
								setAttributes( { width: v } );
							},
						} ),
						el( RangeControl, {
							label: __( 'Height (px)', 'goncalo-portfolio' ),
							value: height,
							min: 200,
							max: 1000,
							help: __(
								'Content taller than this scrolls inside the card.',
								'goncalo-portfolio'
							),
							onChange: function ( v ) {
								setAttributes( { height: v } );
							},
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Colours', 'goncalo-portfolio' ), initialOpen: false },
						el(
							BaseControl,
							{ label: __( 'Background colour', 'goncalo-portfolio' ) },
							el( ColorPalette, {
								colors: PALETTE,
								value: bgColor,
								onChange: function ( v ) {
									setAttributes( { bgColor: v || '#75D2FE' } );
								},
							} )
						),
						el(
							BaseControl,
							{
								label: __( 'Header colour (optional)', 'goncalo-portfolio' ),
								help: __( 'Defaults to the background colour.', 'goncalo-portfolio' ),
							},
							el( ColorPalette, {
								colors: PALETTE,
								value: headerColor,
								onChange: function ( v ) {
									setAttributes( { headerColor: v || '' } );
								},
							} )
						)
					)
				),
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'gp-card-edit__topbar', style: { backgroundColor: headerBg } },
						el( 'span', { className: 'gp-card-edit__label' }, label || __( 'label', 'goncalo-portfolio' ) )
					),
					el( 'div', { className: 'gp-card-edit__scroll' }, el( 'div', innerBlocksProps ) )
				)
			);
		},

		save: function () {
			// Dynamic block: render.php wraps this saved inner HTML with the
			// card chrome. Returning InnerBlocks.Content keeps whatever core
			// blocks were used as real HTML in post_content (SEO-friendly).
			return el( InnerBlocks.Content );
		},
	} );
} )( window.wp );
