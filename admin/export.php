<?php
require '../vendor/autoload.php'; // Đảm bảo đường dẫn đúng tới autoload.php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Khởi tạo model
$productModel = new ProductController($conn);

// Lấy danh sách sản phẩm
$products = $productModel->listProducts();

// Tạo một đối tượng Spreadsheet mới
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Thiết lập tiêu đề cho các cột
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Name');
$sheet->setCellValue('C1', 'Description');
$sheet->setCellValue('D1', 'Price');
$sheet->setCellValue('E1', 'Image');
$sheet->setCellValue('F1', 'Category ID');
$sheet->setCellValue('G1', 'Created At');
$sheet->setCellValue('H1', 'Discount');
$sheet->setCellValue('I1', 'Discount End Time');

// Đưa dữ liệu sản phẩm vào các ô
$row = 2; // Bắt đầu từ hàng thứ 2 để không ghi đè lên tiêu đề
foreach ($products as $product) {
    $sheet->setCellValue('A' . $row, $product['id']);
    $sheet->setCellValue('B' . $row, $product['name']);
    $sheet->setCellValue('C' . $row, $product['description']);
    $sheet->setCellValue('D' . $row, $product['price']);
    $sheet->setCellValue('E' . $row, $product['image']);
    $sheet->setCellValue('F' . $row, $product['category_id']);
    $sheet->setCellValue('G' . $row, $product['created_at']);
    $sheet->setCellValue('H' . $row, $product['discount']);
    $sheet->setCellValue('I' . $row, $product['discount_end_time']);
    $row++;
}

// Lưu tệp Excel vào thư mục hiện tại
$writer = new Xlsx($spreadsheet);
$filename = 'product_list.xlsx';

try {
    $writer->save($filename);
    // Thông báo thành công và chuyển hướng
    echo "<script>alert('The Excel file has been created successfully: $filename'); window.location.href = '/index.php?page=list';</script>";
} catch (Exception $e) {
    echo "<script>alert('An error occurred: " . $e->getMessage() . "'); window.location.href = '/index.php?page=list';</script>";
}
