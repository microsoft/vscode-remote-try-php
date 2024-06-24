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
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$suggestion_link = astra_sites_get_suggestion_link();
?>

<script type="text/template" id="tmpl-ast-template-base-skeleton">
	<div class="dialog-widget dialog-lightbox-widget dialog-type-buttons dialog-type-lightbox" id="ast-sites-modal">
		<div class="dialog-widget-content dialog-lightbox-widget-content">
			<div class="astra-sites-content-wrap" data-page="1">
				<div class="ast-template-library-toolbar">
					<div class="elementor-template-library-filter-toolbar">
						<div class="elementor-template-library-order">
							<select class="elementor-template-library-order-input elementor-template-library-filter-select elementor-select2">
								<option value=""><?php esc_html_e( 'All', 'astra-sites' ); ?></option>
								<option value="free"><?php esc_html_e( 'Free', 'astra-sites' ); ?></option>
								<option value="agency"><?php esc_html_e( 'Premium', 'astra-sites' ); ?></option>
							</select>
						</div>
						<div class="astra-blocks-category-inner-wrap">
							<select id="elementor-template-library-filter" class="astra-blocks-category elementor-template-library-filter-select elementor-select2">
								<option value=""><?php esc_html_e( 'All', 'astra-sites' ); ?></option>
								<# for ( key in astraElementorSites.astra_block_categories ) { #>
								<option value="{{astraElementorSites.astra_block_categories[key].id}}">{{astraElementorSites.astra_block_categories[key].name}}</option>
								<# } #>
							</select>
						</div>
						<div class="astra-blocks-filter-inner-wrap"  id="elementor-template-block-color-filter" style="display: none;"></div>
					</div>
					<div class="ast-sites-template-library-filter-text-wrapper">
						<label for="elementor-template-library-filter-text" class="elementor-screen-only"><?php esc_html_e( 'Search...', 'astra-sites' ); ?></label>
						<input id="wp-filter-search-input" placeholder="<?php esc_attr_e( 'SEARCH', 'astra-sites' ); ?>" class="">
						<i class="eicon-search"></i>
					</div>
				</div>
				<?php
					// Check flexbox container, If inactive then activate it.
					$flexbox_container = get_option( 'elementor_experiment-container' );
					// Check if the value is 'inactive'.
				if ( 'inactive' === $flexbox_container ) { 
					?>
						<div class="ast-sites-container-notice-wrap">
							<div class="ast-sites-container-notice-content">
								<p><?php esc_html_e( "We've observed that the 'Flexbox Container' setting in your Elementor configuration is currently inactive. To ensure a seamless import, please active this option.", 'astra-sites' ); ?></p>
								<div class="ast-sites-container-notice-actions">
									<a href="<?php echo esc_url( home_url( '/wp-admin/admin.php?page=elementor#tab-experiments' ) ); ?>" class="ast-sites-container-notice-button" >
										<span><?php esc_html_e( 'Activate it!', 'astra-sites' ); ?></span>
									</a>
								</div>
							</div>
						</div>
					<?php 
				}
				?>
				<div id="ast-sites-floating-notice-wrap-id" class="ast-sites-floating-notice-wrap"><div class="ast-sites-floating-notice"></div></div>
				<?php
				$manual_sync = get_site_option( 'astra-sites-manual-sync-complete', 'no' );
				if ( 'yes' === $manual_sync ) {
					$batch_status = get_site_option( 'astra-sites-batch-is-complete', 'no' );
					if ( 'yes' === $batch_status ) {
						?>
						<div class="ast-sites-floating-notice-wrap refreshed-notice slide-in">
							<div class="ast-sites-floating-notice">
								<div class="astra-sites-sync-library-message success astra-sites-notice notice notice-success is-dismissible">
									<?php Astra_Sites::get_instance()->get_sync_complete_message( true ); ?> <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss', 'astra-sites' ); ?></span></button>
								</div>
							</div>
						</div>
						<?php
					}
				}
				?>
				<div class="dialog-message dialog-lightbox-message" data-type="pages">
					<div class="dialog-content dialog-lightbox-content theme-browser"></div>
					<div class="theme-preview"></div>
				</div>
				<div class="dialog-message dialog-lightbox-message-block" data-type="blocks">
					<div class="dialog-content dialog-lightbox-content-block theme-browser" data-block-page="1"></div>
					<div class="theme-preview-block"></div>
				</div>
				<div class="astra-loading-wrap"><div class="astra-loading-icon"></div></div>
			</div>
			<div class="dialog-buttons-wrapper dialog-lightbox-buttons-wrapper"></div>
		</div>
		<div class="dialog-background-lightbox"></div>
	</div>
