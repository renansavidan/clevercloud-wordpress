<?php
/**
* Theme functions and definitions
*/

/**
* Load child theme css and optional scripts
*/
function hello_elementor_child_enqueue_scripts() {
	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		'1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts', 20 );


/* Redirect if topic (chapitre) is no sample + attention aux recherches */
add_action( 'template_redirect', 'educawa_template_redirect' );
function educawa_template_redirect() {
	if(!is_user_logged_in() && !(isset($_GET['s'])|isset($_GET['ms']) )){
		if( get_post_type() == 'sfwd-topic' ){
			$id = get_the_id();
			//if ( metadata_exists('post',$id,'_meta_key') ) {
				$freetopic = get_post_meta( $id,'_educawa_free_topic', true );
				if( !$freetopic ) {
					wp_redirect( home_url());
					die;
				}
			//}
		}
	}	
}


/* add ms query var to handle search matiere page matiere */
add_filter( 'query_vars', 'educawa_add_query_vars_filter' );
function educawa_add_query_vars_filter( $vars ) {
    // add custom query vars that will be public
    // https://codex.wordpress.org/WordPress_Query_Vars
	$vars[] .= 'ms';
    return $vars;
}



/* elementor pro main search filter custom query */
add_action( 'elementor/query/educawa_search_2', function( $query ) {
	$edusearch=get_search_query();
	$query->set( 's', $edusearch);

} );


/* custom login button */
add_shortcode( 'educawa_shortcode_7', 'educawa_shortcode_login_button' );
function educawa_shortcode_login_button() {
	if(is_user_logged_in()){
		$user_info = wp_get_current_user();
		if ( ( $user_info->first_name )){
			if ( $user_info->last_name ) {
				$textlink= $user_info->first_name . ' ' . $user_info->last_name;
			}
			else{
					$textlink= $user_info->first_name;
			}			
		}	
		else{
			if ( $user_info->last_name ) {
				$textlink= $user_info->last_name;
			}
			else{
				$textlink= "Votre nom n'est pas renseigné";
			}			
		}
		$urllink=get_permalink( get_option('woocommerce_myaccount_page_id') );
	}
	else{
		$textlink= "Je m'abonne ou je me connecte";
		$urllink="/inscription-connexion";
	}
	$login_button = '<div class="educawalogin-wrap"><a class="educawa-login" href="'.$urllink.'">'.$textlink.'</a></div>';
	//$login_button = '<a class="educawa-login" href="'.$urllink.'">'.$textlink.'</a>';
	return $login_button;
}


/*
** Custom WooCommerce checkout 
*/

/* remove notice login top page */
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );


/* WooCommerce: The Code Below Removes Checkout Fields */
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {
	//unset($fields['billing']['billing_first_name']);
	//unset($fields['billing']['billing_last_name']);
	unset($fields['billing']['billing_company']);
	unset($fields['billing']['billing_address_1']);
	unset($fields['billing']['billing_address_2']);
	unset($fields['billing']['billing_city']);
	unset($fields['billing']['billing_postcode']);
	unset($fields['billing']['billing_country']);
	unset($fields['billing']['billing_state']);
	unset($fields['billing']['billing_phone']);
	unset($fields['order']['order_comments']);
	//unset($fields['billing']['billing_email']);
	//unset($fields['account']['account_username']);
	//unset($fields['account']['account_password']);
	//unset($fields['account']['account_password-2']);
	/* Custom fields Educawa */
	unset($fields['account']['user_birth']);
	return $fields;
}

//Change the 'Billing details' checkout label to 'Contact Information'
function wc_billing_field_strings( $translated_text, $text, $domain ) {
	//return "";
	switch ( $translated_text ) {
		case 'Billing details' :
			//$translated_text = __( 'Contact Information', 'woocommerce' );
			return "";
			break;
	}
	return $translated_text;
	//return "";
}
//add_filter( 'gettext', 'wc_billing_field_strings', 20, 3 );




