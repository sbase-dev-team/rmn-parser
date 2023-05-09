<?php

class Worldsbest_rehab extends AScraper
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

        // parse pages like https://rehabnet.com/centers/miami-fl/
        $rso = $xpath->query("//div/div/div/div/div/div/div/table/tbody/tr/td[contains(concat(' ', normalize-space(@class), ' '), ' name ')]");

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

                $itemprop = $xpath->query("td[contains(concat(' ', normalize-space(@class), ' '), ' categories ')]", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            $tempDataItem["categories"][] = rmnTrim($value);
                        }
                    }
                }

                $itemprop = $xpath->query("td[contains(concat(' ', normalize-space(@class), ' '), ' phone ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("td[contains(concat(' ', normalize-space(@class), ' '), ' address ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);

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