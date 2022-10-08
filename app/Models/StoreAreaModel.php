<?php

namespace App\Models;

use CodeIgniter\Model;

class StoreAreaModel extends Model
{
    protected $table            = 'store_area';
    protected $primaryKey       = 'area_id';
    protected $allowedFields    = ['area_name'];

    // Dates
    protected $useTimestamps = true;

    public function load_chart($area)
    {
        if ($area != null) {
            $this->whereIn('area_id', $area);
        }
        return $this->findAll();
    }
}
