<?php

class Drugabuse_com extends AScraper
{
    private bool $sponsor = true;
    /**
     * @param $dataHTML
     * @return array|null
     */
    public function parseData($dataHTML): ?array
    {
        $data = [];

        //response to dom document
        $dom = new DOMDocument();

        if (empty($dataHTML)) {
            return null;
        }
        @$dom->loadHTML($dataHTML);

        $xpath = new DOMXPath($dom);

        // parse only sponsored items from pages like https://drugabuse.com/treatment-centers/california/
        $rso = $xpath->query("//div[div[div[p[contains(text(), 'Sponsored')]]]]/div[contains(concat(' ', normalize-space(@class), ' '), ' listing-details ')]/a/span");

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

                $itemprop = $xpath->query("parent::div/div[contains(concat(' ',normalize-space(@class),' '),' listing-details__address-container ')]/div", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['address'] = rmnTrim($firstItem->textContent);
                    $arr = explode(',' , $firstItem->textContent);
                    if (count($arr) === 2) {
                        if (!empty($arr[1])) {
                            $regionAndPostalCode = explode(' ', trim($arr[1]));
                            if (count($regionAndPostalCode) > 1) {
                                if (!empty($regionAndPostalCode[0])) {
                                    $tempDataItem['addressRegion'] = rmnTrim($regionAndPostalCode[0]);
                                }
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem['postalCode'] = rmnTrim($regionAndPostalCode[1]);
                                }
                            }
                        }
                    }
                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem['addressStreet'] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem['addressLocality'] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[2])) {
                            $regionAndPostalCode = explode(' ', trim($arr[2]));
                            if (count($regionAndPostalCode) > 1) {
                                if (!empty($regionAndPostalCode[0])) {
                                    $tempDataItem['addressRegion'] = rmnTrim($regionAndPostalCode[0]);
                                }
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem['postalCode'] = rmnTrim($regionAndPostalCode[1]);
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

        // parse non-sponsored items from pages like https://drugabuse.com/treatment-centers/california/
        // and pages like https://drugabuse.com/treatment-centers/california/los-angeles/
        $rso = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' listing-free__container ')]/div/a/span");

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

                $itemprop = $xpath->query("parent::div/div[contains(concat(' ',normalize-space(@class),' '),' listing-details__address-container ')]/div", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['address'] = rmnTrim($firstItem->textContent);
                    $arr = explode(',' , $firstItem->textContent);
                    if (count($arr) === 2) {
                        if (!empty($arr[1])) {
                            $regionAndPostalCode = explode(' ', trim($arr[1]));
                            if (count($regionAndPostalCode) > 1) {
                                if (!empty($regionAndPostalCode[0])) {
                                    $tempDataItem['addressRegion'] = rmnTrim($regionAndPostalCode[0]);
                                }
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem['postalCode'] = rmnTrim($regionAndPostalCode[1]);
                                }
                            }
                        }
                    }
                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem['addressStreet'] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem['addressLocality'] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[2])) {
                            $regionAndPostalCode = explode(' ', trim($arr[2]));
                            if (count($regionAndPostalCode) > 1) {
                                if (!empty($regionAndPostalCode[0])) {
                                    $tempDataItem['addressRegion'] = rmnTrim($regionAndPostalCode[0]);
                                }
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem['postalCode'] = rmnTrim($regionAndPostalCode[1]);
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

        // parse pages like https://drugabuse.com/treatment-centers/california/los-angeles/recover-integrity-for-men-1636782866/
        $rso = $xpath->query("//div/parent::section/div[contains(concat(' ', normalize-space(@class), ' '), ' profile__header-container ')]/div/h1");

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

                $checkSponsored = $xpath->query("parent::div/parent::section/div/div[contains(text(), 'Sponsored Facility')]", $parent);
                if ($checkSponsored->length > 0 && !$this->sponsor) {
                    continue;
                }

                $tempDataItem = [];

                $tempDataItem["name"] = rmnTrim($item->textContent);

                $itemprop = $xpath->query("parent::div/div[contains(concat(' ',normalize-space(@class),' '),' profile__header-actions ')]/div/div/a/text()[2]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['telephone'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/div[contains(concat(' ',normalize-space(@class),' '),' profile__header-actions ')]/div/div/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['telephoneLink'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::section/parent::div/section/div/div[contains(concat(' ',normalize-space(@class),' '),' profile__content--body ')]/div/ul/li/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['website'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("div[contains(concat(' ',normalize-space(@class),' '),' jsx-2645139338 ')]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['address'] = rmnTrim($firstItem->textContent);
                    $arr = explode(',' , $firstItem->textContent);
                    if (count($arr) > 1) {
                        if (!empty($arr[0] && !empty($arr[1]))) {
                            $tempDataItem['addressStreet'] = rmnTrim($arr[0]) . ' ' . rmnTrim($arr[1]);
                        }
                        if (!empty($arr[2])) {
                            $tempDataItem['addressLocality'] = rmnTrim($arr[2]);
                        }
                        if (!empty($arr[3])) {
                            $tempDataItem['addressRegion'] = rmnTrim($arr[3]);
                        }
                        if (!empty($arr[4])) {
                            $tempDataItem['postalCode'] = rmnTrim($arr[4]);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::section/parent::div/section/aside/div[contains(concat(' ',normalize-space(@class),' '),' treatment-types ')]/div/ul/li[span]/div", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::section/parent::div/section/aside/section/ul/li[contains(concat(' ', normalize-space(@class), ' '), ' li--checked ')]/div[contains( concat(' ', normalize-space(@class), ' '),' type_of_care__li__wrapper--text ')]", $parent);
                }

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentTypes'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::section/parent::div/section/div/div/div/div/div/div[div/div/h3[contains(text(), 'Facility Highlights')]]/div/div/ul/li/div", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['facilityHighlights'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::section/parent::div/section/div/div/div/div/div/div[div/div/h3[contains(text(), 'Specialization')]]/div/div/ul/li/h4", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['specialization'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::section/parent::div/section/div/div/div/div/div/div[div/div/h3[contains(text(), 'Facility Settings')]]/div/div/ul/li/div", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['facilitySettings'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::section/parent::div/section/div/div/div/div/div/div[div/div/h3[contains(text(), 'Financial Details')]]/div/div/ul/li/div", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::section/parent::div/section/div/div/div/div/ul/li[p[contains(text(), 'Payment')]]/ul/li", $parent);
                }

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['financialDetails'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::section/parent::div/section/div/div/div/div/ul/li[p[contains(text(), 'Facility Details')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['facilityDetails'][] = rmnTrim($value->textContent);
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