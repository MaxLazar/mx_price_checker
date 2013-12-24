<?php

/**
 *  MX Price Checker Class for ExpressionEngine2
 *
 * @package  ExpressionEngine
 * @subpackage Plugins
 * @category Plugins
 * @author    Max Lazar <max@eec.ms>
 */

require_once PATH_THIRD . 'mx_price_checker/config.php';


$plugin_info = array(
	'pi_name'   => MX_PRICE_CHECKER_NAME,
	'pi_version'  => MX_PRICE_CHECKER_VER,
	'pi_author'   => MX_PRICE_CHECKER_AUTHOR,
	'pi_author_url'  => MX_PRICE_CHECKER_DOCS,
	'pi_description' => MX_PRICE_CHECKER_DESC,
	'pi_usage'   => mx_price_checker::usage()
);


class Mx_price_checker {

	var $return_data="";
	var $cache_path = false;

	function __construct() {

		$this->EE =& get_instance();

		$default_page = ( !$this->EE->TMPL->fetch_param( 'redirect' ) ) ? $this->EE->functions->fetch_current_uri() : $this->EE->TMPL->fetch_param( 'redirect' );
		$field_id = ( !$this->EE->TMPL->fetch_param( 'field_id' ) ) ? 104 : (int) $this->EE->TMPL->fetch_param( 'field_id' );
		$col_id = ( !$this->EE->TMPL->fetch_param( 'col_id' ) ) ? 71 : (int) $this->EE->TMPL->fetch_param( 'col_id' );
		$entry_id = ( !$this->EE->TMPL->fetch_param( 'entry_id' ) ) ? false : (int) $this->EE->TMPL->fetch_param( 'entry_id' );
		$price = ( !$this->EE->TMPL->fetch_param( 'price' ) ) ? 999999999 : (float) $this->EE->TMPL->fetch_param( 'price' );
		$url_title  = ( !$this->EE->TMPL->fetch_param( 'url_title' ) ) ? false : $this->EE->TMPL->fetch_param( 'url_title' );

		if ( !$entry_id ) {
			$entry_id_query = $this->EE->db->select( 'entry_id' )
			->where( 'url_title', $url_title )
			->get( 'channel_titles', 1 );

			if
			( $entry_id_query->num_rows() ) {
				$entry_id= $entry_id_query->row()->entry_id;
			} else {
				$this->EE->functions->redirect( $default_page );
			}

		};

		$limits = $this->EE->db->query( "SELECT col_id_$col_id FROM `exp_matrix_data` WHERE entry_id = $entry_id AND field_id = $field_id" )->result_array();

		foreach ( $limits as $v ) {
			$prices[] = (float) $v["col_id_".$col_id];
		}

		if ( !in_array( $price, $prices ) ) {
			$this->EE->functions->redirect( $default_page );
		}

		return true;

	}


	// ----------------------------------------
	//  Plugin Usage
	// ----------------------------------------

	// This function describes how the plugin is used.
	//  Make sure and use output buffering

	function usage() {
		ob_start();
?>


User Guide


<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
	/* END */

}
