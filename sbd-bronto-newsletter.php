<?php
/**
* Plugin Name: Bronto Newsletter
* Plugin URI: https://client.scottishbordersdesign.co.uk/scripts/20/Wordpress+Bronto+Newsletter+Signup.html
* Description: Get new bronto subscribers from your wordpress website.
* Version: 1.0.0
* Author: Scottish Borders Design
* Author URI: http://scottishbordersdesign.co.uk/
* License: GPL2
*/

defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

add_action('wp_footer', 'insertSignupForm');
add_action('admin_post_add_subscriber', 'addSubscriber');
add_action('admin_menu', 'bronto_menu');
add_action('admin_init', 'bronto_settings');

function bronto_menu() {
	$url = plugins_url();
	add_menu_page('Bronto Settings', 'Bronto Settings', 'administrator', 'bronto-settings', 'bronto_settings_page', plugins_url( 'assets/25x25-icon.png', __FILE__ ));
}

function bronto_settings() {
	register_setting( 'bronto-settings-group', 'api_key' );
	register_setting( 'bronto-settings-group', 'list_id' );
	register_setting( 'bronto-settings-group', 'customize_html' );
}

function testconnection($token){
	include( plugin_dir_path( __FILE__ ) . 'bronto.inc.php');
	$bronto = new brontoEmailSender;
	if ($bronto->testConnection($token)) {
		echo "Connection Successful!";
	} else {
		echo "Connection Failed!";
	}
}

function bronto_settings_page() {
	if (isset($_POST['test_con'])) {
		testconnection(get_option('api_key'));
	}
	?>
	<style>
	.form-table th{
		vertical-align: middle;
	}
	</style>
	<div class="wrap">
		<img style="display: inline;float: left;margin-top: -30px;" src="<?php echo plugins_url( 'assets/100x100-icon.png', __FILE__ );?>" alt=""><h2> Bronto Settings</h2>
		 
		<form method="post" action="options.php">
			<?php settings_fields( 'bronto-settings-group' ); ?>
			<?php do_settings_sections( 'bronto-settings-group' ); ?>
			<table class="form-table">
				<tr valign="middle">
					<th scope="row">API Key</th>
					<td>
						<input type="text" name="api_key" style="width:100%;" value="<?php echo esc_attr( get_option('api_key') ); ?>" />
					</td>
					<td width="350px">
						<p>You can get your Bronto API key from Bronto Dashboard &gt; Home (Menu Item) &gt; Settings &gt; Data Exchange.</p>
						<p>If a key is not visible you may need to create one, please see their documentation on how to do this.</p>
					</td>
				</tr>

				<tr valign="middle">
					<th scope="row">List ID</th>
					<td>
						<input type="text" name="list_id" style="width:100%;" value="<?php echo esc_attr( get_option('list_id') ); ?>" />
					</td>
					<td width="350px">
						<p>To get your list ID go to Bronto Dashboard &gt; Tables (Menu Item) &gt; Click on the list you want people added to &gt; Scroll to the bottom of the page and in the bottom right corner you will see <b>List API ID</b> copy the entire string after it it will look simmilar to this <i>e8s62fe6-df54-4df8-asd2-5e46cf0c7a5</i></p>
						<p>If no lists are visible - you may need to create one.</p>
						<p><strong>For multiple lists seperate them by a comma ',' (NO SPACES)</strong></p>
					</td>
				</tr>

				<tr valign="middle">
					<th scope="row">Customize HTML</th>
					<td>
						<textarea name="customize_html" id="customize_html" style="width:100%;" id="" cols="30" rows="15">
							<?php
							$customHTML = get_option('customize_html');
								if (empty( $customHTML )) {
									?>
	<label for="newsletter"> Sign Up for Promotions &amp; Updates</label>

	<div class="input-group">
	    <input type="email" name="email" id="bronto-newsletter" title="Enter Email Address" class="input-text required-entry validate-email blur">
	</div>

	<button type="submit" class="button button-primary">
	    <span>
	        <span>
	            Subscribe
	        </span>
	    </span>
	</button>
										<?php
									} else {
										echo esc_attr( get_option('customize_html') );
									}
								?>
							</textarea>
						</td>
						<td width="350px">
							<p>You can change the html that the form uses (please ensure all IDs are kept or the signup wont work.)</p>
							<p>If your custom form stops working, you can reset it to the original form by clicking <a href='#' onClick='jQuery("#customize_html").val($html);return false;'>here</a></p>
						</td>
					</tr>

				<tr valign="middle">
					<th scope="row">Test Connection</th>
					<td>
						<?php
						if (!$_GET['settings-updated'] == 'true') {
							$status = "disabled=disabled";
						} else {
							$status ='';
						}
						?>
						<input type="button" name="test_con" id="test_con" style="width:100%;" value="Test Connection" onClick="testConnection();return false;" <?php echo $status;?> />
						<script>
						function testConnection(){
							jQuery('#test_con').val("Just a moment ...");
					        jQuery.ajax({
					            url: 'admin.php?page=bronto-settings',
					            type: "POST",
					            data: {test_con:'test_con'},
					            success: function(data, textStatus, jqXHR) {
					                var n = data.search("Uncaught SoapFault");
					                if (n = 0) {
					                	alert("Error with API Key :(");
					                		Query('#test_con').val("Test Connection");
					                } else {
					                	jQuery('#test_con').val("Awesome :)");
					                	alert("A Connection was made (Awesome!)");
					                }
					            },
					            error: function(jqXHR, textStatus, errorThrown) {
					                alert(errorThrown);
					                Query('#test_con').val("Test Connection");
					            }
					        });
						}
						</script>
					</td>
					<td width="350px">
						<p>After you have saved this page - click the test connection button to ensure that your API key is valid</p>
					</td>
				</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>

		<br />
		<center>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="KZX6WZ5XL7DM6">
				<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
			</form>

			<small><a href="http://scottishbordersdesign.co.uk" target=_blank>&copy; Scottish Borders Design 2015</a></small>
		</center>

		<script>
	$html = '<label for="newsletter"> Sign Up for Promotions &amp; Updates</label>\r \
	\
	<div class="input-group">\
	    <input type="email" name="email" id="bronto-newsletter" title="Enter Email Address" class="input-text required-entry validate-email blur">\
	</div>\
	\
	<button type="submit" class="button button-primary">\
	    <span>\
	        <span>\
	            Subscribe\
	        </span>\
	    </span>\
	</button>'
		</script>
		<?php
}

