<?php

class Drug_rehabs_org extends AScraper
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
        $rso = $xpath->query("//div[contains(concat(' ',normalize-space(@class),' '),' panel-heading panel-heading-dark ')]/h4");

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

                $itemprop = $xpath->query("parent::article/div[contains(concat(' ',normalize-space(@class),' '),' panel-heading clearfix ')]/div[contains(concat(' ',normalize-space(@class),' '),' panel-body ')]/div[contains(concat(' ',normalize-space(@class),' '),' col-md-8 ')]/address", $parent);
                $parsePhone = true;
                if ($itemprop->length === 0) {
                    $itemprop = $xpath->query("parent::article/div[contains(concat(' ',normalize-space(@class),' '),' panel-heading clearfix ')]/address", $parent);
                    $parsePhone = false;
                }

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $arr = explode(PHP_EOL, trim($firstItem->textContent));
                    if (count($arr) > 1) {
                        $tempDataItem['address'] = rmnTrim($arr[0]) . ' ' . rmnTrim($arr[1]);
                        ($parsePhone) ? $tempDataItem['telephone'] = rmnTrim($arr[2]) : '';
                        $localityData = explode(',', rmnTrim($arr[1]));
                        if (count($localityData) > 1) {
                            $tempDataItem['addressStreet'] = rmnTrim($arr[0]) . ' ' . rmnTrim($localityData[0]);
                            $tempDataItem['addressLocality'] = rmnTrim($localityData[0]);
                            $postalCodeAndRegion = explode('.', rmnTrim($localityData[1]));
                            if (count($postalCodeAndRegion) > 1) {
                                $tempDataItem['addressRegion'] = rmnTrim($postalCodeAndRegion[0]);
                                $tempDataItem['postalCode'] = rmnTrim($postalCodeAndRegion[1]);
                            }
                        }
                    }
                }

                if (!$parsePhone) {
                    $itemprop = $xpath->query("parent::article/div[contains(concat(' ',normalize-space(@class),' '),' panel-heading clearfix ')]/div[contains(concat(' ',normalize-space(@class),' '),' row ')]/div/p/span", $parent);

                    if ($itemprop->length > 0) {
                        $firstItem = $itemprop->item(0);
                        $tempDataItem['telephone'] = rmnTrim($firstItem->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::article/div[contains(concat(' ',normalize-space(@class),' '),' panel-heading clearfix ')]/div[contains(concat(' ',normalize-space(@class),' '),' panel-body ')]/div[contains(concat(' ',normalize-space(@class),' '),' bluebackground ')]", $parent);

                if ($itemprop->length === 0) {

                    $itemprop = $xpath->query("parent::article/div[contains(concat(' ',normalize-space(@class),' '),' panel-heading clearfix ')]/div[contains(concat(' ',normalize-space(@class),' '),' bluebackground ')]", $parent);
                }

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $arr = explode(PHP_EOL, trim($firstItem->textContent));
                    if (count($arr) > 1) {
                        if (in_array('Type of Care:', $arr)) {
                            $str = $arr[array_search('Type of Care:', $arr) + 1];
                            $typeOfCare = preg_split("~[,|;](?![^(]*\\))~", $str);
                            if (count($typeOfCare) > 1) {
                                foreach ($typeOfCare as $value) {
                                    $tempDataItem['typeOfCare'][] = rmnTrim($value);
                                }
                            } else {
                                $tempDataItem['typeOfCare'][] = rmnTrim($str);
                            }
                        }
                        if (in_array('Payment Types Accepted:', $arr)) {
                            $str = $arr[array_search('Payment Types Accepted:', $arr) + 1];
                            $paymentTypesAccepted = preg_split("~[,|;](?![^(]*\\))~", $str);
                            if (count($paymentTypesAccepted) > 1) {
                                foreach ($paymentTypesAccepted as $value) {
                                    $tempDataItem['paymentTypesAccepted'][] = rmnTrim($value);
                                }
                            } else {
                                $tempDataItem['paymentTypesAccepted'][] = rmnTrim($str);
                            }
                        }
                        if (in_array('Service Setting:', $arr)) {
                            $str = $arr[array_search('Service Setting:', $arr) + 1];
                            $serviceSetting = preg_split("~[,|;](?![^(]*\\))~", $str);
                            if (count($serviceSetting) > 1) {
                                foreach ($serviceSetting as $value) {
                                    $tempDataItem['serviceSetting'][] = rmnTrim($value);
                                }
                            } else {
                                $tempDataItem['serviceSetting'][] = rmnTrim($str);
                            }
                        }
                        if (in_array('Treatment Approaches:', $arr)) {
                            $str = $arr[array_search('Treatment Approaches:', $arr) + 1];
                            $treatmentApproaches = preg_split("~[,|;](?![^(]*\\))~", $str);
                            if (count($treatmentApproaches) > 1) {
                                foreach ($treatmentApproaches as $value) {
                                    $tempDataItem['treatmentApproaches'][] = rmnTrim($value);
                                }
                            } else {
                                $tempDataItem['treatmentApproaches'][] = rmnTrim($str);
                            }
                        }
                        if (in_array('Special Programs Offered:', $arr)) {
                            $str = $arr[array_search('Special Programs Offered:', $arr) + 1];
                            $specialProgramsOffered = preg_split("~[,|;](?![^(]*\\))~", $str);
                            if (count($specialProgramsOffered) > 1) {
                                foreach ($specialProgramsOffered as $value) {
                                    $tempDataItem['specialProgramsOffered'][] = rmnTrim($value);
                                }
                            } else {
                                $tempDataItem['specialProgramsOffered'][] = rmnTrim($str);
                            }
                        }
                        if (in_array('Age Groups Accepted:', $arr)) {
                            $str = $arr[array_search('Age Groups Accepted:', $arr) + 1];
                            $ageGroupsAccepted = preg_split("~[,|;](?![^(]*\\))~", $str);
                            if (count($ageGroupsAccepted) > 1) {
                                foreach ($ageGroupsAccepted as $value) {
                                    $tempDataItem['ageGroupsAccepted'][] = rmnTrim($value);
                                }
                            } else {
                                $tempDataItem['ageGroupsAccepted'][] = rmnTrim($str);
                            }
                        }
                        if (in_array('Gender Accepted:', $arr)) {
                            $str = $arr[array_search('Gender Accepted:', $arr) + 1];
                            $genderAccepted = preg_split("~[,|;](?![^(]*\\))~", $str);
                            if (count($genderAccepted) > 1) {
                                foreach ($genderAccepted as $value) {
                                    $tempDataItem['genderAccepted'][] = rmnTrim($value);
                                }
                            } else {
                                $tempDataItem['genderAccepted'][] = rmnTrim($str);
                            }
                        }
                        if (in_array('Language Services:', $arr)) {
                            $str = $arr[array_search('Language Services:', $arr) + 1];
                            $languageServices = preg_split("~[,|;](?![^(]*\\))~", $str);
                            if (count($languageServices) > 1) {
                                foreach ($languageServices as $value) {
                                    $tempDataItem['languageServices'][] = rmnTrim($value);
                                }
                            } else {
                                $tempDataItem['languageServices'][] = rmnTrim($str);
                            }
                        }
                        if (in_array('Smoking Policy:', $arr)) {
                            $str = $arr[array_search('Smoking Policy:', $arr) + 1];
                            $smokingPolicy = preg_split("~[,|;](?![^(]*\\))~", $str);
                            if (count($smokingPolicy) > 1) {
                                foreach ($smokingPolicy as $value) {
                                    $tempDataItem['smokingPolicy'][] = rmnTrim($value);
                                }
                            } else {
                                $tempDataItem['smokingPolicy'][] = rmnTrim($str);
                            }
                        }
                        if (in_array('Facility Operation:', $arr)) {
                            $str = $arr[array_search('Facility Operation:', $arr) + 1];
                            $facilityOperation = preg_split("~[,|;](?![^(]*\\))~", $str);
                            if (count($facilityOperation) > 1) {
                                foreach ($facilityOperation as $value) {
                                    $tempDataItem['facilityOperation'][] = rmnTrim($value);
                                }
                            } else {
                                $tempDataItem['facilityOperation'][] = rmnTrim($str);
                            }
                        }
                        if (in_array('Exclusive Services:', $arr)) {
                            $str = $arr[array_search('Exclusive Services:', $arr) + 1];
                            $exclusiveServices = preg_split("~[,|;](?![^(]*\\))~", $str);
                            if (count($exclusiveServices) > 1) {
                                foreach ($exclusiveServices as $value) {
                                    $tempDataItem['exclusiveServices'][] = rmnTrim($value);
                                }
                            } else {
                                $tempDataItem['exclusiveServices'][] = rmnTrim($str);
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