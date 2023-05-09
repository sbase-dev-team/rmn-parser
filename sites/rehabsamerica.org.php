<?php

class Rehabsamerica_org extends AScraper
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

        // parse pages like https://www.rehabsamerica.org/new-york/woodstock/drug-rehab-facility/acacia-network-la-casita-3
        $rso = $xpath->query("//div/div/div/div/h1[contains(concat(' ', normalize-space(@class), ' '), ' bold ')]");

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

                $itemprop = $xpath->query("//span[contains(concat(' ', normalize-space(@class), ' '), ' phone phone-phone_number ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("//span[contains(concat(' ', normalize-space(@class), ' '), ' category category-profession_id ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["facilityType"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("//span[contains(concat(' ', normalize-space(@class), ' '), ' textbox textbox-address1 ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressStreet"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("//span[contains(concat(' ', normalize-space(@class), ' '), ' textbox textbox-city ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressLocality"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("//span[contains(concat(' ', normalize-space(@class), ' '), ' textbox textbox-state_ln ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressRegion"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("//span[contains(concat(' ', normalize-space(@class), ' '), ' textbox textbox-zip_code ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["postalCode"] = rmnTrim($itemprop->item(0)->textContent);
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

                $itemprop = $xpath->query("//span[contains(concat(' ', normalize-space(@class), ' '), ' url url-website ')]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["website"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("//span[contains(concat(' ', normalize-space(@class), ' '), ' url url-twitter ')]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["twitter"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("//span[contains(concat(' ', normalize-space(@class), ' '), ' url url-facebook ')]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["facebook"] = rmnTrim($itemprop->item(0)->textContent);
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse pages like https://www.rehabsamerica.org/new-york/short-term-drug-rehab-centers
        $rso = $xpath->query("//div/div/div/a/h2[contains(concat(' ', normalize-space(@class), ' '), 'h3 ')]/b");

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

                $itemprop = $xpath->query("parent::a/parent::div/table/tr/td[2]", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            $tempDataItem["services"][] = $value;
                        }
                    }
                }

                $itemprop = $xpath->query("parent::a/parent::div/text()[preceding-sibling::i[contains(concat(' ', normalize-space(@class), ' '), ' fa fa-map-marker text-danger ')]][1]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                    $arr = explode(", ", $tempDataItem["address"]);

                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressRegion"] = rmnTrim($arr[1]);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::a/parent::div/span[contains(concat(' ', normalize-space(@class), ' '), ' btn-default btn-sm bold text-center btn-block nomargin phone_number ')][i[contains(concat(' ', normalize-space(@class), ' '), ' fa fa-phone fa-fw ')]]", $parent);

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

        if(empty($data)){
            return null;
        }

        return $data;
    }

}