<?php
namespace Luxifer\Leboncoin\Manager;

use Doctrine\DBAL\Connection;

class BidManager
{
    protected $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Insert bid in database
     *
     * @param array $bid bid
     */
    public function add(array $bid)
    {
        $result = $this->find($bid['bid_id']);

        if (!$result) {
            $this->conn->insert('bid', $bid);

            return $this->conn->lastInsertId();
        }

        return $result['id'];
    }

    /**
     * Link bid to alert
     *
     * @param  integer $alertId alert it
     * @param  integer $bidId   bid id
     */
    public function link($alertId, $bidId)
    {
        if (!$this->linkExist($alertId, $bidId)) {
            $this->conn->insert('alert_bid', array(
                'alert_id' => $alertId,
                'bid_id'   => $bidId
            ));
        }
    }

    /**
     * Mark bids as sent
     *
     * @param  array  $ids bid ids
     */
    public function sent(array $ids)
    {
        array_walk($ids, function ($id) {
            $this->conn->update('bid', array('is_sent' => true), array('id' => $id));
        });
    }

    /**
     * Check if a bid exist in the database
     *
     * @param  string $bidId leboncoin bid id
     * @return array|boolean        bid|false
     */
    protected function find($bidId)
    {
        return $this->conn->fetchAssoc('SELECT * FROM bid WHERE bid_id = ?', array($bidId));
    }

    /**
     * Check if a link exist between an alert and a bid
     *
     * @param  integer $alertId alert id
     * @param  integer $bidId   bid id
     * @return array|boolean          link between an alert and a bid|false
     */
    protected function linkExist($alertId, $bidId)
    {
        return $this->conn->fetchAssoc('SELECT * FROM alert_bid WHERE alert_id = ? AND bid_id = ?', array($alertId, $bidId));
    }
}
