<?php

class Addictioncenter_com extends AScraper
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

        $rso = $xpath->query("//div[id('city-tagged-rehabs')]/h4[contains(@itemprop, 'name')]");
        if($rso->length === 0){
            $rso = $xpath->query("//div[id('state-tagged-rehabs')]/h4[contains(@itemprop, 'name')]");
        }
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

                //query parent for the first p tag with the itemprop of itemprop
                $itemprop = $xpath->query("p[contains(@itemprop, 'address')]/span[contains(@itemprop, 'addressLocality')]", $parent);

                if($itemprop->length > 0){
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["addressLocality"] = rmnTrim($firstItem->textContent);
                }

                //query parent for the first p tag with the itemprop of itemprop
                $itemprop = $xpath->query("p[contains(@itemprop, 'address')]/span[contains(@itemprop, 'addressRegion')]", $parent);

                if($itemprop->length > 0){
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["addressRegion"] = rmnTrim($firstItem->textContent);
                }

                if(!empty($tempDataItem)){
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