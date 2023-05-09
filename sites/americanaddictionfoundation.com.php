<?php

class Americanaddictionfoundation_com extends AScraper
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

        // parse pages like https://www.americanaddictionfoundation.com/directory/categories/huntsville-al
        $rso = $xpath->query("//div/div/div/div/div/div/div/div/div/div/div/a[contains(concat(' ', normalize-space(@class), ' '), ' sabai-entity-type-content sabai-entity-bundle-name-directory-listing sabai-entity-bundle-type-directory-listing ')]");

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

                $itemprop = $xpath->query("parent::div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' sabai-directory-location ')]/span", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                    $arr = explode(", ", $tempDataItem["address"]);

                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressLocation"] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[0])) {
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
                            $tempDataItem["addressLocation"] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[0])) {
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

                $itemprop = $xpath->query("parent::div/div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' sabai-directory-contact-tel ')]/span[@itemprop='telephone']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' sabai-directory-contact-website ')]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["website"] = rmnTrim($itemprop->item(0)->textContent);
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse pages like https://www.americanaddictionfoundation.com/medicare-finder/florida/
        $rso = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' fusion-text ')]/h2[preceding-sibling::h2 and following-sibling::p]");

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

                $itemprop = $xpath->query("//p[preceding-sibling::h2[span[contains(text(), '". $tempDataItem["name"] . "')]]][1]", $parent);

                if ($itemprop->length > 0) {
                    if (str_contains(rmnTrim($itemprop->item(0)->textContent), "–"))
                    {
                        $tempDataItem["address"] = str_replace(
                            "– ",
                            "",
                            rmnTrim($itemprop->item(0)->textContent)
                        );
                    } else {
                        $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                    }

                    $arr = explode(", ", $tempDataItem["address"]);

                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressLocation"] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[0])) {
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
                            $tempDataItem["addressLocation"] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[0])) {
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

        // parse pages like https://www.americanaddictionfoundation.com/directory/listing/mental-health-center-of-madison-county-new-horizons-recovery-center
        $rso = $xpath->query("//div/div/section/div/div/div/div/h1[contains(concat(' ', normalize-space(@class), ' '), ' entry-title ')]");

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

                $itemprop = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' sabai-directory-location ')]/span", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                    $arr = explode(", ", $tempDataItem["address"]);

                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressLocation"] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[0])) {
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
                            $tempDataItem["addressLocation"] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[0])) {
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

                $itemprop = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' sabai-directory-contact-tel ')]/span[@itemprop='telephone']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' sabai-directory-contact-website ')]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["website"] = rmnTrim($itemprop->item(0)->textContent);
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