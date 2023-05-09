<?php

class Usrehab_org extends AScraper
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

        // parse pages like https://usrehab.org/rehab-centers/al/alabama/
        $rso = $xpath->query("//div/article[contains(concat(' ', normalize-space(@class), ' '), ' business-entry ')]/div/div/h3");

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

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '), ' addresses ')]/div/a", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                    $arr = explode(", ", $tempDataItem["address"]);
                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[2])) {
                            $regionAndPostalCode = explode(" ", rmnTrim($arr[2]));
                            if (count($regionAndPostalCode) > 0) {
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

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '), ' contact ')]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["website"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '), ' details ')]/div[div[contains(text()[2], 'Treatment Type')]]/div[contains(concat(' ', normalize-space(@class), ' '), ' value ')]", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            $tempDataItem["treatmentTypes"][] = rmnTrim($value);
                        }
                    }
                }

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '), ' details ')]/div[div[contains(text()[2], 'Specialization')]]/div[contains(concat(' ', normalize-space(@class), ' '), ' value ')]", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            $tempDataItem["specializations"][] = rmnTrim($value);
                        }
                    }
                }

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '), ' details ')]/div[div[contains(text()[2], 'Genders Treated')]]/div[contains(concat(' ', normalize-space(@class), ' '), ' value ')]", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            $tempDataItem["gendersTreated"][] = rmnTrim($value);
                        }
                    }
                }

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '), ' details ')]/div[div[contains(text()[2], 'Ages Treated')]]/div[contains(concat(' ', normalize-space(@class), ' '), ' value ')]", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            $tempDataItem["agesTreated"][] = rmnTrim($value);
                        }
                    }
                }

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '), ' details ')]/div[div[contains(text()[2], 'Payment Accepted')]]/div[contains(concat(' ', normalize-space(@class), ' '), ' value ')]", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            $tempDataItem["paymentAccepted"][] = rmnTrim($value);
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