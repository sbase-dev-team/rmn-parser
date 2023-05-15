<?php

class Greenfieldcenter_net extends AScraper
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

        // parse pages like http://greenfieldcenter.net/
        $rso = $xpath->query("//div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' pagelayer-col-holder ')]/div/div[contains(concat(' ', normalize-space(@class), ' '), ' pagelayer-post-title ')][1]");

        $count = 0;
        if ($rso->length > 0) {
            //get parent element
            $parent = $rso->item(0)->parentNode;

            if ($parent instanceof DOMDocument) {
                return null;
            }

            if (!$rso->item(0)->textContent) {
                return null;
            }

            $tempDataItem = [];

            $tempDataItem["name"] = rmnTrim($rso->item(0)->textContent);

            $itemprop = $xpath->query("//div[div[div[iframe]]]/a[preceding-sibling::div[div[iframe]]]", $parent);

            if ($itemprop->length > 0) {
                $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
            }

            $itemprop = $xpath->query("//div[div[div[iframe]]]/a[preceding-sibling::div[div[iframe]]]/@href", $parent);

            if ($itemprop->length > 0) {
                $tempDataItem["telephoneLink"] = rmnTrim($itemprop->item(0)->textContent);
            }

            $itemprop = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' wp-block-group__inner-container ')][h2[contains(text(), 'Categories')]]/ul/li/a", $parent);

            if ($itemprop->length > 0) {
                foreach ($itemprop as $value) {
                    if (!empty($value->textContent)) {
                        $tempDataItem["categories"][] = rmnTrim($value->textContent);
                    }
                }
            }

            if (!empty($tempDataItem)) {
                $count++;
                $tempDataItem["position"] = $count;
                $data[] = $tempDataItem;
            }
        }

        if(empty($data)){
            return null;
        }

        return $data;
    }

}