<?php

class Addictionresource_com extends AScraper
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

        // parce pages like https://addictionresource.com/listings/parkdale-center-chesterton-in/
        $rso = $xpath->query("//h1");
        $checkPage = $xpath->query("//div[contains(concat(' ',normalize-space(@class),' '),' aside-cta-widget-small ')]");

        $count = 0;
        if ($rso->length > 0 && $checkPage->length > 0) {
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

                $itemprop = $xpath->query("//div[contains(concat(' ',normalize-space(@class),' '),' aside-cta-widget-small__btn ')][2]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["website"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("//div[contains(concat(' ',normalize-space(@class),' '),' aside-cta-widget-small__address ')]/p/text()", $parent);

                if ($itemprop->length > 0) {
                    $arr = [];
                    foreach ($itemprop as $value) {
                        $arr[] = $value->textContent;
                    }

                    if (count($arr) > 1) {
                        $tempDataItem["address"] = rmnTrim($arr[0]) . ' ' . rmnTrim($arr[1]);
                        $localityData = explode(',', trim($arr[1]));
                        if (count($localityData) > 1) {
                            $tempDataItem["addressStreet"] = rmnTrim($localityData[0]);
                            $tempDataItem["addressRegion"] = rmnTrim($localityData[1]);
                            $tempDataItem["postalCode"] = rmnTrim($localityData[2]);
                        }
                    }
                }

                $itemprop = $xpath->query("//div[contains(concat(' ',normalize-space(@class),' '),' aside-cta-widget-small__btn ')][3]/a", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["telephone"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("//div[contains(concat(' ',normalize-space(@class),' '),' aside-cta-widget-small__btn ')][3]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["telephoneLink"] = rmnTrim($firstItem->textContent);
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parce pages like https://addictionresource.com/drug-rehab/florida/
        $checkPage = $xpath->query("//div[contains(concat(' ',normalize-space(@class),' '),' rehab-centers-card__content ')]/h3");

        if ($rso->length > 0 && $checkPage->length > 0) {
            foreach ($checkPage as $item) {
                $parent = $item->parentNode;

                if ($parent instanceof DOMDocument) {
                    continue;
                }

                if (!$item->textContent) {
                    continue;
                }

                $tempDataItem = [];

                $tempDataItem["name"] = rmnTrim($item->textContent);

                $itemprop = $xpath->query("div/div[contains(concat(' ',normalize-space(@class),' '),' rehab-centers-card__meta-container ')]/div[1]/span/text()", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['address'] = rmnTrim($firstItem->textContent);
                    $arr = explode(',', rmnTrim($firstItem->textContent));
                    if (count($arr) > 1) {
                        $tempDataItem['addressLocality'] = rmnTrim($arr[0]);
                        $tempDataItem['addressRegion'] = rmnTrim($arr[1]);
                    }
                }

                $itemprop = $xpath->query("div/div[contains(concat(' ',normalize-space(@class),' '),' rehab-centers-card__btn ')]/a", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['telephone'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("div/div[contains(concat(' ',normalize-space(@class),' '),' rehab-centers-card__btn ')]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['telephoneLink'] = rmnTrim($firstItem->textContent);
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parce pages like https://addictionresource.com/listings/
        $checkPage = $xpath->query("//div[contains(concat(' ',normalize-space(@class),' '),' hubs')][2]//li[contains(concat(' ',normalize-space(@class),' '),' hubs__item ')]/div/a/div[2]/p");

        if ($rso->length > 0 && $checkPage->length > 0) {
            foreach ($checkPage as $item) {
                $parent = $item->parentNode;

                if ($parent instanceof DOMDocument) {
                    continue;
                }

                if (!$item->textContent) {
                    continue;
                }

                $tempDataItem = [];

                $tempDataItem["name"] = rmnTrim($item->textContent);

                $itemprop = $xpath->query("div/p/text()", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['address'] = rmnTrim($firstItem->textContent);
                    $arr = explode(',', rmnTrim($firstItem->textContent));
                    if (count($arr) > 1) {
                        $tempDataItem['addressStreet'] = rmnTrim($arr[0]);
                        $tempDataItem['addressLocality'] = rmnTrim($arr[1]);
                        $tempDataItem['addressRegion'] = rmnTrim($arr[2]);
                    }
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parce pages like https://addictionresource.com/drug-rehab/florida/bunnell/
        $checkPage = $xpath->query("//li[contains(concat(' ',normalize-space(@class),' '),' featured-facilities__item ')]/a/div[2]/div[contains(concat(' ', normalize-space(@class), ' '),' card-link__heading ')]/p");

        if ($rso->length > 0 && $checkPage->length > 0) {
            foreach ($checkPage as $item) {
                $parent = $item->parentNode;

                if ($parent instanceof DOMDocument) {
                    continue;
                }

                if (!$item->textContent) {
                    continue;
                }

                $tempDataItem = [];

                $tempDataItem["name"] = rmnTrim($item->textContent);

                $itemprop = $xpath->query("parent::div//div[contains(concat(' ', normalize-space(@class), ' '),' card-link__meta ')]/div/span[2]/text()", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['address'] = rmnTrim($firstItem->textContent);
                    $arr = explode(',', rmnTrim($firstItem->textContent));
                    if (count($arr) > 1) {
                        $localityData = explode("  ", rmnTrim(end($arr)));
                        if (count($localityData) > 1) {
                            $tempDataItem['addressLocality'] = rmnTrim($localityData[0]);
                            $tempDataItem['postalCode'] = rmnTrim($localityData[1]);
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

        if (empty($data)) {
            return null;
        }

        return $data;
    }

}