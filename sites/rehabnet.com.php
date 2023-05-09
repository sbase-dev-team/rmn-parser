<?php

class Rehabnet_com extends AScraper
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
        $rso = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' facility-container ')]/div/div/div/h4");

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

                $itemprop = $xpath->query("parent::div/div/div/a[1]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['telephone'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/div/div/a[1]/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['telephoneLink'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/div/div/a[2]/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['website'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/div/div/p[contains(concat(' ', normalize-space(@class), ' '), ' address ')]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['address'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/div/div/p[contains(concat(' ', normalize-space(@class), ' '), ' address ')]/text()[1]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['addressStreet'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/div/div/p[contains(concat(' ', normalize-space(@class), ' '), ' address ')]/text()[2]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $arr = explode(',', rmnTrim($firstItem->textContent));
                    if (count($arr) === 2) {
                        if (!empty($arr[0])) {
                            $tempDataItem['addressLocality'] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $regionAndPostalCode = explode(' ', rmnTrim($arr[1]));
                            if (count($regionAndPostalCode) === 2) {
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

                $itemprop = $xpath->query("p[contains(concat(' ', normalize-space(@class), ' '), ' abbreviation-square ')]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['treatmentType'] = rmnTrim($firstItem->textContent);
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