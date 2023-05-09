<?php

class Sobernation_com extends AScraper
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

        $rso = $xpath->query("//h4[contains(@itemprop, 'name')]/a");

        // parse pages like https://sobernation.com/rehabs/desert-hot-springs-ca/
        $count = 0;
        if($rso->length > 0){
            foreach ($rso as $item){
                //get parent element
                $parent = $item->parentNode;

                if($parent instanceof DOMDocument){
                    continue;
                }

                if (!$item->textContent) {
                    continue;
                }

                $tempDataItem = [];

                $tempDataItem["name"] = rmnTrim($item->textContent);

                $itemprop = $xpath->query("parent::span/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' address ')]/span/address[contains(@itemprop, 'address')]/span[contains(@itemprop, 'streetAddress')]", $parent);

                if($itemprop->length > 0){
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["addressStreet"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::span/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' address ')]/span/address[contains(@itemprop, 'address')]/span[contains(@itemprop, 'addressLocality')]", $parent);

                if($itemprop->length > 0){
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["addressLocality"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::span/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' address ')]/span/address[contains(@itemprop, 'address')]/span[contains(@itemprop, 'addressRegion')]", $parent);

                if($itemprop->length > 0){
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["addressRegion"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::span/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' address ')]/span/address[contains(@itemprop, 'address')]/span[contains(@itemprop, 'postalCode')]", $parent);

                if($itemprop->length > 0){
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["postalCode"] = rmnTrim($firstItem->textContent);
                }


                if(!empty($tempDataItem)){
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        $rso = $xpath->query("//div[contains(concat(' ',normalize-space(@class),' '),' uk-margin-bottom ')]/h4");

        // parse pages like https://sobernation.com/listing/institute-for-the-hispanic-family-hispanic-alcohol-substance-abuse-prog-hartford-ct/

        if($rso->length > 0){
            foreach ($rso as $item){
                //get parent element
                $parent = $item->parentNode;

                if($parent instanceof DOMDocument){
                    continue;
                }

                if (!$item->textContent) {
                    continue;
                }

                $tempDataItem = [];

                $tempDataItem["name"] = rmnTrim($item->textContent);

                $itemprop = $xpath->query("p[b[contains(text(), 'Address')]]/text()", $parent);

                if($itemprop->length > 0){
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["address"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("p[b[contains(text(), 'Phone Number')]]/text()", $parent);

                if($itemprop->length > 0){
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["telephone"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("p[b[contains(text(), 'Website')]]/a/@href", $parent);

                if($itemprop->length > 0){
                    $firstItem = $itemprop->item(0);
                    if (!empty($firstItem->textContent)) {
                        $tempDataItem["website"] = rmnTrim($firstItem->textContent);
                    }
                }

                $itemprop = $xpath->query("p[b[contains(text(), 'Primary Focus')]]/text()", $parent);

                if($itemprop->length > 0){
                    $firstItem = $itemprop->item(0);
                    if (!str_contains(trim($firstItem->textContent), 'No information available, please contact the facility directly.')) {
                        $tempDataItem["primaryFocus"] = rmnTrim($firstItem->textContent);
                    }
                }

                $itemprop = $xpath->query("p[b[contains(text(), 'Treatment Type')]]/text()", $parent);

                if($itemprop->length > 0){
                    $firstItem = $itemprop->item(0);
                    if (!str_contains(trim($firstItem->textContent), 'No information available, please contact the facility directly.')) {
                        $arr = explode(',', trim($firstItem->textContent));
                        if (count($arr) > 1) {
                            foreach ($arr as $value) {
                                $tempDataItem['treatmentType'][] = rmnTrim($value);
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("p[b[contains(text(), 'Treatment Approaches')]]/text()", $parent);

                if($itemprop->length > 0){
                    $firstItem = $itemprop->item(0);
                    if (!str_contains(trim($firstItem->textContent), 'No information available, please contact the facility directly.')) {
                        $arr = explode(',', trim($firstItem->textContent));
                        if (count($arr) > 1) {
                            foreach ($arr as $value) {
                                $tempDataItem['treatmentApproaches'][] = rmnTrim($value);
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' bottom-listing-container ')]/ul/li", $parent);

                if($itemprop->length > 0){
                    $arr = [];
                    foreach ($itemprop as $value) {
                        $arr[] = rmnTrim($value->textContent);
                    }

                    if (count($arr) > 1) {
                        if (!empty($arr[0])) {
                            if (!str_contains($arr[0], 'No payment accepted, Federal, or any government funding for substance abuse programs')) {
                                $paymentAccepted = preg_split("~[,|;](?![^(]*\\))~", $arr[0]);
                                if (count($paymentAccepted) > 0) {
                                    foreach ($paymentAccepted as $tmpVal) {
                                        $tempDataItem['paymentAccepted'][] = rmnTrim($tmpVal);
                                    }
                                }
                            }
                        }
                        if (!empty($arr[1])) {
                            if (!str_contains($arr[1], 'No information available, please contact the facility directly.')) {
                                $paymentAssistance = preg_split("~[,|;](?![^(]*\\))~", $arr[1]);
                                if (count($paymentAssistance) > 0) {
                                    foreach ($paymentAssistance as $tmpVal) {
                                        $tempDataItem['paymentAssistance'][] = rmnTrim($tmpVal);
                                    }
                                }
                            }
                        }
                        if (!empty($arr[2])) {
                            if (!str_contains($arr[2], 'No information available, please contact the facility directly.')) {
                                $typeOfCare = preg_split("~[,|;](?![^(]*\\))~", $arr[2]);
                                if (count($typeOfCare) > 0) {
                                    foreach ($typeOfCare as $tmpVal) {
                                        $tempDataItem['typeOfCare'][] = rmnTrim($tmpVal);
                                    }
                                }
                            }
                        }
                        if (!empty($arr[3])) {
                            if (!str_contains($arr[3], 'No information available, please contact the facility directly.')) {
                                $facilityOperation = preg_split("~[,|;](?![^(]*\\))~", $arr[3]);
                                if (count($facilityOperation) > 0) {
                                    foreach ($facilityOperation as $tmpVal) {
                                        $tempDataItem['facilityOperation'][] = rmnTrim($tmpVal);
                                    }
                                }
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/div[contains(concat(' ',normalize-space(@class),' '),' bottom-listing-container ')]/ul[last()]", $parent);

                if($itemprop->length > 0){
                    $firstItem = $itemprop->item(0);

                    if (!str_contains(rmnTrim($firstItem->textContent), 'No information available, please contact the facility directly.')) {
                        $specialProgramsProvided = preg_split("~[,|;](?![^(]*\\))~", $firstItem->textContent);
                        if (count($specialProgramsProvided) > 0) {
                            foreach ($specialProgramsProvided as $tmpVal) {
                                $tempDataItem['specialProgramsProvided'][] = rmnTrim($tmpVal);
                            }
                        }
                    }
                }

                if(!empty($tempDataItem)){
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