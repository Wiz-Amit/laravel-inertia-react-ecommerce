import cart from '@/routes/cart';
import { router, usePage } from '@inertiajs/react';
import { useState } from 'react';

interface CartSummary {
    item_count: number;
    product_ids: number[];
}

interface UseCartOptions {
    onSuccess?: () => void;
    onError?: (error: string) => void;
}

export function useCart(options: UseCartOptions = {}) {
    const page = usePage<{ cartSummary: CartSummary | null }>();
    const [addingToCart, setAddingToCart] = useState<number | null>(null);

    const isProductInCart = (productId: number): boolean => {
        return page.props.cartSummary?.product_ids.includes(productId) ?? false;
    };

    const handleAddToCart = (productId: number, quantity: number = 1) => {
        setAddingToCart(productId);
        router.post(
            cart.items.store.url(),
            {
                product_id: productId,
                quantity,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    options.onSuccess?.();
                },
                onError: (errors) => {
                    const errorMessage = errors.error || Object.values(errors)[0] || 'Failed to add product to cart.';
                    options.onError?.(errorMessage);
                },
                onFinish: () => {
                    setAddingToCart(null);
                },
            }
        );
    };

    const handleGoToCart = () => {
        router.visit(cart.show.url());
    };

    return {
        isProductInCart,
        handleAddToCart,
        handleGoToCart,
        addingToCart,
        cartSummary: page.props.cartSummary,
    };
}


