<?php

class Nationaltasc_org extends AScraper
{
    /**
     * @param $dataHTML
     * @return array|null
     */
    public function parseData($dataHTML): ?array
    {
        $data = [];

        //response to dom document
        $dom = new DOMDocument();

        if(empty($dataHTML)){
            return null;
        }
        @$dom->loadHTML($dataHTML);

        $xpath = new DOMXPath($dom);
        $rso = $xpath->query("//div[contains(concat(' ',normalize-space(@class),' '),' single-head-top-left ')]/h1");

        $count = 0;
        if ($rso->length > 0) {
            foreach ($rso as $item) {
                //get parent element
                $parent = $item->parentNode;

                if ($parent instanceof DOMDocument) {
                    continue;
                }

                if (!$item->textContent) {
                    continue;
                }

                $tempDataItem = [];

                $tempDataItem["name"] = rmnTrim($item->textContent);

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element azp-element-azp-2x9n130lnp6 azp_col azp-col-33 lcontent-widget ')]/div[1]/div/section/div/div/div/div[2]/div/div/a/@href", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element azp-element-azp-2x9n130lnp6 azp_col azp-col-33 lcontent-widget ')]/div[2]/div[2]/div[2]/div[2]/div/ul/li[contains(concat(' ',normalize-space(@class),' '),' aucontact-web ')]/a/@href", $parent);
                }

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['website'] = rmnTrim($firstItem->textContent);
                }


                $itemprop = $xpath->query("div/a[2]/text()[2]", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element azp-element-azp-2x9n130lnp6 azp_col azp-col-33 lcontent-widget ')]/div[2]/div[2]/div[2]/div[2]/div/ul/li[contains(concat(' ',normalize-space(@class),' '),' aucontact-phone ')]/a", $parent);
                }

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['telephone'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("div/a[2]/@href", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element azp-element-azp-2x9n130lnp6 azp_col azp-col-33 lcontent-widget ')]/div[2]/div[2]/div[2]/div[2]/div/ul/li[contains(concat(' ',normalize-space(@class),' '),' aucontact-phone ')]/a/@href", $parent);
                }

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['telephoneLink'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("div/a[1]/text()", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element azp-element-azp-2x9n130lnp6 azp_col azp-col-33 lcontent-widget ')]/div[2]/div[2]/div[2]/div[2]/div/ul/li[contains(concat(' ',normalize-space(@class),' '),' aucontact-address ')]/a", $parent);
                }

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['address'] = rmnTrim($firstItem->textContent);
                    $arr = explode(',', rmnTrim($firstItem->textContent));
                    if (count($arr) > 1) {
                        if (!empty($arr[0])) {
                            $tempDataItem['addressStreet'] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem['addressLocality'] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[2])) {
                            $regionAndCode = explode(' ', rmnTrim($arr[2]));
                            if (count($regionAndCode) > 1) {
                                $tempDataItem['addressRegion'] = rmnTrim($regionAndCode[0]);
                                $tempDataItem['postalCode'] = rmnTrim($regionAndCode[1]);
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Addiction Treatment')]]/following-sibling::ul[1]/li/b", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Addiction Counseling')]]/following-sibling::ul[1]/li/b", $parent);
                }

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Addiction Treatment')]]/following-sibling::ul[1]/li/b", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Treatment Programs')]]/following-sibling::ul[1]/li/b", $parent);
                    if ($itemprop->length === 0) {
                        $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Treatment Programs')]]/following-sibling::ul[1]/li/b", $parent);
                    }
                }

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Substance Abuse Treatment Programs')]]/following-sibling::ul[1]/li/strong", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Substance Abuse Treatment Programs')]]/following-sibling::ul[1]/li/strong/span", $parent);
                    if ($itemprop->length === 0) {
                        $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Substance Abuse')]]/following-sibling::ul[1]/li/b", $parent);
                        if ($itemprop->length === 0) {
                            $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Mental Health Problems')]]/following-sibling::ul[1]/li/b", $parent);
                        }
                    }
                }

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Additional Services')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Drug or Alcohol Treatment')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Vice Triggers')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Treatment Concepts')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Recovery Program Types')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Counseling Components')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Holistic Treatment')]]/following-sibling::ul[1]/li/b", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Holistic Treatment')]]/following-sibling::ul[1]/li/span", $parent);
                }

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Diagnosis Treatment')]]/following-sibling::ul[1]/li/strong/span", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Diagnosis Treatment')]]/following-sibling::ul[1]/li/span", $parent);
                }

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Detoxification')]]/following-sibling::ul[1]/li/strong/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Cognitive Behavioral Therapy')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Emotional Health Recovery')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Specialties')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentPrograms'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Gender-Specific')]]/following-sibling::ul[1]/li/b", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Gender-Specific')]]/following-sibling::ul[1]/li/b", $parent);
                    if ($itemprop->length === 0) {
                        $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Gender-Specific')]]/following-sibling::ul[1]/li/strong/span", $parent);
                    }
                }

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['genderSpecific'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Therapy Services')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['therapyTypes'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Emotional and Spiritual Therapy')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['therapyTypes'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Types of Addiction')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['therapyTypes'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Experiential and Alternative Therapy')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['therapyTypes'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Additional Therapy')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['therapyTypes'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Therapy Types')]]/following-sibling::ul[1]/li/strong/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['therapyTypes'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Therapy Types')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['therapyTypes'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/p[b[contains(text(), 'Residential Program')]]/following-sibling::ul[1]/li/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['therapyTypes'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[b[contains(text(), 'Types of Therapy')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['therapyTypes'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Drug Addiction Abuse Treatment')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['drugAddiction'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h3[span[contains(text(), 'Sober Activities')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['soberActivities'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Life Skills Education and Aftercare')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['lifeSkills'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Skills')]]/following-sibling::ul[1]/li/strong/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['lifeSkills'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' azp_element ldescription azp-element-azp-se6qk9apvyj authplan-hide-false ')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-box')]/div[contains(concat(' ',normalize-space(@class),' '),' lsingle-block-content ')]/h2[span[contains(text(), 'Mission Statement')]]/following-sibling::ul[1]/li/b", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['missionStatement'][] = rmnTrim($value->textContent);
                    }
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }

            }
        }

        if(empty($data)){
            return null;
        }

        return $data;
    }

}