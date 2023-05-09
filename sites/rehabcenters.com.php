<?php

class Rehabcenters_com extends AScraper
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

        // parse pages like https://rehabcenters.com/state/alabama/mobile
        $rso = $xpath->query("//div[@id='centers_listing']/div/div/p/a/span");

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

                $itemprop = $xpath->query("parent::p/parent::div/span[@itemprop='address']/p", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim(str_replace("\n", " ", $itemprop->item(0)->textContent));
                }

                $itemprop = $xpath->query("parent::p/parent::div/span[@itemprop='address']/p/text()[1]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressStreet"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::p/parent::div/span[@itemprop='address']/p/text()[2]", $parent);

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
                                    $tempDataItem["addressRegion"] = $regionAndPostalCode[0];
                                }
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem["postalCode"] = $regionAndPostalCode[1];
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

        // parse pages like https://rehabcenters.com/listing/centre-inc-3
        $rso = $xpath->query("//div[@id='content']/span/h1[@itemprop='name']");

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

                $itemprop = $xpath->query("div/div/span[@itemprop='telephone']/p", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("//div[@id='content']/span[h1[@itemprop='name']]/div/div/p[preceding-sibling::h3[contains(text(), 'Address')]]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim(str_replace("\n", ", ", $itemprop->item(0)->textContent));
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

                            if (count($regionAndPostalCode) === 2) {
                                if (!empty($regionAndPostalCode[0])) {
                                    $tempDataItem["addressRegion"] = $regionAndPostalCode[0];
                                }
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem["postalCode"] = $regionAndPostalCode[1];
                                }
                            } else {
                                $tempDataItem["addressRegion"] = rmnTrim($arr[2]);
                            }
                        }
                    }

                    if (count($arr) === 4) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1]) && !empty($arr[2])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[1]) . " " . rmnTrim($arr[2]);
                        }
                        if (!empty($arr[3])) {
                            $regionAndPostalCode = explode(" ", rmnTrim($arr[3]));

                            if (count($regionAndPostalCode) === 2) {
                                if (!empty($regionAndPostalCode[0])) {
                                    $tempDataItem["addressRegion"] = $regionAndPostalCode[0];
                                }
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem["postalCode"] = $regionAndPostalCode[1];
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