# Supplier & CJ Dropshipping Integration Architecture

## Overview
CommerceOS features an integrated international supply-chain engine supporting **CJ Dropshipping** (and prepared for Alibaba) to supplement local Bangladeshi inventory with trending blanks, streetwear fabrics, and international fashion items.

---

## 1. Relational Data Model

- **`suppliers`**: Partner credentials, API endpoints, reliability ratings, lead time (7–14 days), and operational config.
- **`supplier_products`**: Sourced wholesale products mapped to local catalog products (`product_id`), tracking wholesale cost in USD cents, estimated packet freight, and synchronization timestamps.
- **`supplier_variants`**: Maps CJ variant IDs (`vid`) to local `product_variants` with weights and stock buffers.
- **`supplier_orders`**: Sourcing orders created with CJ Dropshipping tracking courier codes and status updates.

---

## 2. CJ Dropshipping Open API 2.0 Endpoints

| Purpose | Method | Endpoint | Description |
|---|---|---|---|
| **Authentication** | `POST` | `/authentication/getAccessToken` | Generates or refreshes OAuth access token using email and API Key. |
| **Product Search** | `GET` | `/product/query` | Keyword and category search for apparel items. |
| **Product Detail** | `GET` | `/product/detail` | Fetches variant matrix (`vid`), sizes, colors, and weights. |
| **Freight Estimation** | `POST` | `/logistic/freightCalculate` | Computes live packet shipping costs to Bangladesh (`startCountryCode: CN`, `endCountryCode: BD`). |
| **Order Placement** | `POST` | `/shopping/order/createOrder` | Dispatches customer delivery details to CJ for automated fulfillment. |
| **Tracking Inquiry** | `GET` | `/shopping/order/getOrderDetail` | Inquires live tracking numbers and carrier updates. |

---

## 3. Resilient Sandbox Fallback
If `CJ_DROPSHIPPING_API_KEY` or `CJ_DROPSHIPPING_EMAIL` are absent:
- The system operates transparently in **Deterministic Sandbox Mode**.
- Returns realistic mock apparel blanks (Acid Wash Tee, French Terry Hoodie, Mock Neck Tee).
- Displays clear amber warning banners in the Admin Sourcing Desk: `[SANDBOX MODE: CJ_DROPSHIPPING_API_KEY Not Configured]`.
- Guarantees zero crashes and complete end-to-end simulation of pricing, variant mapping, and catalog import.

---

## 4. Landed Cost & Retail Pricing Engine

The system uses a mathematical formula to determine profitable Bangladeshi retail pricing:

$$\text{Landed Cost (BDT)} = (\text{Supplier Cost USD} + \text{Freight USD}) \times \text{FX Rate} \times (1 + \text{Customs Buffer})$$

$$\text{Recommended Retail (BDT)} = \frac{\text{Landed Cost}}{1 - (\text{Target Margin} + \text{Payment Gateway Fee})}$$

- **Defaults**:
  - FX Rate: ৳122.00 / $1 USD
  - Customs/Import Buffer: 10%
  - Payment Gateway Fee: 2.5% (SSLCOMMERZ / bKash)
  - Target Margin: 35% – 50%
  - Pricing Clean Rounding: Ceiled to nearest ৳50 or ৳90 price point.
