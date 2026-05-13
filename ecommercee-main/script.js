// Shopping Cart
let cart = JSON.parse(localStorage.getItem('cart')) || [];

function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartBadge();
}

function updateCartBadge() {
    const badge = document.getElementById('cart-badge');
    if (badge) {
        const count = cart.reduce((sum, item) => sum + item.qty, 0);
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline' : 'none';
    }
}

function addToCart(name, price) {
    const existing = cart.find(item => item.name === name);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({ name, price, qty: 1 });
    }
    saveCart();
    showToast(`${name} added to cart`);
}

function removeFromCart(name) {
    cart = cart.filter(item => item.name !== name);
    saveCart();
    renderCart();
}

function changeQty(name, delta) {
    const item = cart.find(i => i.name === name);
    if (item) {
        item.qty += delta;
        if (item.qty <= 0) {
            removeFromCart(name);
            return;
        }
        saveCart();
        renderCart();
    }
}

function getCartTotal() {
    return cart.reduce((sum, item) => sum + item.price * item.qty, 0);
}

function checkout() {
    if (cart.length === 0) {
        showToast('Your cart is empty.');
        return;
    }
    const total = getCartTotal().toFixed(2);
    if (confirm(`Your total is $${total}. Proceed with the order?`)) {
        cart = [];
        saveCart();
        renderCart();
        showToast('Order placed successfully!');
    }
}

function renderCart() {
    const container = document.getElementById('cart-items');
    const totalEl = document.getElementById('cart-total');
    if (!container) return;
    if (cart.length === 0) {
        container.innerHTML = '<p>Your cart is empty.</p>';
        if (totalEl) totalEl.textContent = '0.00';
        return;
    }
    container.innerHTML = cart.map(item => `
        <div class="cart-item">
            <span>${item.name}</span>
            <span>$${(item.price * item.qty).toFixed(2)}</span>
            <div class="cart-qty">
                <button onclick="changeQty('${item.name}', -1)">-</button>
                <span>${item.qty}</span>
                <button onclick="changeQty('${item.name}', 1)">+</button>
            </div>
            <button class="remove-btn" onclick="removeFromCart('${item.name}')">Remove</button>
        </div>
    `).join('');
    if (totalEl) totalEl.textContent = getCartTotal().toFixed(2);
}

function showToast(msg) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2000);
}

// Search / Filter
function filterProducts() {
    const input = document.getElementById('search-input');
    if (!input) return;
    const query = input.value.toLowerCase();
    document.querySelectorAll('.product').forEach(el => {
        const name = el.querySelector('h3')?.textContent?.toLowerCase() || '';
        el.style.display = name.includes(query) ? '' : 'none';
    });
}

// Contact form validation
document.addEventListener('DOMContentLoaded', function () {
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            const name = document.getElementById('contact-name')?.value.trim();
            const email = document.getElementById('contact-email')?.value.trim();
            const msg = document.getElementById('contact-msg')?.value.trim();
            if (!name || !email || !msg) {
                e.preventDefault();
                showToast('Please fill in all fields.');
                return;
            }
            if (!email.includes('@')) {
                e.preventDefault();
                showToast('Enter a valid email address.');
            }
        });
    }

    // Cart page render
    renderCart();
    updateCartBadge();
});