</script>

<script type="text/template" id="tmpl-ast-template-block-color-filters">
	<select  class="astra-blocks-filter elementor-template-library-filter-select elementor-select2">
		<option value=""><?php esc_html_e( 'Filter by Color', 'astra-sites' ); ?></option>
		<# for ( key in data ) { #>
			<option value="{{data[key]}}">{{data[key]}}</option>
		<# } #>
		</select>
</script>

<script type="text/template" id="tmpl-ast-template-modal__header-back">
	<div class="dialog-lightbox-back"><span class="dialog-lightbox-back-text"><?php esc_html_e( 'Back to Pages', 'astra-sites' ); ?></span></div>
</script>

<script type="text/template" id="tmpl-ast-template-modal__header">
	<div class="dialog-header dialog-lightbox-header">
		<div class="ast-sites-modal__header">
			<div class="ast-sites-modal__header__logo-area">
				<?php
				if ( ! Astra_Sites_White_Label::get_instance()->is_white_labeled() ) {
					?>
				<div class="ast-sites-modal__header__logo">
					<span class="ast-sites-modal__header__logo__icon-wrapper"></span>
				</div>
					<?php
				} else {
					?>
				<div class="ast-sites-modal__header__logo">
					<span class="ast-sites-modal__header__logo__text-wrapper"><?php echo esc_html( Astra_Sites_White_Label::get_instance()->get_white_label_name() ); ?></span>
				</div>
					<?php
				}
				?>
				<div class="back-to-layout" title="<?php esc_attr_e( 'Back to Layout', 'astra-sites' ); ?>" data-step="1"><i class="ast-icon-chevron-left"></i></div>
			</div>
			<div class="elementor-templates-modal__header__menu-area astra-sites-step-1-wrap ast-sites-modal__options">
				<div class="elementor-template-library-header-menu">
					<div class="elementor-template-library-menu-item elementor-active" data-template-source="remote" data-template-type="pages"><span class="ast-icon-file"></span><?php esc_html_e( 'Pages', 'astra-sites' ); ?></div>
					<div class="elementor-template-library-menu-item" data-template-source="remote" data-template-type="blocks"><span class="ast-icon-layers"></span><?php esc_html_e( 'Blocks', 'astra-sites' ); ?></div>
				</div>
			</div>
			<div class="elementor-templates-modal__header__items-area">
				<div class="ast-sites-modal__header__close ast-sites-modal__header__close--normal ast-sites-modal__header__item">
					<i class="dashicons close dashicons-no-alt" aria-hidden="true" title="<?php esc_attr_e( 'Close', 'astra-sites' ); ?>"></i>
					<span class="elementor-screen-only"><?php esc_html_e( 'Close', 'astra-sites' ); ?></span>
				</div>
				<div class="astra-sites__sync-wrap">
					<div class="astra-sites-sync-library-button">
						<span class="ast-icon-refresh" aria-hidden="true" title="<?php esc_attr_e( 'Sync Library', 'astra-sites' ); ?>"></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="tmpl-astra-sites-list">

	<#
		var count = 0;
		for ( key in data ) {
			var page_data = data[ key ][ 'pages' ];
			var site_type = data[ key ][ 'astra-sites-type' ] || '';
			if ( 0 == Object.keys( page_data ).length ) {
				continue;
			}
			if ( undefined == site_type ) {
				continue;
			}
			if ( '' !== AstraElementorSitesAdmin.siteType ) {
				if ( 'free' == AstraElementorSitesAdmin.siteType && site_type != 'free' ) {
					continue;
				}

				if ( 'free' != AstraElementorSitesAdmin.siteType && site_type == 'free' ) {
					continue;
				}
			}
			var type_class = ' site-type-' + data[ key ]['astra-sites-type'];
			var site_title = data[ key ]['title'].slice( 0, 25 );
			if ( data[ key ]['title'].length > 25 ) {
				site_title += '...';
			}
			count++;
	#>
			<div class="theme astra-theme site-single publish page-builder-elementor {{type_class}}" data-site-id={{key}} data-template-id="">
				<div class="inner">
					<span class="site-preview" data-href="" data-title={{site_title}}>
						<div class="theme-screenshot one loading" data-step="1" data-src={{data[ key ]['thumbnail-image-url']}} data-featured-src={{data[ key ]['featured-image-url']}}>
							<div class="elementor-template-library-template-preview">
								<i class="eicon-zoom-in" aria-hidden="true"></i>
							</div>
						</div>
					</span>
					<div class="theme-id-container">
						<h3 class="theme-name">{{site_title}}</h3>
					</div>
					<# if ( site_type && 'free' !== site_type ) { #>
						<?php /* translators: %1$s are white label strings. */ ?>
						<div class="agency-ribbons" title="<?php printf( esc_attr__( 'This premium template is accessible with %1$s "Premium" Package.', 'astra-sites' ), esc_html( Astra_Sites_White_Label::get_instance()->get_white_label_name() ) ); ?>"><img class="premium-crown-icon" src="<?php echo esc_url( ASTRA_SITES_URI . 'inc/assets/images/premium-crown.svg' ); ?>" alt="premium-crown"><?php esc_html_e( 'Premium', 'astra-sites' ); ?></div>
					<# } #>
				</div>
			</div>
	<#
		}
	#>
