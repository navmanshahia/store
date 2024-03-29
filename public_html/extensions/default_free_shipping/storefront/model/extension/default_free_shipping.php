<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  Modified by WHY2 for AbanteCart

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelExtensionDefaultFreeShipping extends Model
{
    function getQuote($address)
    {
        //create new instance of language for case when model called from admin-side
        $language = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
        $language->load($language->language_details['filename']);
        $language->load('default_free_shipping/default_free_shipping');

        if ($this->config->get('default_free_shipping_status')) {
            $query = $this->db->query("SELECT *
										FROM ".$this->db->table("zones_to_locations")."
										WHERE location_id = '".(int)$this->config->get('default_free_shipping_location_id')."'
											AND country_id = '".(int)$address['country_id']."'
											AND (zone_id = '".(int)$address['zone_id']."' OR zone_id = '0')");

            if (!$this->config->get('default_free_shipping_location_id')) {
                $status = true;
            } elseif ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $quote_data = array();

            $quote_data['default_free_shipping'] = array(
                'id'           => 'default_free_shipping.default_free_shipping',
                'title'        => $language->get('text_description'),
                'cost'         => 0.00,
                'tax_class_id' => 0,
                'text'         => $language->get('text_free'),
            );

            $method_data = array(
                'id'         => 'default_free_shipping',
                'title'      => $language->get('text_title'),
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('default_free_shipping_sort_order'),
                'error'      => false,
            );
        }

        return $method_data;
    }
}