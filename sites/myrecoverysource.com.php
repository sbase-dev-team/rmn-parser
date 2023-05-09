<?php

class Myrecoverysource_com extends AScraper
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

        // parse pages like https://myrecoverysource.com/listings/dallas-detox-center/
        $rso = $xpath->query("//div/section/div/div/div/div/div[div[div[div[contains(concat(' ', normalize-space(@class), ' '), ' elementor-heading-title elementor-size-default ')]]]]/div/div/h2/text()[1]");

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

                $itemprop = $xpath->query("parent::div/parent::div/div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' raven-widget-wrapper ')]/a[span[span]][1]/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["website"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/div/div/ul/li[span[i[contains(concat(' ', normalize-space(@class), ' '), ' fas fa-map-pin ')]]]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                    $arr = explode(", ", $tempDataItem["address"]);

                    if (count($arr) > 0) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressRegion"] = rmnTrim($arr[1]);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/parent::section/parent::div/section/div/div/div/div/div/section/div/div/div/div/div[div[div[div[h4[span[contains(text(), 'Levels of Care')]]]]]]/div/div/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value)) {
                            $tempDataItem["levelsOfCare"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/parent::section/parent::div/section/div/div/div/div/div/section/div/div/div/div/div[div[div[div[h4[span[contains(text(), 'Facility Amenities')]]]]]]/div/div/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value)) {
                            $tempDataItem["facilityAmenities"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/parent::section/parent::div/section/div/div/div/div/div/section/div/div/div/div/div[div[div[div[h4[span[contains(text(), 'Therapies & Approach')]]]]]]/div/div/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value)) {
                            $tempDataItem["therapiesAndApproach"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/parent::section/parent::div/section/div/div/div/div/div/section/div/div/div/div/div[div[div[div[h4[span[contains(text(), 'Finances & Insurance')]]]]]]/div/div/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value)) {
                            $tempDataItem["financesAndInsurance"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
                break;
            }
        }

        // parse pages like https://myrecoverysource.com/find-treatment/alabama/
        // parse Data from top
        $rso = $xpath->query("//section[contains(concat(' ', normalize-space(@class), ' '), ' elementor-section elementor-top-section elementor-element elementor-element-4e99111 elementor-section-boxed elementor-section-height-default elementor-section-height-default ')]/div/div/div/div/div/div/div/h3");

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

                $itemprop = $xpath->query("parent::div/parent::div/div/div/ul/li/a/span[contains(concat(' ', normalize-space(@class), ' '), ' raven-icon-list-text raven-post-meta-item raven-post-meta-item-type-custom ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressLocality"] = rmnTrim($itemprop->item(0)->textContent);
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse pages like https://myrecoverysource.com/find-treatment/alabama/
        // parse Drug & Alcohol Rehabs
        $rso = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' eael-filterable-gallery-item-wrap ')]/div/div/h5");
        if ($rso->length === 0) {
            $rso = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-81a6e84 ')]/div/div/section[contains(concat(' ', normalize-space(@class), ' '), ' elementor-section elementor-inner-section elementor-element ')]/div/div/div/div/div/div/div/div/div/h3");
        }

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

                $name = explode(" ", rmnTrim(preg_replace("([A-Z])", " $0", $item->textContent)));
                $tempStr = "";
                foreach ($name as $str) {
                    if (!empty($str)) {
                        $tempStr .= $str . " ";
                    }
                }

                $tempDataItem["name"] = rmnTrim($tempStr);

                $itemprop = $xpath->query("div/div/p/text()", $parent);
                $checkPage = false;
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("div/p/text()", $parent);
                    if ($itemprop->length === 0) {
                        $itemprop = $xpath->query("p[contains(concat(' ', normalize-space(@class), ' '), ' elementor-icon-box-description ')]", $parent);
                        $checkPage = true;
                    }
                }

                if ($itemprop->length > 0) {
                    if (!$checkPage) {
                        $tempDataItem["address"] = substr(rmnTrim($itemprop->item(0)->textContent), 5);
                    } else {
                        $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                    }
                    $arr = explode(", ", $tempDataItem["address"]);

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

        if(empty($data)){
            return null;
        }

        return $data;
    }

}