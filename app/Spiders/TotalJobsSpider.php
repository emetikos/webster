<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;

class TotalJobsSpider extends BasicSpider
{
    const FIRST_PAGE = 1;
    const LAST_PAGE = 5;
    const PLATFORM = 'TotalJobs';
    const DOMAIN = 'https://www.totaljobs.com';
    const KENT_REGION = 'kent';

    public array $startUrls = [
        'kent' => self::DOMAIN.'/jobs/in-kent',
    ];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        UserAgentMiddleware::class,
    ];

    public array $spiderMiddleware = [
        //
    ];

    public array $itemProcessors = [
        //
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 1;

    /** @return Request[] */
    protected function initialRequests(): array
    {

        // first page  : https://www.totaljobs.com/jobs/in-kent?radius=5&sort=2&action=sort_publish
        // second page : https://www.totaljobs.com/jobs/in-kent?radius=5&page=2&sort=2&action=sort_publish

        // Loop through the pages
//        for ($page = self::FIRST_PAGE; $page <= self::LAST_PAGE; $page++) {
//
//            $request[] =  new Request(
//                'GET',
//                $this->startUrls['kent']."?radius=5&sort=2&action=sort_publish",
//                [$this, 'parse']
//            );
//        }
//
//        return $request;

        return [
            new Request(
                'GET',
               'https://www.totaljobs.com/jobs/in-kent?radius=5&sort=2&action=sort_publish',
                [$this, 'parse']
            ),
        ];
    }

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {

        $job_urls = $response->filter('div.ResultsSectionContainer-sc-gdhf14-0 > div.Wrapper-sc-11673k2-0')
            ->each(function ($node) {
//                ($node->filter('a[data-at="job-item-title"]')->attr('href')) ?
//                dump($node->filter('a[data-at="job-item-title"]')->attr('href')) : '';

                return $node->filter('a[data-at="job-item-title"]')->attr('href');
            });


        for ($i = 0; $i < count($job_urls); $i++) {

            if (! str_starts_with($job_urls[$i], 'http')){
                dump(self::DOMAIN.$job_urls[$i]);
//                yield $this->request(
//                    'GET',
//                    self::DOMAIN.$job_urls[$i],
//                    'parseJobPage'
//                );
            }
        }

    }

    public function parseJobPage(Response $response) :Generator
    {
        dd($response->getRequest()->getUri());
        ($response->getRequest()->getUri()) ? $url = $response->getRequest()->getUri() : $url = '';

        dump($url);
    }
}
