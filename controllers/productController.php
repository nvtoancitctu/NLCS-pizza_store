<?php
require_once '../models/Product.php';
require_once '../config.php'; // Kết nối tới database

class ProductController
{
  private $productModel;

  public function __construct($conn)
  {
    $this->productModel = new Product($conn);
  }

  // Lấy tất cả sản phẩm hoặc sản phẩm theo danh mục
  public function listProducts($category_id = null)
  {
    if ($category_id) {
      return $this->productModel->getProductsByCategory($category_id);
    } else {
      return $this->productModel->getAllProducts();
    }
  }

  // Lấy chi tiết sản phẩm
  public function getProductDetails($id)
  {
    return $this->productModel->getProductById($id);
  }

  // Lấy danh sách danh mục
  public function getCategories()
  {
    return $this->productModel->getCategories();
  }

  // Lấy sản phẩm ngẫu nhiên
  public function getRandomProducts($limit = 3)
  {
    return $this->productModel->getRandomProducts($limit);
  }

  // Lấy sản phẩm đang giảm giá (nếu có)
  public function getDiscountProduct()
  {
    return $this->productModel->getDiscountProduct();
  }

  // Thêm sản phẩm mới
  public function createProduct($name, $description, $price, $image, $category_id, $discount, $discount_end_time)
  {
    return $this->productModel->createProduct($name, $description, $price, $image, $category_id, $discount, $discount_end_time);
  }

  // Cập nhật sản phẩm
  public function updateProduct($id, $name, $description, $price, $image, $category_id, $discount, $discount_end_time)
  {
    return $this->productModel->updateProduct($id, $name, $description, $price, $image, $category_id, $discount, $discount_end_time);
  }

  // Xóa sản phẩm
  public function deleteProduct($id)
  {
    return $this->productModel->deleteProduct($id);
  }

  // Tìm kiếm sản phẩm
  public function searchProducts($searchTerm)
  {
    return $this->productModel->searchProducts($searchTerm);
  }

  // Lấy tên loại sản phẩm
  public function getDistinctCategories()
  {
    return $this->productModel->getDistinctCategories();
  }
}
