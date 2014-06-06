<?php
namespace Luxifer\Leboncoin\Http;

use Guzzle\Http\Client as GuzzleClient;
use Symfony\Component\DomCrawler\Crawler;
use Luxifer\Leboncoin\Datetime\LeboncoinDatetime;

class Client
{
    protected $requestUrl;
    protected $guzzle;

    public function __construct(GuzzleClient $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * Fetch bids form Leboncoin
     *
     * @param array $criteria request configuration
     * @return array           list of bids
     */
    public function fetch(array $criteria)
    {
        array_walk($criteria['filters'], function (&$item) {
            $item = $item['value'];
        });

        $query = array_merge(
            array(
                'location' => $criteria['location'],
                'f'        => $criteria['f'],
                'q'        => $criteria['q']
            ),
            $criteria['filters']
        );

        $query = array_filter($query);

        $request = $this->guzzle->get(sprintf('/%s/offres/%s/%s', $criteria['category'], $criteria['region'], (isset($criteria['department']) ? $criteria['department'].'/' : '')), null, array(
            'query' => $query
        ));

        $this->requestUrl = $request->getUrl();

        $response = $request->send();
        $crawler = new Crawler((string) $response->getBody());

        $bids = $crawler->filter('.list-lbc > a')->each(function ($node, $i) {
            return $this->processBid($node);
        });

        return $bids;
    }

    /**
     * Transform a bid HTML node into a bid array
     * @param  Crawler $node HTML bid node
     * @return array        bid
     */
    protected function processBid(Crawler $node)
    {
        $bid = array();

        $bid['title'] = $node->attr('title');
        $bid['price'] = $node->filter('.price')->count() ? trim($node->filter('.price')->text()) : null;
        $bid['url'] = $node->attr('href');

        preg_match('@/\w+/(?P<id>\d+)\.htm\?ca=\d+_s@', $bid['url'], $matches);

        $bid['bid_id'] = $matches['id'];

        $category = trim($node->filter('.category')->text());
        $bid['is_pro'] = strstr($category, '(pro)') ? true : false;

        list($date, $time) = $node->filter('.date > div')->each(function ($node, $i) {
            return $node->text();
        });

        $bid['created_at'] = new LeboncoinDatetime($date, $time);
        $bid['picture'] = $node->filter('.image')->children()->count() ? $node->filter('.image-and-nb > img')->attr('src') : null;

        $placement = trim($node->filter('.placement')->text());
        $placement = explode('/', $placement);
        $placement = array_map(function ($item) {
            return trim($item);
        }, $placement);

        $bid['placement'] = implode(' / ', $placement);

        return $bid;
    }

    /**
     * @return string request url
     */
    public function getRequestUrl()
    {
        return $this->requestUrl;
    }
}
