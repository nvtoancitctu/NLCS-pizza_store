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
                        setTimeout(function () {
                            location.reload();
                        }, 100);   // reload page after 0.1s
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

});

function showLoginModal() {
    const modalHtml = `
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" id="loginModal">
            <div class="bg-white shadow-2xl rounded-xl max-w-xl w-full p-10 text-center transform scale-95 transition-transform duration-300">
                <h2 class="text-2xl font-bold mb-4">Please log in to add items to your cart!</h3>
                <p class="mb-8 text-lg text-gray-600">To add products to your cart, you need to log in. You can also continue browsing product details.</p>
                <div class="flex justify-center space-x-14">
                    <button type="button" class="bg-blue-500 text-white font-semibold px-6 py-3 rounded-xl shadow-lg hover:bg-blue-600 hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-105"
                      onclick="window.location.href='/index.php?page=login'">Log In</button>
                    <button id="continueBrowsing" class="bg-gray-500 text-white font-semibold px-6 py-3 rounded-xl shadow-lg hover:bg-gray-600 hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-105">Continue Browsing</button>
                </div>
            </div>
        </div>`;

    // Xóa modal cũ nếu có
    const existingModal = document.getElementById('loginModal');
    if (existingModal) {
        existingModal.remove();
    }

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    document.getElementById('continueBrowsing').addEventListener('click', function () {
        document.getElementById('loginModal').remove();
    });

    document.getElementById('loginModal').addEventListener('click', function (event) {
        if (event.target === this) {
            this.remove();
        }
    });
}

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