function addSubscriber(){
	status_header(200);
	if ($_POST['action'] == 'add_subscriber') {
		$lists = explode(',',get_option('list_id'));
		$lists = array_values($lists);
		array_filter(array_map('trim', $lists));
		include( plugin_dir_path( __FILE__ ) . 'bronto.inc.php');
		$bronto = new brontoEmailSender;
		$email = $_POST['email'];
		$bronto->checkEmail(htmlentities($email), htmlentities($email));
		$bronto->addContact(htmlentities($email), $lists, // lists
		            get_option('api_key')
		            );
	} else{
		echo "Error!";
	}
}

function insertSignupForm(){
	$options['api_key'] = get_option('api_key');
	$options['list_id'] = get_option('list_id');
	$error = 'n';
	?>
	<style>
		.info, .success, .warning, .error, .validation {
		border: 1px solid;
		margin: 10px 0px;
		padding:15px 10px 15px 50px;
		background-repeat: no-repeat;
		background-position: 10px center;
		}
		.info {
		color: #00529B;
		background-color: #BDE5F8;
		}
		.success {
		color: #4F8A10;
		background-color: #DFF2BF;
		}
		.warning {
		color: #9F6000;
		background-color: #FEEFB3;
		}
		.error {
		color: #D8000C;
		background-color: #FFBABA;
		}
	</style>

	<?php
	if (empty($options['api_key']) && current_user_can('level_10') ) {
		echo "<div class='error'>Please check your API key isnt empty</div>";
		$error = 'y';
	}

	if (empty($options['list_id']) && current_user_can('level_10')) {
		echo "<div class='error'>Please check your List ID isnt empty</div>";
		$error = 'y';
	}

	if ($error == 'n') {
	?>
	<div class="block block-subscribe">

	    <form onSubmit="return false;" action="<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-post.php" method="POST" id="bronto-newsletter-form" novalidate="">
	    	<input type="hidden" name="action" value="add_subscriber">
	    	<?php
	    	$customhtml2 = get_option('customize_html');
	    	if (empty( $customhtml2 )) {
	    		?>
				<label for="newsletter"> Sign Up for Promotions &amp; Updates</label>

				<div class="input-group">
				    <input type="email" name="email" id="bronto-newsletter" title="Enter Email Address" class="input-text required-entry validate-email blur">
				</div>

				<button type="submit" class="button button-primary">
				    <span>
				        <span>
				            Subscribe
				        </span>
				    </span>
				</button>							
	    		<?php
	    	} else {
				echo get_option('customize_html');
	    	}
			?>
	    </form>
		<div id="bronto-validate-email" style="display:none;">&nbsp;</div>
	</div>

	<script>
	function validateEmail(email) {
	    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    return re.test(email);
	}

	jQuery("#bronto-newsletter-form").submit(function(e) {
	    jQuery("#bronto-validate-email").slideUp();
	    if (!validateEmail(jQuery('#bronto-newsletter').val())) {
	        jQuery("#bronto-validate-email")
	            .html('<div class="warning">Your email address is invalid!</div>')
	            .slideDown()
	            .delay(10000)
	            .slideUp();

	    } else {
	        jQuery("#bronto-validate-email")
	            .html("<img style='width:90px;' src='<?php bloginfo( 'wpurl' ); ?>/wp-content/plugins/sbd-bronto-newsletter/assets/loading.gif' alt='Loading ...'>")
	            .slideDown()
	            .delay(10000)
	            .slideUp();
	        var postData = jQuery(this).serializeArray();
	        var formURL = jQuery(this).attr("action");
	        jQuery.ajax({
	            url: formURL,
	            type: "POST",
	            data: postData,
	            success: function(data, textStatus, jqXHR) {
	                jQuery('#bronto-validate-email').html(data);
	                jQuery('#bronto-newsletter-form').slideUp();
	                jQuery("#bronto-validate-email")
	                    .html('<div class="success">' + data + '</div>')
	                    .slideDown()
	                    .delay(10000)
	                    .slideUp();
	            },
	            error: function(jqXHR, textStatus, errorThrown) {
	                // It failed  
	                // AHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHH!!!!!
	                jQuery("#bronto-validate-email")
	                    .html('<div class="error">Something went wrong, please try again!</div>')
	                    .slideDown()
	                    .delay(10000)
	                    .slideUp();
	            }
	        });
	        e.preventDefault();
	        e.unbind();
	    }
	});
	</script>
	<?php
	}
}