/* make fields optional */

// Billing and shipping addresses fields
add_filter( 'woocommerce_default_address_fields' , 'filter_default_address_fields', 20, 1 );
function filter_default_address_fields( $address_fields ) {
    // Only on account pages
    if(  ! is_account_page() ) return $address_fields;

    // All field keys in this array
    $key_fields = array('first_name','last_name','state','address_2','company','country');

    // Loop through each address fields (billing and shipping)
    foreach( $key_fields as $key_field )
		unset( $address_fields[$key_field]);
		
	$address_fields['postcode']['class'] = array('form-row-first');
	$address_fields['city']['class'] = array('form-row-last');

    return $address_fields;
}

// For billing email and phone - Make them not required
//add_filter( 'woocommerce_billing_fields', 'filter_billing_fields', 20, 1 );
function filter_billing_fields( $billing_fields ) {
    // Only on checkout page
    if( ! is_checkout() ) return $billing_fields;

    $billing_fields['billing_phone']['required'] = false;
    //$billing_fields['billing_email']['required'] = false;
    return $billing_fields;
}

/* hide additional info */
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );



/* customize form fields */
/*
****   WooCommerce fields customization   ****
*/

/**
* Get additional account fields.
*/
function educawa_get_account_fields() {
	return apply_filters( 'educawa_account_fields', array(
		'user_birth' => array(
			'type'        => 'date',
			'label'       => __( 'Date de Naissance', 'hello-elementor' ),
			//'placeholder' => __( 'Date de Naissance', 'hello-elementor' ),
			'required'    => false,
		),
	) );
}


/**
* Add fields to registration form and account area.
**/
add_action( 'woocommerce_edit_account_form', 'educawa_print_user_frontend_fields', 10 ); // my account
add_action( 'woocommerce_register_form', 'educawa_print_user_frontend_fields', 10 ); // register form
function educawa_print_user_frontend_fields() {
	$fields = educawa_get_account_fields();

	foreach ( $fields as $key => $field_args ) {
		woocommerce_form_field( $key, $field_args );
	}
}

/**
* Show fields at checkout.
*/
add_filter( 'woocommerce_checkout_fields', 'educawa_checkout_fields', 10, 1 );
function educawa_checkout_fields( $checkout_fields ) {
	$fields = educawa_get_account_fields();

	foreach ( $fields as $key => $field_args ) {
		// Make sure our fields have a default priority so
		// no error is thrown when sorting them.
		$field_args['priority'] = isset( $field_args['priority'] ) ? $field_args['priority'] : 0;

		$checkout_fields['account'][ $key ] = $field_args;
	}

	// Default password field has no priority which throws an
	// error when it tries to order the fields by priority.
	if ( ! empty( $checkout_fields['account']['account_password'] ) && ! isset( $checkout_fields['account']['account_password']['priority'] ) ) {
		$checkout_fields['account']['account_password']['priority'] = 0;
	}

	return $checkout_fields;
}



