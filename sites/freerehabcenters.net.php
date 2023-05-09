<?php

class Freerehabcenters_net extends AScraper
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

        // parse pages like https://www.freerehabcenters.net/utah/
        $rso = $xpath->query("//div/article/*[self::h2[not(span)] or self::h3][following-sibling::div[contains(concat(' ', normalize-space(@class), ' '), ' location-info')]]");

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

                $numberForReplace = ((int) rmnTrim($item->textContent));
                $number = $numberForReplace - 1;
                $name = explode(",", rmnTrim($item->textContent));

                if (empty(rmnTrim($name[0]))) {
                    continue;
                }

                $tempDataItem["name"] = str_replace("$numberForReplace. ", "", rmnTrim($name[0]));

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '), ' location-info ')]/p[2]", $parent);

                if ($itemprop->length > 0) {
                    if ($itemprop->item($number)?->textContent !== null) {
                        $str = str_replace("\n", " ", $itemprop->item($number)->textContent) . " ";
                        preg_match_all("/\(?\d{3}\)?[\s-]?\d{3}[\s-]? ?\d{4}/", $str, $matches, PREG_SET_ORDER, 0);
                        if (!empty($matches[0][0])) {
                            $tempDataItem["telephone"] = rmnTrim($matches[0][0]);
                            $str = str_replace($matches[0][0], "", $str);
                        }

                        if (!empty($str)) {
                            $arr = explode(",", rmnTrim($str));
                            if (count($arr) === 2) {
                                if (!empty($arr[0])) {
                                    $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                                }
                                if (!empty($arr[1])) {
                                    $regionAndPostalCode = explode(" ", rmnTrim($arr[1]));
                                    if (count($regionAndPostalCode) >= 2) {
                                        if (!empty($regionAndPostalCode[0])) {
                                            $tempDataItem["addressRegion"] = rmnTrim($regionAndPostalCode[0]);
                                        }
                                        if (!empty($regionAndPostalCode[1])) {
                                            $tempDataItem["postalCode"] = rmnTrim($regionAndPostalCode[1]);
                                        }
                                        if (!empty($regionAndPostalCode[0]) &&
                                            !empty($regionAndPostalCode[1]) &&
                                            !empty($arr[0])
                                        ) {
                                            $tempDataItem["address"] = rmnTrim($arr[0]) . ", " . rmnTrim($regionAndPostalCode[0]) . " " . rmnTrim($regionAndPostalCode[1]);
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

        // parse pages like https://www.freerehabcenters.net/virginia/fort-lee/
        $rso = $xpath->query("//section[@id='listings']/div/div/a[contains(concat(' ', normalize-space(@class), ' '),' listing-box-head ')]");

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

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '),' listing-location-pin ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '),' listing-location-details ')]/div[contains(concat(' ', normalize-space(@class), ' '), ' listing-attr ')]", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["treatmentTypes"][] = rmnTrim($value->textContent);
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