# CommerceOS Architecture Blueprint & Specification

This repository represents the official implementation of **CommerceOS** for **Nazeefa** (`nazeefa.com`).

## Core Invariants

1. **Explicit State Transitions**: Never mutate order or production status arbitrarily. All status mutations must pass through dedicated domain actions (e.g., `TransitionOrderStatusAction`).
2. **Strict Financial Minor Units**: Never use floating-point math for pricing calculations. All calculations use minor integer units (poisha / cents) or exact 2-decimal arithmetic tagged with ISO currency (`currency`).
3. **Double-Entry Inventory**: Stock can never be directly overridden. Every inventory alteration must emit an immutable `StockMovement` row with an audit source (`Order`, `PurchaseOrder`, `ProductionJob`, or `Adjustment`).
4. **Provider Decoupling**: HTTP controllers never interact directly with external payment gateways, courier APIs, or suppliers. All external integrations adhere to domain contracts (`PaymentProviderInterface`, `CourierProviderInterface`, `SupplierProviderInterface`).
5. **Bangladesh-First UX**: Native support for Bangladeshi phone numbers, 64 districts, police stations/upazilas, Cash on Delivery (COD), bKash/Nagad copy-paste numbers, and direct Pathao/Steadfast waybill generation.
