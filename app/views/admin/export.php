<?php
require_once '../vendor/autoload.php';  // Kiểm tra đường dẫn đúng

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
$row = 2;
foreach ($products as $product) {
    $sheet->setCellValue('A' . $row, $product['id']);
    $sheet->setCellValue('B' . $row, $product['name']);
    $sheet->setCellValue('C' . $row, $product['description']);
    $sheet->setCellValue('D' . $row, $product['price']);
    $sheet->setCellValue('E' . $row, $product['image']);
    $sheet->setCellValue('F' . $row, $product['category_id']);
    $sheet->setCellValue('G' . $row, $product['created_at'] ?? 'N/A');
    $sheet->setCellValue('H' . $row, $product['discount']);
    $sheet->setCellValue('I' . $row, $product['discount_end_time'] ?? 'N/A');
    $row++;
}

// Thiết lập headers để tải xuống file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="product_list.xlsx"'); // Đảm bảo phần mở rộng là .xlsx
header('Cache-Control: max-age=0');

// Lưu file vào output stream để tải xuống
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
