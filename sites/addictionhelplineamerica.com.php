<?php

class Addictionhelplineamerica_com extends AScraper
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

        // parse pages like https://addictionhelplineamerica.com/findrehab/results/michigan/albion
        // https://addictionhelplineamerica.com/findrehab/results/illinois/east+moline
        $rso = $xpath->query("//div[@id='rehabscontainer']/div/div/div/div/h3/a");

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

                $itemprop = $xpath->query("parent::div/p/span", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['treatmentType'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/p[last()]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['address'] = rmnTrim($firstItem->textContent);
                    $arr = explode(',', rmnTrim($firstItem->textContent));
                    if (count($arr) === 2) {
                        if (!empty($arr[0])) {
                            $tempDataItem['addressLocality'] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem['addressRegion'] = rmnTrim($arr[1]);
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

        // parse pages like https://addictionhelplineamerica.com/rehabs/profiles/mental-health-association-alliance-house-residential-center-baton-rouge-la
        // https://addictionhelplineamerica.com/rehabs/profiles/a-forever-recovery-battle-creek-mi
        $rso = $xpath->query("//section[@id='content']/div/div/div/div/div/div/h1");

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
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['address'] = rmnTrim($firstItem->textContent);
                    $arr = explode(',', rmnTrim($firstItem->textContent));
                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem['addressStreet'] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem['addressLocality'] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[2])) {
                            $regionAndPostalCode = explode(' ', rmnTrim($arr[2]));
                            if (count($regionAndPostalCode) === 2) {
                                if (!empty($regionAndPostalCode[0])) {
                                    $tempDataItem['addressRegion'] = rmnTrim($regionAndPostalCode[0]);
                                }
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem['postalCode'] = rmnTrim($regionAndPostalCode[1]);
                                }
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/div[@id='contacts']/text()[4]", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/div[@id='contacts']/p[3]", $parent);
                    if (($itemprop->length > 0 && !preg_match("/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})$/", rmnTrim($itemprop->item(0)->textContent))) || $itemprop->length === 0) {
                        $itemprop = $xpath->query("parent::div/parent::div/div[@id='contacts']/p[2]", $parent);
                    }
                }

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    if (preg_match("/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})$/", rmnTrim($firstItem->textContent))) {
                        $tempDataItem['website'] = rmnTrim($firstItem->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/div[@id='contacts']/text()[5]", $parent);
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::div/parent::div/div[@id='contacts']/text()[4]", $parent);
                    if (($itemprop->length > 0 && !preg_match("/^(\([0-9]{3}\) |[0-9]{3}-)[0-9]{3}-[0-9]{4}$/", rmnTrim($itemprop->item(0)->textContent))) || $itemprop->length === 0) {
                        $itemprop = $xpath->query("parent::div/parent::div/div[@id='contacts']/p[4]", $parent);
                        if (($itemprop->length > 0 && !preg_match("/^(\([0-9]{3}\) |[0-9]{3}-)[0-9]{3}-[0-9]{4}$/", rmnTrim($itemprop->item(0)->textContent))) || $itemprop->length === 0) {
                            $itemprop = $xpath->query("parent::div/parent::div/div[@id='contacts']/p[3]", $parent);
                        }
                    }
                }

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    if (preg_match("/^(\([0-9]{3}\) |[0-9]{3}-)[0-9]{3}-[0-9]{4}$/", rmnTrim($firstItem->textContent))) {
                        $tempDataItem['telephone'] = rmnTrim($firstItem->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/div[@id='admissions']/text()", $parent);

                if ($itemprop->length > 0) {
                    $arr = [];
                    foreach ($itemprop as $value) {
                        if (!empty(rmnTrim($value->textContent))) {
                            $arr[] = rmnTrim($value->textContent);
                        }
                    }

                    foreach ($arr as $value) {
                        $tempDataItem['admissions'][] = $value;
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/div[@id='financing']/text()", $parent);

                if ($itemprop->length > 0) {
                    $arr = [];
                    foreach ($itemprop as $value) {
                        if (!empty(rmnTrim($value->textContent))) {
                            $arr[] = rmnTrim($value->textContent);
                        }
                    }

                    foreach ($arr as $value) {
                        $tempDataItem['paymentTypes'][] = $value;
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/div[div[h2[contains(text(), 'Program Types')]]]/h3", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        if (!empty(rmnTrim($value->textContent))) {
                            $tempDataItem['programTypes'][] = rmnTrim($value->textContent);
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