/**
* Add fields to admin area.
*/
add_action( 'show_user_profile', 'educawa_print_user_admin_fields', 30 ); // admin: edit profile
add_action( 'edit_user_profile', 'educawa_print_user_admin_fields', 30 ); // admin: edit other users
function educawa_print_user_admin_fields() {
	$fields = educawa_get_account_fields();
	?>
	<h2><?php _e( 'Informations additionelles Educawa', 'hello-elementor' ); ?></h2>
	<table class="form-table" id="educawa-additional-information">
		<tbody>
		<?php foreach ( $fields as $key => $field_args ) { ?>
			<tr>
				<th>
					<label for="<?php echo $key; ?>"><?php echo $field_args['label']; ?></label>
				</th>
				<td>
					<?php $field_args['label'] = false; ?>
					<?php woocommerce_form_field( $key, $field_args ); ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php
}


/* Add placeholders */
add_filter( 'woocommerce_form_field_args', 'educawa_custom_form_field_args',10,3 );
function educawa_custom_form_field_args( $args, $key, $value ) { 
	if ( $args['id'] == 'billing_first_name' ) {
		$args['placeholder'] = 'Marie-Anne';
		//$args['label'] = '';
	} 
	elseif ( $args['id'] == 'billing_last_name' ) {
		$args['placeholder'] = 'Langlais';
		//$args['label'] = '';
	} 
	elseif ( $args['id'] == 'billing_email' ) {
		$args['placeholder'] = 'votreemail@gmail.com'; 
		//$args['label'] = '';
	} 
	elseif ( $args['id'] == 'account_first_name' ) {
		$args['placeholder'] = 'Marie-Anne';
		//$args['label'] = '';
	} 
	elseif ( $args['id'] == 'account_last_name' ) {
		$args['placeholder'] = 'Langlais';
		//$args['label'] = '';
	} 
	elseif ( $args['id'] == 'account_email' ) {
		$args['placeholder'] = 'votreemail@gmail.com';  
		//$args['label'] = '';
	} 
	elseif ( $args['id'] == 'password_current' ) {
		$args['placeholder'] = 'Seul vous le connaîtrez';
		//$args['label'] = '';
	} 
	elseif ( $args['id'] == 'password_1' ) {
		$args['placeholder'] = 'Nouveau mot de passe';  
		//$args['label'] = '';
	} 
	elseif ( $args['id'] == 'password_2' ) {
		$args['placeholder'] = 'Confirmez le mot de passe';
		//$args['label'] = '';
	}

   return $args;
};

add_filter( 'woocommerce_checkout_fields' , 'educawa_custom_checkout_fields');
function educawa_custom_checkout_fields( $fields ) {
	$fields['billing']['billing_first_name']['placeholder'] = 'Marie-Anne';
	//$fields['billing']['billing_first_name']['label'] = '';
	$fields['billing']['billing_last_name']['placeholder'] = 'Langlais';
	//$fields['billing']['billing_last_name']['label'] = '';
	$fields['billing']['billing_email']['placeholder'] = 'votreemail@gmail.com';  
	//$fields['billing']['billing_email']['label'] = '';  
	$fields['account']['account_password']['placeholder'] = 'Seul vous le connaîtrez';
	//$fields['account']['account_password']['label'] = '';  
	return $fields;
}




/* redirect after checkout */
add_action( 'woocommerce_thankyou', 'educawa_checkoutredirect');
function educawa_checkoutredirect( $order_id ){
 	$order = wc_get_order( $order_id );
	$url = home_url();
    	//$url = 'https://yoursite.com/custom-url';
	if ( ! $order->has_status( 'failed' ) ) {
		wp_safe_redirect( $url );
		exit;
	}
}




/* elementor pro custom single matière filter query */
add_action( 'elementor/query/educawa_matiere_1', function( $query ) {
	//$posttype=get_post_type();
	$postid=get_the_id();
	$course_id = get_post_meta( $postid, 'course_id', true );

	$meta_query[] = [          
	'key' => 'course_id',          
	'value' => [ $course_id ],          
	'compare' => 'in', ];  
	$meta_query[] = [          
	'key' => 'lesson_id',          
	'value' => [ $postid ],          
	'compare' => 'in', ]; 
	
	$query->set( 'meta_query', $meta_query );

	/* extra to handle custom search flow */
	$query->set( 'meta_query', $meta_query );
	if (isset($_GET['ms'])){
		$ms = get_query_var('ms');
		/*$query_var[]= [          
			'ms' =>  [ $ms ], 
		];*/ 
		$query->set( 's', $ms);
	}
} );



/* elementor pro custom single classe filter query */
add_action( 'elementor/query/educawa_classe_1', function( $query ) {
	//$posttype=get_post_type();
	$postid=get_the_id();

	$meta_query[] = [          
	'key' => 'course_id',          
	'value' => [ $postid ],          
	'compare' => 'in', ]; 

	$query->set( 'meta_query', $meta_query );
} );

/* elementor pro + ele custom skin get course title from meta key */
add_shortcode( 'educawa_shortcode_2', 'educawa_shortcode_get_course_title' );
function educawa_shortcode_get_course_title() {
		$postid=get_the_id();
		$course_id = get_post_meta( $postid, 'course_id', true );
		$coursetitle=get_the_title($course_id);
		
		return $coursetitle;	
}

/* elementor pro + ele custom skin get lesson title from meta key */
add_shortcode( 'educawa_shortcode_3', 'educawa_shortcode_get_lesson_title' );
function educawa_shortcode_get_lesson_title() {
		$postid=get_the_id();
		$less_id = get_post_meta( $postid, 'lesson_id', true );	
		$lessontitle=get_the_title($less_id);
		
		return $lessontitle;	
}

/* elementor pro + ele custom skin get course materials : excerpt course */
add_shortcode( 'educawa_shortcode_4', 'educawa_shortcode_get_course_materials' );
function educawa_shortcode_get_course_materials() {
	$postid=get_the_id();
	$user_id=1;
	$materials = learndash_elementor_get_step_material( $user_id, $postid );
	return $materials ;	
}

/**
* Example usage for learndash_add_meta_boxes action.
*/

add_action( 'add_meta_boxes', 'learndash_educawa_topic_add_meta_box' );
function learndash_educawa_topic_add_meta_box(){	
	add_meta_box( 
		'learndash-educawa-topic-meta-box', 
		'Paramètres Educawa',
		'learndash_educawa_topic_output_meta_box', 
		'sfwd-topic', 
		'advanced', 
		'high',
		array()
	);
}

function learndash_educawa_topic_output_meta_box($args){
	$post_id       = get_the_ID();
	$post 		   = get_post( $post_id );

	$topic_free_access  = get_post_meta( $post_id, '_educawa_free_topic', true );

	wp_nonce_field( 'learndash_course_educawa_save', 'learndash_course_educawa_nonce' ); 
	
	?>
	<style>
		#learndash-educawa-topic-meta-box{
			display:block !important;
		}
	</style>
	<div class="sfwd_input">
			<span class="sfwd_option_label" style="text-align:right;vertical-align:top;">
				<a class="sfwd_help_text_link" style="cursor:pointer;" title="Click pour Aide!" onclick="toggleVisibility('educawa_free_topic');"><img src="<?php echo LEARNDASH_LMS_PLUGIN_URL . 'assets/images/question.png' ?>">
				<label class="sfwd_label textinput"><?php echo "Chapitre Accès libre"; ?></label></a>
			</span>
			<span class="sfwd_option_input">
				<div class="sfwd_option_div">
					<input type="hidden" name="educawa_free_topic" value="0">
					<input type="checkbox" name="educawa_free_topic" value="1" <?php checked( $topic_free_access, 1, true ); ?>>
				</div>
				<div class="sfwd_help_text_div" style="display:none" id="educawa_free_topic">
					<label class="sfwd_help_text"><?php printf('Activer cette option permet l\'accès libre au chapitre', LearnDash_Custom_Label::label_to_lower( 'topic' ) ) ; ?></label>
				</div>
			</span>
			<p style="clear:left"></p>
		</div>
	<?php
}

add_action( 'save_post', 'learndash_educawa_topic_save_meta_box', 10, 3 );
function learndash_educawa_topic_save_meta_box( $post_id, $post, $update ){
	if ( ! in_array( $post->post_type, array( 'sfwd-topic') ) ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['learndash_course_educawa_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['learndash_course_educawa_nonce'], 'learndash_course_educawa_save' ) ) {
		wp_die( __( 'Cheatin\' huh?' ) );
	}
	update_post_meta( $post_id, '_educawa_free_topic', wp_filter_kses( $_POST['educawa_free_topic'] ) );	
}
