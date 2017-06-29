<?php
/**
 * Left sidebar check.
 *
 * @package understrap
 */

?>

<?php
$sidebar_pos = get_theme_mod( 'understrap_sidebar_position' );
?>

<?php if ( 'left' === $sidebar_pos || 'both' === $sidebar_pos ) : ?>
	<?php get_sidebar( 'left' ); ?>
<?php endif; ?>

<?php {
	//echo '<div class="col-md-12 content-area" id="primary">';
	
	$html = '';
	if ( 'right' === $sidebar_pos || 'left' === $sidebar_pos ) {
		$html = '<div class="';
		if ( is_active_sidebar( 'right-sidebar' ) || is_active_sidebar( 'left-sidebar' ) ) {
			$html .= 'col-lg-8 content-area" id="primary">';
		} else {
			$html .= 'col-lg-12 content-area" id="primary">';
		}
		echo $html; // WPCS: XSS OK.
	} elseif ( is_active_sidebar( 'right-sidebar' ) && is_active_sidebar( 'left-sidebar' ) ) {
		$html = '<div class="';
		if ( 'both' === $sidebar_pos ) {
			$html .= 'col-lg-6 content-area" id="primary">';
		} else {
			$html .= 'col-lg-12 content-area" id="primary">';
		}
		echo $html; // WPCS: XSS OK.
	} else {
	    echo '<div class="col-lg-12 content-area" id="primary">';
	}
	
}
