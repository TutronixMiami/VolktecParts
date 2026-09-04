# Volktec Parts storefront

Static, Hostinger-ready HTML/CSS/JavaScript storefront with client-side search, VW fitment filtering, categories, pagination, and a persistent shopping cart.

## Catalog

`data/products.json` contains representative sample records. It is deliberately separate from the interface so the complete catalog (~511 products) can be imported without rewriting the storefront. `data/product-schema.example.json` documents the complete target record, including SKU, inventory, condition, manufacturer/interchange numbers, images, shipping, position, and original eBay item number.

The sample descriptions, fitment, and prices must be verified before a public launch.

## Stripe preparation

Browser code contains no Stripe secret. `server/create-checkout-session.example.php` shows the intended Hostinger PHP integration. Before enabling checkout:

1. Install Stripe's PHP SDK on the server with Composer.
2. Configure `STRIPE_SECRET_KEY` and `STORE_ORIGIN` as server environment values.
3. Create `server/stripe-price-map.php` from the example and map product IDs to Stripe Price IDs.
4. Update `checkout()` in `app.js` to POST only product IDs and quantities to the server endpoint, then redirect to its returned Checkout URL.

Do not accept prices supplied by the browser.

## Importing the eBay inventory

Export active listings from eBay Seller Hub as CSV. A small import script can then normalize column names, split quantity and price into numeric fields, retain the eBay item number, download listing images into `assets/products/`, and map eBay categories and compatibility fields into the documented schema. Flag rows with missing OE numbers or ambiguous fitment for manual review, validate duplicate SKUs/part numbers, then output a replacement `data/products.json`. The interface does not need to change.

