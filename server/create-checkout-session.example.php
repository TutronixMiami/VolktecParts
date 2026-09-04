<?php
// Example Hostinger endpoint. Install stripe/stripe-php with Composer before use.
// STRIPE_SECRET_KEY must be configured in the hosting environment, never in JavaScript.
declare(strict_types=1);
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
require_once __DIR__ . '/vendor/autoload.php';
$secret = getenv('STRIPE_SECRET_KEY');
if (!$secret) { http_response_code(503); echo json_encode(['error' => 'Checkout is not configured.']); exit; }
\Stripe\Stripe::setApiKey($secret);
$payload = json_decode(file_get_contents('php://input'), true);
// Production: validate product IDs against a server-side price catalog. Never trust browser prices.
$allowed = require __DIR__ . '/stripe-price-map.php';
$lineItems = [];
foreach (($payload['items'] ?? []) as $item) {
  $id = (string)($item['id'] ?? ''); $qty = max(1, min(20, (int)($item['quantity'] ?? 1)));
  if (!isset($allowed[$id])) { http_response_code(400); echo json_encode(['error' => 'Unknown product.']); exit; }
  $lineItems[] = ['price' => $allowed[$id], 'quantity' => $qty];
}
$origin = getenv('STORE_ORIGIN') ?: 'https://example.com';
$session = \Stripe\Checkout\Session::create(['mode'=>'payment','line_items'=>$lineItems,'success_url'=>$origin.'/success.html?session_id={CHECKOUT_SESSION_ID}','cancel_url'=>$origin.'/#catalog']);
echo json_encode(['url' => $session->url]);

