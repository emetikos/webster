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

class IndeedSpider extends BasicSpider
{
    const FIRST_PAGE = 1;
    const LAST_PAGE = 5;
    const PLATFORM = 'Indeed';
    const DOMAIN = 'https://uk.indeed.com/';
    const KENT_REGION = 'kent';

    public array $startUrls = [
        'kent' => self::DOMAIN.'/jobs?q=&l=kent&from=searchOnHP&vjk=a277f3ce0de8a80f',
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
                'https://uk.indeed.com/jobs?q=&l=kent&from=searchOnHP&vjk=a277f3ce0de8a80f',
                [$this, 'parse']
            ),
        ];
    }

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        dd($response->text());

        $job_urls = $response->filter('table.jobCard_mainContent')
            ->each(function ($node) {
               dd($node);
            });
    }
}
