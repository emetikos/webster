<?php

namespace App\SpiderItemProcessors;

use App\Models\HarvestedData;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;

class TotaljobsCrawlerProcessor implements ItemProcessorInterface
{

    public function configure(array $options): void
    {
        // TODO: Implement configure() method.
    }

    public function processItem(ItemInterface $item): ItemInterface
    {
        HarvestedData::create([
            'job_board' => $item->get('job_board'),
            'search_area' => $item->get('search_area'),
            'recruiter' => $item->get('company'),
            'job_title' => $item->get('title'),
            'job_location' => $item->get('location'),
            'job_url' => $item->get('url'),
            'reference' => $item->get('reference'),
        ])->save();

        return $item;
    }
}