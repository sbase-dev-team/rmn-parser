<?php

abstract class AScraper implements IScraper
{
    private Logger $logger;
    private string $proxy;

    public function __construct(Scraper $scraper)
    {
        $this->logger = $scraper->logger;
        $this->proxy = $scraper->proxy;
    }
    /**
     * @param $dataHTML
     * @return array|null
     */
    abstract protected function parseData($dataHTML): ?array;

    /**
     * @param $url
     * @return array|null
     */
    public function getScrapData($url): ?array
    {
        $data = [];
        $tempData = $this->requestSite($url);
        if(empty($tempData)){
            //if try 4 more times until not empty
            for($i = 0; $i < 4; $i++){
                $tempData = $this->requestSite($url);
                if(!empty($tempData)){
                    break;
                }
            }
        }

        if(empty($tempData)){
            $this->logger->error([
                "url"           =>$url,
                "message"       =>"Data request for url: $url"
            ]);
            return null;
        }

        if(empty($tempData[1])){
            $this->logger->error([
                "url"           =>$url,
                "message"       =>"Empty data for url: $url",
                "responseCode"  =>$tempData[0],
            ]);
            return [
                "responseCode"  =>$tempData[0],
                "url"           =>$url,
            ];
        }

        $listings = $this->parseData($tempData[1]);
        if(!empty($listings)){
            $data = [
                "pageHtml"      =>mb_convert_encoding($tempData[1], 'UTF-8'),
                "listings"      =>$listings,
                "responseCode"  =>$tempData[0],
                "url"           =>$url,
            ];
        }else{
            return [
                "responseCode"  =>$tempData[0],
                "url"           =>$url
            ];
        }

        return $data;
    }

    /**
     * @param $url
     * @return bool
     */
    public function testDataResponse($url): ?bool
    {
        $data = $this->getScrapData($url);
        if (empty($data['listings'])) {
            return false;
        }

        return true;
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

}