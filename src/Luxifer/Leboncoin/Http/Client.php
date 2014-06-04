<?php
namespace Luxifer\Leboncoin\Http;

use Guzzle\Http\Client as BaseClient;
use Symfony\Component\DomCrawler\Crawler;
use Luxifer\Leboncoin\Datetime\LeboncoinDatetime;

class Client extends BaseClient
{
    public function __construct($baseUrl = '')
    {
        parent::__construct($baseUrl);
    }

    public function fetch($criteria)
    {
        $query = array_merge(
            array(
                'location' => $criteria['location'],
                'f'        => $criteria['f']
            ),
            $criteria['parameters']
        );

        $request = $this->get(sprintf('/%s/offres/%s/', $criteria['category'], $criteria['region']), null, array(
            'query' => $query
        ));

        $response = $request->send();
        $crawler = new Crawler((string) $response->getBody());

        $bids = $crawler->filter('.list-lbc > a')->each(function ($node, $i) {
            return $this->processBid($node);
        });

        return $bids;
    }

    protected function processBid(Crawler $node)
    {
        $bid = array();

        $bid['title'] = $node->attr('title');
        $bid['price'] = trim($node->filter('.price')->text());
        $bid['url'] = $node->attr('href');

        $category = trim($node->filter('.category')->text());
        $bid['isPro'] = strstr($category, '(pro)') ? true : false;

        list($date, $time) = $node->filter('.date > div')->each(function ($node, $i) {
            return $node->text();
        });

        $bid['date'] = new LeboncoinDatetime($date, $time);

        return $bid;
    }
}
