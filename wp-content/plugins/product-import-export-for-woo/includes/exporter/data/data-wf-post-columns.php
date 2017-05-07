<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters('woocommerce_csv_product_post_columns', array(
	'post_title'		=> '產品標題',
	'post_name'		=> '產品網址',
	'ID' 			=> 'ID',
	'post_excerpt'		=> '說明',
	'post_content'		=> '簡短說明',
	'post_status'		=> '狀態',
	'menu_order'		=> '產品出現順位',
	'post_date'		=> '新增時間',
	'post_author'		=> '新增人',
	'comment_status'	=> '是否開放留言',

	// Meta
	'_sku'			=> '料號',
	'_downloadable' 	=> '是否可下載',
	'_virtual'		=> '是否為虛擬品',
    	'_stock'		=> '庫存數',
    	'_regular_price'	=> '定價',
	'_sale_price'		=> '促銷價',
	'_weight'		=> '重量',
	'_length'		=> '長度',
	'_width'		=> '寬度',
	'_height'		=> '高度',
        '_tax_class'		=> '稅別',
    
	'_visibility'		=> '是否可見',
	'_stock_status'		=> '庫存狀態',
	'_backorders'		=> '是否無庫存可下單',
	'_manage_stock'		=> '有無管理庫存',
	'_tax_status'		=> '稅別狀態',
	'_upsell_ids'		=> '推薦產品',
	'_crosssell_ids'	=> '交叉銷售產品',
	'_featured'		=> '是否為特色產品',

	'_sale_price_dates_from' => '促銷起時',
	'_sale_price_dates_to' 	 => '促銷迄時',

	// Downloadable products
	'_download_limit'	=> '下載限制',
	'_download_expiry'	=> '下載過期日',

	// Virtual products
	'_product_url'		=> '產品連結',
	'_button_text'		=> '按鈕文字',

) );