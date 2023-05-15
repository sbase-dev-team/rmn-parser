<?php

require "AScrapper.php";
//load all files related to classes for sites
foreach (scandir(__DIR__ . '/sites') as $filename) {
    if (strpos($filename, '.php') !== false) {
        require __DIR__ . '/sites/' . $filename;
    }
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit', '2G'); // Increase by 1 Gigabytes.

use Google\Client;
use Google\Service\Sheets\ValueRange;

interface IScraper
{
    /**
     * @param $url
     * @return array|null
     */
    public function getScrapData($url): ?array;

    /**
     * @param $url
     * @return array|null
     */
    public function requestSite($url);

    /**
     * @param $url
     * @return bool
     */
    public function testDataResponse($url): ?bool;

    /**
     * @param Scraper $scraper
     */
    public function __construct(Scraper $scraper);
}

class Scraper {
    public Logger $logger;

    public string $proxy = '';

    public array $scrapers = [
        "https://www.addictioncenter.com/" => "Addictioncenter_com", // example https://www.addictioncenter.com/rehabs/california/oceanside/ - rehab centers in oceanside www.addictioncenter.com
        "https://www.alltreatment.com/" => "Alltreatment_com", // example https://www.alltreatment.com/ny/
        "https://www.freeaddictioncenters.com/" => "Freeaddictioncenters_com", // example https://www.freeaddictioncenters.com/state/
        "https://addictionresource.com/" => "Addictionresource_com", // example https://addictionresource.com/listings/
        "https://www.drug-rehabs.org/" => "Drug_rehabs_org", // example https://www.drug-rehabs.org/
        "https://sobernation.com/" => "Sobernation_com", // example https://sobernation.com/
        "https://www.nationaltasc.org/" => "Nationaltasc_org", // example https://www.nationaltasc.org/listing/hope-by-the-sea-alcohol-substance-abuse-treatment/
        "https://drugabuse.com/" => "Drugabuse_com", // example https://drugabuse.com/treatment-centers/
        "https://www.alcoholrehabguide.org/" => "Alcoholrehabguide_org", // example https://www.alcoholrehabguide.org/
        "https://www.choosehelp.com/" => "Choosehelp_com", // example https://www.choosehelp.com/rehab
        "https://addictionhelplineamerica.com/" => "Addictionhelplineamerica_com", // example https://addictionhelplineamerica.com/
        "https://rehabnet.com/" => "Rehabnet_com", // example https://rehabnet.com/centers/miami-fl/
        "https://yourfirststep.org/" => "Yourfirststep_org", // example https://yourfirststep.org/treatment-centers/anderson-al/
        "https://rehabnow.org/" => "Rehabnow_org", // example https://rehabnow.org/in/
        "https://www.drug-rehab-headquarters.com/" => "DrugRehabHeadquarters_com", // example https://www.drug-rehab-headquarters.com/Alabama/facility/new-beginnings-of-charlotte/
        "https://americanrehabs.com/" => "Americanrehabs_com", // example https://americanrehabs.com/treatment-centers/
        "https://www.detoxrehabs.net/" => "Detoxrehabs_net", // example https://www.detoxrehabs.net/states/alaska/
        "https://www.freerehab.center/" => "Freerehab_center", // example https://www.freerehab.center/
        "https://recovered.org/" => "Recovered_org", // example https://recovered.org/rehabs/alabama
        "https://www.sunshinebehavioralhealth.com/" => "Sunshinebehavioralhealth_com", // example https://www.sunshinebehavioralhealth.com/our-rehab-centers/mountain-springs-recovery/
        "https://www.freerehabcenters.net/" => "Freerehabcenters_net", // example https://www.freerehabcenters.net/utah/
        "https://detoxtorehab.com/" => "Detoxtorehab_com", // example https://detoxtorehab.com/directory/phoenix-house-tuscaloosa-al/
        "https://localrehabreviews.org/" => "Localrehabreviews_org", // example https://localrehabreviews.org/alabama/
        "https://www.caron.org/" => "Caron_org", // example https://www.caron.org/locations
        "https://usrehab.org/" => "Usrehab_org", // example https://usrehab.org/rehab-centers/al/alabama/
        "https://addictiontreatmentmagazine.com/" => "Addictiontreatmentmagazine_com", // example https://addictiontreatmentmagazine.com/rehabs/
        "https://www.therecoveryvillage.com/" => "Therecoveryvillage_com", // example https://www.therecoveryvillage.com/locations/
        "https://alcorehab.org/" => "Alcorehab_org", // example https://alcorehab.org/alcohol-rehabs/alabama/
        "https://www.legacyhealing.com/" => "Legacyhealing_com", // example https://www.legacyhealing.com/locations/cherry-hill/
        "https://www.worldsbest.rehab/" => "Worldsbest_rehab", // example https://www.worldsbest.rehab/rehabs-in-mississippi/
        "https://www.rehabsamerica.org/" => "Rehabsamerica_org", // example https://www.rehabsamerica.org/new-york/woodstock/drug-rehab-facility/acacia-network-la-casita-3
        "https://myrecoverysource.com/" => "Myrecoverysource_com", // example https://myrecoverysource.com/listings/dallas-detox-center/
        "https://www.americanaddictionfoundation.com/" => "Americanaddictionfoundation_com", // example https://www.americanaddictionfoundation.com/directory/categories/huntsville-al
        "https://rehabcenters.com/" => "Rehabcenters_com", // example https://rehabcenters.com/state/alabama/mobile
        "https://rehabadviser.com/" => "Rehabadviser_com", // example https://rehabadviser.com/rehabs/al/
        "https://www.higheredcenter.org/" => "Higheredcenter_org", // example https://www.higheredcenter.org/rehab/Birmingham-AL/
        "https://www.steptorehab.com/" => "Steptorehab_com", // example https://www.steptorehab.com/rehab-centers/alabama
        "https://vertavahealth.com/" => "Vertavahealth_com", // example https://vertavahealth.com/locations/
        "https://recoverycenters.net/" => "Recoverycenters_net", // example https://recoverycenters.net/recovery-center/new-york/
        "https://www.burningtree.com/" => "Burningtree_com", // example https://www.burningtree.com/
        "http://greenfieldcenter.net/" => "Greenfieldcenter_net", // example http://greenfieldcenter.net/
    ];

    public function __construct() {
        // create new logger
        $this->logger = new Logger();
    }

    /**
     * @param $search_term
     * @return array|null
     */
    public function search_term($search_term): ?array {
        $searchTermHTML = $this->get_google_search_result($search_term);
        $linksFound = $this->get_google_rank_pos($searchTermHTML);

        if (count($linksFound) === 0) {
            return null;
        }

        for ($i = 0; $i < count($linksFound); $i++) {
            $link = $linksFound[$i]["link"];

            $parsedUrl = parse_url($link);

            $urlFinal = $parsedUrl['scheme'] . "://" . $parsedUrl['host'] . "/";

            if (array_key_exists($urlFinal, $this->scrapers)) {
                $scraper = new $this->scrapers[$urlFinal]($this);
                $scrapDataGot =  $scraper->getScrapData($link);
                $response = $scraper->testDataResponse($link);
                if ($scrapDataGot !== null && count($scrapDataGot) > 0) {
                    $linksFound[$i]['scrappedData'] = $scrapDataGot;
                    $linksFound[$i]['test'] = [
                        'responseText' => ($response) ? 'Test completed successfully!' : 'Can\'t scrape that site',
                        'link' => $link,
                    ];
                }
            }
        }

        return $linksFound;
    }

    /**
     * Get google rank position data
     * @param $response_data
     * @return array
     */
    public function get_google_rank_pos($response_data) {
        //response to dom document
        $dom = new DOMDocument();

        if (empty($response_data)) {
            return [];
        }
        @$dom->loadHTML($response_data);

        $xpath = new DOMXPath($dom);

        $rso = $xpath->query("//h3[contains(@class, 'LC20lb')]");

        $links = [];

        $num = 0;
        foreach ($rso as $childNode) {
            //query closest [data-query] attribute
            //closest is not supported in php dom, so we have to loop through parents
            $parent = $childNode->parentNode;
            $foundSkipPeopleAlsoAskFor = false;
            while ($parent) {
                //check tp see if domdocument, if so break
                if ($parent instanceof DOMDocument) {
                    break;
                }
                if ($parent->getAttribute('data-q')) {
                    $foundSkipPeopleAlsoAskFor = true;
                    break;
                }
                $parent = $parent->parentNode;
            }

            if ($foundSkipPeopleAlsoAskFor === true) {
                //skip people also ask for
                continue;
            }

            $childNodeFound = ((($childNode->parentNode)->parentNode)->parentNode)->parentNode;

            $other_nodes = $childNodeFound->getElementsByTagName('a');
            $linksFound = "";
            foreach ($other_nodes as $other_node) {
                $linksFound = $other_node->getAttribute('href');
                break;
            }
            if ($linksFound === "") {
                continue;
            }
            $num++;

            //check just the text of the childnode in the text, not all the text not textContent

            $tempArrayItem = [
                "link" => $linksFound,
                "position" => $num
            ];
            $links[] = $tempArrayItem;
        }

        return $links;
    }

    /**
     * @param $searchTerm
     * @return bool|string
     */
    public function get_google_search_result($searchTerm) {

        //curl url and get response
        $curl = curl_init();
        //turn spaces into + signs
        $searchTerm = str_replace(' ', '+', $searchTerm);
        $url = "https://www.google.com/search?q=$searchTerm&num=100&filter=0";

        //add curl headers
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,   // return web page
            CURLOPT_HEADER         => false,  // don't return headers
            CURLOPT_FOLLOWLOCATION => true,   // follow redirects
            CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
            CURLOPT_PROXY          => $this->proxy,
            CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36", // name of client
        );

        curl_setopt_array($curl, $options);

        $response_data = curl_exec($curl);

        if (curl_errno($curl)) {
            return "";
        }
        return $response_data;
    }

    /**
     * @return void
     */
    public function run() {
//        $scrappedData = $this->search_term("inurl:https://www.addictioncenter.com/rehabs/california/oceanside/");
//        $scrappedData = $this->search_term("inurl:https://www.alltreatment.com/ny/");
//        $scrappedData = $this->search_term("inurl:https://www.freeaddictioncenters.com/");
//        $scrappedData = $this->search_term("inurl:https://addictionresource.com/listings/");
//        $scrappedData = $this->search_term("inurl:https://www.drug-rehabs.org/");
//        $scrappedData = $this->search_term("inurl:https://sobernation.com/");
//        $scrappedData = $this->search_term("inurl:https://www.nationaltasc.org/listing/");
//        $scrappedData = $this->search_term("inurl:https://drugabuse.com/treatment-centers/");
//        $scrappedData = $this->search_term("inurl:https://www.alcoholrehabguide.org/rehabs/");
//        $scrappedData = $this->search_term("inurl:https://www.choosehelp.com/");
//        $scrappedData = $this->search_term("inurl:https://addictionhelplineamerica.com/findrehab/");
//        $scrappedData = $this->search_term("inurl:https://rehabnet.com/centers/");
//        $scrappedData = $this->search_term("inurl:https://yourfirststep.org/treatment-centers/");
//        $scrappedData = $this->search_term("inurl:https://rehabnow.org/in/");
//        $scrappedData = $this->search_term("inurl:https://www.drug-rehab-headquarters.com/Alabama/facility/");
//        $scrappedData = $this->search_term("inurl:https://americanrehabs.com/treatment-centers/");
//        $scrappedData = $this->search_term("inurl:https://www.detoxrehabs.net/");
//        $scrappedData = $this->search_term("inurl:https://www.freerehab.center/");
//        $scrappedData = $this->search_term("inurl:https://recovered.org/rehabs/");
//        $scrappedData = $this->search_term("inurl:https://www.sunshinebehavioralhealth.com/our-rehab-centers/lincoln-recovery/");
//        $scrappedData = $this->search_term("inurl:https://www.freerehabcenters.net/");
//        $scrappedData = $this->search_term("inurl:https://detoxtorehab.com/directory/");
//        $scrappedData = $this->search_term("inurl:https://localrehabreviews.org/alabama/");
//        $scrappedData = $this->search_term("inurl:https://www.caron.org/locations");
//        $scrappedData = $this->search_term("inurl:https://usrehab.org/rehab-centers/al/alabama/");
//        $scrappedData = $this->search_term("inurl:https://addictiontreatmentmagazine.com/rehabs/");
//        $scrappedData = $this->search_term("inurl:https://www.therecoveryvillage.com/locations/");
//        $scrappedData = $this->search_term("inurl:https://alcorehab.org/alcohol-rehabs/alabama/");
//        $scrappedData = $this->search_term("inurl:https://www.legacyhealing.com/locations/cherry-hill/");
//        $scrappedData = $this->search_term("inurl:https://www.worldsbest.rehab/rehabs-in-");
//        $scrappedData = $this->search_term("inurl:https://www.rehabsamerica.org/new-york/");
//        $scrappedData = $this->search_term("inurl:https://myrecoverysource.com/find-treatment/");
//        $scrappedData = $this->search_term("inurl:https://www.americanaddictionfoundation.com/directory/listing/");
//        $scrappedData = $this->search_term("inurl:https://rehabcenters.com/state/alabama/mobile");
//        $scrappedData = $this->search_term("inurl:https://rehabadviser.com/rehabs/al/");
//        $scrappedData = $this->search_term("inurl:https://www.higheredcenter.org/rehab/");
//        $scrappedData = $this->search_term("inurl:https://www.steptorehab.com/rehab-centers/alabama");
//        $scrappedData = $this->search_term("inurl:https://vertavahealth.com/locations/");
//        $scrappedData = $this->search_term("inurl:https://recoverycenters.net/recovery-center/south-dakota/wakpala/");
//        $scrappedData = $this->search_term("inurl:https://www.burningtree.com/");
        $scrappedData = $this->search_term("inurl:http://greenfieldcenter.net/");
        var_dump($scrappedData);
        return;
    }

}
class Logger {
    //log to mongodb
    public function log($data) {
        var_dump($data);
    }

    //log to mongo with the stats of error
    public function error($data) {
        var_dump($data);
    }
}
function rmnTrim($string)
{
    //remove new lines, tabs, return, non breaking space
    $tempString = str_replace(["\n", "\t", "\r", "\xc2\xa0"], "", $string);

    //trim again
    return trim($tempString);
}
$globalScraper = new Scraper();
$globalScraper->run();

