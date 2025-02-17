<?php
/**
 * Woo Address Book
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address-book.php.
 *
 * HOWEVER, on occasion Woo Address Book will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package WooCommerce Address Book/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$wc_address_book = WC_Address_Book::get_instance();

$woo_address_book_customer_id           = get_current_user_id();
$woo_address_book_customer              = new WC_Customer( $woo_address_book_customer_id );
$woo_address_book_billing_address_book  = $wc_address_book->get_address_book( $woo_address_book_customer, 'billing' );
$woo_address_book_shipping_address_book = $wc_address_book->get_address_book( $woo_address_book_customer, 'shipping' );

// Do not display on address edit pages.
if ( ! $type ) {
	if ( $wc_address_book->get_wcab_option( 'billing_enable' ) === true ) {

		// Hide the billing address book if there are no addresses to show and no ability to add new ones.
		$woo_address_book_count_section = count( $woo_address_book_billing_address_book['addresses'] );
		$woo_address_book_save_limit    = get_option( 'woo_address_book_billing_save_limit', 0 );

		if ( 1 == $woo_address_book_save_limit && $woo_address_book_count_section <= 1 ) {
			$woo_address_book_hide_billing_address_book = true;
		} else {
			$woo_address_book_hide_billing_address_book = false;
		}

		if ( ! $woo_address_book_hide_billing_address_book ) {
			?>

			<div class="address_book billing_address_book" data-addresses="<?php echo esc_attr( $woo_address_book_count_section ); ?>" data-limit="<?php echo esc_attr( $woo_address_book_save_limit ); ?>">
				<header>
					<h3><?php esc_html_e( 'Billing Address Book', 'woo-address-book' ); ?></h3>
					<?php
					// Add link/button to the my accounts page for adding addresses.
					$wc_address_book->add_additional_address_button( 'billing' );
					?>
				</header>

				<p class="myaccount_address">
					<?php
					$woo_address_book_billing_description = esc_html( __( 'The following billing addresses are available during the checkout process. ', 'woo-address-book' ) );

					if ( $woo_address_book_save_limit > 1 ) {
						$woo_address_book_billing_description .= ' ' . esc_html(
							sprintf(
								/* translators: %1s: The number of addresses that can be saved. */
								_n(
									'You can save a maximum of %1s address in addition to the default.',
									'You can save a maximum of %1s addresses in addition to the default.',
									$woo_address_book_save_limit - 1,
									'woo-address-book'
								),
								$woo_address_book_save_limit - 1
							)
						);

						if ( 0 === $woo_address_book_save_limit - $woo_address_book_count_section ) {
							$woo_address_book_billing_description .= ' <strong>' . esc_html( __( 'You have reached your saved address limit. ', 'woo-address-book' ) ) . '</strong>';
						}
					}

					/**
					 * Filter the billing address book description.
					 *
					 * @since 3.0.0
					 * @param string $woo_address_book_shipping_description The shipping address book description.
					 * @param int    $woo_address_book_save_limit The shipping address book save limit.
					 * @param int    $woo_address_book_count_section The shipping address book count.
					 * @return string
					 */
					echo apply_filters( 'woo_address_book_billing_description', $woo_address_book_billing_description, $woo_address_book_save_limit, $woo_address_book_count_section ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped It is escaped when building the variable.

					?>
				</p>
				<div class="addresses address-book">
					<?php

					foreach ( $woo_address_book_billing_address_book['addresses'] as $woo_address_book_name => $woo_address_book_fields ) {
						/**
						 * Filter the billing address before formatting.
						 *
						 * This is a core WooCommerce filter that we are also using here for consistent formatting.
						 */
						$woo_address_book_address = apply_filters(
							'woocommerce_my_account_my_address_formatted_address',
							$woo_address_book_fields,
							$woo_address_book_customer_id,
							$woo_address_book_name
						);

						$woo_address_book_formatted_address = WC()->countries->get_formatted_address( $woo_address_book_address );

						$woo_address_book_address_default = ! empty( $woo_address_book_billing_address_book['default'] ) && $woo_address_book_billing_address_book['default'] === $woo_address_book_name;
						?>
						<div class="wc-address-book-address<?php echo esc_attr( $woo_address_book_address_default ? ' wc-address-book-address-default' : '' ); ?>">
							<address>
								<?php echo wp_kses( $woo_address_book_formatted_address, array( 'br' => array() ) ); ?>
							</address>
							<div class="wc-address-book-meta">
								<a href="<?php echo esc_url( $wc_address_book->get_address_book_endpoint_url( $woo_address_book_name, 'billing' ) ); ?>" class="wc-address-book-edit button wp-element-button"><?php echo esc_html__( 'Edit', 'woo-address-book' ); ?></a>
								<?php
								if ( $woo_address_book_address_default ) :
									?>
									<button type="button" data-wc-address-type="billing" data-wc-address-name="<?php echo esc_attr( $woo_address_book_name ); ?>" class="wc-address-book-delete button wp-element-button" disabled style="display: none;"><?php echo esc_html__( 'Delete', 'woo-address-book' ); ?></button>
									<button type="button" data-wc-address-type="billing" data-wc-address-name="<?php echo esc_attr( $woo_address_book_name ); ?>" class="wc-address-book-make-default button wp-element-button" disabled style="display: none;"><?php echo esc_html__( 'Set as Default', 'woo-address-book' ); ?></button>
									<span class="wc-address-book-default-text"><?php _e( 'Default', 'woo-address-book' ); ?></span>
									<?php
								else :
									?>
									<button type="button" data-wc-address-type="billing" data-wc-address-name="<?php echo esc_attr( $woo_address_book_name ); ?>" class="wc-address-book-delete button wp-element-button"><?php echo esc_html__( 'Delete', 'woo-address-book' ); ?></button>
									<button type="button" data-wc-address-type="billing" data-wc-address-name="<?php echo esc_attr( $woo_address_book_name ); ?>" class="wc-address-book-make-default button wp-element-button"><?php echo esc_html__( 'Set as Default', 'woo-address-book' ); ?></button>
									<?php
								endif;
								?>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
		}
	}

	if ( $wc_address_book->get_wcab_option( 'shipping_enable' ) === true ) {

		// Hide the billing address book if there are no addresses to show and no ability to add new ones.
		$woo_address_book_count_section = count( $woo_address_book_shipping_address_book['addresses'] );
		$woo_address_book_save_limit    = intval( get_option( 'woo_address_book_shipping_save_limit', 0 ) );

		if ( 1 == $woo_address_book_save_limit && $woo_address_book_count_section <= 1 ) {
			$woo_address_book_hide_shipping_address_book = true;
		} else {
			$woo_address_book_hide_shipping_address_book = false;
		}

		if ( ! $woo_address_book_hide_shipping_address_book ) {
			?>

			<div class="address_book shipping_address_book" data-addresses="<?php echo esc_attr( $woo_address_book_count_section ); ?>" data-limit="<?php echo esc_attr( $woo_address_book_save_limit ); ?>">

				<header>
					<h3><?php esc_html_e( 'Shipping Address Book', 'woo-address-book' ); ?></h3>
					<?php
					// Add link/button to the my accounts page for adding addresses.
					$wc_address_book->add_additional_address_button( 'shipping' );
					?>
				</header>

				<p class="myaccount_address">
					<?php
					$woo_address_book_shipping_description = esc_html( __( 'The following shipping addresses are available during the checkout process.', 'woo-address-book' ) );

					if ( $woo_address_book_save_limit > 1 ) {
						$woo_address_book_shipping_description .= ' ' . esc_html(
							sprintf(
								/* translators: %1s: The number of addresses that can be saved. */
								_n(
									'You can save a maximum of %1s address in addition to the default.',
									'You can save a maximum of %1s addresses in addition to the default.',
									$woo_address_book_save_limit - 1,
									'woo-address-book'
								),
								$woo_address_book_save_limit - 1
							)
						);

						if ( 0 === $woo_address_book_save_limit - $woo_address_book_count_section ) {
							$woo_address_book_shipping_description .= ' <strong style="color: black">' . esc_html( __( 'You have reached your saved address limit. ', 'woo-address-book' ) ) . '</strong>';
						}
					}

					/**
					 * Filter the shipping address book description.
					 *
					 * @since 3.0.0
					 * @param string $woo_address_book_shipping_description The shipping address book description.
					 * @param int    $woo_address_book_save_limit The shipping address book save limit.
					 * @param int    $woo_address_book_count_section The shipping address book count.
					 * @return string
					 */
					echo apply_filters( 'woo_address_book_shipping_description', $woo_address_book_shipping_description, $woo_address_book_save_limit, $woo_address_book_count_section ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped It is escaped when building the variable.

					?>
				</p>

				<?php
				if ( ! wc_ship_to_billing_address_only() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) {
					echo '<div class="addresses address-book">';

					foreach ( $woo_address_book_shipping_address_book['addresses'] as $woo_address_book_name => $woo_address_book_fields ) {
						/**
						 * Filter the shipping address before formatting.
						 *
						 * This is a core WooCommerce filter that we are also using here for consistent formatting.
						 */
						$woo_address_book_address = apply_filters(
							'woocommerce_my_account_my_address_formatted_address',
							$woo_address_book_fields,
							$woo_address_book_customer_id,
							$woo_address_book_name
						);

						$woo_address_book_formatted_address = WC()->countries->get_formatted_address( $woo_address_book_address );

						$woo_address_book_address_default = ! empty( $woo_address_book_shipping_address_book['default'] ) && $woo_address_book_shipping_address_book['default'] === $woo_address_book_name;
						?>
						<div class="wc-address-book-address<?php echo esc_attr( $woo_address_book_address_default ? ' wc-address-book-address-default' : '' ); ?>">
							<address>
								<?php echo wp_kses( $woo_address_book_formatted_address, array( 'br' => array() ) ); ?>
							</address>
							<div class="wc-address-book-meta">
								<a href="<?php echo esc_url( $wc_address_book->get_address_book_endpoint_url( $woo_address_book_name, 'shipping' ) ); ?>" class="wc-address-book-edit button wp-element-button"><?php echo esc_html__( 'Edit', 'woo-address-book' ); ?></a>
								<?php
								if ( $woo_address_book_address_default ) :
									?>
									<button type="button" data-wc-address-type="shipping" data-wc-address-name="<?php echo esc_attr( $woo_address_book_name ); ?>" class="wc-address-book-delete button wp-element-button" disabled style="display: none;"><?php echo esc_html__( 'Delete', 'woo-address-book' ); ?></button>
									<button type="button" data-wc-address-type="shipping" data-wc-address-name="<?php echo esc_attr( $woo_address_book_name ); ?>" class="wc-address-book-make-default button wp-element-button" disabled style="display: none;"><?php echo esc_html__( 'Set as Default', 'woo-address-book' ); ?></button>
									<span class="wc-address-book-default-text"><?php _e( 'Default', 'woo-address-book' ); ?></span>
									<?php
								else :
									?>
									<button type="button" data-wc-address-type="shipping" data-wc-address-name="<?php echo esc_attr( $woo_address_book_name ); ?>" class="wc-address-book-delete button wp-element-button"><?php echo esc_html__( 'Delete', 'woo-address-book' ); ?></button>
									<button type="button" data-wc-address-type="shipping" data-wc-address-name="<?php echo esc_attr( $woo_address_book_name ); ?>" class="wc-address-book-make-default button wp-element-button"><?php echo esc_html__( 'Set as Default', 'woo-address-book' ); ?></button>
									<?php
								endif;
								?>
							</div>
						</div>
						<?php
					}

					echo '</div>';
				}
				?>
			</div>
			<?php
		}
	}

	if ( $wc_address_book->get_wcab_option( 'tools_enable', 'no' ) === true && ( $wc_address_book->get_wcab_option( 'billing_enable' ) === true || $wc_address_book->get_wcab_option( 'shipping_enable' ) === true ) ) {
		?>
		<div class="address_book address_book_tools">
			<header>
				<h3><?php esc_html_e( 'Address Book Tools', 'woo-address-book' ); ?></h3>
			</header>
			<div class="woocommerce-addresses woo-address-book-import-export">
				<?php
				if ( $wc_address_book->get_wcab_option( 'billing_enable' ) === true ) {
					?>
				<div class="woocommerce-address">
					<h4 class="wc-address-book-import"><?php echo esc_html_e( 'Import new Billing Addresses', 'woo-address-book' ); ?></h4>
					<form method="post" enctype="multipart/form-data" id="wc_address_book_upload_billing" name="wc_address_book_upload_billing">
						<?php
						wp_nonce_field( 'woo-address-book-billing-csv-import', 'woo-address-book_nonce' );
						?>
						<div class="wc-address-book-file-select wc-address-book-form-section">
							<input type="file" accept=".csv" id="wc_address_book_upload_billing_csv" name="wc_address_book_upload_billing_csv">
						</div>
						<div class="wc-address-book-file-submit wc-address-book-form-section">
							<input class="alt billing-import-btn" style="display: none;" type="submit" value="<?php echo esc_attr__( 'Import', 'woo-address-book' ); ?>">
						</div>
					</form>
					<hr>
					<p><strong><?php $wc_address_book->add_wc_address_book_export_button( 'billing' ); ?></strong></p>
				</div>
					<?php
				}
				?>
				<?php
				if ( $wc_address_book->get_wcab_option( 'shipping_enable' ) === true ) {
					?>

				<div class="woocommerce-address">
					<h4 class="wc-address-book-import"><?php echo esc_html_e( 'Import new Shipping Addresses', 'woo-address-book' ); ?></h4>
					<form method="post" enctype="multipart/form-data" id="wc_address_book_upload_shipping" name="wc_address_book_upload_shipping">
						<?php
						wp_nonce_field( 'woo-address-book-shipping-csv-import', 'woo-address-book_nonce' );
						?>
						<div class="wc-address-book-file-select wc-address-book-form-section">
							<input type="file" accept=".csv" id="wc_address_book_upload_shipping_csv" name="wc_address_book_upload_shipping_csv">
						</div>
						<div class="wc-address-book-file-submit wc-address-book-form-section">
							<input class="alt shipping-import-btn" style="display: none;" type="submit" value="<?php echo esc_attr__( 'Import', 'woo-address-book' ); ?>">
						</div>
					</form>
					<hr>
					<p><strong><?php $wc_address_book->add_wc_address_book_export_button( 'shipping' ); ?></strong></p>
				</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}
}
