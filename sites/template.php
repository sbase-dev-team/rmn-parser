<?php

class WebsiteNameHere extends AScraper
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


        //

        //return items
//         name
//         website
//         telephone
//         telephoneLink
//         address
//         addressStreet
//         addressLocality
//         addressRegion
//         postalCode
//         treatmentTypes
//         paymentTypes
//         position

        if(empty($data)){
            return null;
        }

        return $data;
    }

}