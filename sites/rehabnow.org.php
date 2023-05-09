<?php

class Rehabnow_org extends AScraper
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


        // parse pages like https://rehabnow.org/in/alabama/
        $rso = $xpath->query("//div/div/div/div/div/div/div/div/div/div/h5/a");

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

                $itemprop = $xpath->query("parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' result-location')]/text()", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' result-location-minimal ')]", $parent);
                }

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["address"] = rmnTrim($firstItem->textContent);

                    $arr = explode(',', rmnTrim($firstItem->textContent));

                    if (count($arr) === 2) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressRegion"] = rmnTrim($arr[1]);
                        }
                    }

                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[2])) {
                            $regionAndPostalCode = explode(" ", rmnTrim($arr[2]));
                            if (count($regionAndPostalCode) === 2) {
                                if (!empty($regionAndPostalCode[0])) {
                                    $tempDataItem["addressRegion"] = rmnTrim($regionAndPostalCode[0]);
                                }
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem["postalCode"] = rmnTrim($regionAndPostalCode[1]);
                                }
                            }
                        }
                    }

                    if (count($arr) === 4) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[2])) {
                            $tempDataItem["addressRegion"] = rmnTrim($arr[2]);
                        }
                        if (!empty($arr[3])) {
                            $tempDataItem["postalCode"] = rmnTrim($arr[3]);
                        }
                    }

                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' result-loc ')]/p", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(",", rmnTrim($itemprop->item(0)->textContent));

                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            $tempDataItem["treatmentTypes"][] = rmnTrim($value);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/ul[contains(concat(' ', normalize-space(@class), ' '), ' result-payment ')]/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["paymentTypes"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' result-additional ')]/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["additional"][] = rmnTrim($value->textContent);
                    }
                }



                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse pages like https://rehabnow.org/center/bradford-health-services-birmingham-regional-office-jefferson-birmingham-al/
        $rso = $xpath->query("//div/div/div/div/div/h1");

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

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/div[h5[@id='location']]/p[1]/text()", $parent);

                if ($itemprop->length > 0) {
                    $addressStreet = rmnTrim($itemprop->item(0)->textContent);
                    $addressLocalityAndRegionAndPostalCode = rmnTrim($itemprop->item(1)->textContent);
                    $tempDataItem["address"] = "$addressStreet, $addressLocalityAndRegionAndPostalCode";
                    $tempDataItem["addressStreet"] = $addressStreet;
                    $arr = explode(",", $addressLocalityAndRegionAndPostalCode);
                    if (count($arr) === 2) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $regionAndPostalCode = explode(" ", rmnTrim($arr[1]));
                            if (count($regionAndPostalCode) === 2) {
                                if (!empty($regionAndPostalCode[0])) {
                                    $tempDataItem["addressRegion"] = rmnTrim($regionAndPostalCode[0]);
                                }
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem["postalCode"] = rmnTrim($regionAndPostalCode[1]);
                                }
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/section[@id='treatment-for']/div/div/div/div/p", $parent);

                if ($itemprop->length > 0) {
                    $conditions = rmnTrim($itemprop->item(0)->textContent);
                    if (!empty($conditions)) {
                        $arr = explode(",", $conditions);
                        foreach ($arr as $value) {
                            $tempDataItem["additionalConditions"][] = rmnTrim($value);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/section[@id='levels-of-care']/h3", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["levelsOfCare"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/section[@id='therapies']/div/div[div[span[a[span[contains(text(), 'Additional Therapies & Programs')]]]]]/div/div/p", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(",", rmnTrim($itemprop->item(0)->textContent));
                    foreach ($arr as $value) {
                        $tempDataItem["additionalPrograms"][] = rmnTrim($value);
                    }
                }


                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/section[@id='payment-options']/div/div/div/li[contains(concat(' ', normalize-space(@class), ' '), ' single-payment-option yes ')]", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["paymentTypes"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/section[@id='additional']/div/div[contains(concat(' ', normalize-space(@class), ' '), ' wp-block-toolset-blocks-grid-column tb-grid-column tb-grid-align-top center-addl ')]", $parent);

                if ($itemprop->length > 0) {
                    preg_match_all("/\(?\d{3}\)?[\s-]?\d{3}[\s-]\d{4}/", rmnTrim($itemprop->item(0)->textContent), $matches, PREG_SET_ORDER, 0);
                    if (!empty($matches[0][0])) {
                        $tempDataItem["telephone"] = $matches[0][0];
                    }
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse bottom data from pages like
        // https://rehabnow.org/center/bradford-health-services-birmingham-regional-office-jefferson-birmingham-al/
        $rso = $xpath->query("//section[@id='float-end']/div/div/div/div/div/div/h5");

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

                $itemprop = $xpath->query("parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' result-location ')]", $parent);

                if ($itemprop->length > 0) {
                    $address = rmnTrim($itemprop->item(0)->textContent);
                    $tempDataItem["address"] = $address;
                    $arr = explode(",", $address);
                    
                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[2])) {
                            $regionAndPostalCode = explode(" ", rmnTrim($arr[2]));
                            if (count($regionAndPostalCode) === 2) {
                                if (!empty($regionAndPostalCode[0])) {
                                    $tempDataItem["addressRegion"] = rmnTrim($regionAndPostalCode[0]);
                                }
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem["postalCode"] = rmnTrim($regionAndPostalCode[1]);
                                }
                            }
                        }
                    }

                    if (count($arr) === 4) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[2])) {
                            $tempDataItem["addressRegion"] = rmnTrim($arr[2]);
                        }
                        if (!empty($arr[3])) {
                            $tempDataItem["postalCode"] = rmnTrim($arr[3]);
                        }
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