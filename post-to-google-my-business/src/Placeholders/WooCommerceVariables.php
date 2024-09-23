<?php

namespace PGMB\Placeholders;

use PGMB\Vendor\Html2Text\Html2Text;

class WooCommerceVariables implements VariableInterface {

	private $woocommerce;
	private $parent_post_id;
	private $do_links;

	public function __construct($parent_post_id, $do_links) {
		if(function_exists('wc_get_product') && get_post_type($parent_post_id) === 'product'){
			$this->woocommerce = true;
			$this->parent_post_id = $parent_post_id;
		}
		$this->do_links = $do_links;
	}

	public function check_and_return_method($product, $method, ...$parameters){
		if(!method_exists($product, $method)){
			return '';
		}
		return (string)$product->$method(...$parameters);
	}

	public function variables() {
		if(!$this->woocommerce){ return []; }

		//Get the product
		$product = wc_get_product($this->parent_post_id);

		//Parse WooCommerce rich content fields.
		$product_description = wpautop($this->check_and_return_method($product,'get_description')); //Add paragraph tags
		$product_description = preg_replace("~(?:\[/?)[^\]]+/?\]~s", '', $product_description);
		$product_description = new Html2Text($product_description,
			[
				'width' => 0,
				'do_links'  => $this->do_links,
			]
		);
		$short_description = new Html2Text($this->check_and_return_method($product,'get_short_description'),
			[
				'width' => 0,
				'do_links'  => $this->do_links,
			]
		);

		//Create our new variables
		return [
			'%wc_product_price%'                => $this->check_and_return_method($product,'get_price'),
			'%wc_product_name%'                 => $this->check_and_return_method($product,'get_name'),
			'%wc_product_description%'          => trim($product_description->getText()),
			'%wc_product_short_description%'    => $short_description->getText(),
			'%wc_product_sku%'                  => $this->check_and_return_method($product,'get_sku'),
			'%wc_product_virtual%'              => $this->check_and_return_method($product,'get_virtual'),
			'%wc_product_regular_price%'        => $this->check_and_return_method($product,'get_regular_price'),
			'%wc_product_sale_price%'           => $this->check_and_return_method($product,'get_sale_price'),
			'%wc_product_price_including_tax%'  => function_exists('wc_get_price_including_tax') ? wc_get_price_including_tax( $product ) : '',
			'%wc_currency_symbol%'              => function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : '',
			'%wc_variation_min_current_price%'  => $this->check_and_return_method($product,'get_variation_price', 'min', true),
			'%wc_variation_max_current_price%'  => $this->check_and_return_method($product,'get_variation_price', 'max', true),
			'%wc_variation_min_regular_price%'  => $this->check_and_return_method($product,'get_variation_regular_price', 'min', true),
			'%wc_variation_max_regular_price%'  => $this->check_and_return_method($product,'get_variation_regular_price', 'max', true),
			'%wc_variation_min_sale_price%'     => $this->check_and_return_method($product,'get_variation_sale_price', 'min', true),
			'%wc_variation_max_sale_price%'     => $this->check_and_return_method($product,'get_variation_sale_price', 'max', true),
		];

	}
}
