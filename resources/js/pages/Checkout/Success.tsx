import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { formatPrice } from '@/lib/format-price';
import AppLayout from '@/layouts/app-layout';
import orders from '@/routes/orders';
import products from '@/routes/products';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { CheckCircle2, ShoppingBag } from 'lucide-react';
import { useEffect, useState } from 'react';
import Confetti from 'react-confetti';

interface OrderItem {
    id: number;
    product: {
        id: number;
        name: string;
        image: string | null;
    };
    quantity: number;
    price: string;
}

interface Order {
    id: number;
    subtotal: string;
    tax: string;
    total: string;
    status: string;
    items: OrderItem[];
}

interface Props {
    order: Order;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Cart',
        href: '/cart',
    },
    {
        title: 'Success',
        href: '/checkout/success',
    },
];

export default function CheckoutSuccess({ order }: Props) {
    const [showConfetti, setShowConfetti] = useState(true);
    const [windowSize, setWindowSize] = useState({ width: 0, height: 0 });

    useEffect(() => {
        // Set window size for confetti
        const updateSize = () => {
            setWindowSize({ width: window.innerWidth, height: window.innerHeight });
        };
        updateSize();
        window.addEventListener('resize', updateSize);
        
        // Hide confetti after 5 seconds
        const timer = setTimeout(() => {
            setShowConfetti(false);
        }, 5000);
        
        return () => {
            window.removeEventListener('resize', updateSize);
            clearTimeout(timer);
        };
    }, []);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Order Success" />
            {showConfetti && windowSize.width > 0 && (
                <Confetti
                    width={windowSize.width}
                    height={windowSize.height}
                    recycle={false}
                    numberOfPieces={200}
                    gravity={0.3}
                />
            )}
            <div className="flex h-full flex-1 flex-col items-center justify-center gap-6 p-6">
                <div className="text-center space-y-4 max-w-2xl">
                    <div className="flex justify-center">
                        <div className="rounded-full bg-green-100 dark:bg-green-900/20 p-4">
                            <CheckCircle2 className="h-16 w-16 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <div>
                        <h1 className="text-4xl font-bold tracking-tight">Order Placed Successfully!</h1>
                        <p className="text-muted-foreground mt-2">
                            Thank you for your purchase. Your order has been confirmed.
                        </p>
                    </div>

                    <Card className="mt-8">
                        <CardHeader>
                            <CardTitle>Items Purchased</CardTitle>
                            <CardDescription>Order #{order.id}</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {order.items.map((item) => (
                                <div key={item.id} className="flex items-center justify-between border-b pb-4 last:border-0 last:pb-0">
                                    <div className="flex items-center gap-4 flex-1">
                                        <div className="h-16 w-16 overflow-hidden rounded-lg bg-muted flex items-center justify-center flex-shrink-0">
                                            {item.product.image ? (
                                                <img
                                                    src={item.product.image}
                                                    alt={item.product.name}
                                                    className="h-full w-full object-cover"
                                                />
                                            ) : (
                                                <ShoppingBag className="h-6 w-6 text-muted-foreground/50" />
                                            )}
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <p className="font-medium block truncate">
                                                {item.product.name}
                                            </p>
                                            <p className="text-sm text-muted-foreground">
                                                Quantity: {item.quantity} × {formatPrice(item.price)}
                                            </p>
                                        </div>
                                    </div>
                                    <p className="font-semibold ml-4">
                                        {formatPrice(Number(item.price) * item.quantity)}
                                    </p>
                                </div>
                            ))}
                            <div className="pt-4 border-t">
                                <div className="flex justify-between text-lg font-semibold">
                                    <span>Total</span>
                                    <span>{formatPrice(order.total)}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <div className="flex gap-4 justify-center mt-6">
                        <Button onClick={() => router.visit(products.index.url())} variant="outline">
                            Continue Shopping
                        </Button>
                        <Button onClick={() => router.visit(orders.index.url())}>
                            Order History
                        </Button>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

