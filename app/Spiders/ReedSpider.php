<?php

namespace App\Spiders;

use App\SpiderItemProcessors\ReedCrawlerProcessor;
use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;

class ReedSpider extends BasicSpider
{
    const FIRST_PAGE = 1;
    const LAST_PAGE = 5;
    const PLATFORM = 'Reed';
    const DOMAIN = 'https://www.reed.co.uk';
    const KENT_REGION = 'kent';

    public array $startUrls = [
        'kent' => self::DOMAIN.'/jobs/jobs-in-kent/',
    ];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        UserAgentMiddleware::class,
    ];

    public array $spiderMiddleware = [];

    public array $itemProcessors = [
        ReedCrawlerProcessor::class,
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 2;

    /** @return Request[] */
    protected function initialRequests(): array
    {
        // Loop through the pages
        for ($page = self::FIRST_PAGE; $page <= self::LAST_PAGE; $page++) {

            $request[] =  new Request(
                'GET',
                $this->startUrls['kent']."?pageno={$page}&sortby=DisplayDate",
                [$this, 'parse']
            );
        }

        return $request;
    }

    public function parse(Response $response): Generator
    {
        $job_urls = $response->filter('section#server-results > article.job-result-card')
            ->each(function ($node, $i) {

            return $node->filter('a.job-result-card__block-link')->attr('href');
        });

        for ($i = 0; $i < count($job_urls); $i++) {

            yield $this->request(
                'GET',
                self::DOMAIN.$job_urls[$i],
                'parseJobPage',
            );

        }
    }

    public function parseJobPage(Response $response) :Generator
    {
        ($response->getRequest()->getUri()) ? $url = $response->getRequest()->getUri() : $url = '';
        ($response->filter('h1')->text()) ? $title =  $response->filter('h1')->text() : $title = '';
        ($response->filter('span[itemprop="name"]')->text()) ? $company = $response->filter('span[itemprop="name"]')->text() : $company = '';
        ($response->filter('span[itemprop="addressLocality"]')->text()) ? $location = $response->filter('span[itemprop="addressLocality"]')->text() : $location = '';
        ($response->filter('p.reference')->text()) ? $reference = $response->filter('p.reference')->text() : $reference = '';

        yield $this->item([
            'job_board' => self::PLATFORM,
            'search_area' => self::KENT_REGION,
            'url' => $url,
            'title' => $title,
            'company' => $company,
            'location' =>  $location,
            'reference' =>  $reference,
        ]);
    }
}
