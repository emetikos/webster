<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DataCrawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:job-board {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape job board for data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $jobBoard = $this->argument('name');

        switch ($jobBoard) {
            case 'reed':
                $this->call('roach:run', [ 'spider' => 'ReedSpider' ]);
                break;
            case 'indeed':
                $this->call('roach:run', [ 'spider' => 'IndeedSpider' ]);
                break;
            case 'totaljobs':
                $this->call('roach:run', [ 'spider' => 'TotalJobsSpider' ]);
                break;
        }
    }
}
