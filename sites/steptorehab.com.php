<?php

class Steptorehab_com extends AScraper
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

        // parse pages like https://www.steptorehab.com/rehab-centers/alabama
        $rso = $xpath->query("//ul/li/div/div/div/div/h4/a");

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

                $itemprop = $xpath->query("parent::div/span[contains(concat(' ', normalize-space(@class), ' '), ' gender ')]", $parent);

                if ($itemprop->length > 0) {
                    $str = "";
                    foreach ($itemprop as $value) {
                        if (!empty($value)) {
                            $str .= $value->textContent . ", ";
                        }
                    }
                    if (!empty($str)) {
                        $arr = explode(", ", $str);
                        if ($itemprop->length >= 3) {
                            array_shift($arr);
                        }
                        array_pop($arr);

                        if (count($arr) === 3) {
                            if (!empty($arr[0])) {
                                $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                            }
                            if (!empty($arr[1])) {
                                $tempDataItem["addressLocality"] = rmnTrim($arr[1]);
                            }
                            if (!empty($arr[2])) {
                                $regionAndPostalCode = explode(" - ", rmnTrim($arr[2]));

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

        // parse pages like https://www.steptorehab.com/RehabCenterDetail/Index/e8313bec-cf3b-4a2b-8e49-040fb0e77007?facilityname=Alternative%20Counseling%20Rehabilitation&city=Priest%20River&state=ID
        $rso = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' wrapper ')]/div/div/div/div/div/h1");

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

                $itemprop = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' wrapper ')]/div/div/div/div/div[h1]/parent::div/div/ul[contains(concat(' ', normalize-space(@class), ' '), ' margin-bottom-30 ')]/li", $parent);

                if ($itemprop->length > 0) {
                    $arr = [];
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $arr[] .= rmnTrim($value->textContent);
                        }
                    }

                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }

                        if (!empty($arr[1])) {
                            $newArr = explode(", ", rmnTrim($arr[1]));

                            if (count($newArr) === 2) {
                                if (!empty($newArr[0])) {
                                    $tempDataItem["addressLocality"] = rmnTrim($newArr[0]);
                                }
                                if (!empty($newArr[1])) {
                                    $regionAndPostalCode = explode(
                                        "                            ",
                                        rmnTrim($newArr[1])
                                    );

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
                            $tempDataItem["telephone"] = str_replace(": ", "", rmnTrim($arr[2]));
                        }
                    }

                    if (count($arr) === 4) {
                        if (!empty($arr[0]) && !empty($arr[1])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]) . " " . rmnTrim($arr[1]);
                        }

                        if (!empty($arr[2])) {
                            $newArr = explode(", ", rmnTrim($arr[2]));

                            if (count($newArr) === 2) {
                                if (!empty($newArr[0])) {
                                    $tempDataItem["addressLocality"] = rmnTrim($newArr[0]);
                                }
                                if (!empty($newArr[1])) {
                                    $regionAndPostalCode = explode(
                                        "                            ",
                                        rmnTrim($newArr[1])
                                    );

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

                        if (!empty($arr[3])) {
                            $tempDataItem["telephone"] = str_replace(": ", "", rmnTrim($arr[3]));
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

                $itemprop = $xpath->query("//div[div[h3[contains(text(), 'Forms of Payment Accepted')]]]/ul/li/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["paymentTypes"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("//div[div[h3[contains(text(), 'Primary Focus')]]]/ul/li/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["primaryFocus"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("//div[div[h3[contains(text(), 'Services Provided')]]]/ul/li/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["servicesProvided"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("//div[div[h3[contains(text(), 'Special Programs')]]]/ul/li/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["specialPrograms"][] = rmnTrim($value->textContent);
                        }
                    }
                }

                $itemprop = $xpath->query("//div[div[h3[contains(text(), 'Type of care')]]]/ul/li/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty($value->textContent)) {
                            $tempDataItem["typesOfCare"][] = rmnTrim($value->textContent);
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