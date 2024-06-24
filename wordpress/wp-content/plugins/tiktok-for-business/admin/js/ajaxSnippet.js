jQuery(
		function( $ ) {
			$( document.body ).on(
				'added_to_cart',
				function( e, fragments, cart_hash, thisbutton ) {
					// var pixel_code = window.tt4b_script_vars.pixel_code;
					// var currency = window.tt4b_script_vars.currency;
					// var product_id = thisbutton.data('product_id');
					// var product_name = thisbutton.data('product_name');
					// var price = thisbutton.data('price');
					//
					// ttq.instance(pixel_code).track(
					// 	'AddToCart',
					// 	{
					// 		'content_id': product_id,
					// 		'content_name': product_name,
					// 		'content_type': 'product',
					// 		'price': price,
					// 		'value': price,
					// 		'quantity': 1,
					// 		'currency': currency
					// 	}
					// );
				}
				);


	// add library to help parse phone numbers
	let script = document.createElement('script');
	script.src = 'https://unpkg.com/libphonenumber-js@1.10.45/bundle/libphonenumber-min.js';
	document.head.appendChild(script);

	// add library to generate uuid4 for event api
	let uuidScript = document.createElement('script');
	uuidScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/uuid/8.1.0/uuidv4.min.js';
	document.head.appendChild(uuidScript);

	// advanced matching - will send hashed advanced_matching fields if true, otherwise will exit if advanced_matching value is false or null
	function getEmailAndPhone(inputElement, pixelCode, source) {
		if (window.tt4b_script_vars.advanced_matching !== '1') {
			return;
		}
		let result = {
			email: "",
			phone_number: ""
		};
		if (!inputElement) {
			return result
		}
		let form = inputElement.closest('form') || inputElement.querySelector('form');
		if ((!form || form.length === 0) && source !== 'ninjaforms') {
			return;
		} else if ((!form || form.length === 0) && source === 'ninjaforms') {
			form = inputElement.first('form')[0];
		}
		let inputElements = form.querySelectorAll('input');
		for (let input of inputElements) {
			if (input.type === 'email') {
				result.email = input.value;
			} else if (input.type === 'tel') {
				try {
					let phone_number = input.value;
					result.phone_number = libphonenumber.parsePhoneNumber(phone_number, window.tt4b_script_vars.country).number
				} catch (error) {
					console.warn("Error occurred while parsing phone number: ", error);
				}
			}
		}
		ttq.instance(pixelCode).identify(result);
	}

	// Fires a Contact event if AM data present in form, otherwise fires a SubmitForm vent
	function firePixelBasedOnFormIntent(inputElement, pixelCode, source) {
		let form = inputElement.closest('form') || inputElement.querySelector('form');
		if ((!form || form.length === 0) && source !== 'ninjaforms') {
			return;
		} else if ((!form || form.length === 0) && source === 'ninjaforms') {
			form = inputElement.first('form')[0];
		}
		let inputElements = form.querySelectorAll('input');
		let hasAMData = false;
		for (let input of inputElements) {
			if (input.type === 'email' || input.type === 'tel') {
				hasAMData = true;
				break;
			}
		}
		let eventType = hasAMData ? 'Contact' : 'SubmitForm'
		let event_id = ""
		try {
			event_id = uuidv4()
		} catch (error) {
			console.warn("Error occurred while generating uuidv4: ", error);
		}
		ttq.instance(pixelCode).track(eventType, {
			'source' : source,
			'wp_plugin' : source,
			"event_id" : event_id
		})
	}

	// Some forms will trigger the submit event, even if the form submission is unsuccessful
	// In this case we want to make sure the form submission is successful by checking for the success message
	// once the message is visible, we will fire the pixel event
	function createObserver(source) {
		return new MutationObserver(function(mutations) {
			mutations.forEach(function(mutation) {
				if (window.getComputedStyle(mutation.target).display !== 'none') {
					getEmailAndPhone(mutation.target, window.tt4b_script_vars.pixel_code);
					firePixelBasedOnFormIntent(mutation.target, window.tt4b_script_vars.pixel_code, source)
				}
			});
		});
	}

	// fallback case
	document.addEventListener('submit', function(event) {
		var pixel_code = window.tt4b_script_vars.pixel_code;
		getEmailAndPhone(event.target, pixel_code);
		firePixelBasedOnFormIntent(event.target, pixel_code, "fallback")
	});

	// ButtonClick Lead Gen Event
	$('button, :submit').on( 'click', function(event) {
		var pixel_code = window.tt4b_script_vars.pixel_code;
		// AM based on form inputs when submit clicked
		getEmailAndPhone(event.target, pixel_code);
		let event_id = ""
		try {
			event_id = uuidv4()
		} catch (error) {
			console.warn("Error occurred while generating uuidv4: ", error);
		}
		ttq.instance(pixel_code).track('ClickButton', {
			'content': 'SubmitClick',
			"event_id": event_id
		});
	});

	// Contact Lead Gen Event: Contact Form 7
	// https://wordpress.org/plugins/contact-form-7/
	document.addEventListener('wpcf7mailsent', function(event) {
		var pixel_code = window.tt4b_script_vars.pixel_code;
		getEmailAndPhone(event.target, pixel_code);
		firePixelBasedOnFormIntent(event.target, pixel_code, "contactform7")
	 }, false);

	// Contact Lead Gen Event: MC4WP: Mailchimp for Wordpress
	// https://wordpress.org/plugins/mailchimp-for-wp/
	var mailchimp_forms = document.querySelectorAll('.mc4wp-form');
	mailchimp_forms.forEach(function(form) {
		var pixel_code = window.tt4b_script_vars.pixel_code;
		form.addEventListener('submit', function(event) {
			getEmailAndPhone(event.target, pixel_code);
			firePixelBasedOnFormIntent(event.target, pixel_code, "mailchimp4wordpress")
		});
	});

	// Contact Lead Gen Event: Jetpack + Mailchimp
	// https://jetpack.com/support/jetpack-blocks/mailchimp-block/
	var jetpackMailchimpNodes = document.querySelectorAll('.wp-block-jetpack-mailchimp_success');
	if (jetpackMailchimpNodes.length > 0) {
		var jetpackMailchimpObserver = createObserver('jetpackmailchimp');
		jetpackMailchimpNodes.forEach(function(targetNode) {
			jetpackMailchimpObserver.observe(targetNode, { attributes: true, childList: true, subtree: true });
		});
	}

	// Contact Lead Get Event: MailPoet
	// https://wordpress.org/plugins/mailpoet/
	$('input.mailpoet_submit').on('click', function() {
		var pixel_code = window.tt4b_script_vars.pixel_code;
		getEmailAndPhone(event.target, pixel_code);
		firePixelBasedOnFormIntent(event.target, pixel_code, "mailpoet")
	});

	// Contact Lead Gen Event: Spectra
	// https://wordpress.org/plugins/ultimate-addons-for-gutenberg/
	var spectraForms = document.querySelectorAll('.uagb-forms-main-form');
	spectraForms.forEach(function(form) {
		var pixel_code = window.tt4b_script_vars.pixel_code;
		form.addEventListener('submit', function(event) {
			getEmailAndPhone(event.target, pixel_code);
			firePixelBasedOnFormIntent(event.target, pixel_code, "spectra")
		});
	});

	// Submit Form Lead Gen Event: WPForms
	// https://wordpress.org/plugins/wpforms-lite/
	$('form.wpforms-form').on('wpformsAjaxSubmitSuccess', (event) => {
		var pixel_code = window.tt4b_script_vars.pixel_code;
		getEmailAndPhone(event.target, pixel_code);
		firePixelBasedOnFormIntent(event.target, pixel_code, "wpforms")
	})


	// Submit Form Lead Gen Events: JetPack
	// https://wordpress.org/plugins/jetpack/
	if (document.querySelector('[class*=jetpack-contact-form]')) {
		document.addEventListener('submit', (event) => {
			var pixel_code = window.tt4b_script_vars.pixel_code;
			getEmailAndPhone(event.target, pixel_code);
			firePixelBasedOnFormIntent(event.target, pixel_code, "jetpack")
		});
	}

	// Submit Form Lead Gen Events: Ninja Forms
	// https://wordpress.org/plugins/ninja-forms/
	$(document).on('nfFormSubmitResponse', (event) => {
		event.preventDefault();
		var pixel_code = window.tt4b_script_vars.pixel_code;
		getEmailAndPhone($('.nf-form-layout'), pixel_code, "ninjaforms");
		firePixelBasedOnFormIntent($('.nf-form-layout'), pixel_code, "ninjaforms")
	});

	$(document).on('nfFormReady', (event) => {
		event.preventDefault();
		$('button, :submit, input[type="submit"]').on( 'click', function(event) {
			var pixel_code = window.tt4b_script_vars.pixel_code;
			// AM based on form inputs when submit clicked
			getEmailAndPhone(event.target, pixel_code);
			ttq.instance(pixel_code).track('ClickButton', {
				'content': 'SubmitClick'
			});
		});
	})

});

