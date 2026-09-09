<?php
/**
 * Server render for the goncalo/card block.
 *
 * Available vars: $attributes, $content (inner blocks HTML), $block.
 * Wraps the semantic inner HTML with the draggable-card chrome.
 *
 * `.card__content` carries `is-layout-flow` so WordPress's own layout rules
 * apply to the blocks inside: `.is-layout-flow > .alignleft/.alignright` float,
 * `.aligncenter` centres, and blockGap supplies the vertical rhythm. Without
 * that class the native alignment controls render but do nothing.
 *
 * @package goncalo-portfolio
 */

$label        = isset( $attributes['label'] ) ? $attributes['label'] : '';
$bg_color     = isset( $attributes['bgColor'] ) ? $attributes['bgColor'] : '#75D2FE';
$header_color = ! empty( $attributes['headerColor'] ) ? $attributes['headerColor'] : $bg_color;
$width        = isset( $attributes['width'] ) ? (int) $attributes['width'] : 360;
$height       = isset( $attributes['height'] ) ? (int) $attributes['height'] : 480;
$tints_body   = $header_color !== $bg_color;

$anchor   = ! empty( $attributes['anchor'] ) ? $attributes['anchor'] : '';
$style    = sprintf(
	'background-color:%s;width:%dpx;height:%dpx;',
	$bg_color,
	$width,
	$height
);
$body_bg  = $tints_body ? sprintf( 'background-color:%s;', $header_color ) : '';

$drag_svg = '<svg width="16" height="16" viewBox="0 0 128 128" aria-hidden="true" style="transform:rotate(270deg)"><path d="M52 30A6 6 0 1 1 46 24 6 6 0 0 1 52 30Zm30 6a6 6 0 1 0-6-6A6 6 0 0 0 82 36ZM46 58a6 6 0 1 0 6 6A6 6 0 0 0 46 58Zm36 0a6 6 0 1 0 6 6A6 6 0 0 0 82 58ZM46 92a6 6 0 1 0 6 6A6 6 0 0 0 46 92Zm36 0a6 6 0 1 0 6 6A6 6 0 0 0 82 92Z" fill="#606060"/></svg>';

$layers_svg = '<svg width="16" height="16" viewBox="0 0 128 128" aria-hidden="true"><path d="M115.455 86A4 4 0 0 1 114 91.455l-48 28a4 4 0 0 1-4.03 0l-48-28A4 4 0 0 1 18 84.545l46 26.825 46-26.825A4 4 0 0 1 115.455 86ZM110 60.545l-46 26.825L18 60.545A4 4 0 0 0 14 67.455l48 28a4 4 0 0 0 4.03 0l48-28A4 4 0 1 0 110 60.545ZM12 40a4 4 0 0 1 2-3.455l48-28a4 4 0 0 1 4.03 0l48 28a4 4 0 0 1 0 6.91l-48 28a4 4 0 0 1-4.03 0l-48-28A4 4 0 0 1 12 40Zm11.94 0L64 63.37 104.06 40 64 16.63Z" fill="#101010"/></svg>';

$wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'card',
		'style' => $style,
	)
);
?>
<article <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	data-pf-card
	data-bg="<?php echo esc_attr( $header_color ); ?>"
	data-label="<?php echo esc_attr( $label ); ?>"
	data-w="<?php echo esc_attr( $width ); ?>"
	data-h="<?php echo esc_attr( $height ); ?>"
	<?php echo $anchor ? 'id="' . esc_attr( $anchor ) . '"' : ''; ?>>

	<button type="button" class="card__drag" data-pf-drag aria-label="<?php echo esc_attr( sprintf( /* translators: card label */ __( 'Move %s card', 'goncalo-portfolio' ), $label ) ); ?>">
		<?php echo $drag_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</button>

	<div class="card__scroll">
		<div class="card__content is-layout-flow" style="<?php echo esc_attr( $body_bg ); ?>">
			<div class="card__topbar" style="<?php echo esc_attr( sprintf( 'background-color:%s;', $header_color ) ); ?>">
				<span class="card__label"><?php echo esc_html( $label ); ?></span>
				<span class="card__actions">
					<button type="button" class="card__back" data-pf-back aria-label="<?php echo esc_attr( sprintf( /* translators: card label */ __( 'Send %s card to back', 'goncalo-portfolio' ), $label ) ); ?>">
						<?php echo $layers_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</span>
			</div>

			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — inner blocks are already sanitised by WP ?>
		</div>
	</div>

	<span class="card__resize" data-pf-resize role="separator" aria-label="<?php echo esc_attr( sprintf( /* translators: card label */ __( 'Resize %s card', 'goncalo-portfolio' ), $label ) ); ?>"></span>
</article>
