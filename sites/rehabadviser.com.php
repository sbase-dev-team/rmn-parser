<?php

class Rehabadviser_com extends AScraper
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

        // parse pages like https://rehabadviser.com/rehabs/al/
        $rso = $xpath->query("//ul[contains(concat(' ', normalize-space(@class), ' '), ' location-wrap ')]/li/div/a/div/div/h3");

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

                $itemprop = $xpath->query("p[contains(concat(' ', normalize-space(@class), ' '), ' address ')]", $parent);

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
                        if (!empty($arr[0])) {
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

                $itemprop = $xpath->query("parent::div/parent::a/parent::div/div/div/a[contains(concat(' ', normalize-space(@class), ' '), ' ra-locateUrl ')]/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["website"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::a/parent::div/div/div/a[contains(concat(' ', normalize-space(@class), ' '), ' ra-phoneUrl ')]", $parent);

                if ($itemprop->length > 0) {
                    if (!empty(rmnTrim($itemprop->item(0)->textContent))) {
                        $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::a/parent::div/div/div/a[contains(concat(' ', normalize-space(@class), ' '), ' ra-phoneUrl ')]/@href", $parent);

                if ($itemprop->length > 0) {
                    if (!empty($tempDataItem["telephone"])) {
                        $tempDataItem["telephoneLink"] = rmnTrim($itemprop->item(0)->textContent);
                    }
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse pages like https://rehabadviser.com/aletheia-house-crenshaw-county-mjc7uvjl/
        $rso = $xpath->query("//main[@id='site-content']/header/div/h1");

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

                $itemprop = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' ac-address row ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = str_replace("                ", ", ", rmnTrim($itemprop->item(0)->textContent));
                    $arr = explode(", ", $tempDataItem["address"]);

                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[0])) {
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

                $itemprop = $xpath->query("//div[h3[contains(text(), 'Contact Information')]]/ul/li[1]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["website"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("//div[h3[contains(text(), 'Contact Information')]]/ul/li[2]/a", $parent);

                if ($itemprop->length > 0) {
                    if (!empty(rmnTrim($itemprop->item(0)->textContent))) {
                        $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                    }
                }

                $itemprop = $xpath->query("//div[h3[contains(text(), 'Contact Information')]]/ul/li[2]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    if (!empty($tempDataItem["telephone"])) {
                        $tempDataItem["telephoneLink"] = rmnTrim($itemprop->item(0)->textContent);
                    }
                }

                $itemprop = $xpath->query("//div[h3[contains(text(), 'Location Type')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["locationType"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("//div[h3[contains(text(), 'Treatment Types')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["treatmentTypes"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("//div[h3[contains(text(), 'Payment Methods')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["paymentMethods"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("//div[h3[contains(text(), 'Age Groups Accepted')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["ageGroupsAccepted"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("//div[h3[contains(text(), 'Supported Genders')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["supportedGenders"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("//div[h3[contains(text(), 'Accreditations')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["accreditations"][] = rmnTrim($value->textContent);
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