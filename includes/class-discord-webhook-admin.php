<?php
/**
 * Discord Webhook Admin
 *
 * @author      Monster2408
 * @license     GPLv2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class for the admin settings of Discord Webhook.
 */
class Discord_Webhook_Admin {
	/**
	 * Inits the admin panel.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_init', array( $this, 'add_privacy_policy_content' ) );
	}

	/**
	 * Adds the menu Settings > Discord Webhook.
	 */
	public function add_menu() {
		add_options_page(
			__( 'Discord Webhook Settings', 'discord-webhook' ),
			__( 'Discord Webhook', 'discord-webhook' ),
			'manage_options',
			'discord-webhook',
			array( $this, 'settings_page_html' )
		);
	}

	/**
	 * Generates the settings page.
	 */
	public function settings_page_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		settings_errors( 'discord-webhook-messages' );
		?>

		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
			<?php
			settings_fields( 'discord-webhook' );
			do_settings_sections( 'discord-webhook' );
			submit_button( __( 'Save Settings', 'discord-webhook' ) );
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Inits the settings page.
	 */
	public function settings_init() {
		add_settings_section(
			'discord_webhook_settings',
			esc_html__( 'General', 'discord-webhook' ),
			array( $this, 'settings_callback' ),
			'discord-webhook'
		);

		add_settings_section(
			'discord_webhook_post_settings',
			esc_html__( 'Posts', 'discord-webhook' ),
			array( $this, 'settings_callback' ),
			'discord-webhook'
		);

		add_settings_field(
			'discord_webhook_bot_username',
			esc_html__( 'Bot Username', 'discord-webhook' ),
			array( $this, 'print_bot_username_field' ),
			'discord-webhook',
			'discord_webhook_settings'
		);

		add_settings_field(
			'discord_webhook_avatar_url',
			esc_html__( 'Avatar URL', 'discord-webhook' ),
			array( $this, 'print_avatar_url_field' ),
			'discord-webhook',
			'discord_webhook_settings'
		);

		add_settings_field(
			'discord_webhook_bot_token',
			esc_html__( 'Discord Bot Token', 'discord-webhook' ),
			array( $this, 'print_bot_token_field' ),
			'discord-webhook',
			'discord_webhook_settings'
		);

		add_settings_field(
			'discord_webhook_webhook_url',
			esc_html__( 'Discord Webhook URL', 'discord-webhook' ),
			array( $this, 'print_webhook_url_field' ),
			'discord-webhook',
			'discord_webhook_settings'
		);

		add_settings_field(
			'discord_webhook_logging',
			esc_html__( 'Logging', 'discord-webhook' ),
			array( $this, 'print_logging_field' ),
			'discord-webhook',
			'discord_webhook_settings'
		);

		add_settings_field(
			'discord_webhook_mention_everyone',
			esc_html__( 'Mention Everyone', 'discord-webhook' ),
			array( $this, 'print_mention_everyone_field' ),
			'discord-webhook',
			'discord_webhook_settings'
		);

		add_settings_field(
			'discord_webhook_disable_embed',
			esc_html__( 'Disable Embed Content', 'discord-webhook' ),
			array( $this, 'print_disable_embed_field' ),
			'discord-webhook',
			'discord_webhook_settings'
		);

		add_settings_field(
			'discord_webhook_post_webhook_url',
			esc_html__( 'Discord Posts Webhook URL', 'discord-webhook' ),
			array( $this, 'print_post_webhook_url_field' ),
			'discord-webhook',
			'discord_webhook_post_settings'
		);

		add_settings_field(
			'discord_webhook_message_format',
			esc_html__( 'Post Message Format', 'discord-webhook' ),
			array( $this, 'print_message_format_field' ),
			'discord-webhook',
			'discord_webhook_post_settings'
		);

		// Enable support for Contact Form 7 if it's active.
		if ( class_exists( 'WPCF7' ) ) {
			add_settings_section(
				'discord_webhook_cf7_settings',
				esc_html__( 'Contact Forms 7 Support', 'discord-webhook' ),
				array( $this, 'settings_callback' ),
				'discord-webhook'
			);

			add_settings_field(
				'discord_webhook_enabled_for_cf7',
				esc_html__( 'Enable', 'discord-webhook' ),
				array( $this, 'print_enabled_for_cf7_field' ),
				'discord-webhook',
				'discord_webhook_cf7_settings'
			);

			if ( 'yes' === get_option( 'discord_webhook_enabled_for_cf7' ) ) {
				add_settings_field(
					'discord_webhook_cf7_webhook_url',
					esc_html__( 'Discord CF7 Webhook URL', 'discord-webhook' ),
					array( $this, 'print_cf7_webhook_url_field' ),
					'discord-webhook',
					'discord_webhook_cf7_settings'
				);

				register_setting( 'discord-webhook', 'discord_webhook_cf7_webhook_url' );
			}

			register_setting( 'discord-webhook', 'discord_webhook_enabled_for_cf7' );
		}

		// Enable support for Jetpack Contact Form if it's active.
		if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'contact-form' ) ) {
			add_settings_section(
				'discord_webhook_jetpack_settings',
				esc_html__( 'Jetpack Support', 'discord-webhook' ),
				array( $this, 'settings_callback' ),
				'discord-webhook'
			);

			add_settings_field(
				'discord_webhook_enabled_for_jetpack_cf',
				esc_html__( 'Enable', 'discord-webhook' ),
				array( $this, 'print_enabled_for_jetpack_cf_field' ),
				'discord-webhook',
				'discord_webhook_jetpack_settings'
			);

			if ( 'yes' === get_option( 'discord_webhook_enabled_for_jetpack_cf' ) ) {
				add_settings_field(
					'discord_webhook_jetpack_webhook_url',
					esc_html__( 'Discord Jetpack Webhook URL', 'discord-webhook' ),
					array( $this, 'print_jetpack_webhook_url_field' ),
					'discord-webhook',
					'discord_webhook_jetpack_settings'
				);

				register_setting( 'discord-webhook', 'discord_webhook_jetpack_webhook_url' );
			}

			register_setting( 'discord-webhook', 'discord_webhook_enabled_for_jetpack_cf' );
		}

		// Enable support for Contact Form 7 if it's active.
		if ( class_exists( 'GFForms' ) ) {
			add_settings_section(
				'discord_webhook_gf_settings',
				esc_html__( 'Gravity Forms Support', 'discord-webhook' ),
				array( $this, 'settings_callback' ),
				'discord-webhook'
			);

			add_settings_field(
				'discord_webhook_enabled_for_gf',
				esc_html__( 'Enable', 'discord-webhook' ),
				array( $this, 'print_enabled_for_gf_field' ),
				'discord-webhook',
				'discord_webhook_gf_settings'
			);

			if ( 'yes' === get_option( 'discord_webhook_gf_settings' ) ) {
				add_settings_field(
					'discord_webhook_gf_webhook_url',
					esc_html__( 'Discord GF Webhook URL', 'discord-webhook' ),
					array( $this, 'print_gf_webhook_url_field' ),
					'discord-webhook',
					'discord_webhook_gf_settings'
				);

				register_setting( 'discord-webhook', 'discord_webhook_gf_webhook_url' );
			}

			register_setting( 'discord-webhook', 'discord_webhook_enabled_for_gf' );
		}

		// Enable support for WooCommerce if it's active.
		if ( class_exists( 'WooCommerce' ) ) {
			add_settings_section(
				'discord_webhook_woocommerce_settings',
				esc_html__( 'WooCommerce Support', 'discord-webhook' ),
				array( $this, 'settings_callback' ),
				'discord-webhook'
			);

			add_settings_field(
				'discord_webhook_enabled_for_woocommerce_products',
				esc_html__( 'Send Products', 'discord-webhook' ),
				array( $this, 'print_enabled_for_woocommerce_products_field' ),
				'discord-webhook',
				'discord_webhook_woocommerce_settings'
			);

			if ( 'yes' === get_option( 'discord_webhook_enabled_for_woocommerce_products' ) ) {
				add_settings_field(
					'discord_webhook_product_webhook_url',
					esc_html__( 'Discord Products Webhook URL', 'discord-webhook' ),
					array( $this, 'print_product_webhook_url_field' ),
					'discord-webhook',
					'discord_webhook_woocommerce_settings'
				);

				add_settings_field(
					'discord_webhook_product_message_format',
					esc_html__( 'Product Message Format', 'discord-webhook' ),
					array( $this, 'print_product_message_format_field' ),
					'discord-webhook',
					'discord_webhook_woocommerce_settings'
				);

				register_setting( 'discord-webhook', 'discord_webhook_product_webhook_url' );
				register_setting( 'discord-webhook', 'discord_webhook_product_message_format' );
			}

			add_settings_field(
				'discord_webhook_enabled_for_woocommerce',
				esc_html__( 'Send Orders', 'discord-webhook' ),
				array( $this, 'print_enabled_for_woocommerce_field' ),
				'discord-webhook',
				'discord_webhook_woocommerce_settings'
			);

			if ( 'yes' === get_option( 'discord_webhook_enabled_for_woocommerce' ) ) {
				add_settings_field(
					'discord_webhook_order_webhook_url',
					esc_html__( 'Discord Orders Webhook URL', 'discord-webhook' ),
					array( $this, 'print_order_webhook_url_field' ),
					'discord-webhook',
					'discord_webhook_woocommerce_settings'
				);

				add_settings_field(
					'discord_webhook_order_message_format',
					esc_html__( 'Order Message Format', 'discord-webhook' ),
					array( $this, 'print_order_message_format_field' ),
					'discord-webhook',
					'discord_webhook_woocommerce_settings'
				);

				register_setting( 'discord-webhook', 'discord_webhook_order_webhook_url' );
				register_setting( 'discord-webhook', 'discord_webhook_order_message_format' );
			}

			register_setting( 'discord-webhook', 'discord_webhook_enabled_for_woocommerce_products' );
			register_setting( 'discord-webhook', 'discord_webhook_enabled_for_woocommerce' );
		}

		add_settings_section(
			'discord_webhook_dank_meme_settings',
			esc_html__( 'Dank Memes', 'discord-webhook' ),
			array( $this, 'settings_callback' ),
			'discord-webhook'
		);

		add_settings_field(
			'discord_webhook_giphy_api_key',
			esc_html__( 'Giphy API Key', 'discord-webhook' ),
			array( $this, 'print_giphy_api_key_field' ),
			'discord-webhook',
			'discord_webhook_dank_meme_settings'
		);

		add_settings_field(
			'discord_webhook_send_dank_meme',
			esc_html__( 'Hit me!', 'discord-webhook' ),
			array( $this, 'print_send_dank_meme_field' ),
			'discord-webhook',
			'discord_webhook_dank_meme_settings'
		);

		register_setting( 'discord-webhook', 'discord_webhook_bot_username' );
		register_setting( 'discord-webhook', 'discord_webhook_avatar_url' );
		register_setting( 'discord-webhook', 'discord_webhook_bot_token' );
		register_setting( 'discord-webhook', 'discord_webhook_webhook_url' );
		register_setting( 'discord-webhook', 'discord_webhook_logging' );
		register_setting( 'discord-webhook', 'discord_webhook_mention_everyone' );
		register_setting( 'discord-webhook', 'discord_webhook_disable_embed' );
		register_setting( 'discord-webhook', 'discord_webhook_post_webhook_url' );
		register_setting( 'discord-webhook', 'discord_webhook_message_format' );
		register_setting( 'discord-webhook', 'discord_webhook_giphy_api_key' );
	}

	/**
	 * Prints the description in the settings page.
	 */
	public function settings_callback() {
		esc_html_e( 'Configure your Discord Webhook instance to write on your Discord server', 'discord-webhook' );
	}

	/**
	 * Prints the Bot Username settings field.
	 */
	public function print_bot_username_field() {
		$value = get_option( 'discord_webhook_bot_username' );

		echo '<input type="text" name="discord_webhook_bot_username" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . esc_html__( 'The username that you want to use for the bot on your Discord server.', 'discord-webhook' ) . '</span>';
	}

	/**
	 * Prints the Avatar URL settings field.
	 */
	public function print_avatar_url_field() {
		$value = get_option( 'discord_webhook_avatar_url' );

		echo '<input type="text" name="discord_webhook_avatar_url" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . esc_html__( 'The URL of the avatar that you want to use for the bot on your Discord server.', 'discord-webhook' ) . '</span>';
	}

	/**
	 * Prints the Bot Token settings field.
	 */
	public function print_bot_token_field() {
		$value = get_option( 'discord_webhook_bot_token' );

		echo '<input type="text" name="discord_webhook_bot_token" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The token of your Discord bot. %1$sLearn more%2$s', 'discord-webhook' ), '<a href="https://discordapp.com/developers/docs/intro">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_webhook_url_field() {
		$value = get_option( 'discord_webhook_webhook_url' );

		echo '<input type="text" name="discord_webhook_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'discord-webhook' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Logging settings field.
	 */
	public function print_logging_field() {
		$value = get_option( 'discord_webhook_logging' );

		echo '<input type="checkbox" name="discord_webhook_logging" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Save debug data to the PHP error log.', 'discord-webhook' ) . '</span>';
	}

	/**
	 * Prints the Mention Everyone settings field.
	 */
	public function print_mention_everyone_field() {
		$value = get_option( 'discord_webhook_mention_everyone' );

		echo '<input type="checkbox" name="discord_webhook_mention_everyone" value="yes"' . checked( 'yes', $value, false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Mention @everyone when sending the message to Discord.', 'discord-webhook' ) . '</span>';
	}

	/**
	 * Prints the Disable embed settings field.
	 */
	public function print_disable_embed_field() {
		$value = get_option( 'discord_webhook_disable_embed' );

		echo '<input type="checkbox" name="discord_webhook_disable_embed" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Disable the embed content added by Discord Webhook and use the default content automatically added by Discord.', 'discord-webhook' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_post_webhook_url_field() {
		$value = get_option( 'discord_webhook_post_webhook_url' );

		echo '<input type="text" name="discord_webhook_post_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'discord-webhook' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Message Format settings field.
	 */
	public function print_message_format_field() {
		$value       = get_option( 'discord_webhook_message_format' );
		$placeholder = __( '%author% just published the %post_type% %title% on their blog: %url%', 'discord-webhook' );

		echo '<textarea style="width:500px;height:150px;" name="discord_webhook_message_format" placeholder="' . esc_attr( $placeholder ) . '">' . esc_textarea( $value ) . '</textarea><br />';
		echo '<span class="description">' . esc_html__( 'Change the format of the message sent to Discord. The available placeholders are %post_type%, %title%, %author%, and %url%. HTML is not supported.', 'discord-webhook' ) . '</span>';
	}

	/**
	 * Prints the Enabled for Contact Form 7 settings field.
	 */
	public function print_enabled_for_cf7_field() {
		$value = get_option( 'discord_webhook_enabled_for_cf7' );

		echo '<input type="checkbox" name="discord_webhook_enabled_for_cf7" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Catch emails sent via Contact Form 7 and send them to Discord.', 'discord-webhook' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_cf7_webhook_url_field() {
		$value = get_option( 'discord_webhook_cf7_webhook_url' );

		echo '<input type="text" name="discord_webhook_cf7_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'discord-webhook' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Enabled for Jetpack Contact Form settings field.
	 */
	public function print_enabled_for_jetpack_cf_field() {
		$value = get_option( 'discord_webhook_enabled_for_jetpack_cf' );

		echo '<input type="checkbox" name="discord_webhook_enabled_for_jetpack_cf" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Catch emails sent via Jetpack Contact Form and send them to Discord.', 'discord-webhook' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_jetpack_webhook_url_field() {
		$value = get_option( 'discord_webhook_jetpack_webhook_url' );

		echo '<input type="text" name="discord_webhook_jetpack_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'discord-webhook' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Enabled for Gravity Forms settings field.
	 */
	public function print_enabled_for_gf_field() {
		$value = get_option( 'discord_webhook_enabled_for_gf' );

		echo '<input type="checkbox" name="discord_webhook_enabled_for_gf" value="yes"' . checked( $value, 'yes', false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Catch emails sent via Gravity Forms and send them to Discord.', 'discord-webhook' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_gf_webhook_url_field() {
		$value = get_option( 'discord_webhook_gf_webhook_url' );

		echo '<input type="text" name="discord_webhook_gf_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'discord-webhook' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Send Products settings field.
	 */
	public function print_enabled_for_woocommerce_products_field() {
		$value = get_option( 'discord_webhook_enabled_for_woocommerce_products' );

		echo '<input type="checkbox" name="discord_webhook_enabled_for_woocommerce_products" value="yes"' . checked( 'yes', $value, false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Write in Discord when a new WooCommerce product is published.', 'discord-webhook' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_product_webhook_url_field() {
		$value = get_option( 'discord_webhook_product_webhook_url' );

		echo '<input type="text" name="discord_webhook_product_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'discord-webhook' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Product Message Format settings field.
	 */
	public function print_product_message_format_field() {
		$value       = get_option( 'discord_webhook_product_message_format' );
		$placeholder = __( 'A new product is available in our store. Check it out!', 'discord-webhook' );

		echo '<textarea style="width:500px;height:150px;" name="discord_webhook_product_message_format" placeholder="' . esc_attr( $placeholder ) . '">' . esc_textarea( $value ) . '</textarea><br />';
		echo '<span class="description">' . esc_html__( 'Change the format of the message sent to Discord when a new product is published. The available placeholders are %title%, %url%, and %price%.', 'discord-webhook' ) . '</span>';
	}

	/**
	 * Prints the Send Orders settings field.
	 */
	public function print_enabled_for_woocommerce_field() {
		$value = get_option( 'discord_webhook_enabled_for_woocommerce' );

		echo '<input type="checkbox" name="discord_webhook_enabled_for_woocommerce" value="yes"' . checked( 'yes', $value, false ) . ' />';
		echo '<span class="description">' . esc_html__( 'Write in Discord when a new WooCommerce order is created.', 'discord-webhook' ) . '</span>';
	}

	/**
	 * Prints the Webhook URL settings field.
	 */
	public function print_order_webhook_url_field() {
		$value = get_option( 'discord_webhook_order_webhook_url' );

		echo '<input type="text" name="discord_webhook_order_webhook_url" value="' . esc_url( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'The webhook URL from your Discord server. %1$sLearn more%2$s', 'discord-webhook' ), '<a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks?page=2">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Order Message Format settings field.
	 */
	public function print_order_message_format_field() {
		$value       = get_option( 'discord_webhook_order_message_format' );
		$placeholder = __( 'Order #%order_number% by %order_customer% has been created. The order total is %order_total%.', 'discord-webhook' );

		echo '<textarea style="width:500px;height:150px;" name="discord_webhook_order_message_format" placeholder="' . esc_attr( $placeholder ) . '">' . esc_textarea( $value ) . '</textarea><br />';
		echo '<span class="description">' . esc_html__( 'Change the format of the message sent to Discord when a new order is created in WooCommerce. The available placeholders are %order_number%, %order_customer%, and %order_total%.', 'discord-webhook' ) . '</span>';
	}

	/**
	 * Prints the Giphy API Key settings field.
	 */
	public function print_giphy_api_key_field() {
		$value = get_option( 'discord_webhook_giphy_api_key' );

		echo '<input type="text" name="discord_webhook_giphy_api_key" value="' . esc_attr( $value ) . '" style="width:300px;margin-right:10px;" />';
		echo '<span class="description">' . sprintf( esc_html__( 'Your API key from Giphy. %1$sLearn more%2$s', 'discord-webhook' ), '<a href="https://developers.giphy.com/docs/#api-keys" target="_blank">', '</a>' ) . '</span>';
	}

	/**
	 * Prints the Hit Me! field.
	 */
	public function print_send_dank_meme_field() {
		echo '<a href="' . add_query_arg( 'dank_meme', 'yes' ) . '" title="' . esc_attr__( 'Send Dank Meme!', 'discord-webhook' ) . '" class="button primary">' . esc_html__( 'Send Dank Meme!', 'discord-webhook' ) . '</a>';
		echo '<br><br><span class="description">' . esc_html__( 'You do this at your own risk... there is no coming back from the dank world.', 'discord-webhook' ) . '</span>';
	}

	/**
	 * Adds some content to the Privacy Policy default content.
	 */
	public function add_privacy_policy_content() {
	    if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
	        return;
	    }

		$content = '';

		if ( 'yes' === get_option( 'discord_webhook_enabled_for_woocommerce' ) ) {
		    $content .= __( 'When you place an order on this site, we send your order details to discordapp.com.', 'discord-webhook' );
		}

		if ( 'yes' === get_option( 'discord_webhook_enabled_for_jetpack_cf' ) || 'yes' === get_option( 'discord_webhook_enabled_for_cf7' ) ) {
			$content .= __( 'When you use the contact forms on this site, we send their content to discordapp.com.', 'discord-webhook' );
		}

		if ( ! empty( $content ) ) {
			$content .= sprintf( ' ' . __( 'The discordapp.com privacy policy is <a href="%s" target="_blank">here</a>.', 'discord-webhook' ), 'https://discordapp.com/privacy' );
		}

	    wp_add_privacy_policy_content(
	        'Discord Webhook',
	        wp_kses_post( wpautop( $content, false ) )
	    );
	}
}

new Discord_Webhook_Admin();
