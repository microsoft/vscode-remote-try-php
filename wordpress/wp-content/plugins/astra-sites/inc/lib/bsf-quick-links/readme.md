# BSF Quick Links

BSF Quick Links allows you to show a list of your most commonly used links and access them from our plugins settings page 
> Example 
> - Upgrade to Pro.
> - Documentation 
> - Join our community and access them from our plugins settings page.


### How to use? ###
@see https://github.com/brainstormforce/astra-sites/blob/3c42ceeeb466a2f4e7656ba0d5b43a8a9909e6fd/inc/classes/class-astra-sites.php#L143

- Add below action into respective plugins settings page.  
```sh
    add_action( 'admin_footer', array( $this, 'add_quick_links' ) );
```

- Callback function
```sh
public function add_quick_links() {
    $current_screen = get_current_screen();

    if ( 'plugin_settings_screen_name' !== $current_screen->id ) {
        return;
    }

    if ( Astra_Sites_White_Label::get_instance()->is_white_labeled() ) {
        return;
    }
    
    $data = array(
        'default_logo' => array(
            'title' => '', //title on logo hover.
            'url'   => '',
            ),
        'links'        => array(
            array('label' => '','icon' => '','url' => ''),
            array('label' => '','icon' => '','url' => ''),
            array('label' => '','icon' => '','url' => ''),
            ...
        )
    );
    if ( defined( 'ASTRA_PRO_SITES_VER' ) ) { 
		array_shift( $data['links'] ); //Exclude upgrade to pro link.
	}

	bsf_quick_links( $data );
}
``` 
