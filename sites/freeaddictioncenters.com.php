<?php

class Freeaddictioncenters_com extends AScraper
{

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

        $rso = $xpath->query("//div[id('content')]//div[contains(@class, 'row-fluid')]//h2/a");

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

                $itemprop = $xpath->query("parent::div/div/p", $parent);
                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $arr = explode(PHP_EOL, trim($firstItem->textContent));
                    if (count($arr) > 1) {
                        $arr['country'] = explode(',', $arr[1]);
                        $tempDataItem["telephone"] = rmnTrim($arr[2]);
                        $tempDataItem["address"] = rmnTrim($arr[0]);
                        if (count($arr['country']) > 1) {
                            $arr['country'][1] = explode('-', $arr['country'][1]);
                            if (count($arr['country'][1]) > 1) {
                                $tempDataItem["addressLocality"] = rmnTrim($arr['country'][0]);
                                $tempDataItem["addressRegion"] = rmnTrim($arr['country'][1][0]);
                                $tempDataItem["postalCode"] = rmnTrim($arr['country'][1][1]);
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

        if (empty($data)) {
            return null;
        }

        return $data;
    }

}