<?php

class Vertavahealth_com extends AScraper
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

        // parse pages like https://vertavahealth.com/locations/
        $rso = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' elementor-toggle-item ')]/div/h5/a");

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

                $itemprop = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' elementor-toggle-item ')]/div/h5[a]/parent::div/p[preceding-sibling::h5[a[contains(text(), '" . $tempDataItem["name"] . "')]]]/text()[1]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressStreet"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' elementor-toggle-item ')]/div/h5[a]/parent::div/p[preceding-sibling::h5[a[contains(text(), '" . $tempDataItem["name"] . "')]]]/text()[2]", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));

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

                if (
                    !empty($tempDataItem["addressStreet"]) &&
                    !empty($tempDataItem["addressLocality"]) &&
                    !empty($tempDataItem["addressRegion"]) &&
                    !empty($tempDataItem["postalCode"])
                ) {
                    $tempDataItem["address"] = $tempDataItem["addressStreet"] . ", " .
                        $tempDataItem["addressLocality"] . ", " .
                        $tempDataItem["addressRegion"] . " " .
                        $tempDataItem["postalCode"];
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