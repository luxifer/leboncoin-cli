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
        if (!$this->find($bid['bid_id'])) {
            $this->conn->insert('bid', $bid);
        }
    }

    protected function find($bidId)
    {
        return $this->conn->fetchAssoc('SELECT * FROM bid WHERE bid_id = ?', array($bidId));
    }

    public function last()
    {
        return $this->conn->fetchAssoc('SELECT * FROM bid ORDER BY inserted_at DESC LIMIT 1');
    }
}
