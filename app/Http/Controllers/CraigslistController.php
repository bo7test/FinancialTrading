<?php namespace App\Http\Controllers;

use Request;
use App\Services\Scraper;
use App\Services\CraigslistHelpers;
use App\Services\ProcessLogger;

use App\Model\Craigslist\Brand;
use App\Model\Craigslist\Item;
use App\Model\Craigslist\ArchiveItem;
use App\Model\Craigslist\ItemImage;
use App\Model\Craigslist\ArchiveItemImage;
use App\Model\Craigslist\City;
use App\Model\Craigslist\BrandCheckTracker;
use App\Model\Craigslist\Section;
use App\Services\Utility;
use App\Http\Controllers\ProcessScheduleController;

class CraigslistController extends \App\Http\Controllers\Controller {

    public $baseUrl;
    public $searchSection = 'fuo';
    public $scraper;

    public $logger;

    public function scrapeBrandFurniture($tracker_id) {
        $this->logger = new ProcessLogger('craig_scrape');

        $this->scraper = new Scraper();
        $savedUrlsFoundInSearch = [];

        $brandCityTracker = BrandCheckTracker::find($tracker_id);
        $brandCityTracker->last_search = time();

        $city = City::find($brandCityTracker->city_id);
        $section = Section::find($brandCityTracker->section_id);

        $this->searchSection = $section->craigslist_string;

        $this->scraper->baseUrl = 'https://'.$city->craigslist_string.'.craigslist.org';
        $this->baseUrl = 'https://'.$city->craigslist_string.'.craigslist.org';

        $brand = Brand::find($brandCityTracker->brand_id)->toArray();

        $this->logger->logMessage('Processing City '.$city->name.' Brand '.$brand['name']);

        $brands = Brand::get()->toArray();

        $allBrands = array_column($brands, 'name');
        $newItemCount = 0;

        $responseUrls = $this->getAllSearchLinks($brand['search_text']);


        $item_urls = Item::where('brand_id', '=', $brand['id'])->get(['craigslist_url'])->toArray();
        $alreadySavedItems = array_column($item_urls, 'craigslist_url');


        foreach ($responseUrls as $url) {
            $craigslistId = $this->scraper->reverseGetInBetween($url, '.html', '/');
            $craigslistUrl = $this->scraper->getToEnd($url, '=/'.$this->searchSection.'/d', '.html');
            $craigslistDBUrl = $this->scraper->getToEnd($url, 'https://houston.craigslist.org/'.$this->searchSection.'/d');

            if (!in_array(trim($craigslistDBUrl), $alreadySavedItems)) {
                $newItemCount++;

                $secondsDelay = rand(4, 10);
                sleep($secondsDelay);

                $itemHtmlContent = $this->scraper->getCurl($url);

                $manufacturer = $this->scraper->returnIfExistsElseBlank($itemHtmlContent, '<span>make / manufacturer: <b>', '</b>');

                $brandCount = $this->scraper->getCountOfValuesExistingInText($itemHtmlContent, $allBrands);

                $deletedByAuthor = $this->scraper->inString($itemHtmlContent, 'This posting has been deleted by its author');

                $header = trim($this->scraper->getInBetween($itemHtmlContent , '<span id="titletextonly">', "</span>"));

                //Already Saved Check
                if ($brandCount < 3 && strtoupper($manufacturer) != strtoupper($brand['name']) && !$deletedByAuthor
                    && $this->scraper->inString($header, $brand['name'])) {

                    $xmlDoc = new \DOMDocument();
                    libxml_use_internal_errors(true);
                    $xmlDoc->loadHTML($itemHtmlContent);

                    $desc = $xmlDoc->getElementById("postingbody");

                    if (isset($desc->textContent)) {
                        $description = trim(str_replace('QR Code Link to This Post', '', $desc->textContent));
                    }
                    else {
                        echo "lkajsdflkjasfd";
                    }

                    libxml_use_internal_errors(false);

                    $condition = $this->scraper->returnIfExistsElseBlank($itemHtmlContent, '<span>condition: <b>', '</b>');
                    $manufacturer = $this->scraper->returnIfExistsElseBlank($itemHtmlContent, '<span>make / manufacturer: <b>', '</b>');
                    $dimensions = $this->scraper->returnIfExistsElseBlank($itemHtmlContent, '<span>size / dimensions: <b>', '</b>');
                    $modelName = $this->scraper->returnIfExistsElseBlank($itemHtmlContent, '<span>model name / number: : <b>', '</b>');

                    $postedDateString = $this->scraper->getWithinTagOpenBased($itemHtmlContent, '<time');
                    $postedDate = $datetime = date("Y-m-d H:i:s", strtotime(trim($postedDateString)));
                    $price = (float) str_replace('$', '', $this->scraper->getWithinTagOpenBased($itemHtmlContent, '<span class="price"'));

                    $imageListText = $this->scraper->getInBetween($itemHtmlContent, 'var imgList = ', ';');

                    $imageSearchValues = [
                        [
                            'name'=>'full',
                            'start'=>'"url":"',
                            'end'=>'",'
                        ],
                        [
                            'name'=>'thumb',
                            'start'=>'"thumb":"',
                            'end'=>'",'
                        ]
                    ];

                    $imagesValues = $this->scraper->stripSetsOfValuesWhereSetItemsRelated($imageListText, $imageSearchValues);

                    if (!Item::where('craigslist_url', '=', trim($craigslistUrl))->exists()) {

                        try {
                            $newItem = new Item();

                            $newItem->brand_id = $brand['id'];
                            $newItem->title = trim($header);
                            $newItem->description = trim($description);
                            $newItem->item_condition = trim($condition);
                            $newItem->manufacturer = trim($manufacturer);
                            $newItem->model_name_number = trim($modelName);
                            $newItem->dimensions = trim($dimensions);
                            $newItem->craigslist_id = $craigslistId;
                            $newItem->craigslist_url = trim($craigslistUrl);
                            $newItem->posted_date = $postedDate;
                            $newItem->price = $price;

                            $newItem->save();

                            $alreadySavedItems[] = trim($craigslistUrl);

                            foreach ($imagesValues as $image) {
                                $newItemImage = new ItemImage();

                                $newItemImage->item_id = $newItem->id;
                                $newItemImage->thumb = $image['thumb'];
                                $newItemImage->main = $image['full'];

                                $newItemImage->save();
                            }
                        }
                        catch (\Exception $e) {
                            $this->logger->logMessage('Error on Save'.$e->getMessage());
                        }

                    }
                }
            }
            else {
                $savedUrlsFoundInSearch[] = trim($craigslistDBUrl);
            }
        }

        try {
            foreach($alreadySavedItems as $savedItemUrl) {

                if (!in_array($savedItemUrl, $savedUrlsFoundInSearch)) {
                    $item = Item::where('craigslist_url', '=', $savedItemUrl)->first()->toArray();

                    $item_images = ItemImage::where('item_id', '=', $item['id'])->get()->toArray();

                    foreach($item_images as $item_image) {
                        ArchiveItemImage::insert($item_image);
                        ItemImage::destroy($item_image['id']);
                    }
                    ArchiveItem::insert($item);
                    Item::destroy($item['id']);
                }
            }
        } catch (\Exception $e) {

        }

        $this->logger->processEnd();
    }

    public function getAllSearchLinks($searchText) {

        $searchUrl = $this->baseUrl.'/search/'.$this->searchSection."?query=".$searchText;
        $htmlContent = $this->scraper->getCurl($searchUrl);

        $searchUrls = [];
        $start = 120;

        while (strpos($htmlContent, 'result-row') !== false) {

            $responseUrls = $this->scraper->getLinksInClass($htmlContent, 'result-row');

            $searchUrls = array_merge($searchUrls, $responseUrls);

            $searchUrl = $this->baseUrl.'/search/'.$this->searchSection."?s=".$start."&query=".$searchText;
            $htmlContent = $this->scraper->getCurl($searchUrl);

            $start = $start + 120;
        }
        return $searchUrls;
    }

    public function createProcesses() {
        $this->logger = new ProcessLogger('craig_scrape');

        $scheduleController = new ProcessScheduleController();

        $trackers = BrandCheckTracker::get()->toArray();

        $processIds = array_column($trackers,'id');

        $scheduleController->createQueueRecordsWithVariableIds('craig_scrape', $processIds);

        $this->logger->processEnd();
    }
}
