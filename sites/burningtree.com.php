<?php

class Burningtree_com extends AScraper
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

        // parse pages like https://www.burningtree.com/
        $rso = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' uc_classic_carousel_content ')]/a");

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

                $name = explode("TREATMENT", rmnTrim($item->textContent));

                if (empty($name[1])) {
                    continue;
                }

                $tempDataItem["name"] = rmnTrim($name[1]);

                $itemprop = $xpath->query("div[contains(concat(' ', normalize-space(@class), ' '), ' ue_button ')]/a/@href", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["website"] = rmnTrim($itemprop->item(0)->textContent);
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