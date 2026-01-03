import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { useFlashMessages } from '@/hooks/use-flash-messages';
import { formatPrice } from '@/lib/format-price';
import AppLayout from '@/layouts/app-layout';
import checkout from '@/routes/checkout';
import products from '@/routes/products';
import { type BreadcrumbItem, type Cart, type CartItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { CreditCard, Loader2, Lock, Minus, Plus, ShoppingCart, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface Props {
    cart: Cart | null;
    stripeKey: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Cart',
        href: '/cart',
    },
];

export default function CartIndex({ cart }: Props) {
    useFlashMessages();
    const [updating, setUpdating] = useState<number | null>(null);
    const [removing, setRemoving] = useState<number | null>(null);
    const [processing, setProcessing] = useState(false);
    const page = usePage<{ csrf_token?: string }>();

    const handleUpdateQuantity = (itemId: number, newQuantity: number) => {
        if (newQuantity < 1) return;

        setUpdating(itemId);
        router.put(
            `/cart/items/${itemId}`,
            { quantity: newQuantity },
            {
                preserveScroll: true,
                onFinish: () => setUpdating(null),
            }
        );
    };

    const handleRemoveItem = (itemId: number) => {
        setRemoving(itemId);
        router.delete(`/cart/items/${itemId}`, {
            preserveScroll: true,
            onFinish: () => setRemoving(null),
        });
    };

    const handleClearCart = () => {
        if (confirm('Are you sure you want to clear your cart?')) {
            router.delete('/cart', {
                preserveScroll: true,
            });
        }
    };

    const handleCheckout = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        setProcessing(true);
        
        // Use a regular form submission to avoid CORS issues with Stripe Checkout
        // This ensures a full page redirect (not an AJAX request)
        const form = e.currentTarget;
        form.submit();
    };

    if (!cart || cart.items.length === 0) {
        return (
            <AppLayout breadcrumbs={breadcrumbs}>
                <Head title="Cart" />
                <div className="flex h-full flex-1 flex-col items-center justify-center gap-4 p-6">
                    <ShoppingCart className="h-16 w-16 text-muted-foreground/50" />
                    <div className="text-center">
                        <h2 className="text-2xl font-bold">Your cart is empty</h2>
                        <p className="text-muted-foreground">Add some products to get started!</p>
                    </div>
                    <Button onClick={() => router.visit(products.index.url())}>Browse Products</Button>
                </div>
            </AppLayout>
        );
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Cart" />
            <div className="flex h-full flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Shopping Cart</h1>
                        <p className="text-muted-foreground">
                            {cart.items.length} {cart.items.length === 1 ? 'item' : 'items'} in your cart
                        </p>
                    </div>
                    {cart.items.length > 0 && (
                        <Button variant="outline" onClick={handleClearCart}>
                            Clear Cart
                        </Button>
                    )}
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="lg:col-span-2 space-y-4">
                        {cart.items.map((item) => (
                            <Card key={item.id}>
                                <CardHeader>
                                    <div className="flex items-start gap-4">
                                        <Link
                                            href={products.show.url(item.product.id)}
                                            className="flex-shrink-0"
                                        >
                                            <div className="h-12 w-12 overflow-hidden rounded-lg bg-muted flex items-center justify-center">
                                                {item.product.image ? (
                                                    <img
                                                        src={item.product.image}
                                                        alt={item.product.name}
                                                        className="h-full w-full object-cover"
                                                    />
                                                ) : (
                                                    <ShoppingCart className="h-5 w-5 text-muted-foreground/50" />
                                                )}
                                            </div>
                                        </Link>
                                        <div className="flex-1 min-w-0">
                                            <Link
                                                href={products.show.url(item.product.id)}
                                                className="block hover:underline"
                                            >
                                                <CardTitle className="line-clamp-1">{item.product.name}</CardTitle>
                                            </Link>
                                            <CardDescription className="mt-1 line-clamp-2">
                                                {item.product.description || 'Eco-friendly product'}
                                            </CardDescription>
                                        </div>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            onClick={() => handleRemoveItem(item.id)}
                                            disabled={removing === item.id}
                                            className="flex-shrink-0"
                                        >
                                            {removing === item.id ? (
                                                <span className="text-xs">...</span>
                                            ) : (
                                                <Trash2 className="h-4 w-4" />
                                            )}
                                        </Button>
                                    </div>
                                </CardHeader>
                                <CardContent>
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center gap-2">
                                            <Button
                                                variant="outline"
                                                size="icon"
                                                onClick={() => handleUpdateQuantity(item.id, item.quantity - 1)}
                                                disabled={item.quantity <= 1 || updating === item.id}
                                            >
                                                <Minus className="h-4 w-4" />
                                            </Button>
                                            <Input
                                                type="number"
                                                min="1"
                                                max={item.product.stock_quantity}
                                                value={item.quantity}
                                                onChange={(e) => {
                                                    const newQuantity = parseInt(e.target.value) || 1;
                                                    handleUpdateQuantity(item.id, newQuantity);
                                                }}
                                                className="w-20 text-center"
                                                disabled={updating === item.id}
                                            />
                                            <Button
                                                variant="outline"
                                                size="icon"
                                                onClick={() => handleUpdateQuantity(item.id, item.quantity + 1)}
                                                disabled={
                                                    item.quantity >= item.product.stock_quantity || updating === item.id
                                                }
                                            >
                                                <Plus className="h-4 w-4" />
                                            </Button>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-sm text-muted-foreground">{formatPrice(item.product.price)} each</p>
                                            <p className="text-lg font-semibold">
                                                {formatPrice(item.quantity * Number(item.product.price))}
                                            </p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>

                    <div className="lg:col-span-1 space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Order Summary</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">Subtotal</span>
                                    <span>{formatPrice(cart.subtotal || 0)}</span>
                                </div>
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">Tax</span>
                                    <span>{formatPrice(cart.tax || 0)}</span>
                                </div>
                                <div className="border-t pt-4">
                                    <div className="flex justify-between text-lg font-semibold">
                                        <span>Total</span>
                                        <span>{formatPrice(cart.total || 0)}</span>
                                    </div>
                                </div>
                                
                                <form action={checkout.process.url()} method="POST" onSubmit={handleCheckout} className="mt-5">
                                    <input type="hidden" name="_token" value={page.props.csrf_token || ''} />
                                    <Button
                                        type="submit"
                                        className="w-full"
                                        size="lg"
                                        disabled={processing}
                                    >
                                        {processing ? (
                                            <>
                                                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                                Processing...
                                            </>
                                        ) : (
                                            <>
                                                <CreditCard className="mr-2 h-4 w-4" />
                                                Complete Payment
                                            </>
                                        )}
                                    </Button>
                                </form>

                                <p className="text-sm text-muted-foreground text-center">
                                    <Lock className="inline h-3 w-3 mr-1" />
                                    Your payment information is encrypted and secure
                                </p>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

