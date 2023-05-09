<?php

class Sunshinebehavioralhealth_com extends AScraper
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

        // parse pages like https://www.sunshinebehavioralhealth.com/our-rehab-centers/mountain-springs-recovery/
        $rso = $xpath->query("//div/div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' content ')]/div/div/div/div/div/h1");

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

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' section center-info ')]/div/div/div/div/div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' description ')]", $parent);

                if ($itemprop->length) {
                    preg_match_all("/([0-9]{1,} [\s\S]*? [0-9]{5}(?:-[0-9]{4})?)/", rmnTrim($itemprop->item(0)->textContent), $address);

                    if (!empty($address[0][0])) {
                        $tempDataItem["address"] = $address[0][0];
                        $arr = explode(",", $tempDataItem["address"]);
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
                                $tempDataItem["addressLocation"] = rmnTrim($arr[1]);
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
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' section center-benefits ')]/div/div/div/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["benefits"][] = rmnTrim($value->textContent);
                    }
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse pages like https://www.sunshinebehavioralhealth.com/our-rehab-centers/mountain-springs-recovery/
        $rso = $xpath->query("//section[contains(concat(' ', normalize-space(@class), ' '), ' page_content list_styling ipad_width_restriction ')]/h3[following-sibling::h3 or following-sibling::h2]/span");

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

                $itemprop = $xpath->query("parent::section/p[preceding-sibling::p[b[contains(text(), '" . $tempDataItem["name"] ."' )]]]", $parent);

                if ($itemprop->length) {
                    for ($i = 0; $i < 3; $i++) {
                        if (preg_match("/^(\([0-9]{3}\) |[0-9]{3}-)[0-9]{3}-[0-9]{4}$/", rmnTrim($itemprop->item($i)->textContent))) {
                            $tempDataItem["telephone"] = rmnTrim($itemprop->item($i)->textContent);
                        }

                        // $itemprop->item(1)->textContent - website
                        if (preg_match("/[a-zA-Z0-9]+\.[^\s]{2,}$/", rmnTrim($itemprop->item($i)->textContent))) {
                            $tempDataItem["website"] = rmnTrim($itemprop->item($i)->textContent);
                        }

                        // $itemprop->item(0)->textContent - address
                        preg_match_all("/([0-9]{1,} [\s\S]*? [0-9]{5}(?:-[0-9]{4})?)/", rmnTrim($itemprop->item($i)->textContent), $address);

                        if (!empty($address[0][0])) {
                            $tempDataItem["address"] = $address[0][0];
                            $arr = explode(",", $tempDataItem["address"]);
                            if (count($arr) === 3) {
                                if (!empty($arr[0])) {
                                    $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                                }
                                if (!empty($arr[1])) {
                                    $tempDataItem["addressLocation"] = rmnTrim($arr[1]);
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