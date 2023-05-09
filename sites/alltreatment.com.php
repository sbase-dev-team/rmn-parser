<?php

class Alltreatment_com extends AScraper
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

        if (empty($dataHTML)) {
            return null;
        }
        @$dom->loadHTML($dataHTML);

        $xpath = new DOMXPath($dom);

        $rso = $xpath->query("//div[id('facilityList')]/h2/a");
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

                $itemprop = $xpath->query("a/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["website"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div//strong[contains(text(), 'Address')]/parent::li/text()", $parent);
                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["address"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div//strong[contains(text(), 'State')]/parent::li/a/text()", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["region"] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div//strong[contains(text(), 'Zip Code')]/parent::li/text()", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem["postalCode"] = rmnTrim($firstItem->textContent);
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
