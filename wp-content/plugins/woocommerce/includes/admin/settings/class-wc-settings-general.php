<?php
/**
 * WooCommerce General Settings
 *
 * @author      WooThemes
 * @category    Admin
 * @package     WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Settings_General' ) ) :

/**
 * WC_Admin_Settings_General.
 */
class WC_Settings_General extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'general';
		$this->label = __( 'General', 'woocommerce' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {

		$currency_code_options = get_woocommerce_currencies();

		foreach ( $currency_code_options as $code => $name ) {
			$currency_code_options[ $code ] = $name . ' (' . get_woocommerce_currency_symbol( $code ) . ')';
		}

		$settings = apply_filters( 'woocommerce_general_settings', array(

			array( 'title' => __( 'General Options', 'woocommerce' ), 'type' => 'title', 'desc' => '', 'id' => 'general_options' ),

			array(
				'title'    => __( 'Base Location', 'woocommerce' ),
				'desc'     => __( 'This is the base location for your business. Tax rates will be based on this country.', 'woocommerce' ),
				'id'       => 'woocommerce_default_country',
				'css'      => 'min-width:350px;',
				'default'  => 'GB',
				'type'     => 'single_select_country',
				'desc_tip' =>  true,
			),

			array(
				'title'    => __( 'Selling Location(s)', 'woocommerce' ),
				'desc'     => __( 'This option lets you limit which countries you are willing to sell to.', 'woocommerce' ),
				'id'       => 'woocommerce_allowed_countries',
				'default'  => 'all',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width: 350px;',
				'desc_tip' =>  true,
				'options'  => array(
					'all'        => __( 'Sell to All Countries', 'woocommerce' ),
					'all_except' => __( 'Sell to All Countries, Except For&hellip;', 'woocommerce' ),
					'specific'   => __( 'Sell to Specific Countries', 'woocommerce' )
				)
			),

			array(
				'title'   => __( 'Sell to All Countries, Except For&hellip;', 'woocommerce' ),
				'desc'    => '',
				'id'      => 'woocommerce_all_except_countries',
				'css'     => 'min-width: 350px;',
				'default' => '',
				'type'    => 'multi_select_countries'
			),

			array(
				'title'   => __( 'Sell to Specific Countries', 'woocommerce' ),
				'desc'    => '',
				'id'      => 'woocommerce_specific_allowed_countries',
				'css'     => 'min-width: 350px;',
				'default' => '',
				'type'    => 'multi_select_countries'
			),

			array(
				'title'    => __( 'Shipping Location(s)', 'woocommerce' ),
				'desc'     => __( 'Choose which countries you want to ship to, or choose to ship to all locations you sell to.', 'woocommerce' ),
				'id'       => 'woocommerce_ship_to_countries',
				'default'  => '',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'desc_tip' => true,
				'options'  => array(
					''         => __( 'Ship to all countries you sell to', 'woocommerce' ),
					'all'      => __( 'Ship to all countries', 'woocommerce' ),
					'specific' => __( 'Ship to specific countries only', 'woocommerce' ),
					'disabled' => __( 'Disable shipping &amp; shipping calculations', 'woocommerce' ),
				)
			),

			array(
				'title'   => __( 'Ship to Specific Countries', 'woocommerce' ),
				'desc'    => '',
				'id'      => 'woocommerce_specific_ship_to_countries',
				'css'     => '',
				'default' => '',
				'type'    => 'multi_select_countries'
			),

			array(
				'title'    => __( 'Default Customer Location', 'woocommerce' ),
				'id'       => 'woocommerce_default_customer_address',
				'desc_tip' =>  __( 'This option determines a customers default location. The MaxMind GeoLite Database will be periodically downloaded to your wp-content directory if using geolocation.', 'woocommerce' ),
				'default'  => 'geolocation',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					''                 => __( 'No location by default', 'woocommerce' ),
					'base'             => __( 'Shop base address', 'woocommerce' ),
					'geolocation'      => __( 'Geolocate', 'woocommerce' ),
					'geolocation_ajax' => __( 'Geolocate (with page caching support)', 'woocommerce' ),
				),
			),

			array(
				'title'   => __( 'Enable Taxes', 'woocommerce' ),
				'desc'    => __( 'Enable taxes and tax calculations', 'woocommerce' ),
				'id'      => 'woocommerce_calc_taxes',
				'default' => 'no',
				'type'    => 'checkbox'
			),

			array(
				'title'   => __( 'Store Notice', 'woocommerce' ),
				'desc'    => __( 'Enable site-wide store notice text', 'woocommerce' ),
				'id'      => 'woocommerce_demo_store',
				'default' => 'no',
				'type'    => 'checkbox'
			),

			array(
				'title'    => __( 'Store Notice Text', 'woocommerce' ),
				'desc'     => '',
				'id'       => 'woocommerce_demo_store_notice',
				'default'  => __( 'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.', 'woocommerce' ),
				'type'     => 'textarea',
				'css'     => 'width:350px; height: 65px;',
				'autoload' => false
			),

            array(
                'title'    => __( '台灣簡訊帳號', 'woocommerce' ),
                'desc'     => '',
                'id'       => 'woocommerce_sms_account',
                'default'  => __( '', 'woocommerce' ),
                'type'  => 'text',
            ),

            array(
                'title'    => __( '台灣簡訊密碼', 'woocommerce' ),
                'desc'     => '',
                'id'       => 'woocommerce_sms_password',
                'default'  => __( '', 'woocommerce' ),
                'type'  => 'text',
            ),


            array(
                'title'    => __( '註冊訊息', 'woocommerce' ),
                'desc'     => '%cert = 認證碼',
                'id'       => 'woocommerce_registration_notice',
                'default'  => __( '【買肉找我】 您的手機認證碼為 %cert ，此認證碼有效時間為 1 小時。', 'woocommerce' ),
                'css'     => 'width:350px; height: 65px;',
                'type'  => 'textarea',
                'autoload' => false
            ),

            array(
                'title'   => __( '啟用下單訊息', 'woocommerce' ),
                'desc'    => __( '下單時寄送簡訊', 'woocommerce' ),
                'id'      => 'woocommerce_enable_order_notice',
                'default' => 'yes',
                'type'    => 'checkbox',
            ),

            array(
                'title'    => __( '下單訊息', 'woocommerce' ),
                'desc'     => '%no = 訂單單號, %payment = 金額, %phone = 電話',
                'id'       => 'woocommerce_order_notice',
                'default'  => __( '【買肉找我】您的訂單 (編號# %no) 已確立，請於指定時間內付款，我們將在2個工作天內寄送至您指定的地址，感謝您的支持。', 'woocommerce' ),
                'css'     => 'width:350px; height: 65px;',
                'type'  => 'textarea',
                'autoload' => false
            ),

            array(
                'title'   => __( '啟用付款訊息', 'woocommerce' ),
                'desc'    => __( '於訂單變更狀態為【已付款】時寄送通知簡訊(即為付款完成)', 'woocommerce' ),
                'id'      => 'woocommerce_enable_payment_notice',
                'default' => 'yes',
                'type'    => 'checkbox',
            ),

            array(
                'title'    => __( '付款訊息', 'woocommerce' ),
                'desc'     => '%no = 訂單編號, %payment = 金額',
                'id'       => 'woocommerce_payment_notice',
                'default'  => __( '【買肉找我】已收到訂單 (編號# %no) 款項共 %payment，我們將在2個工作天內寄送至您指定的地址，感謝您的支持', 'woocommerce' ),
                'css'     => 'width:350px; height: 65px;',
                'type'  => 'textarea',
                'autoload' => false
            ),

            array(
                'title'   => __( '啟用出貨訊息', 'woocommerce' ),
                'desc'    => __( '於訂單變更狀態為【完成】時寄送通知簡訊', 'woocommerce' ),
                'id'      => 'woocommerce_enable_delivery_notice',
                'default' => 'yes',
                'type'    => 'checkbox',
            ),

            array(
                'title'    => __( '出貨訊息', 'woocommerce' ),
                'desc'     => '%no = 訂單編號, %phone = 電話',
                'id'       => 'woocommerce_delivery_notice',
                'default'  => __( '【買肉找我】 您的訂單 (編號# %no) 已出貨，若有問題歡迎電洽 $phone，將有專員為您答覆，感謝您的支持。', 'woocommerce' ),
                'css'     => 'width:350px; height: 65px;',
                'type'  => 'textarea',
                'autoload' => false
            ),

            array(
                'title'    => __( '匯款資訊', 'woocommerce' ),
                'desc'     => '在 email 中的匯款資訊',
                'id'       => 'woocommerce_transfer_notice',
                'default'  => __( '銀行：xxx, 帳號：xxx, 戶名：xxx', 'woocommerce' ),
                'css'     => 'width:350px; height: 65px;',
                'type'  => 'textarea',
                'autoload' => false
            ),

            array(
                'title'    => __( '簡訊測試用手機', 'woocommerce' ),
                'desc'     => '正式使用時請留空',
                'id'       => 'woocommerce_message_phone',
                'default'  => __( '', 'woocommerce' ),
                'type'  => 'text',
            ),

            array(
                'title'    => __( '公司電話(使用於簡訊中)', 'woocommerce' ),
                'desc'     => '',
                'id'       => 'woocommerce_company_number',
                'default'  => __( '', 'woocommerce' ),
                'type'  => 'text',
            ),

            array(
                'title'    => __( '產品頁限時結束文字', 'woocommerce' ),
                'desc'     => '限時活動結束後，該頁面取代倒數計時的文字',
                'id'       => 'woocommerce_countdown_word',
                'default'  => __( '限時搶購已經截止，買肉將不定時推出各種好康優惠，請多多密切關注！', 'woocommerce' ),
                'type'  => 'text',
            ),

            array( 'type' => 'sectionend', 'id' => 'general_options'),

			array( 'title' => __( 'Currency Options', 'woocommerce' ), 'type' => 'title', 'desc' => __( 'The following options affect how prices are displayed on the frontend.', 'woocommerce' ), 'id' => 'pricing_options' ),

			array(
				'title'    => __( 'Currency', 'woocommerce' ),
				'desc'     => __( 'This controls what currency prices are listed at in the catalog and which currency gateways will take payments in.', 'woocommerce' ),
				'id'       => 'woocommerce_currency',
				'css'      => 'min-width:350px;',
				'default'  => 'GBP',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'desc_tip' =>  true,
				'options'  => $currency_code_options
			),

			array(
				'title'    => __( 'Currency Position', 'woocommerce' ),
				'desc'     => __( 'This controls the position of the currency symbol.', 'woocommerce' ),
				'id'       => 'woocommerce_currency_pos',
				'css'      => 'min-width:350px;',
				'class'    => 'wc-enhanced-select',
				'default'  => 'left',
				'type'     => 'select',
				'options'  => array(
					'left'        => __( 'Left', 'woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . '99.99)',
					'right'       => __( 'Right', 'woocommerce' ) . ' (99.99' . get_woocommerce_currency_symbol() . ')',
					'left_space'  => __( 'Left with space', 'woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ' 99.99)',
					'right_space' => __( 'Right with space', 'woocommerce' ) . ' (99.99 ' . get_woocommerce_currency_symbol() . ')'
				),
				'desc_tip' =>  true,
			),

			array(
				'title'    => __( 'Thousand Separator', 'woocommerce' ),
				'desc'     => __( 'This sets the thousand separator of displayed prices.', 'woocommerce' ),
				'id'       => 'woocommerce_price_thousand_sep',
				'css'      => 'width:50px;',
				'default'  => ',',
				'type'     => 'text',
				'desc_tip' =>  true,
			),

			array(
				'title'    => __( 'Decimal Separator', 'woocommerce' ),
				'desc'     => __( 'This sets the decimal separator of displayed prices.', 'woocommerce' ),
				'id'       => 'woocommerce_price_decimal_sep',
				'css'      => 'width:50px;',
				'default'  => '.',
				'type'     => 'text',
				'desc_tip' =>  true,
			),

			array(
				'title'    => __( 'Number of Decimals', 'woocommerce' ),
				'desc'     => __( 'This sets the number of decimal points shown in displayed prices.', 'woocommerce' ),
				'id'       => 'woocommerce_price_num_decimals',
				'css'      => 'width:50px;',
				'default'  => '2',
				'desc_tip' =>  true,
				'type'     => 'number',
				'custom_attributes' => array(
					'min'  => 0,
					'step' => 1
				)
			),

			array( 'type' => 'sectionend', 'id' => 'pricing_options' )

		) );

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
	}

	/**
	 * Output a colour picker input box.
	 *
	 * @param mixed $name
	 * @param string $id
	 * @param mixed $value
	 * @param string $desc (default: '')
	 */
	public function color_picker( $name, $id, $value, $desc = '' ) {
		echo '<div class="color_box">' . wc_help_tip( $desc ) . '
			<input name="' . esc_attr( $id ). '" id="' . esc_attr( $id ) . '" type="text" value="' . esc_attr( $value ) . '" class="colorpick" /> <div id="colorPickerDiv_' . esc_attr( $id ) . '" class="colorpickdiv"></div>
		</div>';
	}

	/**
	 * Save settings.
	 */
	public function save() {
		$settings = $this->get_settings();

		WC_Admin_Settings::save_fields( $settings );
	}

}

endif;

return new WC_Settings_General();
