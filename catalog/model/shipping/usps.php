<?php // Member MODIFIED copy
class ModelShippingUsps extends Model {
	public function getQuote($address) {
		$this->load->language('shipping/usps');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('usps_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('usps_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$this->load->model('localisation/country');

			$quote_data = array();
			$packages = array();
			$dimensional_padding_multiplier = 1 + ($this->config->get('member_shipping_padding') / 100); // extra % to add for package padding

			// Membership: modified for Packages per Member
			if ($this->config->get('member_status')) {
				foreach ($this->cart->getPackages() as $member_customer_id => $package) {
					$packages[$member_customer_id]['weight'] = $this->weight->convert($package['weight'], $this->config->get('config_weight_class_id'), $this->config->get('usps_weight_class_id'));
					$packages[$member_customer_id]['weight'] =  ($packages[$member_customer_id]['weight'] < 0.1 ? 0.1 : $packages[$member_customer_id]['weight']);
					$packages[$member_customer_id]['pounds'] = floor($packages[$member_customer_id]['weight']);
					$packages[$member_customer_id]['ounces'] = $ounces = round(16 * ($packages[$member_customer_id]['weight'] - $packages[$member_customer_id]['pounds']), 2); // max 5 digits
					
					$volume_max = $package['dimensions']['max_width'] * $package['dimensions']['max_length'] * $package['dimensions']['max_height'];
					
					if ($package['dimensions']['volume'] > $volume_max) {
						$dimension_multiplier = round(((float)$package['dimensions']['volume'] / (float)$volume_max), 2);
						$packages[$member_customer_id]['width'] = $this->length->convert($package['dimensions']['max_width'] * $dimension_multiplier * $dimensional_padding_multiplier, $this->config->get('config_length_class_id'), $this->config->get('usps_length_class_id'));
						$packages[$member_customer_id]['length'] = $this->length->convert($package['dimensions']['max_length'] * $dimension_multiplier * $dimensional_padding_multiplier, $this->config->get('config_length_class_id'), $this->config->get('usps_length_class_id'));
						$packages[$member_customer_id]['height'] = $this->length->convert($package['dimensions']['max_height'] * $dimension_multiplier * $dimensional_padding_multiplier, $this->config->get('config_length_class_id'), $this->config->get('usps_length_class_id'));
					} else {
						$packages[$member_customer_id]['width'] = $this->length->convert($package['dimensions']['max_width'] * $dimensional_padding_multiplier, $this->config->get('config_length_class_id'), $this->config->get('usps_length_class_id'));
						$packages[$member_customer_id]['length'] = $this->length->convert($package['dimensions']['max_length'] * $dimensional_padding_multiplier, $this->config->get('config_length_class_id'), $this->config->get('usps_length_class_id'));
						$packages[$member_customer_id]['height'] = $this->length->convert($package['dimensions']['max_height'] * $dimensional_padding_multiplier, $this->config->get('config_length_class_id'), $this->config->get('usps_length_class_id'));
					}
					
					$packages[$member_customer_id]['girth'] = round(((float)$packages[$member_customer_id]['width'] * 2 + (float)$packages[$member_customer_id]['height'] * 2), 1);
					
					if (($packages[$member_customer_id]['width'] > 12) || ($packages[$member_customer_id]['length'] > 12) || ($packages[$member_customer_id]['height'] > 12)) {
						$packages[$member_customer_id]['size'] = 'LARGE';
						$packages[$member_customer_id]['container'] = 'RECTANGULAR';
					} else {
						$packages[$member_customer_id]['size'] = 'REGULAR';
						$packages[$member_customer_id]['container'] = 'VARIABLE';
					}
				}
			} else {
				$packages[1]['weight'] = $this->weight->convert($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->config->get('usps_weight_class_id'));
				$packages[1]['weight'] = ($packages[1]['weight'] < 0.1 ? 0.1 : $packages[01]['weight']);
				$packages[1]['pounds'] = floor($packages[1]['weight']);
				$packages[1]['ounces'] = round(16 * ($packages[1]['weight'] - $packages[1]['pounds']), 2); // max 5 digits
				$packages[1]['width'] = $this->config->get('usps_width');
				$packages[1]['length'] = $this->config->get('usps_length');
				$packages[1]['height'] = $this->config->get('usps_height');
				$packages[1]['container'] = $this->config->get('usps_container');
				$packages[1]['size'] = $this->config->get('usps_size');
				$packages[1]['girth'] = round(((float)$packages[1]['width'] * 2 + (float)$packages[1]['height'] * 2), 1);
			}

			$postcode = str_replace(' ', '', $address['postcode']);

			if ($address['iso_code_2'] == 'US') {
				$xml  = '<RateV4Request USERID="' . $this->config->get('usps_user_id') . '">';
				
				foreach ($packages as $member_customer_id => $package) {
				
					$xml .= '	<Package ID="' . $member_customer_id . '">';
					$xml .=	'		<Service>ALL</Service>';
					$xml .=	'		<ZipOrigination>' . substr($this->config->get('usps_postcode'), 0, 5) . '</ZipOrigination>';
					$xml .=	'		<ZipDestination>' . substr($postcode, 0, 5) . '</ZipDestination>';
					$xml .=	'		<Pounds>' . $package['pounds'] . '</Pounds>';
					$xml .=	'		<Ounces>' . $package['ounces'] . '</Ounces>';

					// Prevent common size mismatch error from USPS (Size cannot be Regular if Container is Rectangular for some reason)
					if ($package['container'] == 'RECTANGULAR' && $package['size'] == 'REGULAR') {
						$package['container'] = 'VARIABLE';
					}

					$xml .=	'		<Container>' . $package['container'] . '</Container>';
					$xml .=	'		<Size>' . $package['size'] . '</Size>';
					$xml .= '		<Width>' . $package['width'] . '</Width>';
					$xml .= '		<Length>' . $package['length'] . '</Length>';
					$xml .= '		<Height>' . $package['height'] . '</Height>';
					
					// Calculate girth based on usps calculation
					$xml .= '		<Girth>' . $package['girth'] . '</Girth>';
					 
					 
					$xml .=	'		<Machinable>' . ($this->config->get('usps_machinable') ? 'true' : 'false') . '</Machinable>';
					$xml .=	'	</Package>';
					
				} // end foreach $packages
				
				$xml .= '</RateV4Request>';

				$request = 'API=RateV4&XML=' . urlencode($xml);
			} else {
      			$country = array(
					'AF' => 'Afghanistan',
                    'AL' => 'Albania',
                    'DZ' => 'Algeria',
                    'AD' => 'Andorra',
                    'AO' => 'Angola',
                    'AI' => 'Anguilla',
                    'AG' => 'Antigua and Barbuda',
                    'AR' => 'Argentina',
                    'AM' => 'Armenia',
                    'AW' => 'Aruba',
                    'AU' => 'Australia',
                    'AT' => 'Austria',
                    'AZ' => 'Azerbaijan',
                    'BS' => 'Bahamas',
                    'BH' => 'Bahrain',
                    'BD' => 'Bangladesh',
                    'BB' => 'Barbados',
                    'BY' => 'Belarus',
                    'BE' => 'Belgium',
                    'BZ' => 'Belize',
                    'BJ' => 'Benin',
                    'BM' => 'Bermuda',
                    'BT' => 'Bhutan',
                    'BO' => 'Bolivia',
                    'BA' => 'Bosnia-Herzegovina',
                    'BW' => 'Botswana',
                    'BR' => 'Brazil',
                    'VG' => 'British Virgin Islands',
                    'BN' => 'Brunei Darussalam',
                    'BG' => 'Bulgaria',
                    'BF' => 'Burkina Faso',
                    'MM' => 'Burma',
                    'BI' => 'Burundi',
                    'KH' => 'Cambodia',
                    'CM' => 'Cameroon',
                    'CA' => 'Canada',
                    'CV' => 'Cape Verde',
                    'KY' => 'Cayman Islands',
                    'CF' => 'Central African Republic',
                    'TD' => 'Chad',
                    'CL' => 'Chile',
                    'CN' => 'China',
                    'CX' => 'Christmas Island (Australia)',
                    'CC' => 'Cocos Island (Australia)',
                    'CO' => 'Colombia',
                    'KM' => 'Comoros',
                    'CG' => 'Congo (Brazzaville),Republic of the',
                    'ZR' => 'Congo, Democratic Republic of the',
                    'CK' => 'Cook Islands (New Zealand)',
                    'CR' => 'Costa Rica',
                    'CI' => 'Cote d\'Ivoire (Ivory Coast)',
                    'HR' => 'Croatia',
                    'CU' => 'Cuba',
                    'CY' => 'Cyprus',
                    'CZ' => 'Czech Republic',
                    'DK' => 'Denmark',
                    'DJ' => 'Djibouti',
                    'DM' => 'Dominica',
                    'DO' => 'Dominican Republic',
                    'TP' => 'East Timor (Indonesia)',
                    'EC' => 'Ecuador',
                    'EG' => 'Egypt',
                    'SV' => 'El Salvador',
                    'GQ' => 'Equatorial Guinea',
                    'ER' => 'Eritrea',
                    'EE' => 'Estonia',
                    'ET' => 'Ethiopia',
                    'FK' => 'Falkland Islands',
                    'FO' => 'Faroe Islands',
                    'FJ' => 'Fiji',
                    'FI' => 'Finland',
                    'FR' => 'France',
                    'GF' => 'French Guiana',
                    'PF' => 'French Polynesia',
                    'GA' => 'Gabon',
                    'GM' => 'Gambia',
                    'GE' => 'Georgia, Republic of',
                    'DE' => 'Germany',
                    'GH' => 'Ghana',
                    'GI' => 'Gibraltar',
                    'GB' => 'Great Britain and Northern Ireland',
                    'GR' => 'Greece',
                    'GL' => 'Greenland',
                    'GD' => 'Grenada',
                    'GP' => 'Guadeloupe',
                    'GT' => 'Guatemala',
                    'GN' => 'Guinea',
                    'GW' => 'Guinea-Bissau',
                    'GY' => 'Guyana',
                    'HT' => 'Haiti',
                    'HN' => 'Honduras',
                    'HK' => 'Hong Kong',
                    'HU' => 'Hungary',
                    'IS' => 'Iceland',
                    'IN' => 'India',
                    'ID' => 'Indonesia',
                    'IR' => 'Iran',
                    'IQ' => 'Iraq',
                    'IE' => 'Ireland',
                    'IL' => 'Israel',
                    'IT' => 'Italy',
                    'JM' => 'Jamaica',
                    'JP' => 'Japan',
                    'JO' => 'Jordan',
                    'KZ' => 'Kazakhstan',
                    'KE' => 'Kenya',
                    'KI' => 'Kiribati',
                    'KW' => 'Kuwait',
                    'KG' => 'Kyrgyzstan',
                    'LA' => 'Laos',
                    'LV' => 'Latvia',
                    'LB' => 'Lebanon',
                    'LS' => 'Lesotho',
                    'LR' => 'Liberia',
                    'LY' => 'Libya',
                    'LI' => 'Liechtenstein',
                    'LT' => 'Lithuania',
                    'LU' => 'Luxembourg',
                    'MO' => 'Macao',
                    'MK' => 'Macedonia, Republic of',
                    'MG' => 'Madagascar',
                    'MW' => 'Malawi',
                    'MY' => 'Malaysia',
                    'MV' => 'Maldives',
                    'ML' => 'Mali',
                    'MT' => 'Malta',
                    'MQ' => 'Martinique',
                    'MR' => 'Mauritania',
                    'MU' => 'Mauritius',
                    'YT' => 'Mayotte (France)',
                    'MX' => 'Mexico',
                    'MD' => 'Moldova',
                    'MC' => 'Monaco (France)',
                    'MN' => 'Mongolia',
                    'MS' => 'Montserrat',
                    'MA' => 'Morocco',
                    'MZ' => 'Mozambique',
                    'NA' => 'Namibia',
                    'NR' => 'Nauru',
                    'NP' => 'Nepal',
                    'NL' => 'Netherlands',
                    'AN' => 'Netherlands Antilles',
                    'NC' => 'New Caledonia',
                    'NZ' => 'New Zealand',
                    'NI' => 'Nicaragua',
                    'NE' => 'Niger',
                    'NG' => 'Nigeria',
                    'KP' => 'North Korea (Korea, Democratic People\'s Republic of)',
                    'NO' => 'Norway',
                    'OM' => 'Oman',
                    'PK' => 'Pakistan',
                    'PA' => 'Panama',
                    'PG' => 'Papua New Guinea',
                    'PY' => 'Paraguay',
                    'PE' => 'Peru',
                    'PH' => 'Philippines',
                    'PN' => 'Pitcairn Island',
                    'PL' => 'Poland',
                    'PT' => 'Portugal',
                    'QA' => 'Qatar',
                    'RE' => 'Reunion',
                    'RO' => 'Romania',
                    'RU' => 'Russia',
                    'RW' => 'Rwanda',
                    'SH' => 'Saint Helena',
                    'KN' => 'Saint Kitts (St. Christopher and Nevis)',
                    'LC' => 'Saint Lucia',
                    'PM' => 'Saint Pierre and Miquelon',
                    'VC' => 'Saint Vincent and the Grenadines',
                    'SM' => 'San Marino',
                    'ST' => 'Sao Tome and Principe',
                    'SA' => 'Saudi Arabia',
                    'SN' => 'Senegal',
                    'YU' => 'Serbia-Montenegro',
                    'SC' => 'Seychelles',
                    'SL' => 'Sierra Leone',
                    'SG' => 'Singapore',
                    'SK' => 'Slovak Republic',
                    'SI' => 'Slovenia',
                    'SB' => 'Solomon Islands',
                    'SO' => 'Somalia',
                    'ZA' => 'South Africa',
                    'GS' => 'South Georgia (Falkland Islands)',
                    'KR' => 'South Korea (Korea, Republic of)',
                    'ES' => 'Spain',
                    'LK' => 'Sri Lanka',
                    'SD' => 'Sudan',
                    'SR' => 'Suriname',
                    'SZ' => 'Swaziland',
                    'SE' => 'Sweden',
                    'CH' => 'Switzerland',
                    'SY' => 'Syrian Arab Republic',
                    'TW' => 'Taiwan',
                    'TJ' => 'Tajikistan',
                    'TZ' => 'Tanzania',
                    'TH' => 'Thailand',
                    'TG' => 'Togo',
                    'TK' => 'Tokelau (Union) Group (Western Samoa)',
                    'TO' => 'Tonga',
                    'TT' => 'Trinidad and Tobago',
                    'TN' => 'Tunisia',
                    'TR' => 'Turkey',
                    'TM' => 'Turkmenistan',
                    'TC' => 'Turks and Caicos Islands',
                    'TV' => 'Tuvalu',
                    'UG' => 'Uganda',
                    'UA' => 'Ukraine',
                    'AE' => 'United Arab Emirates',
                    'UY' => 'Uruguay',
                    'UZ' => 'Uzbekistan',
                    'VU' => 'Vanuatu',
                    'VA' => 'Vatican City',
                    'VE' => 'Venezuela',
                    'VN' => 'Vietnam',
                    'WF' => 'Wallis and Futuna Islands',
                    'WS' => 'Western Samoa',
                    'YE' => 'Yemen',
                    'ZM' => 'Zambia',
                    'ZW' => 'Zimbabwe'
				);

				if (isset($country[$address['iso_code_2']])) {
					$xml  = '<IntlRateV2Request USERID="' . $this->config->get('usps_user_id') . '">';
					
					foreach ($packages as $member_customer_id => $package) {
				
						$xml .= '	<Package ID="' . $member_customer_id . '">';
						$xml .=	'		<Pounds>' . $package['pounds'] . '</Pounds>';
						$xml .=	'		<Ounces>' . $package['ounces'] . '</Ounces>';
						$xml .=	'		<MailType>All</MailType>';
						$xml .=	'		<GXG>';
						$xml .=	'		  <POBoxFlag>N</POBoxFlag>';
						$xml .=	'		  <GiftFlag>N</GiftFlag>';
						$xml .=	'		</GXG>';
						$xml .=	'		<ValueOfContents>' . $this->cart->getSubTotal() . '</ValueOfContents>';
						$xml .=	'		<Country>' . $country[$address['iso_code_2']] . '</Country>';

						// Intl only supports RECT and NONRECT
						if ($this->config->get('usps_container') == 'VARIABLE') {
							$this->config->set('usps_container', 'NONRECTANGULAR');
						}

						$xml .=	'		<Container>' . $this->config->get('usps_container') . '</Container>';
						$xml .=	'		<Size>' . $this->config->get('usps_size') . '</Size>';
						$xml .= '		<Width>' . $this->config->get('usps_width') . '</Width>';
						$xml .= '		<Length>' . $this->config->get('usps_length') . '</Length>';
						$xml .= '		<Height>' . $this->config->get('usps_height') . '</Height>';
						$xml .= '		<Girth>' . $this->config->get('usps_girth') . '</Girth>';
						$xml .= '		<CommercialFlag>N</CommercialFlag>';
						$xml .=	'	</Package>';
					
					} // end foreach $packages
					
					$xml .=	'</IntlRateV2Request>';

					$request = 'API=IntlRateV2&XML=' . urlencode($xml);
				} else {
					$status = false;
				}
			}

			if ($status) {
				$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, 'production.shippingapis.com/ShippingAPI.dll?' . $request);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

				$result = curl_exec($ch);

				curl_close($ch);

				// strip reg, trade and ** out 01-02-2011 - updated 2013-07-30 per http://forum.opencart.com/viewtopic.php?f=114&t=106096&sid=584c08b9f944a8ec24f522123e039561
				$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
				$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
				$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '<sup>&#174</sup>', $result);
				$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '<sup>&#8482</sup>', $result);
				$result = str_replace('1-Day', '', $result);
				$result = str_replace('2-Day', '', $result);
				$result = str_replace('**', '', $result);
				$result = str_replace("\r\n", '', $result);
				$result = str_replace('\"', '"', $result);

