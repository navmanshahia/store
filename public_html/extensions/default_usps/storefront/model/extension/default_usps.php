<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2023 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
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

/**
 * Class ModelExtensionDefaultUsps
 *
 * @property AWeight                  $weight
 * @property ModelLocalisationCountry $model_localisation_country
 */
class ModelExtensionDefaultUsps extends Model
{
    public function getQuote($address)
    {
        if(!class_exists('DOMDocument')){
            $this->log->write('USPS CRITICAL ERROR! DOMDocument class not found. Please install ext-dom php-extension to solve an issue.');
        }
        if(!function_exists('curl_init')){
            $this->log->write('USPS CRITICAL ERROR! CURL is not supported by php. Please install ext-curl php-extension to solve an issue.');
        }

        //create new instance of language for case when model called from admin-side
        $language = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
        $language->load($language->language_details['directory']);
        $language->load('default_usps/default_usps');
        $country = [];
        $weight = 0.001;
        if (!$this->config->get('default_usps_status')) {
            return false;
        }

        $this->load->model('localisation/country');
        if (!$this->config->get('default_usps_location_id')) {
            $status = true;
        } else {
            $query = $this->db->query(
                "SELECT *
                 FROM ".$this->db->table('zones_to_locations')."
                 WHERE location_id = '".(int)$this->config->get('default_usps_location_id')."'
                     AND country_id = '".(int)$address['country_id']."'
                     AND (zone_id = '".(int)$address['zone_id']."' OR zone_id = '0')"
            );
            $status = (bool)$query->num_rows;
        }

        //load all countries and codes
        $countries = $this->model_localisation_country->getCountries();
        $country = array_column($countries, 'name', 'iso_code_2');

        if ($status && !has_value($country[$address['iso_code_2']])) {
            $status = false;
        }

        if (!$status) {
            return [];
        }

        $method_data = [];
        $quote_data = [];

        //build array with cost for shipping
        // ids of products without special shipping cost
        $generic_product_ids = $free_shipping_ids = $shipping_price_ids = [];
        // total shipping cost of product with fixed shipping price
        $shipping_price_cost = 0;
        $cart_products = $this->cart->getProducts();
        foreach ($cart_products as $product) {
            //(exclude free shipping products)
            if ($product['free_shipping']) {
                $free_shipping_ids[] = $product['product_id'];
                continue;
            }
            if ($product['shipping_price'] > 0) {
                $shipping_price_ids[] = $product['product_id'];
                $shipping_price_cost += $product['shipping_price'] * $product['quantity'];
            }
            $generic_product_ids[] = $product['product_id'];
        }
        //convert fixed prices to USD
        $shipping_price_cost = $this->currency->convert(
            $shipping_price_cost,
            $this->config->get('config_currency'),
            'USD'
        );

        if ($generic_product_ids) {
            $api_weight_product_ids = array_diff($generic_product_ids, $shipping_price_ids);
            //WHEN ONLY PRODUCTS WITH FIXED SHIPPING PRICES ARE IN BASKET
            if (!$api_weight_product_ids) {
                $cost = $shipping_price_cost;
                $quote_data = [
                    'default_usps' => [
                        'id'           => 'default_usps.default_usps',
                        'title'        => $language->get('text_title'),
                        'cost'         => $this->currency->convert(
                            $cost,
                            'USD',
                            $this->config->get('config_currency')
                        ),
                        'tax_class_id' => $this->config->get('default_usps_tax_class_id'),
                        'text'         => $this->currency->format(
                            $this->tax->calculate($this->currency->convert($cost,
                                'USD',
                                $this->currency->getCode()
                            ),
                                $this->config->get('default_usps_tax_class_id'),
                                $this->config->get('config_tax')),
                            $this->currency->getCode(),
                            1.0000000),
                    ],
                ];
                return [
                    'id'         => 'default_usps',
                    'title'      => $language->get('text_title'),
                    'quote'      => $quote_data,
                    'sort_order' => $this->config->get('default_usps_sort_order'),
                    'error'      => '',
                ];
            }
        } else {
            $api_weight_product_ids = $shipping_price_ids;
        }

        if ($api_weight_product_ids) {
            //do trick to get int instead unit name
            //TODO: change this in 2.0
            $cart_weight_class_id = $this->weight->getClassIDByUnit($this->config->get('config_weight_class'));
            $cart_weight = $this->cart->getWeight($api_weight_product_ids);
            $weight = $this->weight->convertByID(
            //get weight non-free shipping products only
                $cart_weight,
                $cart_weight_class_id,
                $this->weight->getClassIDByCode('PUND')
            );
            $weight = max($weight, 0.001);
        }

        $pounds = floor($weight);
        $ounces = round(16 * ($weight - $pounds), 2); // max 5 digits
        $postcode = str_replace(' ', '', $address['postcode']);

        // FOR CASE WHEN ONLY FREE SHIPPING PRODUCTS IN BASKET
        if (!$api_weight_product_ids && $free_shipping_ids) {
            $quote_data = [
                'default_usps' => [
                    'id'           => 'default_usps.default_usps',
                    'title'        => $language->get('text_'.($address['iso_code_2'] == 'US'
                            ? $this->config->get('default_usps_free_domestic_method')
                            : $this->config->get('default_usps_free_international_method'))),
                    'cost'         => 0.0,
                    'tax_class_id' => $this->config->get('default_usps_tax_class_id'),
                    'text'         => $language->get('text_free'),
                ],
            ];
            $method_data = [
                'id'         => 'default_usps',
                'title'      => $language->get('text_title'),
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('default_usps_sort_order'),
                'error'      => '',
            ];
            return $method_data;
        }

        if ($address['iso_code_2'] == 'US') {
            $xml = '<RateV4Request USERID="'.$this->config->get('default_usps_user_id').'" PASSWORD="'.$this->config->get('default_usps_password').'">';
            $xml .= '	<Package ID="1">';
            $xml .= '		<Service>ALL</Service>';
            $xml .= '		<ZipOrigination>'.substr($this->config->get('default_usps_postcode'), 0, 5).'</ZipOrigination>';
            $xml .= '		<ZipDestination>'.substr($postcode, 0, 5).'</ZipDestination>';
            $xml .= '		<Pounds>'.$pounds.'</Pounds>';
            $xml .= '		<Ounces>'.$ounces.'</Ounces>';

            // Prevent common size mismatch error from USPS (Size cannot be Regular if Container is Rectangular for some reason)
            if ($this->config->get('default_usps_container') == 'RECTANGULAR' && $this->config->get('default_usps_size') == 'REGULAR') {
                $this->config->set('default_usps_container', 'VARIABLE');
            }

            $xml .= '		<Container>'.$this->config->get('default_usps_container').'</Container>';
            $xml .= '		<Size>'.$this->config->get('default_usps_size').'</Size>';
            $xml .= '		<Width>'.$this->config->get('default_usps_width').'</Width>';
            $xml .= '		<Length>'.$this->config->get('default_usps_length').'</Length>';
            $xml .= '		<Height>'.$this->config->get('default_usps_height').'</Height>';

            // Calculate girth based on usps calculation
            $xml .= '		<Girth>'.(round(((float)$this->config->get('default_usps_length')
                    + (float)$this->config->get('default_usps_width') * 2
                    + (float)$this->config->get('default_usps_height') * 2), 1)).'</Girth>';
            $xml .= '		<Machinable>'.($this->config->get('default_usps_machinable') ? 'true' : 'false').'</Machinable>';
            $xml .= '	</Package>';
            $xml .= '</RateV4Request>';

            $request = 'API=RateV4&XML='.urlencode($xml);
        } else {
            $xml = '<IntlRateV2Request USERID="'.$this->config->get('default_usps_user_id').'">';
            $xml .= '	<Package ID="1">';
            $xml .= '		<Pounds>'.$pounds.'</Pounds>';
            $xml .= '		<Ounces>'.$ounces.'</Ounces>';
            $xml .= '		<MailType>All</MailType>';
            $xml .= '		<GXG>';
            $xml .= '		  <POBoxFlag>N</POBoxFlag>';
            $xml .= '		  <GiftFlag>N</GiftFlag>';
            $xml .= '		</GXG>';
            $xml .= '		<ValueOfContents>'.$this->cart->getSubTotal().'</ValueOfContents>';
            $xml .= '		<Country>'.$country[$address['iso_code_2']].'</Country>';
            // Intl only supports RECT and NONRECT
            if ($this->config->get('default_usps_container') == 'VARIABLE') {
                $this->config->set('default_usps_container', 'NONRECTANGULAR');
            }
            $xml .= '		<Container>'.$this->config->get('default_usps_container').'</Container>';
            $xml .= '		<Size>'.$this->config->get('default_usps_size').'</Size>';
            $xml .= '		<Width>'.$this->config->get('default_usps_width').'</Width>';
            $xml .= '		<Length>'.$this->config->get('default_usps_length').'</Length>';
            $xml .= '		<Height>'.$this->config->get('default_usps_height').'</Height>';
            $xml .= '		<Girth>'.$this->config->get('default_usps_girth').'</Girth>';
            $xml .= '		<CommercialFlag>N</CommercialFlag>';
            $xml .= '	</Package>';
            $xml .= '</IntlRateV2Request>';
            $request = 'API=IntlRateV2&XML='.urlencode($xml);
        }

        $curl = curl_init();
        curl_setopt_array($curl,
            [
                CURLOPT_URL => 'https://secure.shippingapis.com/ShippingAPI.dll?'.$request,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1
            ]
        );

        $result = curl_exec($curl);
        curl_close($curl);

        // strip reg, trade and ** out 01-02-2011
        $result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
        $result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
        $result = str_replace('**', '', $result);
        $result = str_replace("\r\n", '', $result);
        $result = str_replace('\"', '"', $result);

        if ($result) {
            if ($this->config->get('default_usps_debug')) {
                $this->log->write("USPS DATA SENT: ".urldecode($request));
                $this->log->write("USPS DATA RECV: ".$result);
            }
            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->loadXml($result);
            $rate_response = $dom->getElementsByTagName('RateV4Response')->item(0);
            $intl_rate_response = $dom->getElementsByTagName('IntlRateV2Response')->item(0);
            $error = $dom->getElementsByTagName('Error')->item(0);

            $first_classes = [
                'First-Class Mail Parcel',
                'First-Class Mail Large Envelope',
                'First-Class Mail Stamped Letter',
                'First-Class Mail Postcards',
            ];
            if ($rate_response || $intl_rate_response) {
                if ($address['iso_code_2'] == 'US') {
                    $domesticClassIDs = array_keys(USPS_CLASSES['domestic']);
                    $package = $rate_response->getElementsByTagName('Package')->item(0);
                    $error = $package->getElementsByTagName('Error')->item(0);
                    $postages = $package->getElementsByTagName('Postage');

                    if ($postages->length) {
                        foreach ($postages as $postage) {
                            $class_id = (int)$postage->getAttribute('CLASSID');
                            if (in_array($class_id, $domesticClassIDs)) {
                                if ($class_id == 0) {
                                    $mail_service = $postage->getElementsByTagName('MailService')->item(0)->nodeValue;
                                    foreach ($first_classes as $k => $first_class) {
                                        if ($first_class == $mail_service) {
                                            $class_id = $class_id.$k;
                                            break;
                                        }
                                    }
                                    if ( $this->config->get('default_usps_domestic_'.$class_id) ) {
                                        $cost = $postage->getElementsByTagName('Rate')->item(0)->nodeValue;
                                        if ($generic_product_ids) {
                                            $cost += $shipping_price_cost;
                                        }
                                        $quote_data[$class_id] = [
                                            'id'           => 'default_usps.'.$class_id,
                                            'title'        => $postage->getElementsByTagName('MailService')->item(0)->nodeValue,
                                            'cost'         => $this->currency->convert(
                                                $cost,
                                                'USD',
                                                $this->config->get('config_currency')
                                            ),
                                            'tax_class_id' => $this->config->get('default_usps_tax_class_id'),
                                            'text'         => $this->currency->format(
                                                $this->tax->calculate(
                                                    $this->currency->convert(
                                                        $cost,
                                                        'USD',
                                                        $this->currency->getCode()
                                                    ),
                                                    $this->config->get('default_usps_tax_class_id'),
                                                    $this->config->get('config_tax')
                                                ),
                                                $this->currency->getCode(),
                                                1.0000000
                                            ),
                                        ];
                                    }
                                } elseif ($this->config->get('default_usps_domestic_'.$class_id)) {
                                    $cost = $postage->getElementsByTagName('Rate')->item(0)->nodeValue;
                                    if ($generic_product_ids) {
                                        $cost += $shipping_price_cost;
                                    }
                                    $quote_data[$class_id] = [
                                        'id'           => 'default_usps.'.$class_id,
                                        'title'        => $postage->getElementsByTagName('MailService')->item(0)->nodeValue,
                                        'cost'         => $this->currency->convert(
                                            $cost,
                                            'USD',
                                            $this->config->get('config_currency')
                                        ),
                                        'tax_class_id' => $this->config->get('default_usps_tax_class_id'),
                                        'text'         => $this->currency->format(
                                            $this->tax->calculate(
                                                $this->currency->convert(
                                                    $cost,
                                                    'USD',
                                                    $this->currency->getCode()
                                                ),
                                                $this->config->get('default_usps_tax_class_id'),
                                                $this->config->get('config_tax')
                                            ),
                                            $this->currency->getCode(),
                                            1.0000000
                                        )
                                    ];
                                }
                            }
                        }
                    } else {

                        $method_data = [
                            'id'         => 'default_usps',
                            'title'      => $language->get('text_title'),
                            'quote'      => $quote_data,
                            'sort_order' => $this->config->get('default_usps_sort_order'),
                            'error'      => $error->getElementsByTagName('Description')->item(0)->nodeValue,
                        ];
                    }
                } else {
                    $intClassIDs = array_keys(USPS_CLASSES['international']);
                    $package = $intl_rate_response->getElementsByTagName('Package')->item(0);
                    $services = $package->getElementsByTagName('Service');
                    foreach ($services as $service) {
                        $id = $service->getAttribute('ID');
                        if (in_array($id, $intClassIDs) && $this->config->get('default_usps_international_'.$id)) {
                            $title = $service->getElementsByTagName('SvcDescription')->item(0)->nodeValue;
                            if (!$title) {
                                continue;
                            }
                            if ($this->config->get('default_usps_display_time')) {
                                $title .= ' ('.$language->get('text_eta').' '.$service->getElementsByTagName('SvcCommitments')->item(0)->nodeValue.')';
                            }
                            $cost = $service->getElementsByTagName('Postage')->item(0)->nodeValue;
                            if ($generic_product_ids) {
                                $cost += $shipping_price_cost;
                            }
                            $quote_data[$id] = [
                                'id'           => 'default_usps.'.$id,
                                'title'        => $title,
                                'cost'         => $this->currency->convert($cost, 'USD',
                                    $this->config->get('config_currency')),
                                'tax_class_id' => $this->config->get('default_usps_tax_class_id'),
                                'text'         => $this->currency->format(
                                    $this->tax->calculate(
                                        $this->currency->convert(
                                            $cost,
                                            'USD',
                                            $this->currency->getCode()),
                                        $this->config->get('default_usps_tax_class_id'),
                                        $this->config->get('config_tax')),
                                    $this->currency->getCode(),
                                    1.0000000),
                            ];
                        }
                    }
                }
            } elseif ($error) {
                $method_data = [
                    'id'         => 'default_usps',
                    'title'      => $language->get('text_title'),
                    'quote'      => $quote_data,
                    'sort_order' => $this->config->get('default_usps_sort_order'),
                    'error'      => $error->getElementsByTagName('Description')->item(0)->nodeValue,
                ];
            }
        }

        if ($quote_data) {
            $title = $language->get('text_title');
            if ($this->config->get('default_usps_display_weight')) {
                $title .= ' ('.$language->get('text_weight')
                    .' '.$this->weight->formatByID($cart_weight,
                        $this->weight->getClassIDByUnit($this->config->get('config_weight_class'))).')';
            }

            $method_data = [
                'id'         => 'default_usps',
                'title'      => $title,
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('default_usps_sort_order'),
                'error'      => false,
            ];
        }
        return $method_data;
    }
}