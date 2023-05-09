<?php

class Yourfirststep_org extends AScraper
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


        // parse pages like https://yourfirststep.org/treatment-centers/anderson-al/
        $rso = $xpath->query("//div/div/div/span/strong/a/span[@itemprop='name']");

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

                $itemprop = $xpath->query("parent::strong/parent::span/parent::div/span[@itemprop='address']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::strong/parent::span/parent::div/span[@itemprop='address']/span[@itemprop='streetAddress']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressStreet"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::strong/parent::span/parent::div/span[@itemprop='address']/span[@itemprop='addressLocality']", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(",", rmnTrim($itemprop->item(0)->textContent));
                    if (count($arr) === 2) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressRegion"] = rmnTrim($arr[1]);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::strong/parent::span/parent::div/span[@itemprop='address']/span[@itemprop='postalCode']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["postalCode"] = rmnTrim($itemprop->item(0)->textContent);
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse pages like https://yourfirststep.org/treatment-center/jelani-inc-the-family-program-san-francisco-ca/
        $rso = $xpath->query("//body/div/div/main/div/div/div/main/div/div/div/p/strong[@itemprop='name']");

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

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/p/span[@itemprop='telephone']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' website-row white-row uk-position-relative ')]/p/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["website"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/p/span[@itemprop='address']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/p/span[@itemprop='address']/span[@itemprop='streetAddress']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressStreet"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/p/span[@itemprop='address']/span[@itemprop='addressLocality']", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(",", rmnTrim($itemprop->item(0)->textContent));
                    if (count($arr) === 2) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem["addressRegion"] = rmnTrim($arr[1]);
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/div/div/p/span[@itemprop='address']/span[@itemprop='postalCode']", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["postalCode"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/parent::div/parent::div/parent::div/parent::main/div[@id='content']/div/div/div/div/div/div/div[h4[contains(text(), 'Type of Treatment')]]/div/div/div/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["treatmentTypes"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/parent::div/parent::div/parent::div/parent::main/div[@id='content']/div/div/div/div/div/div/div[h4[contains(text(), 'Addiction Treatment Approaches')]]/div/div/div/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["addictionTreatment"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/parent::div/parent::div/parent::div/parent::main/div[@id='content']/div/div/div/div/div/div/div[h4[contains(text(), 'Treatment Facility Smoking Policy')]]/div/div/div/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["treatmentFacilities"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/parent::div/parent::div/parent::div/parent::main/div[@id='content']/div/div/div/div/div/div/div[h4[contains(text(), 'Treatment Service Setting')]]/div/div/div/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["treatmentService"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/parent::div/parent::div/parent::div/parent::main/div[@id='content']/div/div/div/div/div/div/div[h4[contains(text(), 'Treatment Facility Operation')]]/div/div/div/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["facilityOperation"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/parent::div/parent::div/parent::div/parent::main/div[@id='content']/div/div/div/div/div/div/div[h4[contains(text(), 'License/certification/accreditation')]]/div/div/div/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["licenses"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/parent::div/parent::div/parent::div/parent::main/div[@id='content']/div/div/div/div/div/div/div[h4[contains(text(), 'Payment & Insurance Policy')]]/div/div/div/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["paymentTypes"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/parent::div/parent::div/parent::div/parent::main/div[@id='content']/div/div/div/div/div/div/div[h4[contains(text(), 'Financial Aid & Assistance Availability')]]/div/div/div/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["financialAid"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/parent::div/parent::div/parent::div/parent::main/div[@id='content']/div/div/div/div/div/div/div[h4[contains(text(), 'Special Addiction Treatment Programs')]]/div/div/div/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["treatmentPrograms"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/parent::div/parent::div/parent::div/parent::main/div[@id='content']/div/div/div/div/div/div/div[h4[contains(text(), 'Other Treatment Services')]]/div/div/div/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["otherTreatmentServices"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/parent::div/parent::div/parent::div/parent::main/div[@id='content']/div/div/div/div/div/div/div[h4[contains(text(), 'Ages Accepted')]]/div/div/div/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["agesAccepted"][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/parent::div/parent::div/parent::div/parent::main/div[@id='content']/div/div/div/div/div/div/div[h4[contains(text(), 'Genders Accepted')]]/div/div/div/p", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem["gendersAccepted"][] = rmnTrim($value->textContent);
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