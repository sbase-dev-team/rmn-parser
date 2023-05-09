<?php

class Alcoholrehabguide_org extends AScraper
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

        // parse pages like https://www.alcoholrehabguide.org/rehabs/mirror-lake-recovery-center/
        $rso = $xpath->query("//main/div/div/div/div[contains(concat(' ', normalize-space(@class), ' '), ' col--left ')]/h1");

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
                    $tempDataItem['website'] = $firstItem->textContent;
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/div[contains(concat(' ', normalize-space(@class), ' '), ' contact--rehab ')]/div/div/a[contains(concat(' ', normalize-space(@class), ' '), ' Button phone-number ')]", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['telephone'] = rmnTrim($firstItem->textContent);
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/div[contains(concat(' ', normalize-space(@class), ' '), ' contact--rehab ')]/div/div/a[contains(concat(' ', normalize-space(@class), ' '), ' Button phone-number ')]/@href", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['telephoneLink'] = $firstItem->textContent;
                }

                $itemprop = $xpath->query("div/p[contains(concat(' ', normalize-space(@class), ' '), ' hero__address ')]/span", $parent);

                if ($itemprop->length > 0) {
                    $firstItem = $itemprop->item(0);
                    $tempDataItem['address'] = rmnTrim($firstItem->textContent);
                    $arr = explode(',', rmnTrim($firstItem->textContent));
                    if (count($arr) > 1) {
                        if (!empty($arr[0])) {
                            $tempDataItem['addressStreet'] = rmnTrim($arr[0]);
                        }
                        if (!empty($arr[1])) {
                            $tempDataItem['addressLocality'] = rmnTrim($arr[1]);
                        }
                        if (!empty($arr[2])) {
                            $regionAndPostalCode = explode(' ', rmnTrim($arr[2]));
                            if (count($regionAndPostalCode) > 1) {
                                if (!empty($regionAndPostalCode[0])) {
                                    $tempDataItem['addressRegion'] = rmnTrim($regionAndPostalCode[0]);
                                }
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem['postalCode'] = rmnTrim($regionAndPostalCode[1]);
                                }
                            }
                        }
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/div[contains(concat(' ', normalize-space(@class), ' '), ' Container Container--single-rehab ')]/div/div/div[div[contains(text(), 'Payment Options')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['paymentOptions'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/div[contains(concat(' ', normalize-space(@class), ' '), ' Container Container--single-rehab ')]/div/div/div[div[contains(text(), 'Levels of Care')]]/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['levelsOfCare'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/section[contains(concat(' ', normalize-space(@class), ' '), ' Rehab__section--properties ')]/div/div[h3[contains(text(), 'Specialties')]]/div/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['specialties'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/section[contains(concat(' ', normalize-space(@class), ' '), ' Rehab__section--properties ')]/div/div[h3[contains(text(), 'Amenities')]]/div/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['amenities'][] = rmnTrim($value->textContent);
                    }
                }

                $itemprop = $xpath->query("parent::div/parent::div/parent::div/parent::main/section[contains(concat(' ', normalize-space(@class), ' '), ' Rehab__section--properties ')]/div/div[h3[contains(text(), 'Treatment Approach')]]/div/ul/li", $parent);

                if ($itemprop->length > 0) {
                    foreach ($itemprop as $value) {
                        $tempDataItem['treatmentApproach'][] = rmnTrim($value->textContent);
                    }
                }

                if (!empty($tempDataItem)) {
                    $count++;
                    $tempDataItem["position"] = $count;
                    $data[] = $tempDataItem;
                }
            }
        }

        // parse pages like https://www.alcoholrehabguide.org/states/florida/
        // https://www.alcoholrehabguide.org/states/
        // https://www.alcoholrehabguide.org/cities/orlando/
        // https://www.alcoholrehabguide.org/cities/
        $rso = json_decode($dataHTML, true);
        if ($rso) {
            foreach ($rso as $item) {
                if (!$item['title']) {
                    continue;
                }

                $tempDataItem = [];

                $tempDataItem['name'] = rmnTrim($item['title']);

                if (!empty($item['phoneNumber'])) {
                    $tempDataItem['telephone'] = rmnTrim($item['phoneNumber']);
                }

                if (!empty($item['address'])) {
                    $tempDataItem['address'] = rmnTrim(str_replace(" <br/>",'', $item['address']));
                    $arr = explode(',', rmnTrim($item['address']));
                    if (count($arr) === 2) {
                        if (!empty($arr[1])) {
                            $regionAndPostalCode = explode(' ', rmnTrim($arr[1]));
                            if (count($regionAndPostalCode) === 2) {
                                if (!empty($regionAndPostalCode[1])) {
                                    $tempDataItem['postalCode'] = rmnTrim($regionAndPostalCode[1]);
                                }
                            }
                        }
                    }
                }

                if (!empty($item['city'])) {
                    $tempDataItem['addressLocality'] = rmnTrim($item['city']);
                }

                if (!empty($item['state'])) {
                    $tempDataItem['addressRegion'] = rmnTrim($item['state']);
                }

                if (!empty($item['service'])) {
                    $tempDataItem['service'] = rmnTrim($item['service']);
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

        $state = str_contains($url, 'states');
        $city = str_contains($url, 'cities');

        if ($state || $city) {
            $dom = new DOMDocument();
            @$dom->loadHTML($response_data);
            $xpath = new DOMXPath($dom);
            if ($state) {
                $script = $xpath->evaluate("string(//script[@id='sage/state.js-js-extra']/text())");
            }
            if ($city) {
                $script = $xpath->evaluate("string(//script[@id='sage/city.js-js-extra']/text())");
            }
            preg_match_all('![-+]?[0-9]*\.?[0-9]+!', $script, $matches);

            if (!empty($matches)) {
                $lat = $matches[0][0];
                $lng = $matches[0][1];
            }

            //curl url and get response
            $curl = curl_init();

            //add curl headers
            $options = array(
                CURLOPT_URL            => "https://www.alcoholrehabguide.org/wp-json/rehabs/v1/get-posts-within-radius?%20%20%20%20range=500&count=16&lat=$lat&lng=$lng",
                CURLOPT_RETURNTRANSFER => true,   // return web page
                CURLOPT_HEADER         => false,  // don't return headers
                CURLOPT_FOLLOWLOCATION => true,   // follow redirects
                CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
                CURLOPT_PROXY          => $this->proxy,
                CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36", // name of client
            );

            curl_setopt_array($curl, $options);

            $response_data = curl_exec($curl);
        }

        if(curl_errno($curl)){
            return "";
        }

        //get header status from request
        $header_status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        return [$header_status, $response_data];
    }

}