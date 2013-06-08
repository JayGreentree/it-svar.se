<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Forms PRODUCT field
 *
 * @package			DevDemon_Forms
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/forms/
 * @see				http://expressionengine.com/user_guide/development/fieldtypes.html
 */
class FormsField_cart_total extends FormsField
{

	/**
	 * Field info - Required
	 *
	 * @access public
	 * @var array
	 */
	public $info = array(
		'title'		=>	'Total',
		'name' 		=>	'cart_total',
		'category'	=>	'cart_tools',
		'version'	=>	'1.0'
	);

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->default_settings['display_tmpl'] = '${total}';
	}

	// ********************************************************************************* //

	public function render_field($field=array(), $template=TRUE, $data='')
	{
		$settings = array_merge($this->default_settings, $field['settings']);
		$out = $settings['display_tmpl'];
		$out = str_replace('{total}', '<span class="forms_cart_total">0.00</span>', $out);
		return $out;
	}

	// ********************************************************************************* //

	public function save()
	{
		return $this->get_cart_total();
	}

	// ********************************************************************************* //

	public function field_settings($settings=array(), $template=TRUE)
	{
		$vData = array_merge($this->default_settings, $settings);
		return $this->EE->load->view('fields/cart_total', $vData, TRUE);
	}

	// ********************************************************************************* //


}

/* End of file field.cart_total.php */
/* Location: ./system/expressionengine/third_party/forms/fields/field.cart_total.php */
