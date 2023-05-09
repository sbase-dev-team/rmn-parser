<?php

class Detoxrehabs_net extends AScraper
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
        // parse pages like https://www.detoxrehabs.net/states/alaska/
        $rso = $xpath->query("//div/article/*[self::h2[not(span)] or self::h3][following-sibling::div[contains(concat(' ', normalize-space(@class), ' '), ' location-info')]]");

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

                $numberForReplace = ((int) rmnTrim($item->textContent));
                $number = $numberForReplace - 1;

                $tempDataItem["name"] = str_replace("$numberForReplace. ", "", rmnTrim($item->textContent));

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '), ' location-info ')]", $parent);
                if ($itemprop->length > 0 && $rso->length === $itemprop->length) {
                    if ($itemprop->item($number)?->textContent) {
                        $str = str_replace(["Location and contact information:\n", "\n"], "", $itemprop->item($number)->textContent);
                        preg_match_all("/\(?\d{3}\)?[\s-]?\d{3}[\s-]? ?\d{4}/", $str, $matches, PREG_SET_ORDER, 0);
                        if (!empty($matches[0][0])) {
                            $tempDataItem["telephone"] = rmnTrim($matches[0][0]);
                            $str = str_replace($matches[0][0], "", $str);
                        }

                        if (!empty($str)) {
                            $arr = explode(",", rmnTrim($str));
                            if (count($arr) === 2) {
                                if (!empty($arr[0])) {
                                    $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                                }
                                if (!empty($arr[1])) {
                                    $regionAndPostalCode = explode(" ", rmnTrim($arr[1]));
                                    if (count($regionAndPostalCode) >= 2) {
                                        if (!empty($regionAndPostalCode[0])) {
                                            $tempDataItem["addressRegion"] = rmnTrim($regionAndPostalCode[0]);
                                        }
                                        if (!empty($regionAndPostalCode[1])) {
                                            $tempDataItem["postalCode"] = rmnTrim($regionAndPostalCode[1]);
                                        }
                                        if (!empty($regionAndPostalCode[0]) &&
                                            !empty($regionAndPostalCode[1]) &&
                                            !empty($arr[0])
                                        ) {
                                            $tempDataItem["address"] = rmnTrim($arr[0]) . ", " . rmnTrim($regionAndPostalCode[0]) . " " . rmnTrim($regionAndPostalCode[1]);
                                        }
                                    }
                                }
                            }
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

        // parse pages like https://www.detoxrehabs.net/cities/grundy-va/
        $rso = $xpath->query("//div/article/div/div/h3/a");

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

                $itemprop = $xpath->query("parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' address ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                    $arr = explode(",", $tempDataItem["address"]);
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
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse pages like https://www.detoxrehabs.net/centers/family-net-of-catawba-county-2/
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

                $itemprop = $xpath->query("parent::div/div/div/div/div/address", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = str_replace("\n", ", ", trim($itemprop->item(0)->textContent));
                    $arr = explode(",", $tempDataItem["address"]);
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
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/article/div[h2[contains(text(), 'Contact Information')]]/ul/li/a[contains(concat(' ', normalize-space(@class), ' '), ' center-phone ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/article/div[h2[contains(text(), 'Contact Information')]]/ul/li/a[contains(concat(' ', normalize-space(@class), ' '), ' center-phone ')]/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephoneLink"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/article/div[h2[contains(text(), 'Contact Information')]]/ul/li/a[contains(concat(' ', normalize-space(@class), ' '), ' center-website ')]/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["website"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/article/div[h2[contains(text(), 'Primary Type Of Service Provided')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["serviceProvided"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/article/div[h2[contains(text(), 'Type Of Care')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["typeOfCare"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/article/div[h2[contains(text(), 'Service Setting')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["serviceSetting"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/article/div[h2[contains(text(), 'Types Of Payment Accepted')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["paymentTypes"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/article/div[h2[contains(text(), 'Languages')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["languages"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/article/div[h2[contains(text(), 'Special Programs Offered')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["specialPrograms"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/article/div[h2[contains(text(), 'Age Groups Accepted')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["ageGroup"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/div/div/div/article/div[h2[contains(text(), 'Genders Accepted')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["gendersAccepted"][] = rmnTrim($value->textContent);
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