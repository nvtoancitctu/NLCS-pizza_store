// navbar đối với giao diện cửa sổ nhỏ
document.getElementById('navbar-toggler').addEventListener('click', function () {
    document.getElementById('mobile-menu').classList.toggle('hidden');
});

// Khi đăng nhập thành công sẽ thay đổi navbar
const userDropdownToggle = document.getElementById('user-dropdown-toggle');
if (userDropdownToggle) {
    userDropdownToggle.addEventListener('click', function () {
        const userDropdown = document.getElementById('user-dropdown');
        if (userDropdown) {
            userDropdown.classList.toggle('hidden');
        }
    });
}

// Mobile User Dropdown Toggle
const mobileUserDropdownToggle = document.getElementById('mobile-user-dropdown-toggle');
if (mobileUserDropdownToggle) {
    mobileUserDropdownToggle.addEventListener('click', function () {
        const mobileUserDropdown = document.getElementById('mobile-user-dropdown');
        if (mobileUserDropdown) {
            mobileUserDropdown.classList.toggle('hidden');
        }
    });
}


// Xử lý việc thêm SP vào giỏ với Ajax (không tải lại trang)
document.addEventListener("DOMContentLoaded", function () {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-button');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = e.target.closest('.add-to-cart-form');
            const formData = new FormData(form);

            // Add the action parameter for the AJAX endpoint
            formData.append('action', 'add_to_cart');

            fetch('/ajax.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Marks the request as an AJAX request
                }
            })
                .then(response => {
                    if (response.headers.get('content-type')?.includes('application/json')) {
                        return response.json();
                    } else {
                        throw new Error('Response is not JSON');
                    }
                })
                .then(data => {
                    if (data.loggedIn === false) {
                        showLoginModal();
                    } else if (data.success) {
                        alert("Product added to cart successfully!");
                    } else {
                        alert("An error occurred while adding the product to the cart.");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("An error occurred. Please try again.");
                });
        });
    });

    function showLoginModal() {
        const modalHtml = `
            <div class="modal" id="loginModal" style="display: block; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5);">
                <div style="background: #fff; padding: 20px; max-width: 500px; margin: 100px auto; border-radius: 8px;">
                    <h3>Please log in to add items to your cart.</h3>
                    <p>To add products to your cart, you need to log in. You can also continue browsing the product details.</p>
                    <a href="/index.php?page=login" class="btn btn-primary">Log In</a>
                    <button id="continueBrowsing" class="btn btn-secondary">Continue Browsing</button>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        document.getElementById('continueBrowsing').addEventListener('click', function () {
            document.getElementById('loginModal').remove();
        });
    }
});


// Hiển thị thông báo đăng xuất
function showLogoutModal() {
    document.getElementById('logout-modal').classList.remove('hidden');
}

// Check the URL for 'logout=success' and trigger the modal
document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('logout') === 'success') {
        showLogoutModal();  // Show modal if logout was successful
    }
});


function addToCart(productId) {
    // Tạo form ẩn để gửi dữ liệu
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = ''; // Đặt URL đến trang hiện tại

    var inputProductId = document.createElement('input');
    inputProductId.type = 'hidden';
    inputProductId.name = 'product_id';
    inputProductId.value = productId;
    form.appendChild(inputProductId);

    var inputQuantity = document.createElement('input');
    inputQuantity.type = 'hidden';
    inputQuantity.name = 'quantity';
    inputQuantity.value = '1'; // Hoặc giá trị số lượng bạn muốn
    form.appendChild(inputQuantity);

    // Gửi form
    document.body.appendChild(form);
    form.submit();

    // Làm mới trang sau khi gửi form
    setTimeout(function () {
        location.reload(); // Làm mới trang
    }, 1000); // Thời gian delay 1000ms (1 giây)
}
