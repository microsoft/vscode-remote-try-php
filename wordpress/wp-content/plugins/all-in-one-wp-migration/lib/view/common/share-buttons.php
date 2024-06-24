<?php
/**
 * Copyright (C) 2014-2023 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}
?>

<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v9.0&appId=597242117012725" nonce="xH3McWON"></script>
<script async defer src="https://apis.google.com/js/platform.js"></script>
<script>
window.twttr = (function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0],
		t = window.twttr || {};
	if (d.getElementById(id)) return t;
	js = d.createElement(s);
	js.id = id;
	js.src = "https://platform.twitter.com/widgets.js";
	fjs.parentNode.insertBefore(js, fjs);

	t._e = [];
	t.ready = function(f) {
		t._e.push(f);
	};

	return t;
}(document, "script", "twitter-wjs"));
</script>

<div class="ai1wm-share-button-container">
	<span>
		<a
			href="https://twitter.com/share"
			class="twitter-share-button"
			data-url="https://servmask.com"
			data-text="Check this epic WordPress Migration plugin"
			data-via="servmask"
			data-related="servmask"
			data-hashtags="servmask">
			<?php _e( 'Tweet', AI1WM_PLUGIN_NAME ); ?>
		</a>
	</span>
	<span class="ai1wm-top-positive-four">
		<div
			class="fb-like"
			data-href="https://www.facebook.com/servmaskproduct"
			data-width=""
			data-layout="button_count"
			data-action="like"
			data-size="small"
			data-share="false">
		</div>
	</span>
	<span class="ai1wm-top-positive-two">
		<div class="g-ytsubscribe" data-channelid="UCWMNPEnX7KyDLknpcmPaSwg" data-layout="default" data-count="default"></div>
	</span>
</div>
