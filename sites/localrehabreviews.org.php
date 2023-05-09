<?php

class Localrehabreviews_org extends AScraper
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
        $checkPage = true;

        // parse pages like https://localrehabreviews.org/alabama/
        $rso = $xpath->query("//ul/li/a/div/div/p");

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

                $name = explode(", ", rmnTrim($item->textContent));

                if (empty(rmnTrim($name[0]))) {
                    continue;
                }

                $tempDataItem["name"] = rmnTrim($name[0]);

                $itemprop = $xpath->query("parent::div/div/div[span]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);

                    $arr = explode(", ", $tempDataItem["address"]);
                    if (count($arr) === 2) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = $arr[0];
                        }

                        if (!empty($arr[1])) {
                            $regionAndPostalCode = explode("  ", $arr[1]);
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

                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = $arr[0];
                        }

                        if (!empty($arr[1])) {
                            $tempDataItem["addressLocality"] = $arr[1];
                        }

                        if (!empty($arr[2])) {
                            $regionAndPostalCode = explode("  ", $arr[2]);

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
            $checkPage = false;
        }

        // parse pages like https://localrehabreviews.org/kentucky/louisville/beacon-house-kentucky-louisville-ky/
        $rso = $xpath->query("//article/div/h1");

        if ($rso->length > 0 && $checkPage) {
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

                $name = explode(", ", rmnTrim($item->textContent));

                if (empty(rmnTrim($name[0]))) {
                    continue;
                }

                $tempDataItem["name"] = rmnTrim($name[0]);

                $itemprop = $xpath->query("section[contains(concat(' ', normalize-space(@class), ' '), ' main-article__content ')]/address/text()[1]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressStreet"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("section[contains(concat(' ', normalize-space(@class), ' '), ' main-article__content ')]/address/text()[2]", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));

                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressLocality"] = $arr[0];
                        }

                        if (!empty($arr[1])) {
                            $tempDataItem["addressRegion"] = $arr[1];
                        }

                        if (!empty($arr[2])) {
                            $tempDataItem["postalCode"] = $arr[2];
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

        // parse pages like https://localrehabreviews.org/alabama/hayden/royal-pines-center-hayden-al/
        $rso = $xpath->query("//main/section/div/h1");

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

                $name = explode(", ", rmnTrim($item->textContent));

                if (empty(rmnTrim($name[0]))) {
                    continue;
                }

                $tempDataItem["name"] = rmnTrim($name[0]);

                $itemprop = $xpath->query("parent::section/parent::main/div/aside/div/div/div/a[contains(text(), 'Visit Website')]/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["website"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::section/parent::main/div/aside/div/div/div[3]/a", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["telephone"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::section/parent::main/div/aside/div/div/div[3]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["telephoneLink"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::section/parent::main/div/aside/div/div[contains(concat(' ',normalize-space(@class),' '),' aside-cta-widget-small__address ')]/p/text()", $parent);

                if ($itemprop->length > 0) {
                    $arr = [];
                    foreach ($itemprop as $value) {
                        $arr[] = $value->textContent;
                    }

                    if (count($arr) > 1) {
                        $tempDataItem["address"] = rmnTrim($arr[0]) . ' ' . rmnTrim($arr[1]);
                        $localityData = explode(',', trim($arr[1]));
                        if (count($localityData) > 1) {
                            $tempDataItem["addressStreet"] = rmnTrim($localityData[0]);
                            $tempDataItem["addressRegion"] = rmnTrim($localityData[1]);
                            $tempDataItem["postalCode"] = rmnTrim($localityData[2]);
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

        // parse pages like https://localrehabreviews.org/oregon/coburg/serenity-lane-coburg-or/
        $rso = $xpath->query("//main/div/div/h1");

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

                $name = explode(", ", rmnTrim($item->textContent));

                if (empty(rmnTrim($name[0]))) {
                    continue;
                }

                $tempDataItem["name"] = rmnTrim($name[0]);

                $itemprop = $xpath->query("parent::div/parent::main/div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' contact-block__btn-site ')]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["website"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::main/div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' contact-block__btn-tel ')]/a", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["telephone"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::main/div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' contact-block__btn-tel ')]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["telephoneLink"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::main/div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' contact-block__address ')]/address/text()", $parent);

                if ($itemprop->length > 0) {
                    $arr = [];
                    foreach ($itemprop as $value) {
                        $arr[] = $value->textContent;
                    }

                    if (count($arr) > 1) {
                        $tempDataItem["address"] = rmnTrim($arr[0]) . ' ' . rmnTrim($arr[1]);
                        $localityData = explode(',', trim($arr[1]));
                        if (count($localityData) > 1) {
                            $tempDataItem["addressStreet"] = rmnTrim($localityData[0]);
                            $tempDataItem["addressRegion"] = rmnTrim($localityData[1]);
                            $tempDataItem["postalCode"] = rmnTrim($localityData[2]);
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