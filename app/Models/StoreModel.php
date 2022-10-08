<?php

namespace App\Models;

use CodeIgniter\Model;

class StoreModel extends Model
{
    protected $table            = 'store';
    protected $primaryKey       = 'store_id';
    protected $allowedFields    = ['store_name', 'account_id', 'area_id', 'is_active'];

    // Dates
    protected $useTimestamps = true;
}
