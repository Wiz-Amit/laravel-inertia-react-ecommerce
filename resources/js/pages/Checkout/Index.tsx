import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import checkout from '@/routes/checkout';
import { type BreadcrumbItem, type Cart } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { CreditCard, Loader2, Lock } from 'lucide-react';
import { useEffect, useState } from 'react';
import { toast } from 'sonner';

interface Props {
    cart: Cart | null;
    stripeKey: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Cart',
        href: '/cart',
    },
    {
        title: 'Checkout',
        href: '/checkout',
    },
];

export default function CheckoutIndex({ cart }: Props) {
    const [processing, setProcessing] = useState(false);
    const page = usePage<{ flash?: { success?: string; error?: string } }>();

    useEffect(() => {
        const flash = page.props.flash;
        if (flash?.error) {
            toast.error(flash.error);
        }
        if (flash?.success) {
            toast.success(flash.success);
        }
    }, [page.props]);

    if (!cart || cart.items.length === 0) {
        return (
            <AppLayout breadcrumbs={breadcrumbs}>
                <Head title="Checkout" />
                <div className="flex h-full flex-1 flex-col items-center justify-center gap-4 p-6">
                    <p className="text-muted-foreground">Your cart is empty.</p>
                    <Button onClick={() => router.visit('/products')}>Browse Products</Button>
                </div>
            </AppLayout>
        );
    }

    const handleCheckout = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        setProcessing(true);
        
        // Use a regular form submission to avoid CORS issues with Stripe Checkout
        // This ensures a full page redirect (not an AJAX request)
        const form = e.currentTarget;
        form.submit();
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Checkout" />
            <div className="flex h-full flex-1 flex-col gap-6 p-6">
                <div>
                    <h1 className="text-3xl font-bold tracking-tight">Checkout</h1>
                    <p className="text-muted-foreground">Review your order and complete your purchase</p>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="lg:col-span-2 space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Order Summary</CardTitle>
                                <CardDescription>Review the items in your order</CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                {cart.items.map((item) => (
                                    <div key={item.id} className="flex items-center justify-between border-b pb-4">
                                        <div className="flex items-center gap-4">
                                            <div className="h-16 w-16 overflow-hidden rounded-lg bg-muted flex items-center justify-center">
                                                {item.product.image ? (
                                                    <img
                                                        src={item.product.image}
                                                        alt={item.product.name}
                                                        className="h-full w-full object-cover"
                                                    />
                                                ) : (
                                                    <CreditCard className="h-6 w-6 text-muted-foreground/50" />
                                                )}
                                            </div>
                                            <div>
                                                <p className="font-medium">{item.product.name}</p>
                                                <p className="text-sm text-muted-foreground">
                                                    Quantity: {item.quantity} × ${Number(item.product.price).toFixed(2)}
                                                </p>
                                            </div>
                                        </div>
                                        <p className="font-semibold">
                                            ${(item.quantity * Number(item.product.price)).toFixed(2)}
                                        </p>
                                    </div>
                                ))}
                            </CardContent>
                        </Card>
                    </div>

                    <div className="lg:col-span-1 space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Order Total</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">Subtotal</span>
                                    <span>${Number(cart.subtotal || 0).toFixed(2)}</span>
                                </div>
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">Tax</span>
                                    <span>${Number(cart.tax || 0).toFixed(2)}</span>
                                </div>
                                <div className="border-t pt-4">
                                    <div className="flex justify-between text-lg font-semibold">
                                        <span>Total</span>
                                        <span>${Number(cart.total || 0).toFixed(2)}</span>
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

