<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ReportGenerator extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Statistics_model', 'stats');
    }

    /**
     * Generates data required to create reports
     * @param  string $chars    
     * @param  int    $interval 
     * @return array       
     */
    public function calculateTotals(string $chars, int $interval): array
    {
        $chars = substr($chars, 1);
        $chars = substr($chars, 0, -1);
        $group = explode(',', $chars);

        $final  = [];
        $ind    = [];
        $totals = [];

        $total_profit = 0;
        $total_sell   = 0;
        $total_buy    = 0;

        foreach ($group as $row) {
            $id     = (int) $row;
            $profit = $this->getTotalProfit($id, $interval);
            $sell   = $this->getTotalSales($id, $interval);
            $buy    = $this->getTotalExpenses($id, $interval);

            $total_profit += $profit;
            $total_sell += $sell;
            $total_buy += $buy;

            $values = ["profit" => $profit,
                       "sell"   => $sell,
                       "buy"    => $buy];
            array_push($ind, [$id => $values]);
        }

        array_push($totals, ["total_profit" => $total_profit,
                            "total_sell"    => $total_sell,
                            "total_buy"     => $total_buy]);
        array_push($final, $ind);
        array_push($final, $totals);
        return $final;
    }

    /**
     * Get total profits for specified characters and interval
     * @param  int    $character_id 
     * @param  int    $interval     
     * @return string             
     */
    private function getTotalProfit(int $character_id, int $interval): string
    {
        $this->db->select('COALESCE(sum(profit_unit * quantity_profit),0) as sum');
        $this->db->where('profit.timestamp_sell >= now() - INTERVAL ' . $interval . ' day');
        $this->db->where('profit.characters_eve_idcharacters_OUT', $character_id);
        $query  = $this->db->get('profit');
        $result = $query->row()->sum;

        return $result;
    }

     /**
     * Get total expenses for specified characters and interval
     * @param  int    $character_id 
     * @param  int    $interval     
     * @return string             
     */
    private function getTotalExpenses($character_id, $interval) : string
    {
        $this->db->select('COALESCE(sum(price_unit * quantity),0) as sum');
        $this->db->where('time >= now() - INTERVAL ' . $interval . ' day');
        $this->db->where('character_eve_idcharacter', $character_id);
        $this->db->where('transaction_type', 'Buy');
        $query  = $this->db->get('transaction');
        $result = $query->row()->sum;

        return $result;
    }

     /**
     * Get total sales for specified characters and interval
     * @param  int    $character_id 
     * @param  int    $interval     
     * @return string             
     */
    private function getTotalSales($character_id, $interval) : string
    {
        $this->db->select('COALESCE(sum(price_unit * quantity),0) as sum');
        $this->db->where('time >= now() - INTERVAL ' . $interval . ' day');
        $this->db->where('character_eve_idcharacter', $character_id);
        $this->db->where('transaction_type', 'Sell');
        $query  = $this->db->get('transaction');
        $result = $query->row()->sum;

        return $result;
    }

     /**
     * Get best raw profits for specified characters and interval
     * @param  int    $character_id 
     * @param  int    $interval     
     * @return array         
     */
    public function calculateBestRaw(string $chars, int $interval): array
    {
        $configs = [
            'interval' => $interval,
            'chars' => $chars,
            'defs' => [
                'length' => 5,
                'start' => 0,
                'draw' => 1
            ]
        ];
        $result = $this->stats->getBestItemsRaw($configs);
        return json_decode($result, true)['data'];
    }

    /**
     * Get best margins for specified characters and interval
     * @param  int    $character_id 
     * @param  int    $interval     
     * @return array         
     */
    public function calculateBestMargin(string $chars, int $interval): array
    {
        $configs = [
            'interval' => $interval,
            'chars' => $chars,
            'defs' => [
                'length' => 5,
                'start' => 0,
                'draw' => 1
            ]
        ];
        $result = $this->stats->getBestItemsMargin($configs);
        return json_decode($result, true)['data'];
    }

    /**
     * Get problematic items for specified characters and interval
     * @param  int    $character_id 
     * @param  int    $interval     
     * @return array         
     */
    public function calculateProblematicItems(string $chars, int $interval): array
    {
        $configs = [
            'interval' => $interval,
            'chars' => $chars,
            'defs' => [
                'length' => 5,
                'start' => 0,
                'draw' => 1
            ]
        ];
        $result = $this->stats->getProblematicItems($configs);
        return json_decode($result, true)['data'];
    }

    /**
     * Get best customers for specified characters and interval
     * @param  int    $character_id 
     * @param  int    $interval     
     * @return array         
     */
    public function calculateBestCustomers(string $chars, int $interval): array
    {
        $result = $this->stats->getBestCustomersRawProfit($chars, $interval);
        return $result;
    }

    /**
     * Get fastest turnovers for specified characters and interval
     * @param  int    $character_id 
     * @param  int    $interval     
     * @return array         
     */
    public function calculateFastestTurnovers(string $chars, int $interval): array
    {
        $result = $this->stats->getFastestTurnovers($chars, $interval);
        return $result;
    }

    /**
     * Get best isk per hour items for specified characters and interval
     * @param  int    $character_id 
     * @param  int    $interval     
     * @return array         
     */
    public function calculateBestIPH(string $chars, int $interval): array
    {
        $configs = [
            'interval' => $interval,
            'chars' => $chars,
            'defs' => [
                'length' => 5,
                'start' => 0,
                'draw' => 1
            ]
        ];
        $result = $this->stats->getBestIPH($configs);
        return json_decode($result, true)['data'];
    }

    /**
     * Get biggest losses for specified characters and interval
     * @param  int    $character_id 
     * @param  int    $interval     
     * @return array         
     */
    public function calculateBlunders(string $chars, int $interval): array
    {
        $result = $this->stats->getMarketBlunders($chars, $interval);
        return $result;
    }

    /**
     * Get best raw profit stations for specified characters and interval
     * @param  int    $character_id 
     * @param  int    $interval     
     * @return array         
     */
    public function calculateBestStations(string $chars, int $interval): array
    {
        $result = $this->stats->getTopStations($chars, $interval);
        return $result;
    }

    /**
     * Get the trade recap for specified characters and interval
     * @param  int    $character_id 
     * @param  int    $interval     
     * @return array         
     */
    public function calculateRecap(string $chars, int $interval): array
    {
        $result = $this->stats->getProfitsTable($chars, $interval);
        return $result;
    }
}
