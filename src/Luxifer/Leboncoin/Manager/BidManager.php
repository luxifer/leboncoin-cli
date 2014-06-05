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

    public function add(array $bid)
    {
        $result = $this->find($bid['bid_id']);

        if (!$result) {
            $this->conn->insert('bid', $bid);

            return $this->conn->lastInsertId();
        }

        return $result['id'];
    }

    public function link($alertId, $bidId)
    {
        if (!$this->linkExist($alertId, $bidId)) {
            $this->conn->insert('alert_bid', array(
                'alert_id' => $alertId,
                'bid_id'   => $bidId
            ));
        }
    }

    public function sent(array $ids)
    {
        array_walk($ids, function ($id) {
            $this->conn->update('bid', array('is_sent' => true), array('id' => $id));
        });
    }

    protected function find($bidId)
    {
        return $this->conn->fetchAssoc('SELECT * FROM bid WHERE bid_id = ?', array($bidId));
    }

    protected function linkExist($alertId, $bidId)
    {
        return $this->conn->fetchAssoc('SELECT * FROM alert_bid WHERE alert_id = ? AND bid_id = ?', array($alertId, $bidId));
    }
}
