<?php

class Recoverycenters_net extends AScraper
{
    private Logger $logger;
    private string $proxy;

    public function __construct(Scraper $scraper)
    {
        parent::__construct($scraper);
        $this->proxy = $scraper->proxy;
    }
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

        // parse pages like https://recoverycenters.net/recovery-centers/florida/clearwater/fairwinds-residential-treatment-center/
        $rso = $xpath->query("//p[contains(concat(' ', normalize-space(@class), ' '), ' pccolor ')]/text()[1]");

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

                if (str_contains(rmnTrim($item->textContent), " | Located In")) {
                    $tempDataItem["name"] = str_replace(" | Located In", "", rmnTrim($item->textContent));
                } else {
                    $tempDataItem["name"] = rmnTrim($item->textContent);
                }

                $itemprop = $xpath->query("//span[contains(concat(' ', normalize-space(@class), ' '), ' treataddresstop ')]/span/text()[1]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["addressStreet"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("//span[contains(concat(' ', normalize-space(@class), ' '), ' treataddresstop ')]/span/text()[2]", $parent);

                if ($itemprop->length > 0) {
                    $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));
                    if (count($arr) === 2) {
                        if (!empty($arr[0])) {
                            $tempDataItem["addressLocality"] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $regionAndPostalCode = explode(" ", rmnTrim($arr[1]));
                            if (count($regionAndPostalCode) === 2) {
                                if (!empty($regionAndPostalCode[0])) {
                                    $tempDataItem["addressRegion"] = rmnTrim($regionAndPostalCode[0]);
                                }
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem["postalCode"] = rmnTrim($regionAndPostalCode[1]);
                                }
                            }
                        }
                    }
                }

                if (
                    !empty($tempDataItem["addressStreet"]) &&
                    !empty($tempDataItem["addressLocality"]) &&
                    !empty($tempDataItem["addressRegion"]) &&
                    !empty($tempDataItem["postalCode"])
                ) {

                    $tempDataItem["address"] = $tempDataItem["addressStreet"] . ", " .
                        $tempDataItem["addressLocality"] . ", " .
                        $tempDataItem["addressRegion"] . " " .
                        $tempDataItem["postalCode"];
                }

                $itemprop = $xpath->query("//span[contains(concat(' ', normalize-space(@class), ' '), ' treatsec1weburl ')]/span", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["website"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' call_btn_in ')]/a", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' call_btn_in ')]/a/@href", $parent);

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

        // parse listings from pages
        // like https://recoverycenters.net/recovery-centers/florida/clearwater/fairwinds-residential-treatment-center/
        $rso = $xpath->query("//div[@id='view-listings'][div/h2[contains(text(), 'More Recovery Centers Nearby')]]/section/div/div/div/div/header/h4/a");

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

                $itemprop = $xpath->query("parent::header/parent::div/div/p[contains(concat(' ', normalize-space(@class), ' '), ' Card__phone-number xs-hide sm-hide ')]", $parent);

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }

                $itemprop = $xpath->query("parent::header/parent::div/footer/div/div/p[contains(concat(' ', normalize-space(@class), ' '), ' Card__footer__location ')]/text()[1]", $parent);

                if ($itemprop->length > 0) {

                    if (preg_match(
                        "/^(\\d{1,}) [a-zA-Z0-9\\s]+(\\,)? [a-zA-Z]+(\\,)? [A-Z]{2} [0-9]{5,6}$/",
                        rmnTrim($itemprop->item(0)->textContent))
                    ) {
                        $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                        $arr = explode(", ", $tempDataItem["address"]);

                        if (count($arr) === 2) {
                            if (!empty($arr[0])) {
                                $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                            }
                            if (!empty($arr[1])) {
                                $regionAndPostalCode = explode(" ", rmnTrim($arr[1]));

                                if (count($regionAndPostalCode) === 2) {
                                    if (!empty($regionAndPostalCode[0])) {
                                        $tempDataItem["addressRegion"] = rmnTrim($regionAndPostalCode[0]);
                                    }
                                    if (!empty($regionAndPostalCode[1])) {
                                        $tempDataItem["postalCode"] = rmnTrim($regionAndPostalCode[1]);
                                    }
                                }
                            }
                        }
                    } else {
                        $tempDataItem["addressStreet"] = rmnTrim($itemprop->item(0)->textContent);

                        $itemprop = $xpath->query("parent::header/parent::div/footer/div/div/p[contains(concat(' ', normalize-space(@class), ' '), ' Card__footer__location ')]/text()[2]", $parent);

                        if ($itemprop->length > 0) {
                            $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));

                            if (count($arr) === 2) {
                                if (!empty($arr[0])) {
                                    $tempDataItem["addressLocality"] = rmnTrim($arr[0]);
                                }
                                if (!empty($arr[1])) {
                                    $regionAndPostalCode = explode(" ", rmnTrim($arr[1]));

                                    if (count($regionAndPostalCode) === 2) {
                                        if (!empty($regionAndPostalCode[0])) {
                                            $tempDataItem["addressRegion"] = rmnTrim($regionAndPostalCode[0]);
                                        }
                                        if (!empty($regionAndPostalCode[1])) {
                                            $tempDataItem["postalCode"] = rmnTrim($regionAndPostalCode[1]);
                                        }
                                    }
                                }
                            }
                        }

                        if (
                            !empty($tempDataItem["addressStreet"]) &&
                            !empty($tempDataItem["addressLocality"]) &&
                            !empty($tempDataItem["addressRegion"]) &&
                            !empty($tempDataItem["postalCode"])
                        ) {
                            $tempDataItem["address"] = $tempDataItem["addressStreet"] . ", " .
                                $tempDataItem["addressLocality"] . ", " .
                                $tempDataItem["addressRegion"] . " " .
                                $tempDataItem["postalCode"];
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

        // parse pages like https://recoverycenters.net/recovery-center/new-york/
        $rso = $xpath->query("//script[contains(text(), 'window.onload')]");

        if ($rso->length > 0) {
            preg_match_all("/[-+]?\d*[.,]\d+|\d+/", $rso->item(0)->textContent, $matches);
            preg_match_all("'(.*?)'", $rso->item(0)->textContent, $city);
        }

        if (!empty($matches) && !empty($city)) {
            $request = $this->getData(
                $matches[0][0],
                $matches[0][1],
                $city[0][1],
                str_replace(",", "", $matches[0][3]),
                str_replace(",", "", $matches[0][4])
            );
        }

        if (gettype($request) === "array" && !empty($request) && count($request) > 0) {
            foreach ($request as $item) {
                //response to dom document
                $newDom = new DOMDocument();

                if (empty($item)) {
                    return null;
                }
                @$newDom->loadHTML($item);

                $newXpath = new DOMXPath($newDom);

                $itemprop = $newXpath->query("//header/h4/a");

                $tempDataItem = [];

                if ($itemprop->length > 0) {
                    $tempDataItem["name"] = rmnTrim($itemprop->item(0)->textContent);
                } else {
                    continue;
                }

                $itemprop = $newXpath->query("//p[contains(concat(' ', normalize-space(@class), ' '), ' Card__phone-number xs-hide sm-hide ')]");

                if ($itemprop->length > 0) {
                    $tempDataItem["telephone"] = rmnTrim($itemprop->item(0)->textContent);
                }


                $itemprop = $newXpath->query("//p[contains(concat(' ', normalize-space(@class), ' '), ' Card__footer__location ')]/text()[1]");

                if ($itemprop->length > 0) {

                    if (preg_match(
                        "/^(\\d{1,}) [a-zA-Z0-9\\s]+(\\,)? [a-zA-Z]+(\\,)? [A-Z]{2} [0-9]{5,6}$/",
                        rmnTrim($itemprop->item(0)->textContent))
                    )  {
                        $tempDataItem["address"] = rmnTrim($itemprop->item(0)->textContent);
                        $arr = explode(", ", $tempDataItem["address"]);

                        if (count($arr) === 2) {
                            if (!empty($arr[0])) {
                                $tempDataItem["addressStreet"] = rmnTrim($arr[0]);
                            }
                            if (!empty($arr[1])) {
                                $regionAndPostalCode = explode(" ", rmnTrim($arr[1]));

                                if (count($regionAndPostalCode) === 2) {
                                    if (!empty($regionAndPostalCode[0])) {
                                        $tempDataItem["addressRegion"] = rmnTrim($regionAndPostalCode[0]);
                                    }
                                    if (!empty($regionAndPostalCode[1])) {
                                        $tempDataItem["postalCode"] = rmnTrim($regionAndPostalCode[1]);
                                    }
                                }
                            }
                        }
                    } else {
                        $tempDataItem["addressStreet"] = rmnTrim($itemprop->item(0)->textContent);

                        $itemprop = $newXpath->query("//p[contains(concat(' ', normalize-space(@class), ' '), ' Card__footer__location ')]/text()[2]");

                        if ($itemprop->length > 0) {
                            $arr = explode(", ", rmnTrim($itemprop->item(0)->textContent));

                            if (count($arr) === 2) {
                                if (!empty($arr[0])) {
                                    $tempDataItem["addressLocality"] = rmnTrim($arr[0]);
                                }
                                if (!empty($arr[1])) {
                                    $regionAndPostalCode = explode(" ", rmnTrim($arr[1]));

                                    if (count($regionAndPostalCode) === 2) {
                                        if (!empty($regionAndPostalCode[0])) {
                                            $tempDataItem["addressRegion"] = rmnTrim($regionAndPostalCode[0]);
                                        }
                                        if (!empty($regionAndPostalCode[1])) {
                                            $tempDataItem["postalCode"] = rmnTrim($regionAndPostalCode[1]);
                                        }
                                    }
                                }
                            }
                        }

                        if (
                            !empty($tempDataItem["addressStreet"]) &&
                            !empty($tempDataItem["addressLocality"]) &&
                            !empty($tempDataItem["addressRegion"]) &&
                            !empty($tempDataItem["postalCode"])
                        ) {
                            $tempDataItem["address"] = $tempDataItem["addressStreet"] . ", " .
                                $tempDataItem["addressLocality"] . ", " .
                                $tempDataItem["addressRegion"] . " " .
                                $tempDataItem["postalCode"];
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

    /**
     * @param $url
     * @return array|null
     */
    public function requestSite($url)
    {
        //connect to proxy
        $proxyHolder = [];
        if($this->proxy != ""){
            $proxyHolder = [
                CURLOPT_PROXY => $this->proxy
            ];
        }

        //curl url and get response
        $curl = curl_init();

        //add curl headers
        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER         => false,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
            CURLOPT_PROXY          => $this->proxy,
            CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36", // name of client
        );

        curl_setopt_array($curl, $options);

        $response_data = curl_exec($curl);

        if(curl_errno($curl)){
            return "";
        }

        //get header status from request
        $header_status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        return [$header_status, $response_data];
    }

    public function getData($lat, $lng, $city, $getCatDistance, $getCatID)
    {
        //connect to proxy
        $proxyHolder = [];
        if($this->proxy != ""){
            $proxyHolder = [
                CURLOPT_PROXY => $this->proxy
            ];
        }

        $str = "latitude=" . $lat . "&longitude=" . $lng . "&city=" . $city . "&getCatDistance=" . $getCatDistance . "&getCatID=" . $getCatID . "&action=getRecoveryesNearByCenters";

        //curl url and get response
        $curl = curl_init();

        //add curl headers
        $options = array(
            CURLOPT_URL            => "https://recoverycenters.net/wp-admin/admin-ajax.php",
            CURLOPT_POSTFIELDS     => $str,
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER         => false,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
            CURLOPT_PROXY          => $this->proxy,
            CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36", // name of client
        );

        curl_setopt_array($curl, $options);

        $response_data = json_decode(curl_exec($curl));

        if(curl_errno($curl)){
            return "";
        }

        //get header status from request
        $header_status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        return $response_data[1]->mapListingsValue;
    }

}