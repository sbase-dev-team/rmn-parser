<?php

class Americanrehabs_com extends AScraper
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

        // parse pages like https://americanrehabs.com/treatment-centers/mission-treatment-centers/
        $rso = $xpath->query("//div/div/div/h1[@itemprop='name']");

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

                $itemprop = $xpath->query("div/ul/li[@itemprop='telephone']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("div/ul/li[contains(concat(' ', normalize-space(@class), ' '),' location ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                    $arr = explode(",", $tempDataItem["address"]);
                    if (count($arr) === 2) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            preg_match("/\b([A-Z]{2})\b/", $arr[1], $addressRegion);
                            if (!empty($addressRegion[0])) {
                                $tempDataItem["addressRegion"] = rmnTrim($addressRegion[0]);
                                $arr[1] = str_replace($addressRegion[0], "", rmnTrim($arr[1]));
                            }
                            
                            preg_match("/\d{5}(-\d{4})?\b/", $arr[1], $postalCode);
                            if (!empty($postalCode[0])) {
                                $tempDataItem["postalCode"] = rmnTrim($postalCode[0]);
                                $arr[1] = str_replace($postalCode[0], "", rmnTrim($arr[1]));
                            }

                            if (!empty($arr[1])) {
                                $tempDataItem["addressLocality"] = rmnTrim($arr[1]);
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("div/ul/li[@itemprop='url']/a/@href", $parent);

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

        // parse pages like https://americanrehabs.com/treatment-centers/mission-treatment-centers/
        // https://americanrehabs.com/treatment-centers/
        $rso = $xpath->query("//div/div/div/h3[contains(concat(' ', normalize-space(@class), ' '), ' entry-title ')]/a");

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

                $itemprop = $xpath->query("parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' phone ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' address ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                    $arr = explode(",", $tempDataItem["address"]);
                    if (count($arr) === 2) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            preg_match("/\b([A-Z]{2})\b/", $arr[1], $addressRegion);
                            if (!empty($addressRegion[0])) {
                                $tempDataItem["addressRegion"] = rmnTrim($addressRegion[0]);
                                $arr[1] = str_replace($addressRegion[0], "", rmnTrim($arr[1]));
                            }

                            preg_match("/\d{5}(-\d{4})?\b/", $arr[1], $postalCode);
                            if (!empty($postalCode[0])) {
                                $tempDataItem["postalCode"] = rmnTrim($postalCode[0]);
                                $arr[1] = str_replace($postalCode[0], "", rmnTrim($arr[1]));
                            }

                            if (!empty($arr[1])) {
                                $tempDataItem["addressLocality"] = rmnTrim($arr[1]);
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("div/ul/li[@itemprop='url']/a/@href", $parent);

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