<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductBrandModel;
use App\Models\ProductModel;
use App\Models\ReportProductModel;
use App\Models\StoreAccountModel;
use App\Models\StoreAreaModel;
use App\Models\StoreModel;

class ReportProduct extends BaseController
{
    public function __construct()
    {
        $this->productModel         = new ProductModel();
        $this->productBrandModel    = new ProductBrandModel();
        $this->reportProductModel   = new ReportProductModel();
        $this->storeModel           = new StoreModel();
        $this->storeAreaModel       = new StoreAreaModel();
        $this->storeAccountModel    = new StoreAccountModel();
    }

    public function index()
    {
        $data = [
            'content'       => 'report/report',
            'content_foot'  => 'report/report_foot',
        ];
        return view('layouts/wrapper', $data);
    }

    // ajax
    public function load_chart()
    {
        $area       = $this->request->getVar('area');
        $date_from  = $this->request->getVar('date_from');
        $date_to    = $this->request->getVar('date_to');

        $get_area   = $this->storeAreaModel->load_chart($area);

        $nilai = [];
        $nilai2 = [];

        foreach (array_column($get_area, 'area_id') as $a) {
            $get_store      = $this->storeModel->where('area_id', $a)->findAll();
            $ambil_store    = array_column($get_store, 'store_id');

            $get_report = $this->reportProductModel->get_report($date_from, $date_to, $ambil_store)->findAll();

            $hitung_nilai = 0;
            $hitung_nilai2 = 0;

            if (count($get_report) > 0) {

                $compl  = array_sum(array_column($get_report, 'compliance'));
                $hitung = count($get_report);

                $hitung_nilai = $compl / $hitung * 100;
                $hitung_nilai2 = $compl / $hitung * 100;
            }
            $nilai[] = $hitung_nilai;
            $nilai2[$a] = $hitung_nilai2;
        }

        $labels = [];
        foreach ($get_area as $a) {
            $labels[] = $a['area_name'] . ' (' . $nilai2[$a['area_id']] . '%)';
        }

        $get = $this->reportProductModel->select('tanggal');

        if ($date_from == null) {
            $date_from = $get->orderBy('tanggal', 'ASC')->first();
            $date_from = $date_from['tanggal'];
        }
        if ($date_to == null) {
            $date_to = $get->orderBy('tanggal', 'DESC')->first();
            $date_to = $date_to['tanggal'];
        }

        $data = [
            'labels'    => $labels,
            'data'      => $nilai,
            'date_from' => date('d F Y', strtotime($date_from)),
            'date_to'   => date('d F Y', strtotime($date_to)),
        ];

        echo json_encode($data);
    }

    public function get_area()
    {
        $request = service('request');
        $postData = $request->getPost();

        $response = array();

        $response['token'] = csrf_hash();

        if (!isset($postData['searchTerm'])) {
            $list = $this->storeAreaModel
                ->orderBy('area_id')
                ->findAll();
        } else {
            $searchTerm = $postData['searchTerm'];

            $list = $this->storeAreaModel
                ->where("(area_name LIKE '%" . $searchTerm . "%'", NULL, false)
                ->orderBy('area_id')
                ->findAll();
        }

        $data = array();
        foreach ($list as $l) {
            $data[] = array(
                "id"    => $l['area_id'],
                "text"  => $l['area_name'],
            );
        }

        $response['data'] = $data;

        return $this->response->setJSON($response);
    }

    // table
    public function load_table()
    {
        $area       = $this->request->getVar('area');
        $date_from  = $this->request->getVar('date_from');
        $date_to    = $this->request->getVar('date_to');

        $get_area       = $this->storeAreaModel->load_chart($area);
        $product_brand  = $this->productBrandModel->findAll();

        $nilai  = [];
        $nilai2 = [];
        $get_report2 = [];

        foreach (array_column($product_brand, 'brand_id') as $pb) {
            $product = $this->productModel->select('product_id')->where('brand_id', $pb)->findAll();
            $product_id = array_column($product, 'product_id');

            foreach (array_column($get_area, 'area_id') as $a) {
                $get_store      = $this->storeModel->where('area_id', $a)->findAll();
                $ambil_store    = array_column($get_store, 'store_id');

                $get_report = $this->reportProductModel->load_table($date_from, $date_to, $ambil_store, $product_id)->findAll();

                $hitung_nilai = 0;
                $hitung_nilai2 = 0;
                if (count($get_report) > 0) {

                    $compl  = array_sum(array_column($get_report, 'compliance'));
                    $hitung = count($get_report);

                    $hitung_nilai = $compl / $hitung * 100;
                    $hitung_nilai2 = $compl / $hitung * 100;
                }
                $nilai[$pb][$a]     = $hitung_nilai;
                // $nilai2[$pb][$a]    = $hitung_nilai2;
                // $get_report2[$pb][$a] = $get_report;
            }
        }

        $data = [
            'product_brand'     => $product_brand,
            // 'get_report2'    => $get_report2,
            'get_area'          => $get_area,
            'nilai'             => $nilai,
            // 'nilai2'            => $nilai2,
        ];
        // dd($data);

        echo json_encode($data);
    }
}
