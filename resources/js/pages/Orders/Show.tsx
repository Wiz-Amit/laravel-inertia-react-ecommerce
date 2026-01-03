import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { formatPrice } from '@/lib/format-price';
import AppLayout from '@/layouts/app-layout';
import orders from '@/routes/orders';
import products from '@/routes/products';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, ShoppingBag } from 'lucide-react';

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
    created_at: string;
    items: OrderItem[];
}

interface Props {
    order: Order;
}

function getStatusBadge(status: string) {
    const statusColors: Record<string, string> = {
        placed: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
        completed: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    };

    return (
        <span
            className={`inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ${
                statusColors[status.toLowerCase()] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
            }`}
        >
            {status.charAt(0).toUpperCase() + status.slice(1)}
        </span>
    );
}

export default function OrdersShow({ order }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Orders',
            href: orders.index.url(),
        },
        {
            title: `Order #${order.id}`,
            href: orders.show.url(order.id),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Order #${order.id}`} />
            <div className="flex h-full flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <div className="flex items-center gap-4 mb-2">
                            <Button asChild variant="ghost" size="sm">
                                <Link href={orders.index.url()}>
                                    <ArrowLeft className="h-4 w-4 mr-2" />
                                    Back to Orders
                                </Link>
                            </Button>
                        </div>
                        <h1 className="text-3xl font-bold tracking-tight">Order #{order.id}</h1>
                        <p className="text-muted-foreground">
                            Placed on {new Date(order.created_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                            })}
                        </p>
                    </div>
                    {getStatusBadge(order.status)}
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="lg:col-span-2 space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Order Items</CardTitle>
                                <CardDescription>Items included in this order</CardDescription>
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
                                                <Link
                                                    href={products.show.url(item.product.id)}
                                                    className="font-medium hover:underline block truncate"
                                                >
                                                    {item.product.name}
                                                </Link>
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
                            </CardContent>
                        </Card>
                    </div>

                    <div className="lg:col-span-1 space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Order Summary</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="space-y-2">
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">Subtotal</span>
                                        <span>{formatPrice(order.subtotal)}</span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">Tax</span>
                                        <span>{formatPrice(order.tax)}</span>
                                    </div>
                                    <div className="border-t pt-4">
                                        <div className="flex justify-between text-lg font-semibold">
                                            <span>Total</span>
                                            <span>{formatPrice(order.total)}</span>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Order Information</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-2 text-sm">
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Order Number</span>
                                    <span className="font-medium">#{order.id}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Status</span>
                                    {getStatusBadge(order.status)}
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Date</span>
                                    <span className="font-medium">
                                        {new Date(order.created_at).toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric',
                                        })}
                                    </span>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

