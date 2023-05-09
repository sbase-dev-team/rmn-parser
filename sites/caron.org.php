<?php

class Caron_org extends AScraper
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

        // parse pages like https://www.caron.org/locations
        $rso = $xpath->query("//div/div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' card__content ')]/div/h3[contains(concat(' ', normalize-space(@class), ' '), ' card__heading h3 ')]");

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

                $itemprop = $xpath->query("p/text()[1]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressStreet"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("p/text()[2]", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(",", rmnTrim($itemprop->item(0)->textContent));
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

        // parse pages like https://www.caron.org/locations/caron-florida
        $rso = $xpath->query("//main/div/section/div[contains(concat(' ', normalize-space(@class), ' '), ' flexible-content flexible-content--mediaObject ')]/div/div[div[div[contains(concat(' ', normalize-space(@class), ' '), ' media-object__supporting p2 ')]]]/div/h2");
        if ($rso->length === 0) {
            $rso = $xpath->query("//main/div/div[contains(concat(' ', normalize-space(@class), ' '), ' flexible-content flexible-content--mediaObject ')]/div/div[div[div[contains(concat(' ', normalize-space(@class), ' '), ' media-object__supporting p2 ')]]]/div/h2");
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

                $tempDataItem["name"] = rmnTrim($item->textContent);

                if (str_contains(rmnTrim($item->textContent), "We Provide")) {
                    $name = $xpath->query("//main/section/div/div/div[a]/h1");
                    if ($name->length > 0) {
                        $tempDataItem["name"] = rmnTrim($name->item(0)->textContent);
                    }
                }

                if (str_contains(rmnTrim($item->textContent), "#")) {
                    continue;
                }

                if (str_contains($tempDataItem["name"], "Details")) {
                    $tempDataItem["name"] = rmnTrim(str_replace("Details", "", $tempDataItem["name"]));
                }

                $itemprop = $xpath->query("parent::div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' media-object__supporting p2 ')]/text()[1]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressStreet"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' media-object__supporting p2 ')]/text()[2]", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(",", rmnTrim($itemprop->item(0)->textContent));
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

                $itemprop = $xpath->query("parent::div/div[div[contains(concat(' ', normalize-space(@class), ' '), ' media-object__supporting p2 ')]]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephoneLink"] = rmnTrim($itemprop->item(0)->textContent);
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