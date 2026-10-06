# Courier Integration Architecture & Webhook Reference

CommerceOS integrates natively with Bangladesh's premier logistics providers:

## Supported Providers
1. **Pathao Courier** (`pathao`):
   - Hub: API Hermes (`https://api-hermes.pathao.com`) & Aladdin Sandbox.
   - Flow: OAuth Bearer tokens, dynamic store lookup, 64-district delivery.
   - Webhook: Receives status updates (`picked_up`, `in_transit`, `delivered`).

2. **Steadfast Courier** (`steadfast`):
   - Fast door-to-door nationwide delivery with next-day settlement in Dhaka.
   - Authentication via header keys (`Api-Key`, `Secret-Key`).
   - Webhook: Automated consignment milestone synchronization.

## Normalization Table

| External Status (Pathao / Steadfast) | Internal Normalized State (`shipments.status`) | Order Status (`orders.status`) |
|---|---|---|
| `Pickup_Pending` / `In_Review` | `pending` | `ready_to_ship` |
| `Picked_Up` / `Picked` | `picked_up` | `shipped` |
| `In_Transit` / `Sorting` | `in_transit` | `shipped` |
| `Out_For_Delivery` / `On_The_Way` | `out_for_delivery` | `out_for_delivery` |
| `Delivered` / `Delivery_Successful` | `delivered` | `delivered` (COD Marked Paid) |
| `Return` / `Returned` | `returned` | `return_requested` |
