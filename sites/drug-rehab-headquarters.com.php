<?php

class DrugRehabHeadquarters_com extends AScraper
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


        // parse pages like https://www.drug-rehab-headquarters.com/Alabama/facility/new-beginnings-of-charlotte/
        $rso = $xpath->query("//section[contains( concat(' ', normalize-space(@class), ' '), ' section-whitebg section-grey dm-shadow tre-main ')]/div[contains(concat(' ', normalize-space(@class), ' '), ' container ')]/div/h1");

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

                $itemprop = $xpath->query("parent::div/div/div[b[contains(text(), 'Facility Location:')]]", $parent);

                if ($itemprop->length > 0) {
                    $output = preg_replace('!\s+!', ' ', str_replace("Facility Location:", "", trim($itemprop->item(0)->textContent)));
                    $arr = explode(",", rmnTrim($output));
                    if (count($arr) === 5) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[2])) {
                            $tempDataItem["addressRegion"] = rmnTrim($arr[2]);
                        }
                        if (!empty($arr[3])) {
                            $tempDataItem["postalCode"] = rmnTrim($arr[3]);
                        }
                        if (!empty($arr[0]) && !empty($arr[1]) && !empty($arr[2]) && !empty($arr[3])) {
                            $tempDataItem["address"] = rmnTrim($arr[0]) . ", " .
                                rmnTrim($arr[1]) . ", " .
                                rmnTrim($arr[2]) . ", " .
                                rmnTrim($arr[3]);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/div/div[b[contains(text(), 'Phone Number:')]]/div", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/div/div[b[contains(text(), 'Website')]]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["website"] = rmnTrim(str_replace("Website", "", $itemprop->item(0)->textContent));
                }

                $itemprop = $xpath->query("parent::div/div/div[b[contains(text(), 'Primary Focus')]]", $parent);

                if ($itemprop->length > 0) {
                    $str = rmnTrim(str_replace("Primary Focus", "", $itemprop->item(0)->textContent));
                    $arr = explode(",", $str);
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            if (!empty($value)) {
                                $tempDataItem["primaryFocus"][] = rmnTrim($value);
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/div/div[b[contains(text(), 'Services Provided')]]", $parent);

                if ($itemprop->length > 0) {
                    $str = rmnTrim(str_replace("Services Provided", "", $itemprop->item(0)->textContent));
                    $arr = explode(",", $str);
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            if (!empty($value)) {
                                $tempDataItem["servicesProvided"][] = rmnTrim($value);
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/div/div[b[contains(text(), 'Type of Care')]]", $parent);

                if ($itemprop->length > 0) {
                    $str = rmnTrim(str_replace("Type of Care", "", $itemprop->item(0)->textContent));
                    $arr = explode(",", $str);
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            if (!empty($value)) {
                                $tempDataItem["typeOfCare"][] = rmnTrim($value);
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/div/div[b[contains(text(), 'Special Programs/Groups')]]", $parent);

                if ($itemprop->length > 0) {
                    $str = rmnTrim(str_replace("Special Programs/Groups", "", $itemprop->item(0)->textContent));
                    $arr = explode(",", $str);
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            if (!empty($value)) {
                                $tempDataItem["specialPrograms"][] = rmnTrim($value);
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/div/div[b[contains(text(), 'Forms of Payment Accepted')]]", $parent);

                if ($itemprop->length > 0) {
                    $str = rmnTrim(str_replace("Forms of Payment Accepted", "", $itemprop->item(0)->textContent));
                    $arr = preg_split("~[,|;](?![^(]*\\))~", $str);
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            if (!empty($value)) {
                                $tempDataItem["paymentTypes"][] = rmnTrim($value);
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/div/div[b[contains(text(), 'Payment Assistance')]]", $parent);

                if ($itemprop->length > 0) {
                    $str = rmnTrim(str_replace("Payment Assistance", "", $itemprop->item(0)->textContent));
                    $arr = explode(",", $str);
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            if (!empty($value)) {
                                $tempDataItem["paymentAssistance"][] = rmnTrim($value);
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/div/div[b[contains(text(), 'Special Language Services:')]]", $parent);

                if ($itemprop->length > 0) {
                    $str = rmnTrim(str_replace("Special Language Services:", "", $itemprop->item(0)->textContent));
                    $arr = explode(",", $str);
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            if (!empty($value)) {
                                $tempDataItem["languages"][] = rmnTrim($value);
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