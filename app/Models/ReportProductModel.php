<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportProductModel extends Model
{
    protected $table            = 'report_product';
    protected $primaryKey       = 'report_id';
    protected $allowedFields    = ['store_id', 'product_id', 'compliance', 'tanggal'];

    // Dates
    protected $useTimestamps = true;

    public function get_report($date_from, $date_to, $store)
    {
        $this->select('report_product.compliance, report_product.tanggal, store.store_id, store.area_id, store_area.*');
        $this->join('store', 'store.store_id=report_product.store_id');
        $this->join('store_area', 'store_area.area_id=store.area_id');
        // $this->where('store.area_id', $area);

        if ($date_from == null) {
            $get        = $this->select('tanggal')->orderBy('tanggal', 'ASC')->first();
            $date_from  = $get['tanggal'];
        }
        if ($date_to == null) {
            $get        = $this->select('tanggal')->orderBy('tanggal', 'DESC')->first();
            $date_to    = $get['tanggal'];
        }
        $key = "date(tanggal) BETWEEN date('" . date('Y-m-d', strtotime($date_from)) . "') AND date('" . date('Y-m-d', strtotime($date_to)) . "')";

        $this->where($key);
        $this->whereIn('report_product.store_id', $store);
        return $this;
    }

    public function load_table($date_from, $date_to, $store, $product_id)
    {
        $this->select('report_product.compliance, report_product.tanggal, product.product_id, product.brand_id, product_brand.*');
        $this->join('product', 'product.product_id=report_product.product_id');
        $this->join('product_brand', 'product_brand.brand_id=product.brand_id');

        if ($date_from == null) {
            $get        = $this->select('tanggal')->orderBy('tanggal', 'ASC')->first();
            $date_from  = $get['tanggal'];
        }
        if ($date_to == null) {
            $get        = $this->select('tanggal')->orderBy('tanggal', 'DESC')->first();
            $date_to    = $get['tanggal'];
        }
        $key = "date(tanggal) BETWEEN date('" . date('Y-m-d', strtotime($date_from)) . "') AND date('" . date('Y-m-d', strtotime($date_to)) . "')";

        $this->where($key);
        $this->whereIn('report_product.store_id', $store);
        $this->whereIn('report_product.product_id', $product_id);
        return $this;
    }
}
