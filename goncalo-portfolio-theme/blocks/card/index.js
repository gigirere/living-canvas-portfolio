/**
 * "Card" block editor — no build step, uses the wp.* globals registered as
 * script dependencies. Content is authored with core blocks (heading,
 * paragraph, image) via InnerBlocks, so it saves as semantic HTML for SEO.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { InnerBlocks, InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, TextControl, ToggleControl, RangeControl, ColorPalette, BaseControl } =
		wp.components;
	const { createElement: el, Fragment } = wp.element;
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

	const ALLOWED = [
		'core/heading',
		'core/paragraph',
		'core/image',
		'core/list',
		'core/separator',
		'core/buttons',
	];

	const TEMPLATE = [
		[ 'core/heading', { level: 2, placeholder: __( 'Card title', 'goncalo-portfolio' ) } ],
		[ 'core/paragraph', { placeholder: __( 'Write text…', 'goncalo-portfolio' ) } ],
	];

	registerBlockType( 'goncalo/card', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const { label, bgColor, headerColor, width, height } = attributes;
			const headerBg = headerColor || bgColor;

			const blockProps = useBlockProps( {
				className: 'gp-card-edit',
				style: {
					backgroundColor: bgColor,
					width: width + 'px',
					minHeight: height + 'px',
				},
			} );

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
					el(
						'div',
						{ className: 'gp-card-edit__content' },
						el( InnerBlocks, { allowedBlocks: ALLOWED, template: TEMPLATE } )
					)
				)
			);
		},

		save: function () {
			// Dynamic block: render.php wraps this saved inner HTML with the
			// card chrome. Returning InnerBlocks.Content keeps the headings,
			// paragraphs and images as real HTML in post_content (SEO-friendly).
			return el( InnerBlocks.Content );
		},
	} );
} )( window.wp );
