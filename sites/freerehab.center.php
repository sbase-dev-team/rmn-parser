<?php

class Freerehab_center extends AScraper
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
        // parse pages like https://www.freerehab.center/
        $rso = $xpath->query("//div/div/div/a[contains(concat(' ', normalize-space(@class), ' '), ' c-title c-font-uppercase c-font-bold c-theme-on-hover ')]");

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

                $itemprop = $xpath->query("p", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode("\n", trim($itemprop->item(0)->textContent));
                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $localityAndRegion = explode(",", rmnTrim($arr[1]));
                            if (count($localityAndRegion) === 2) {
                                if (!empty($localityAndRegion[0])) {
                                    $tempDataItem["addressLocality"] = rmnTrim($localityAndRegion[0]);
                                }
                                if (!empty($localityAndRegion[1])) {
                                    $regionAndPostalCode = explode(" - ", rmnTrim($localityAndRegion[1]));
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
                        if (!empty($arr[2])) {
                            $tempDataItem["telephone"] = rmnTrim($arr[2]);
                        }
                        if (!empty($arr[0]) && !empty($arr[1])) {
                            $tempDataItem["address"] = str_replace(
                                " - ",
                                " ",
                                rmnTrim($arr[0]) . ", " . rmnTrim($arr[1])
                            );
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

        // parse pages like https://www.freerehab.center/ci/il-arlington_heights
        $rso = $xpath->query("//div/div/h3/a");

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

                $itemprop = $xpath->query("parent::div/p[contains(concat(' ', normalize-space(@class), ' '), ' c-desc c-font-16 c-font-thin ')][1]", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode("\n", trim($itemprop->item(0)->textContent));
                    if (count($arr) === 2) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $localityAndRegion = explode(",", rmnTrim($arr[1]));
                            if (count($localityAndRegion) === 2) {
                                if (!empty($localityAndRegion[0])) {
                                    $tempDataItem["addressLocality"] = rmnTrim($localityAndRegion[0]);
                                }
                                if (!empty($localityAndRegion[1])) {
                                    $regionAndPostalCode = explode(" - ", rmnTrim($localityAndRegion[1]));
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
                        if (!empty($arr[0]) && !empty($arr[1])) {
                            $tempDataItem["address"] = str_replace(
                                " - ",
                                " ",
                                rmnTrim($arr[0]) . ", " . rmnTrim($arr[1])
                            );
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/p[contains(concat(' ', normalize-space(@class), ' '), ' c-price c-font-26 c-font-thin ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse pages like https://www.freerehab.center/li/il_arlington-center-for-recovery-arlington-heights
        $rso = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' c-layout-page ')]/div/div/div/h3[contains(concat(' ', normalize-space(@class), ' '), ' c-font-uppercase c-font-sbold ')]");

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

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/div/div[div[contains(text(), 'Address')]]/p", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode("\n", trim($itemprop->item(0)->textContent));
                    if (count($arr) === 2) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $localityAndRegion = explode(",", rmnTrim($arr[1]));
                            if (count($localityAndRegion) === 2) {
                                if (!empty($localityAndRegion[0])) {
                                    $tempDataItem["addressLocality"] = rmnTrim($localityAndRegion[0]);
                                }
                                if (!empty($localityAndRegion[1])) {
                                    $regionAndPostalCode = explode(" - ", rmnTrim($localityAndRegion[1]));
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
                        if (!empty($arr[0]) && !empty($arr[1])) {
                            $tempDataItem["address"] = str_replace(
                                " - ",
                                " ",
                                rmnTrim($arr[0]) . ", " . rmnTrim($arr[1])
                            );
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/div/div[div[contains(text(), 'Contacts')]]/p/text()[2]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/div/a[@title='Website']/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["website"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/div/a[@title='Twitter']/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["twitter"] = rmnTrim($itemprop->item(0)->textContent);
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