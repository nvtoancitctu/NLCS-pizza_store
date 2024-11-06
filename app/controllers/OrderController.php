<?php

require_once dirname(__DIR__) . '/models/Order.php';

class OrderController
{
  private $orderModel;

  public function __construct($db)
  {
    $this->orderModel = new Order($db);
  }

  // Create a new order
  public function createOrder($user_id, $total, $payment_method, $address)
  {
    return $this->orderModel->createOrder($user_id, $total, $payment_method, $address);
  }

  // Add items to an order
  public function addOrderItem($order_id, $product_id, $quantity, $price)
  {
    return $this->orderModel->addOrderItem($order_id, $product_id, $quantity, $price);
  }

  public function getOrderDetails($order_id, $user_id)
  {
    return $this->orderModel->getOrderDetails($order_id, $user_id);
  }

  public function getOrdersByUserId($user_id)
  {
    return $this->orderModel->getOrdersByUserId($user_id);
  }

  public function getOrderDetailsByOrderId($order_id)
  {
    return $this->orderModel->getOrderDetailsByOrderId($order_id);
  }
}
