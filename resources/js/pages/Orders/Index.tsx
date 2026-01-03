import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { formatPrice } from '@/lib/format-price';
import AppLayout from '@/layouts/app-layout';
import orders from '@/routes/orders';
import products from '@/routes/products';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Package, ShoppingBag } from 'lucide-react';

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
    orders: Order[];
    pagination: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number | null;
        to: number | null;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Orders',
        href: orders.index.url(),
    },
];

function getStatusBadge(status: string) {
    const statusColors: Record<string, string> = {
        placed: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
        completed: 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    };

    return (
        <span
            className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                statusColors[status.toLowerCase()] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
            }`}
        >
            {status.charAt(0).toUpperCase() + status.slice(1)}
        </span>
    );
}

export default function OrdersIndex({ orders: ordersData, pagination }: Props) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Order History" />
            <div className="flex h-full flex-1 flex-col gap-6 p-6">
                <div>
                    <h1 className="text-3xl font-bold tracking-tight">Order History</h1>
                    <p className="text-muted-foreground">View all your past orders</p>
                </div>

                {ordersData.length === 0 ? (
                    <Card>
                        <CardContent className="flex flex-col items-center justify-center py-12">
                            <Package className="h-12 w-12 text-muted-foreground/50 mb-4" />
                            <p className="text-muted-foreground mb-4">You haven't placed any orders yet.</p>
                            <Button asChild>
                                <Link href={products.index.url()}>Browse Products</Link>
                            </Button>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="space-y-4">
                        {ordersData.map((order) => (
                            <Card key={order.id} className="hover:shadow-md transition-shadow">
                                <CardHeader>
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <CardTitle className="flex items-center gap-2">
                                                Order #{order.id}
                                            </CardTitle>
                                            <CardDescription>
                                                Placed on {new Date(order.created_at).toLocaleDateString('en-US', {
                                                    year: 'numeric',
                                                    month: 'long',
                                                    day: 'numeric',
                                                })}
                                            </CardDescription>
                                        </div>
                                        {getStatusBadge(order.status)}
                                    </div>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        <div className="space-y-2">
                                            {(order.items || []).slice(0, 3).map((item) => (
                                                <div key={item.id} className="flex items-center gap-3 text-sm">
                                                    <div className="h-10 w-10 overflow-hidden rounded-lg bg-muted flex items-center justify-center flex-shrink-0">
                                                        {item.product?.image ? (
                                                            <img
                                                                src={item.product.image}
                                                                alt={item.product.name}
                                                                className="h-full w-full object-cover"
                                                            />
                                                        ) : (
                                                            <ShoppingBag className="h-5 w-5 text-muted-foreground/50" />
                                                        )}
                                                    </div>
                                                    <div className="flex-1 min-w-0">
                                                        <p className="font-medium truncate">{item.product?.name || 'Unknown Product'}</p>
                                                        <p className="text-muted-foreground">
                                                            Quantity: {item.quantity} × {formatPrice(item.price)}
                                                        </p>
                                                    </div>
                                                </div>
                                            ))}
                                            {(order.items || []).length > 3 && (
                                                <p className="text-sm text-muted-foreground pt-2">
                                                    +{(order.items || []).length - 3} more item{(order.items || []).length - 3 > 1 ? 's' : ''}
                                                </p>
                                            )}
                                        </div>
                                        <div className="border-t pt-4 flex items-center justify-between">
                                            <div>
                                                <p className="text-sm text-muted-foreground">Total</p>
                                                <p className="text-lg font-semibold">{formatPrice(order.total)}</p>
                                            </div>
                                            <Button asChild variant="outline">
                                                <Link href={orders.show.url(order.id)}>View Details</Link>
                                            </Button>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}

                        {pagination.last_page > 1 && (
                            <div className="flex items-center justify-between pt-4">
                                <p className="text-sm text-muted-foreground">
                                    Showing {pagination.from} to {pagination.to} of {pagination.total} orders
                                </p>
                                <div className="flex gap-2">
                                    {pagination.current_page > 1 && (
                                        <Button
                                            asChild
                                            variant="outline"
                                        >
                                            <Link
                                                href={orders.index.url({ query: { page: pagination.current_page - 1 } })}
                                                preserveState
                                            >
                                                Previous
                                            </Link>
                                        </Button>
                                    )}
                                    {pagination.current_page < pagination.last_page && (
                                        <Button
                                            asChild
                                            variant="outline"
                                        >
                                            <Link
                                                href={orders.index.url({ query: { page: pagination.current_page + 1 } })}
                                                preserveState
                                            >
                                                Next
                                            </Link>
                                        </Button>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

