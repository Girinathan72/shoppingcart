<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

require_once 'config.php';

// Currency conversion rates (USD to other currencies)
define('USD_TO_INR', 83.00); // 1 USD = 83 INR

// Function to convert price from USD to specified currency
function convertCurrency($priceInUSD, $toCurrency = 'USD') {
    $rates = [
        'USD' => 1.00,
        'INR' => USD_TO_INR
    ];
    
    if (!isset($rates[$toCurrency])) {
        return $priceInUSD;
    }
    
    return round($priceInUSD * $rates[$toCurrency], 2);
}

$action = $_GET['action'] ?? '';
$currency = $_GET['currency'] ?? 'USD'; // Default to USD

switch ($action) {

    // GET /api.php?action=products&currency=INR
    case 'products':
        $stmt = $pdo->query("SELECT p.*, c.name AS category FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.name");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Apply currency conversion if needed
        if ($currency !== 'USD') {
            foreach ($products as &$product) {
                if (isset($product['price'])) {
                    $product['price'] = convertCurrency($product['price'], $currency);
                }
            }
        }
        
        echo json_encode(['currency' => $currency, 'products' => $products]);
        break;

    // GET /api.php?action=product&id=X&currency=INR
    case 'product':
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("SELECT p.*, c.name AS category FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            echo json_encode(['error' => 'Product not found']);
            break;
        }
        
        // Apply currency conversion if needed
        if ($currency !== 'USD' && isset($product['price'])) {
            $product['price'] = convertCurrency($product['price'], $currency);
        }
        
        echo json_encode(['currency' => $currency, 'product' => $product]);
        break;

    // POST /api.php?action=submit_contact
    case 'submit_contact':
        $input = json_decode(file_get_contents('php://input'), true);
        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $message = trim($input['message'] ?? '');

        if (!$name || !$email || !$message) {
            http_response_code(400);
            echo json_encode(['error' => 'All fields are required.']);
            break;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid email address.']);
            break;
        }

        // Save to contacts table or send email
        $stmt = $pdo->prepare("INSERT INTO users (name, email, address) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $message]);
        echo json_encode(['success' => 'Thank you! We will get back to you soon.']);
        break;

    // POST /api.php?action=place_order&currency=INR
    case 'place_order':
        $input = json_decode(file_get_contents('php://input'), true);
        $items = $input['items'] ?? [];
        $userId = $input['user_id'] ?? null;

        if (empty($items)) {
            http_response_code(400);
            echo json_encode(['error' => 'Cart is empty.']);
            break;
        }

        $total = 0;
        $convertedItems = [];
        foreach ($items as $item) {
            $itemPrice = $item['price'] * $item['qty'];
            $total += $itemPrice;
            
            // Apply currency conversion
            if ($currency !== 'USD') {
                $itemPrice = convertCurrency($itemPrice, $currency);
            }
            
            $convertedItems[] = [
                'name' => $item['name'],
                'price' => $itemPrice,
                'qty' => $item['qty']
            ];
        }
        
        // Convert total if needed
        $displayTotal = ($currency !== 'USD') ? convertCurrency($total, $currency) : $total;

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
            $stmt->execute([$userId, $total]); // Store in USD
            $orderId = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)");
            foreach ($items as $item) {
                $stmt->execute([$orderId, $item['name'], $item['price'], $item['qty']]);
            }

            $pdo->commit();
            echo json_encode([
                'success' => 'Order placed successfully!', 
                'order_id' => $orderId,
                'currency' => $currency,
                'total' => $displayTotal
            ]);
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Order failed: ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Unknown action.']);
    
    // GET /api.php?action=convert_currency&amount=100&from=USD&to=INR
    case 'convert_currency':
        $amount = $_GET['amount'] ?? 0;
        $fromCurrency = $_GET['from'] ?? 'USD';
        $toCurrency = $_GET['to'] ?? 'USD';
        
        // First convert to USD if needed
        $amountInUSD = $amount;
        if ($fromCurrency !== 'USD') {
            $inverseRates = ['INR' => 1 / USD_TO_INR];
            if (isset($inverseRates[$fromCurrency])) {
                $amountInUSD = $amount * $inverseRates[$fromCurrency];
            }
        }
        
        $convertedAmount = convertCurrency($amountInUSD, $toCurrency);
        
        echo json_encode([
            'original_amount' => $amount,
            'original_currency' => $fromCurrency,
            'converted_amount' => $convertedAmount,
            'converted_currency' => $toCurrency,
            'exchange_rate' => round($convertedAmount / $amount, 4)
        ]);
        break;
}
