<?php
/**
 * Shortcode Markup
 *
 * TMPL - Single Demo Preview
 * TMPL - No more demos
 * TMPL - Filters
 * TMPL - List
 *
 * @package Astra Sites
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<script type="text/template" id="tmpl-ast-image-skeleton">
	<div class="ast-image__skeleton-wrap">
		<div class="ast-image__skeleton-inner-wrap">
			<div class="ast-image__skeleton">
			</div>
			<div class="ast-image__preview-skeleton">
			</div>
		</div>
	</div>
	<div class="ast-image__loader-wrap">
		<div class="ast-image__loader-1"></div>
		<div class="ast-image__loader-2"></div>
		<div class="ast-image__loader-3"></div>
	</div>
</script>

<script type="text/template" id="tmpl-ast-image-list">

	<# var count = 0; #>
		<# for ( key in data ) { count++; #>
			<# var is_imported = _.includes( astraImages.saved_images, data[key]['id'] ); #>
			<# var imported_class = ( is_imported ) ? 'imported' : ''; #>
			<div class="ast-image__list-wrap loading" data-id="{{data[key]['id']}}" data-url="{{data[key]['pageURL']}}">
				<div class="ast-image__list-inner-wrap {{imported_class}}">
					<div class="ast-image__list-img-wrap">
						<img src="{{data[key]['webformatURL']}}" alt="{{data[key]['tags']}}" />
						<div class="ast-image__list-img-overlay" data-img-url={{data[key]['largeImageURL']}} data-img-id={{data[key]['id']}}>
							<span>{{data[key]['tags']}}</span>
							<# if ( '' === imported_class ) { #>
							<span class="ast-image__download-icon dashicons-arrow-down-alt dashicons" data-import-status={{is_imported}}></span>
							<# } #>
						</div>
					</div>
				</div>
			</div>
		<# } #>
		<# if ( 0 === count ) { #>
			<div class="astra-sites-no-sites">
				<h3><?php esc_html_e( 'Sorry No Results Found.', 'astra-sites' ); ?></h3>
			</div>
		<# } #>
</script>

<script type="text/template" id="tmpl-ast-image-filters">
	<div class="ast-image__filter-wrap">
		<ul class="ast-image__filter">
			<li class="ast-image__filter-category">
				<select>
					<# for ( key in astraImages.pixabay_category ) { #>
					<option value="{{key}}">{{astraImages.pixabay_category[key]}}</option>
					<# } #>
				</select>
			</li>
			<li class="ast-image__filter-orientation">
				<select>
					<# for ( key in astraImages.pixabay_orientation ) { #>
					<option value="{{key}}">{{astraImages.pixabay_orientation[key]}}</option>
					<# } #>
				</select>
			</li>
			<li class="ast-image__filter-order">
				<select>
					<# for ( key in astraImages.pixabay_order ) { #>
					<option value="{{key}}">{{astraImages.pixabay_order[key]}}</option>
					<# } #>
				</select>
			</li>
			<li class="ast-image__filter-safesearch">
				<label><input type="checkbox" checked value="1" /><?php esc_html_e( 'SafeSearch', 'astra-sites' ); ?></label>
			</li>
		</ul>
	</div>
	<div class="ast-powered-by-pixabay-wrap"><span><?php esc_html_e( 'Powered by', 'astra-sites' ); ?></span><img src="<?php echo esc_url( ASTRA_SITES_URI . 'inc/assets/images/pixabay-logo.png' ); ?>">
	</div>
</script>

<script type="text/template" id="tmpl-ast-image-no-result">
	<div class="astra-sites-no-sites">
		<h3><?php esc_html_e( 'Sorry No Results Found.', 'astra-sites' ); ?></h3>
		<p class="description">
			<?php
			/* translators: %1$s External Link */
			printf( esc_html__( 'Don\'t see a template you would like to import?<br><a target="_blank" href="%1$s">Make a Template Suggestion!</a>', 'astra-sites' ), esc_url( 'https://wpastra.com/sites-suggestions/?utm_source=demo-import-panel&utm_campaign=astra-sites&utm_medium=suggestions' ) );
			?>
		</p>
	</div>
</script>

<script type="text/template" id="tmpl-ast-image-single">
	<# var is_imported = _.includes( astraImages.saved_images, data.id.toString() ); #>
	<# var disable_class = ( is_imported ) ? 'disabled': ''; #>
	<# var image_type = data.largeImageURL.substring( data.largeImageURL.lastIndexOf( "." ) + 1 ); #>
	<div class="single-site-wrap">
		<div class="single-site">
			<div class="single-site-preview-wrap">
				<div class="single-site-preview">
					<img class="theme-screenshot" src="{{data.largeImageURL}}">
				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="tmpl-ast-image-go-back">
	<div class="ast-image__go-back">
		<i class="ast-icon-chevron-left"></i>
		<span class="ast-image__go-back-text"><?php esc_html_e( 'Back to Images', 'astra-sites' ); ?></span>
	</div>
</script>

<script type="text/template" id="tmpl-ast-image-save">
	<# var is_imported = _.includes( astraImages.saved_images, data.id.toString() ); #>
	<# var disable_class = ( is_imported ) ? 'disabled': ''; #>
	<div class="ast-image__save-wrap">
		<button type="button" class="ast-image__save button media-button button-primary button-large media-button-select {{disable_class}}" data-import-status={{is_imported}}>
			<# if ( is_imported ) { #>
				<?php esc_html_e( 'Already Saved', 'astra-sites' ); ?>
			<# } else { #>
				<?php esc_html_e( 'Save & Insert', 'astra-sites' ); ?>
			<# } #>
		</button>
	</div>
</script>

<?php
