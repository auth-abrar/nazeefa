# Growth, Promotions & Customer CRM Architecture

## Overview
CommerceOS incorporates an advanced **Marketing, Promotional Coupon, Abandoned Cart Recovery, and Customer Risk Profiling subsystem** built specifically for Bangladeshi direct-to-consumer apparel commerce.

---

## 1. Promotional Coupon Engine (`ValidateCouponAction`)

- **Types Supported**:
  - `percentage`: Discount percent stored in basis points (e.g., 1500 = 15%) with optional `max_discount_amount` caps (e.g. ৳500 max).
  - `fixed_amount`: Direct reduction in poisha (e.g. ৳100 flat discount).
  - `free_shipping`: Eliminates courier delivery fee (Dhaka ৳70 / Outside Dhaka ৳130).
- **Enforcement Rules**:
  - Active flag (`is_active`).
  - Scheduled date windows (`starts_at`, `expires_at`).
  - Minimum cart spend (`min_spend_amount`).
  - Global redemption caps (`usage_limit`).

---

## 2. Abandoned Checkout Recovery

In the Bangladeshi ecommerce ecosystem, customers frequently input their phone numbers and delivery districts before abandoning due to payment friction or hesitation.
- **Tracking**: `TrackAbandonedCheckoutAction` saves customer contact details and serialized cart items asynchronously whenever phone + district are entered.
- **Recovery Link**: Generates a cryptographically unique `recovery_token` enabling customers to restore their cart with a single tap.
- **SMS Integration**: Dispatches recovery notifications via SMS (e.g. "Tanvir, your Monsoon Cotton Tee is reserved. Complete your order today with code EID2026 for 15% off!").

---

## 3. Bangladesh Customer CRM & COD Return Risk Intelligence

Reverse logistics and courier rejection fees (৳130–৳150 per package) are the single largest operational cost in Bangladesh apparel ecommerce.

### Mathematical Risk Score Formula:
$$\text{COD Return Risk Score} = \frac{\text{Returned Orders Count}}{\text{Delivered Orders Count} + \text{Returned Orders Count}}$$

### Risk Tiers:
| Tier | Criteria | System Action |
|---|---|---|
| **Verified VIP** | LTV $\ge ৳10,000$ and Risk $< 20\%$ | Automatic express packing, priority dispatch. |
| **Low Risk** | Return Risk $< 15\%$ | Instant 1-click courier dispatch approved. |
| **Moderate Risk** | Return Risk between $15\%$ and $34\%$ | Phone confirmation required prior to dispatch. |
| **High COD Risk** | Return Risk $\ge 35\%$ ($\ge 2$ completed trips) | **Flag Order: Advance Delivery Charge (৳130) Required** via bKash before booking courier parcel. |
