<?php

class Detoxtorehab_com extends AScraper
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

        // parse pages like https://detoxtorehab.com/directory/south-central-alabama-mental-health-outpatient-program/
        $rso = $xpath->query("//div/div/div/h1[@id='facilityName']");

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

                $itemprop = $xpath->query("parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' tms-content_inner tms-nopaddingcontent_inner ')]/div/div/div/div/div[span[contains(concat(' ', normalize-space(@class), ' '), ' fa fa-phone ')]]/a", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' tms-content_inner tms-nopaddingcontent_inner ')]/div/div/div/div/div[span[contains(concat(' ', normalize-space(@class), ' '), ' fa fa-phone ')]]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephoneLink"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/div[contains(concat(' ', normalize-space(@class), ' '), ' tms-content_inner tms-nopaddingcontent_inner ')]/div/div/div/div/div[span[contains(concat(' ', normalize-space(@class), ' '), ' fa fa-globe ')]]/a", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["website"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '), ' tms-div_address_listing ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                    $arr = explode(",", $tempDataItem["address"]);
                    if (count($arr) === 3) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $localityAndRegion = explode(" ", rmnTrim($arr[1]));
                            if (count($localityAndRegion) === 2) {
                                if (!empty($localityAndRegion[0])) {
                                    $tempDataItem["addressLocality"] = rmnTrim($localityAndRegion[0]);
                                }
                                if (!empty($localityAndRegion[1])) {
                                    $tempDataItem["addressRegion"] = rmnTrim($localityAndRegion[1]);
                                }
                            }
                        }
                        if (!empty($arr[2])) {
                            $tempDataItem["postalCode"] = rmnTrim($arr[2]);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/div/div/div/div/div/div/div/div/div[label[contains(text(), 'Payment')]]/div/ul/div/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["paymentTypes"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/div/div/div/div/div/div/div/div/div[label[contains(text(), 'Treatment Model')] or label[contains(text(), 'Special Programs')]]/div/ul/div/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["treatmentPrograms"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/div/div/div/div/div/div/div/div/div[label[contains(text(), 'Therapy Sessions (Outpatient)')]]/div/ul/div/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["therapySessions"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/div/div/div/div/div/div/div/div/div[label[contains(text(), 'Gender Inpatient')]]/div/ul/div/li/a", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["genderInpatient"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/div/div/div/div/div/div/div/div/div[label[contains(text(), 'Setting (Inpatient)')]]/div/ul/div/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["settingInpatient"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/div/div/div/div/div/div/div/div/div[label[contains(text(), 'Length of Stay')]]/div/ul/div/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["lengthOfStay"][] = rmnTrim($value->textContent);
                    }
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse pages like https://detoxtorehab.com/alabama
        $rso = $xpath->query("//h6[@id='nearbyFacilityName']/span");

        if ($rso->length === 0) {
            $rso = $xpath->query("//h6[@id='nearbyFacilityName']");
        }

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