</script>

<script type="text/template" id="tmpl-astra-blocks-list">

	<#
		var count = 0;
		let upper_window = ( AstraElementorSitesAdmin.per_page * ( AstraElementorSitesAdmin.page - 1 ) );
		let lower_window = ( upper_window + AstraElementorSitesAdmin.per_page );

		for ( key in data ) {

			var site_title = ( undefined == data[ key ]['category'] || 0 == data[ key ]['category'].length ) ? data[ key ]['title'] : astraElementorSites.astra_block_categories[data[ key ]['category']].name;

			if ( '' !== AstraElementorSitesAdmin.blockCategory ) {
				if ( AstraElementorSitesAdmin.blockCategory != data[ key ]['category'] ) {
					continue;
				}
			}

			if ( '' !== AstraElementorSitesAdmin.blockColor ) {
				if ( undefined !== data[ key ]['filter'] && AstraElementorSitesAdmin.blockColor != data[ key ]['filter'] ) {
					continue;
				}
			}
			count++;
	#>
		<div class="astra-sites-library-template astra-theme" data-block-id={{key}}>
			<div class="astra-sites-library-template-inner" >
				<div class="elementor-template-library-template-body theme-screenshot" data-step="1">
					<img src="{{data[ key ]['thumbnail-image-url']}}">
					<div class="elementor-template-library-template-preview">
						<i class="eicon-zoom-in" aria-hidden="true"></i>
					</div>
				</div>
				<div class="elementor-template-library-template-footer">
					<a class="elementor-template-library-template-action elementor-template-library-template-insert ast-block-insert">
						<i class="eicon-file-download" aria-hidden="true"></i>
						<span class="elementor-button-title"><?php esc_html_e( 'INSERT', 'astra-sites' ); ?></span>
					</a>
				</div>
			</div>
		</div>
	<#
		}
		if ( count == 0 ) {
	#>
		<div class="astra-sites-no-sites">
			<div class="inner">
				<h3><?php esc_html_e( 'Sorry No Results Found.', 'astra-sites' ); ?></h3>
				<div class="content" style="text-align: center">
					<div class="description">
						<p>
							<?php
								/* translators: %1$s External Link */
								printf( esc_html__( "Don't see a template you would like to import? %s", 'astra-sites' ), nl2br( '<br><a target="_blank" href="' . esc_url( $suggestion_link ) . '">Make a Template Suggestion!</a>' ) );
							?>
						</p>
						<div class="back-to-layout-button"><span class="button astra-sites-back"><?php esc_html_e( 'Back to Templates', 'astra-sites' ); ?></span></div>
					</div>
				</div>
			</div>
		</div>
	<#
		}
	#>
</script>

