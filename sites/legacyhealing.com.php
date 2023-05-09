<?php

class Legacyhealing_com extends AScraper
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

        // parse pages like https://www.legacyhealing.com/locations/cherry-hill/
        $rso = $xpath->query("//main/div/section/div/div/div/div/div/h1");

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

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::section/parent::div/section/div/div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' location-info__phone ')]/a/@href", $parent);

                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::section/parent::div/footer/div/div/div/div/div/p[contains(concat(' ', normalize-space(@class), ' '), ' footer-phone ')]/a/@href", $parent);
                    if ($itemprop->length === 0) {
                        $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::section/parent::div/parent::main/parent::body/footer/div/div/div/div/div/p[contains(concat(' ', normalize-space(@class), ' '), ' footer-phone ')]/a/@href", $parent);
                    }
                }

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::section/parent::div/section/div/div/div/address", $parent);

                $check = false;
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::section/parent::div/footer/div/div/div/div/div/address[contains(concat(' ', normalize-space(@class), ' '), ' footer-address ')]", $parent);
                    if ($itemprop->length === 0) {
                        $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::section/parent::div/parent::main/parent::body/footer/div/div/div/div/div/address[contains(concat(' ', normalize-space(@class), ' '), ' footer-address ')]", $parent);
                    }
                    $check = true;
                }

                if ($itemprop->length > 0) {
                    if ($check) {
                        $tempDataItem["address"] = trim(
                            str_replace(
                                ["\n", "\t", "\r", "\xc2\xa0"],
                                " ",
                                $itemprop->item(0)->textContent
                            )
                        );
                    } else {
                        $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                    }
                    $arr = explode(", ", $tempDataItem["address"]);

                    if (count($arr) === 4) {
                        if (!empty($arr[0]) && !empty($arr[1])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]) . ", " . rmnTrim($arr[1]);
                        }
                        if (!empty($arr[2])) {
                            $tempDataItem["addressLocation"] = rmnTrim($arr[2]);
                        }
                        if (!empty($arr[3])) {
                            $regionAndPostalCode = explode(" ", rmnTrim($arr[3]));

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

                    if (count($arr) === 2) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
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

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::section/parent::div/section/div/div/div[h4[contains(text(), 'Amenities')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["amenities"][] = rmnTrim($value->textContent);
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