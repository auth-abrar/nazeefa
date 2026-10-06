export type Currency = 'BDT' | 'USD';

export interface Money {
  amountCents: number; // Stored as poisha in BDT or cents in USD
  currency: Currency;
}

export interface ProductVariant {
  id: number;
  sku: string;
  size: string;
  color: string;
  price: Money;
  compareAtPrice?: Money;
  stockOnHand: number;
  stockAvailable: number;
}

export interface Product {
  id: number;
  name: string;
  slug: string;
  category: string;
  isCustomizable: boolean;
  variants: ProductVariant[];
  featuredImage: string;
}