<script type="text/template" id="tmpl-astra-sites-list-search">

	<#
		var count = 0;

		for ( ind in data ) {
			var site_type = data[ ind ]['site-pages-type'];
			var type_class = ' site-type-' + site_type;
			var site_id = ( undefined == data.site_id ) ? data[ind].site_id : data.site_id;
			if ( undefined == site_type ) {
				continue;
			}
			if ( 'gutenberg' == data[ind]['site-pages-page-builder'] ) {
				continue;
			}
			var site_title = data[ ind ]['title'].slice( 0, 25 );
			if ( data[ ind ]['title'].length > 25 ) {
				site_title += '...';
			}
			count++;
	#>
		<div class="theme astra-theme site-single publish page-builder-elementor {{type_class}}" data-template-id={{ind}} data-site-id={{site_id}}>
			<div class="inner">
				<span class="site-preview" data-href="" data-title={{site_title}}>
					<div class="theme-screenshot one loading" data-step="2" data-src={{data[ ind ]['thumbnail-image-url']}} data-featured-src={{data[ ind ]['featured-image-url']}}>
						<div class="elementor-template-library-template-preview">
							<i class="eicon-zoom-in" aria-hidden="true"></i>
						</div>
					</div>
				</span>
				<div class="theme-id-container">
					<h3 class="theme-name">{{site_title}}</h3>
					<#
					var is_free = true;
					if ( 'pages' == AstraElementorSitesAdmin.type ) {
						if( 'free' !== data[ ind ]['site-pages-type'] && ! astraElementorSites.license_status ) {
							is_free = false;
						}
					}
					if( is_free ) { #>
						<a class="elementor-template-library-template-action elementor-template-library-template-insert ast-block-insert">
							<i class="eicon-file-download" aria-hidden="true"></i>
							<span class="elementor-button-title"><?php esc_html_e( 'INSERT', 'astra-sites' ); ?></span>
						</a>
					<# } else { #>
						<a class="elementor-template-library-template-action elementor-template-library-template-go-pro" href="{{astraElementorSites.getProURL}}" target="_blank">
							<i class="eicon-external-link-square" aria-hidden="true"></i>
							<span class="elementor-button-title"><?php esc_html_e( 'Get Access!', 'astra-sites' ); ?></span>
						</a>
					<# } #>
				</div>
				<# if ( site_type && 'free' !== site_type ) { #>
					<?php /* translators: %1$s are white label strings. */ ?>
					<div class="agency-ribbons" title="<?php printf( esc_attr__( 'This premium template is accessible with %1$s "Premium" Package.', 'astra-sites' ), esc_html( Astra_Sites_White_Label::get_instance()->get_white_label_name() ) ); ?>"><?php esc_html_e( 'Premium', 'astra-sites' ); ?></div>
				<# } #>
			</div>
		</div>
	<#
		}

		if ( count == 0 ) {
	#>
		<div class="astra-sites-no-sites">
			<div class="inner">
				<h3><?php esc_html_e( 'Sorry No Results Found.', 'astra-sites' ); ?></h3>
				<div class="content">
					<div class="description">
						<p>
						<?php
						/* translators: %1$s External Link */
						printf( esc_attr__( 'Don\'t see a template you would like to import?<br><a target="_blank" href="%1$s">Make a Template Suggestion!</a>', 'astra-sites' ), esc_url( $suggestion_link ) );
						?>
						</p>
						<div class="back-to-layout-button"><span class="button astra-sites-back"><?php esc_html_e( 'Back to Templates', 'astra-sites' ); ?></span></div>
					</div>
				</div>
			</div>
		</div>
	<#
		}
	#>
</script>

<script type="text/template" id="tmpl-astra-sites-search">

	<#
		var count = 0;

		for ( ind in data ) {
			if ( 'gutenberg' == data[ind]['site-pages-page-builder'] ) {
				continue;
			}

			var site_id = ( undefined == data.site_id ) ? data[ind].site_id : data.site_id;
			var site_type = data[ ind ]['site-pages-type'];

			if ( 'site' == data[ind]['type'] ) {
				site_type = data[ ind ]['astra-sites-type'];
			}

			if ( undefined == site_type ) {
				continue;
			}

			var parent_name = '';
			if ( undefined != data[ind]['parent-site-name'] ) {
				var parent_name = jQuery( "<textarea/>") .html( data[ind]['parent-site-name'] ).text();
			}

			var complete_title = parent_name + ' - ' + data[ ind ]['title'];
			var site_title = complete_title.slice( 0, 25 );
			if ( complete_title.length > 25 ) {
				site_title += '...';
			}

			var tmp = site_title.split(' - ');
			var title1 = site_title;
			var title2 = '';
			if ( undefined !== tmp && undefined !== tmp[1] ) {
				title1 = tmp[0];
				title2 = ' - ' + tmp[1];
			} else {
				title1 = tmp[0];
				title2 = '';
			}

			var type_class = ' site-type-' + site_type;
			count++;
	#>
		<div class="theme astra-theme site-single publish page-builder-elementor {{type_class}}" data-template-id={{ind}} data-site-id={{site_id}}>
			<div class="inner">
				<span class="site-preview" data-href="" data-title={{title2}}>
					<div class="theme-screenshot one loading" data-type={{data[ind]['type']}} data-step={{data[ind]['step']}} data-show="search" data-src={{data[ ind ]['thumbnail-image-url']}} data-featured-src={{data[ ind ]['featured-image-url']}}></div>
				</span>
				<div class="theme-id-container">
					<h3 class="theme-name"><strong>{{title1}}</strong>{{title2}}</h3>
				</div>
				<# if ( site_type && 'free' !== site_type ) { #>
					<div class="agency-ribbons" title="
					<?php
						/* translators: %1$s are white label strings. */
						printf( esc_attr__( 'This premium template is accessible with %1$s "Premium" Package.', 'astra-sites' ), esc_html( Astra_Sites_White_Label::get_instance()->get_white_label_name() ) ); 
					?>
						"
						>
						<?php esc_html_e( 'Premium', 'astra-sites' ); ?>
					</div>
				<# } #>
			</div>
		</div>
	<#
		}

		if ( count == 0 ) {
	#>
		<div class="astra-sites-no-sites">
			<div class="inner">
				<h3><?php esc_html_e( 'Sorry No Results Found.', 'astra-sites' ); ?></h3>
				<div class="content" style="text-align: center">
					<div class="description">
						<p>
							<?php
								/* translators: %1$s External Link */
								printf( esc_html__( "Don't see a template you would like to import? %s", 'astra-sites' ), nl2br( '<br><a target="_blank" href="' . esc_url( $suggestion_link ) . '">Make a Template Suggestion!</a>' ) );
							?>
						</p>
						<div class="back-to-layout-button"><span class="button astra-sites-back"><?php esc_html_e( 'Back to Templates', 'astra-sites' ); ?></span></div>
					</div>
				</div>
			</div>
		</div>
	<#
		}
	#>
</script>

<script type="text/template" id="tmpl-astra-sites-insert-button">
	<div id="elementor-template-library-header-preview-insert-wrapper" class="elementor-templates-modal__header__item" data-template-id={{data.template_id}} data-site-id={{data.site_id}}>
		<a class="elementor-template-library-template-action elementor-template-library-template-insert elementor-button">
			<i class="eicon-file-download" aria-hidden="true"></i>
			<span class="elementor-button-title"><?php esc_html_e( 'Insert', 'astra-sites' ); ?></span>
		</a>

	</div>
</script>

<?php
/**
 * TMPL - Third Party Required Plugins
 */
?>
<script type="text/template" id="tmpl-astra-sites-third-party-required-plugins">
	<div class="skip-and-import">
		<div class="heading">
			<h3><?php esc_html_e( 'Required Plugins Missing', 'astra-sites' ); ?></h3>
			<span class="dashicons close dashicons-no-alt"></span>
		</div>
		<div class="astra-sites-import-content">
			<p><?php esc_html_e( 'This starter site requires premium plugins. As these are third party premium plugins, you\'ll need to purchase, install and activate them first.', 'astra-sites' ); ?></p>
			<ul class="astra-sites-third-party-required-plugins">
				<# for ( key in data ) { #>
					<li class="plugin-card plugin-card-{{data[ key ].slug}}'" data-slug="{{data[ key ].slug }}" data-init="{{data[ key ].init}}" data-name="{{data[ key ].name}}"><a href="{{data[ key ].link}}" target="_blank">{{data[ key ].name}}</a></li>
				<# } #>
			</ul>
		</div>
		<div class="ast-actioms-wrap">
			<a href="#" class="button button-hero button-primary astra-sites-skip-and-import-step"><?php esc_html_e( 'Skip & Import', 'astra-sites' ); ?></a>
			<div class="button button-hero site-import-cancel"><?php esc_html_e( 'Cancel', 'astra-sites' ); ?></div>
		</div>
	</div>
</script>

<script type="text/template" id="tmpl-astra-sites-no-sites">
	<div class="astra-sites-no-sites">
		<div class="inner">
			<h3><?php esc_html_e( 'Sorry No Results Found.', 'astra-sites' ); ?></h3>
			<div class="content">
				<div class="empty-item">
					<img class="empty-collection-part" src="<?php echo esc_url( ASTRA_SITES_URI . 'inc/assets/images/empty-collection.svg' ); ?>" alt="empty-collection">
				</div>
				<div class="description">
					<p>
					<?php
					/* translators: %1$s External Link */
					printf( esc_html__( 'Don\'t see a template you would like to import?<br><a target="_blank" href="%1$s">Make a Template Suggestion!</a>', 'astra-sites' ), esc_url( $suggestion_link ) );
					?>
					</p>
					<div class="back-to-layout-button"><span class="button astra-sites-back"><?php esc_html_e( 'Back to Templates', 'astra-sites' ); ?></span></div>
				</div>
			</div>
		</div>
	</div>
	<#
</script>

<script type="text/template" id="tmpl-astra-sites-elementor-preview">
	<#
	let wrap_height = $elscope.find( '.astra-sites-content-wrap' ).height();
	wrap_height = ( wrap_height - 55 );
	wrap_height = wrap_height + 'px';
	#>
	<div id="astra-blocks" class="themes wp-clearfix" data-site-id="{{data.id}}" style="display: block;">
		<div class="single-site-wrap">
			<div class="single-site">
				<div class="single-site-preview-wrap">
					<div class="single-site-preview" style="max-height: {{wrap_height}};">
						<img class="theme-screenshot" data-src="" src="{{data['featured-image-url']}}">
					</div>
				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="tmpl-astra-sites-elementor-preview-actions">
	<#
	var demo_link = '';
	var action_str = '';
	if ( 'blocks' == AstraElementorSitesAdmin.type ) {
		demo_link = astraElementorSites.astra_blocks[AstraElementorSitesAdmin.block_id]['url'];
		action_str = 'Block';
	} else {
		demo_link = data['astra-page-url'];
		action_str = 'Template';
	}
	#>
	<div class="astra-preview-actions-wrap">
		<div class="astra-preview-actions-inner-wrap">
			<div class="astra-preview-actions">
				<div class="site-action-buttons-wrap">
					<div class="astra-sites-import-template-action site-action-buttons-right">
						<div class="astra-sites-tooltip"><span class="astra-sites-tooltip-icon" data-tip-id="astra-sites-tooltip-plugins-settings"><span class="dashicons dashicons-editor-help"></span></span></div>
						<#
						var is_free = true;
						if ( 'pages' == AstraElementorSitesAdmin.type ) {
							if( 'free' !== data['site-pages-type'] && ! astraElementorSites.license_status ) {
								is_free = false;
							}
						}
						if( ! is_free ) { #>
							<a class="button button-hero button-primary" href="{{astraElementorSites.getProURL}}" target="_blank">{{astraElementorSites.getProText}}<i class="dashicons dashicons-external"></i></a>
						<# } else { #>
							<div type="button" class="button button-hero button-primary ast-library-template-insert disabled"><?php esc_html_e( 'Import ', 'astra-sites' ); ?>{{action_str}}</div>
							<div type="button" class="button button-hero button-primary ast-import-elementor-template disabled"><?php esc_html_e( 'Save ', 'astra-sites' ); ?>{{action_str}}</div>
						<# } #>
					</div>
				</div>
			</div>
			<div class="ast-tooltip-wrap">
				<div>
					<div class="ast-tooltip-inner-wrap" id="astra-sites-tooltip-plugins-settings">
						<ul class="required-plugins-list"><span class="spinner is-active"></span></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</script>

<?php
