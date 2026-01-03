/**
 * Formats a price value to a currency string with 2 decimal places
 * @param price - The price value (number or string)
 * @returns Formatted price string (e.g., "$10.99")
 */
export function formatPrice(price: number | string): string {
    return `$${Number(price).toFixed(2)}`;
}



