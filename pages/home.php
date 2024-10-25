<?php
// Kết nối database và nạp model Product
require_once '../config.php';
require_once '../models/Product.php';

// Khởi tạo đối tượng Product
$productModel = new Product($conn);

// Lấy 3 sản phẩm ngẫu nhiên
$randomProducts = $productModel->getRandomProducts(3);

// Lấy 1 sản phẩm giảm giá có thời gian còn lại
$discountProduct = $productModel->getDiscountProduct();
?>

<div class="container mx-auto px-12">
  <!-- Jumbotron -->
  <div class="bg-gradient-to-r from-blue-500 to-blue-700 text-white text-center p-10 rounded-2xl shadow-2xl mt-8">
    <h1 class="text-5xl font-extrabold mb-4 drop-shadow-lg">Welcome to Pizza Store!</h1>
    <p class="mt-2 text-lg font-light">Delicious pizzas made with the finest ingredients. Order now!</p>
    <button type="button"
      class="mt-6 inline-block bg-yellow-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl text-lg transition duration-300 transform hover:-translate-y-1 hover:scale-105 shadow-lg"
      onclick="window.location.href='/index.php?page=products'">Go Shopping Now</button>
  </div>

  <!-- Discount Products -->
  <h2 class="text-4xl font-extrabold text-center my-10 text-blue-700 drop-shadow-lg">Special Discount Offer</h2>
  <?php if (!empty($discountProduct)): ?>
    <?php foreach ($discountProduct as $product): ?>
      <div class="bg-white rounded-lg shadow-xl mb-8 p-6 transition-transform transform hover:scale-105 hover:shadow-2xl duration-300">
        <div class="flex justify-center">
          <div class="flex-shrink-0 w-1/3 flex justify-center items-center">
            <img src="/images/<?php echo htmlspecialchars($product['image']); ?>" class="w-3/5 h-auto mx-auto object-cover rounded-lg"
              alt="<?php echo htmlspecialchars($product['name']); ?>">
          </div>
          <div class="flex-grow p-4">
            <h5 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($product['name']); ?></h5>
            <p class="text-gray-700"><?php echo htmlspecialchars($product['description']); ?></p>
            <p class="mt-2 mb-2">
              <small class="text-gray-600 line-through">Original Price: $<?php echo htmlspecialchars($product['price']); ?></small><br>
              <strong class="text-blue-500 text-2xl font-bold">Discounted Price: </strong>
              <span class="text-red-600 text-3xl font-bold">$<?php echo htmlspecialchars($product['discount']); ?></span>
            </p>
            <p class="text-red-600 font-bold text-lg mt-2 mb-4" id="discount-timer-<?php echo $product['id']; ?>">Limited Time Offer!</p>
            <button type="button" class="bg-yellow-500 text-white px-5 py-2 rounded-lg transition duration-300 hover:bg-green-600 shadow-lg"
              onclick="window.location.href='/index.php?page=product-detail&id=<?php echo $product['id']; ?>'">Buy Now</button>
          </div>
        </div>

        <script>
          // JavaScript countdown timer
          function countdownTimer(endTime, elementId) {
            var countDownDate = new Date(endTime).getTime();

            var x = setInterval(function() {
              var now = new Date().getTime();
              var distance = countDownDate - now;

              // Tính toán ngày, giờ, phút, giây còn lại
              var days = Math.floor(distance / (1000 * 60 * 60 * 24));
              var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
              var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
              var seconds = Math.floor((distance % (1000 * 60)) / 1000);

              // Đảm bảo có hai chữ số cho giờ, phút, giây
              hours = hours.toString().padStart(2, '0');
              minutes = minutes.toString().padStart(2, '0');
              seconds = seconds.toString().padStart(2, '0');

              // Cập nhật nội dung phần tử với thời gian đếm ngược
              document.getElementById(elementId).innerHTML = `
                <div class="flex justify-center items-center p-2 w-1/4 bg-gray border border-gray-500 rounded-xl shadow-sm">
                  <div class="flex space-x-3 text-center">
                    <div class="flex flex-col items-center">
                      <span class="text-3xl font-bold text-red-600">${days}</span>
                      <span class="text-xs font-medium text-blue-500">Days</span>
                    </div>
                    <div class="flex flex-col items-center">
                      <span class="text-3xl font-bold text-red-600">${hours}</span>
                      <span class="text-xs font-medium text-blue-500">Hours</span>
                    </div>
                    <div class="flex flex-col items-center">
                      <span class="text-3xl font-bold text-red-600">${minutes}</span>
                      <span class="text-xs font-medium text-blue-500">Minutes</span>
                    </div>
                    <div class="flex flex-col items-center">
                      <span class="text-3xl font-bold text-red-600">${seconds}</span>
                      <span class="text-xs font-medium text-blue-500">Seconds</span>
                    </div>
                  </div>
                </div>
              `;

              // Kiểm tra nếu thời gian hết
              if (distance < 0) {
                clearInterval(x);
                document.getElementById(elementId).innerHTML = "EXPIRED";
              }
            }, 1000);
          }

          // Gọi hàm countdownTimer cho từng sản phẩm
          countdownTimer('<?php echo $product['discount_end_time']; ?>', 'discount-timer-<?php echo $product['id']; ?>');
        </script>

      </div>

    <?php endforeach; ?>
  <?php else: ?>
    <p class="text-center text-gray-700">Currently, no products are on discount.</p>
  <?php endif; ?>

  <!-- Featured Pizzas -->
  <h2 class="text-4xl font-extrabold text-center my-10 text-blue-700 drop-shadow-lg">Featured Pizzas</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-6">
    <?php foreach ($randomProducts as $product): ?>
      <div class="bg-white rounded-lg shadow-lg transition-transform transform hover:scale-105">
        <img src="/images/<?php echo htmlspecialchars($product['image']); ?>"
          class="w-3/5 h-auto mx-auto object-cover rounded-lg"
          alt="<?php echo htmlspecialchars($product['name']); ?>">
        <div class="p-6">
          <h5 class="text-2xl font-bold mb-2 text-center"><?php echo htmlspecialchars($product['name']); ?></h5>
          <p class="card-text text-gray-600 text-center mb-2"><?php echo htmlspecialchars($product['description']); ?></p>
          <div class="text-center mt-auto mb-2">
            <button type="button" class="bg-blue-500 text-white px-5 py-2 rounded-lg transition duration-300 hover:bg-green-500 shadow-lg"
              onclick="window.location.href='/index.php?page=product-detail&id=<?php echo $product['id']; ?>'">View Details</button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Logout Modal -->
<div id="logout-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
  <div class="bg-white shadow-2xl rounded-xl max-w-xl w-full p-10 text-center transform scale-95 transition-transform duration-300">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">You have successfully logged out!</h2>
    <p class="mb-8 text-lg text-gray-600">To place orders, please log in or sign up for an account.</p>
    <div class="flex justify-center space-x-14">
      <button type="button" class="bg-blue-500 text-white font-semibold px-6 py-3 rounded-xl shadow-lg hover:bg-blue-600 hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-105"
        onclick="window.location.href='/index.php?page=login'">Log In</button>
      <button type="button" class="bg-gray-500 text-white font-semibold px-6 py-3 rounded-xl shadow-lg hover:bg-gray-600 hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-105"
        onclick="window.location.href='/index.php?page=home'">Continue Shopping</button>
    </div>
  </div>
</div>