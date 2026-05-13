// Mock product data for frontend
const MOCK_PRODUCTS = [
    { id: 1, name: "Wheat", price: 2.99, category: "Grains", image: "images/wheat.jpeg" },
    { id: 2, name: "Rice", price: 3.49, category: "Grains", image: "images/rice.jpeg" },
    { id: 3, name: "Sugar", price: 1.99, category: "Sweeteners", image: "images/sugar.jpeg" },
    { id: 4, name: "Salt", price: 0.99, category: "Seasonings", image: "images/salt.jpeg" },
    { id: 5, name: "Oil", price: 5.99, category: "Oils", image: "images/oil.jpeg" },
    { id: 6, name: "Honey", price: 7.99, category: "Condiments", image: "images/honey.jpeg" }
];

// Mock API for products
async function fetchProducts() {
    return new Promise(resolve => {
        setTimeout(() => resolve(MOCK_PRODUCTS), 100);
    });
}

async function fetchProduct(id) {
    return new Promise(resolve => {
        setTimeout(() => {
            const product = MOCK_PRODUCTS.find(p => p.id === parseInt(id));
            resolve(product || null);
        }, 100);
    });
}
