<?php

class Recovered_org extends AScraper
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

        // parse pages like https://recovered.org/rehabs/alabama
        $rso = $xpath->query("//div/div/ul/li/div/div/div/div/div/div/h3[@itemprop='name legalName']/a");

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

                $itemprop = $xpath->query("parent::div/a/span[@itemprop='address']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/a/span[@itemprop='address']/span[@itemprop='streetAddress']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressStreet"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/a/span[@itemprop='address']/span[@itemprop='addressLocality']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressLocality"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/a/span[@itemprop='address']/span[@itemprop='addressRegion']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressRegion"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/a/span[@itemprop='address']/span[@itemprop='postalCode']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["postalCode"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["treatmentTypes"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/span[contains(concat(' ', normalize-space(@class), ' '), ' ml-2 ')]", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["accreditations"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/a/span[@itemprop='telephone']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/a[span[@itemprop='telephone']]/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephoneLink"] = rmnTrim($itemprop->item(0)->textContent);
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse pages like https://recovered.org/rehabs/fellowship-house-inc-birmingham-al
        $rso = $xpath->query("//main/div/div/div/div/div/div/h1");

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

                $itemprop = $xpath->query("parent::div/div/address/text()", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent) .
                        ", " .
                        rmnTrim($itemprop->item(1)->textContent);

                    $arr = explode(", ", $tempDataItem["address"]);
                    if (count($arr) === 4) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressStreet"] = $arr[0];
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressLocality"] = $arr[1];
                        }
                        if (!empty($arr[2])) {
                            $tempDataItem["addressRegion"] = $arr[2];
                        }
                        if (!empty($arr[3])) {
                            $tempDataItem["postalCode"] = $arr[3];
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div/div[h2[contains(text(), 'About us')]]/p[contains(text(), 'Phone')]/a", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div/div[h2[contains(text(), 'About us')]]/p[contains(text(), 'Phone')]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephoneLink"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div/div[h2[contains(text(), 'Center overview')]]/table/tbody/tr[td[contains(text(), 'Age Groups Accepted')]]/th", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            $tempDataItem["ageGroup"][] = $value;
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div/div[h2[contains(text(), 'Center overview')]]/table/tbody/tr[td[contains(text(), 'Languages')]]/th", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            $tempDataItem["languages"][] = $value;
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div/div[h2[contains(text(), 'Center overview')]]/table/tbody/tr[td[contains(text(), 'Specialization')]]/th", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));
                    if (count($arr) > 0) {
                        foreach ($arr as $value) {
                            $tempDataItem["specialPrograms"][] = $value;
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div/div[h2[contains(text(), 'Center overview')]]/table/tbody/tr[td[contains(text(), 'Special Programs/Groups Offered')]]/th", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $itempropValue) {
                        $arr = explode(",", rmnTrim($itempropValue->textContent));
                        if (count($arr) > 0) {
                            foreach ($arr as $value) {
                                $tempDataItem["specialization"][] = rmnTrim($value);
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Counseling')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["counseling"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Counseling')]]]/div/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["counseling"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Assessment/Pre-treatment')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["assessments"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Treatment Approaches')]]]/div/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["treatmentTypes"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Setting')]]]/div/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["setting"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Setting')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["setting"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Testing')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["testing"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Recovery Support Services')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["recoverySupportServices"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Type of Opioid Treatment')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["opioidTreatment"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Transitional Services')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["transitionalServices"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Pharmacotherapies')]]]/div/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["pharmacotherapies"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Pharmacotherapies')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["pharmacotherapies"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Ancillary Services')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["ancillaryServices"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'External Source of Medications Used for Alcohol Use Disorder Treatment')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["externalSources"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Payment Assistance Available')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["paymentAssistanceAvailable"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Gender Accepted')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["genderAccepted"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Opioid Medications used in Treatment')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["opioidMedicationsTreatment"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Facility Operation (e.g., Private, Public)')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["facilityOperation"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Facility Smoking Policy')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["facilitySmokingPolicy"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Facility Vaping Policy')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["facilityVapingPolicy"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::div/parent::div/parent::main/div/div[div[h2[contains(text(), 'Services that we offer')]]]/div/div[div[span[contains(text(), 'Type of Alcohol Use Disorder Treatment')]]]/div/div/div/span", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["alcoholUseDisorderTreatment"][] = rmnTrim($value->textContent);
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