				if ($result) {
					if ($this->config->get('usps_debug')) {
						$this->log->write("USPS DATA SENT: " . urldecode($request));
						$this->log->write("USPS DATA RECV: " . $result);
					}

					$dom = new DOMDocument('1.0', 'UTF-8');
					$dom->loadXml($result);

					$rate_response = $dom->getElementsByTagName('RateV4Response')->item(0);
					$intl_rate_response = $dom->getElementsByTagName('IntlRateV2Response')->item(0);
					$error = $dom->getElementsByTagName('Error')->item(0);

					$firstclasses = array ( // updated 2013-07-30
						  'First-Class Mail<sup>&174</sup> Parcel',
						  'First-Class Mail<sup>&174</sup> Large Envelope',
						  'First-Class Mail<sup>&174</sup> Letter',
						  'First-Class Mail<sup>&174</sup> Postcards'
					       );

					if ($rate_response || $intl_rate_response) {
						if ($address['iso_code_2'] == 'US') {
							$allowed = array(0, 1, 2, 3, 4, 5, 6, 7, 12, 13, 16, 17, 18, 19, 22, 23, 25, 27, 28);
							$cost = array(); // running sum total cost of all packages indexed by postage class
							$member_cost = array(); // by package (member customer/member)
							$package_info = array(); // package info by member_customer_id

							$return_packages = $rate_response->getElementsByTagName('Package'); // modified for multiple packages (Member)
							
							foreach ($return_packages as $return_package) {
								$packageid = $return_package->getAttribute('ID'); // package id returned is member_customer_id
								
								$package_zip_origination = $return_package->getElementsByTagName('ZipOrigination')->item(0);
								$package_zip_destination = $return_package->getElementsByTagName('ZipDestination')->item(0);
								$package_pounds = $return_package->getElementsByTagName('Pounds')->item(0);
								$package_ounces = $return_package->getElementsByTagName('Ounces')->item(0);
								$package_size = $return_package->getElementsByTagName('Size')->item(0);
								$package_width = $return_package->getElementsByTagName('Width')->item(0);
								$package_length = $return_package->getElementsByTagName('Length')->item(0);
								$package_height = $return_package->getElementsByTagName('Height')->item(0);
								$package_machinable = $return_package->getElementsByTagName('Machinable')->item(0);
								$package_zone = $return_package->getElementsByTagName('Zone')->item(0);
								
								$package_info[$packageid] = array(
									'zip_origination' => (!empty($package_zip_origination) ? $package_zip_origination->nodeValue : '0'),
									'zip_destination' => (!empty($package_zip_destination) ? $package_zip_destination->nodeValue : '0'),
									'pounds' => (!empty($package_pounds) ? $package_pounds->nodeValue : '0'),
									'ounces' => (!empty($package_ounces) ? $package_ounces->nodeValue : '0'),
									'size' => (!empty($package_size) ? $package_size->nodeValue : ''),
									'width' => (!empty($package_width) ? $package_width->nodeValue : '0'),
									'length' => (!empty($package_length) ? $package_length->nodeValue : '0'),
									'height' => (!empty($package_height) ? $package_height->nodeValue : '0'),
									'machinable' => (!empty($package_machinable) ? $package_machinable->nodeValue : ''),
									'zone' => (!empty($package_zone) ? $package_zone->nodeValue : '0')
								);

								$postages = $return_package->getElementsByTagName('Postage');

								if ($postages->length) {
																		
									foreach ($postages as $postage) {
										$classid = $postage->getAttribute('CLASSID'); // postage class id

										if (in_array($classid, $allowed)) {
											// first class rates
											if ($classid == '0') {
												$mailservice = $postage->getElementsByTagName('MailService')->item(0)->nodeValue;

												foreach ($firstclasses as $k => $firstclass)  {
													if ($firstclass == $mailservice) {
														$classid = $classid . $k;
														break;
													}
												}

												if (($this->config->get('usps_domestic_' . $classid))) {
													$member_cost[$packageid] = $postage->getElementsByTagName('Rate')->item(0)->nodeValue;
													if (isset($cost[$classid])) {
														$cost[$classid] += $postage->getElementsByTagName('Rate')->item(0)->nodeValue; // running sum total cost of all packages
													} else {
														$cost[$classid] = $postage->getElementsByTagName('Rate')->item(0)->nodeValue; // package cost for this class
													}

													$quote_data[$classid] = array(
														'code'         => 'usps.' . $classid,
														'title'        => $postage->getElementsByTagName('MailService')->item(0)->nodeValue,
														'cost'         => $this->currency->convert($cost[$classid], 'USD', $this->config->get('config_currency')),
														'tax_class_id' => $this->config->get('usps_tax_class_id'),
														'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($cost[$classid], 'USD', $this->currency->getCode()), $this->config->get('usps_tax_class_id'), $this->config->get('config_tax')), $this->currency->getCode(), 1.0000000),
														'member_cost'		=> $member_cost
													);
												}
											/*
											 * 2013-09-13
											 * tried to hide Standard Post (classid=4) when Priority (classid=1) already available,
											 * but this caused problems with multiple products from different manufacturers.
											 * if a product was small and priority was returned for one vendor, then a large product for another vendor would not get calculated.
											 * abandoned; would have to split out into separate packages and calc each rate quotes separately for each package (vendor).
											 */
											// } elseif (($classid == '4') && !empty($quote_data[1])) {
												
											} elseif ($this->config->get('usps_domestic_' . $classid)) {
												$member_cost[$packageid] = $postage->getElementsByTagName('Rate')->item(0)->nodeValue;
												if (isset($cost[$classid])) {
													$cost[$classid] += $postage->getElementsByTagName('Rate')->item(0)->nodeValue; // running sum total cost of all packages
												} else {
													$cost[$classid] = $postage->getElementsByTagName('Rate')->item(0)->nodeValue; // package cost for this class
												}

												// USPS domestic US rate quotes
												$quote_data[$classid] = array(
													'code'         => 'usps.' . $classid,
													'title'        => $postage->getElementsByTagName('MailService')->item(0)->nodeValue,
													'cost'         => $this->currency->convert($cost[$classid], 'USD', $this->config->get('config_currency')),
													'tax_class_id' => $this->config->get('usps_tax_class_id'),
													'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($cost[$classid], 'USD', $this->currency->getCode()), $this->config->get('usps_tax_class_id'), $this->config->get('config_tax')), $this->currency->getCode(), 1.0000000),
													'member_cost'		=> $member_cost
												);
											}
										}
									}
								} else {
									$error = $return_package->getElementsByTagName('Error')->item(0);

									$method_data = array(
										'id'         => 'usps',
										'title'      => $this->language->get('text_title'),
										'quote'      => $quote_data,
										'sort_order' => $this->config->get('usps_sort_order'),
										'error'      => $error->getElementsByTagName('Description')->item(0)->nodeValue
									);
								}
							}
						} else {
							$allowed = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 21);

							$package = $intl_rate_response->getElementsByTagName('Package')->item(0); // not modified by Member

							$services = $package->getElementsByTagName('Service');

							foreach ($services as $service) {
								$id = $service->getAttribute('ID');

								if (in_array($id, $allowed) && $this->config->get('usps_international_' . $id)) {
									$title = $service->getElementsByTagName('SvcDescription')->item(0)->nodeValue;

									if ($this->config->get('usps_display_time')) {
										$title .= ' (' . $this->language->get('text_eta') . ' ' . $service->getElementsByTagName('SvcCommitments')->item(0)->nodeValue . ')';
									}

									$cost = $service->getElementsByTagName('Postage')->item(0)->nodeValue;

									$quote_data[$id] = array(
										'code'         => 'usps.' . $id,
										'title'        => $title,
										'cost'         => $this->currency->convert($cost, 'USD', $this->config->get('config_currency')),
										'tax_class_id' => $this->config->get('usps_tax_class_id'),
										'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($cost, 'USD', $this->currency->getCode()), $this->config->get('usps_tax_class_id'), $this->config->get('config_tax')), $this->currency->getCode(), 1.0000000)
									);
								}
							}
						}
					} elseif ($error) {
						$method_data = array(
							'code'       => 'usps',
							'title'      => $this->language->get('text_title'),
							'quote'      => $quote_data,
							'sort_order' => $this->config->get('usps_sort_order'),
							'error'      => $error->getElementsByTagName('Description')->item(0)->nodeValue
						);
					}
				}
			}

	  		if ($quote_data) {
				$title = $this->language->get('text_title');

				if ($this->config->get('usps_display_weight')) {
					$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('usps_weight_class_id')) . ')';
				}
								
      			$method_data = array(
        			'code'       => 'usps',
        			'title'      => $title,
        			'quote'      => $quote_data,
        			'packages'	 => $package_info,
					'sort_order' => $this->config->get('usps_sort_order'),
        			'error'      => false
      			);
			}
		}

		return $method_data;
	}
}
?>