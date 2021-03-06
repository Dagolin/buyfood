<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Expedited_Cancel_Email' ) ) :
/**
 * A custom Expedited Cancel WooCommerce Email class
 *
 * @since 0.1
 * @extends \WC_Email
 */
class WC_Expedited_Cancel_Email extends WC_Email {


	/**
	 * Set email defaults
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// set ID, this simply needs to be a unique name
		$this->id = 'wc_expedited_cancel';

		// this is the title in WooCommerce Email settings
		$this->title = '取消訂單(顧客)';


		// this is the description in WooCommerce email settings
		$this->description = '取消訂單後顧客收到的確認信';

		// these are the default heading and subject lines that can be overridden using the settings
		$this->heading = __( '感謝您的支持', 'woocommerce' );
		$this->subject = __( '【{site_title}】 訂單 #{order_date} 取消通知', 'woocommerce' );

		// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
		$this->template_html  = 'emails/customer-cancel-order.php';
		$this->template_plain = 'emails/plain/customer-cancel-order.php';

        global $wp_filter;

        $registed = false;

        if (isset($wp_filter['woocommerce_order_status_cancelled'])) {
            foreach ($wp_filter['woocommerce_order_status_cancelled'] as $actionPriority) {
                foreach ($actionPriority as $uniqueAction) {
                    if ($uniqueAction['function'][1] == 'trigger') {
                        $registed = true;
                    }
                }
            }
        }

        // Trigger on new paid orders
        if (!$registed) {
            add_filter( 'woocommerce_order_status_cancelled', array( $this, 'trigger' ),  1, 2 );
        }
        
		// Call parent constructor to load any other defaults not explicity defined here
		parent::__construct();

        $this->customer_email   = true;
	}


	/**
	 * Determine if the email should actually be sent and setup email merge variables
	 *
	 * @since 0.1
	 * @param int $order_id
	 */
	public function trigger( $order_id , $post) {

		// bail if no order ID is present
		if ( ! $order_id )
			return;

		// setup order object
		$this->object = new WC_Order( $order_id );

		// bail if shipping method is not expedited
//		if ( ! in_array( $this->object->get_shipping_method(), array( 'Three Day Shipping', 'Next Day Shipping' ) ) )
//			return;

		// replace variables in the subject/headings
		$this->find[] = '{order_date}';
		$this->replace[] = date_i18n( woocommerce_date_format(), strtotime( $this->object->order_date ) );

		$this->find[] = '{order_number}';
		$this->replace[] = $this->object->get_order_number();

        $this->find[] = '{order_id}';
        $this->replace[] = $order_id;

		if ( ! $this->is_enabled())
			return;

        $this->send( $this->object->billing_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}


    /**
     * Get content html.
     *
     * @access public
     * @return string
     */
    public function get_content_html() {
        return wc_get_template_html( $this->template_html, array(
            'order'         => $this->object,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text'    => false,
            'email'			=> $this
        ) );
    }


    /**
     * Get content plain.
     *
     * @access public
     * @return string
     */
    public function get_content_plain() {
        return wc_get_template_html( $this->template_plain, array(
            'order'         => $this->object,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text'    => true,
            'email'			=> $this
        ) );
    }


	/**
	 * Initialize Settings Form Fields
	 *
	 * @since 2.0
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'    => array(
				'title'   => '啓用/停用',
				'type'    => 'checkbox',
				'label'   => '啟用此電子郵件通知',
				'default' => 'yes'
			),

			'subject'    => array(
				'title'       => '電子郵件主旨',
				'type'        => 'text',
				'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading'    => array(
				'title'       => '電子郵件內文主旨',
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => '電子郵件類型',
				'type'        => 'select',
				'description' => '選擇電子郵件的傳送格式',
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'	    => __( 'Plain text', 'woocommerce' ),
					'html' 	    => __( 'HTML', 'woocommerce' ),
					'multipart' => __( 'Multipart', 'woocommerce' ),
				)
			)
		);
	}


}
endif;

return new WC_Expedited_Cancel_Email();
