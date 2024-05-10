<?php
require_once '../models/Sach.php';

class SachController {
    private $model;

    public function __construct($db) {
        $this->model = new Sach($db);
    }

    // Hiển thị trang Sach
    public function index() {
        // Gọi hàm lấy danh sách sách từ model
        $sachList = $this->model->getList();
        
        // Hiển thị view với dữ liệu sách
        include '../views/sach/index.php';
    }
}
?>
