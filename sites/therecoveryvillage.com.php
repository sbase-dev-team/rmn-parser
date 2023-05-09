<?php

class Therecoveryvillage_com extends AScraper
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

        // parse pages like https://www.therecoveryvillage.com/locations/
        $rso = $xpath->query("//div/div/div/div/div/div/div/a[contains(concat(' ', normalize-space(@class), ' '), ' text-reset ')]");

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

                $itemprop = $xpath->query("parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' f_location ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                    $arr = explode(", ", $tempDataItem["address"]);

                    if (count($arr) === 2) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressRegion"] = rmnTrim($arr[1]);
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

        // parse pages like https://www.therecoveryvillage.com/locations/cherry-hill/
        $rso = $xpath->query("//div[@id='page-content-container']/div/div/div[not(a[contains(text(), 'Call Us Today')])]/h1");

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

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div[@id='Overview']/div/div[contains(concat(' ', normalize-space(@class), ' '), ' address with-svg-icon mb-3 fs-6 fs-md-4 ')]/text()[2]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressStreet"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div[@id='Overview']/div/div[contains(concat(' ', normalize-space(@class), ' '), ' address with-svg-icon mb-3 fs-6 fs-md-4 ')]/text()[3]", $parent);

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
                                    $tempDataItem["addressRegion"] = rmnTrim($regionAndPostalCode[0]);
                                }
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem["postalCode"] = rmnTrim($regionAndPostalCode[1]);
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

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div[@id='Overview']/div/div[contains(concat(' ', normalize-space(@class), ' '), ' phone with-svg-icon mb-3 fs-6 fs-md-4 ')]/a", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div[@id='Overview']/div/div[contains(concat(' ', normalize-space(@class), ' '), ' phone with-svg-icon mb-3 fs-6 fs-md-4 ')]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephoneLink"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/main/div/div[h2[contains(text(), 'Levels of Care')] or div[h2[contains(text(), 'Levels of Care')]]]/div/div/div/div/p[span or strong]", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["levelsOfCare"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/main/div/div[div[h2[contains(text(), 'What Treatment Looks Like')]]]/div/div/div/div/p[span or strong]", $parent);

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