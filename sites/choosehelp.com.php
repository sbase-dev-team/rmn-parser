<?php

class Choosehelp_com extends AScraper
{
    public bool $sponsor = true;
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

        // parse pages with sponsors like https://www.choosehelp.com/rehab
        // https://www.choosehelp.com/rehab?page=2#collapsible-regions
        // https://www.choosehelp.com/indiana
        $rso = $xpath->query("//li[contains(concat(' ', normalize-space(@class), ' '), ' rd-facilityItem--featured ')]/div/a/div[contains(@itemprop, 'name')]");

        $count = 0;
        if ($rso->length > 0 && $this->sponsor) {
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

                $itemprop = $xpath->query("parent::div/a/span", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['telephone'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['telephoneLink'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("div/span[contains(@itemprop, 'addressLocality')]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['addressLocality'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("div/span[contains(@itemprop, 'addressRegion')]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['addressRegion'] = rmnTrim($firstItem->textContent);
                }


                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '), ' rd-facilityItem__type--featured ')]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $arr = explode(',', rmnTrim($firstItem->textContent));
                    if (count($arr) > 1) {
                        foreach ($arr as $value) {
                            $tempDataItem['facility'][] = rmnTrim($value);
                        }
                    } else {
                        $tempDataItem['facility'][] = rmnTrim($firstItem->textContent);
                    }
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse pages like https://www.choosehelp.com/alabama
        // https://www.choosehelp.com/alabama/outpatient-rehab-3?subcategory=free-rehab-no-insurance
        $rso = $xpath->query("//li[contains(concat(' ', normalize-space(@class), ' '), ' rd-facilityItem ')]/a/div[contains(@itemprop, 'name')]");

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

                $itemprop = $xpath->query("div/span[contains(@itemprop, 'address')]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['address'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '), ' rd-facilityItem__type ')]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $arr = explode(',', rmnTrim($firstItem->textContent));
                    if (count($arr) > 1) {
                        foreach ($arr as $value) {
                            $tempDataItem['facility'][] = rmnTrim($value);
                        }
                    } else {
                        $tempDataItem['facility'][] = rmnTrim($firstItem->textContent);
                    }
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse pages like https://www.choosehelp.com/rehab/regional-economic-comm-action-program-recapoutpatient-rehab-program-middletown-newyork-7087
        $rso = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' rd-section rdFacility-header ')]/div/div/div/div/div/h1[contains(@itemprop, 'name')]");

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

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/a[contains(@itemprop, 'url')]/@href", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' rdFacility-locationSection ')]/div/a[contains(@itemprop, 'url')]", $parent);
                }

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['website'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/a[contains(@itemprop, 'telephone')]/text()[2]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['telephone'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/a[contains(@itemprop, 'telephone')]/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['telephoneLink'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/a[meta[contains(@itemprop, 'email')]]/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['emailLink'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' rdFacility-locationSection ')]/div/div/span[contains(@itemprop, 'streetAddress')]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['addressStreet'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' rdFacility-locationSection ')]/div/div/strong[contains(@itemprop, 'addressLocality')]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['addressLocality'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' rdFacility-locationSection ')]/div/div/span[contains(@itemprop, 'addressRegion')]", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['addressRegion'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' rdFacility-locationSection ')]/div/div/span[contains(@itemprop, 'addressCountry')]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['addressCountry'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' rdFacility-locationSection ')]/div/div/span[contains(@itemprop, 'postalCode')]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['postalCode'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '), ' rdFacility-facilityType ')]/text()[1]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $arr = explode(',', rmnTrim($firstItem->textContent));
                    if (count($arr) > 1) {
                        foreach ($arr as $value) {
                            $tempDataItem['facility'][] = rmnTrim($value);
                        }
                    } else {
                        $tempDataItem['facility'][] = rmnTrim($firstItem->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' rd-flexContainer ')]/div/div[h3[contains(text(), 'Payment Options')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['paymentOptions'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' rdFacility-detailsList ')]/div/div[h3[contains(text(), 'Treatments Offered')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentTypes'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' rdFacility-detailsList ')]/div/div[h3[contains(text(), 'Patient Population')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['patientPopulation'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' rdFacility-detailsList ')]/div/div[h3[contains(text(), 'Extra Services')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['extraServices'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' rdFacility-detailsList ')]/div/div[h3[contains(text(), 'Spoken Languages')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['spokenLanguages'][] = rmnTrim($value->textContent);
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