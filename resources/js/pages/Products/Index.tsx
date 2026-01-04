import { Button } from '@/components/ui/button';
import ProductCard from '@/components/product-card';
import { useCart } from '@/hooks/use-cart';
import { useFlashMessages } from '@/hooks/use-flash-messages';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type Paginated, type Product } from '@/types';
import { Head, router } from '@inertiajs/react';
import { ShoppingCart } from 'lucide-react';

interface Props {
    products: Paginated<Product>;
    search?: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
];

export default function ProductsIndex({ products, search }: Props) {
    useFlashMessages();
    const { isProductInCart, handleAddToCart, handleGoToCart, addingToCart } = useCart();

    if (!products || !products.data) {
        return (
            <AppLayout breadcrumbs={breadcrumbs}>
                <Head title="Products" />
                <div className="flex h-full flex-1 flex-col gap-6 p-6">
                    <div className="flex flex-col items-center justify-center gap-4 py-12">
                        <ShoppingCart className="h-16 w-16 text-muted-foreground/50" />
                        <div className="text-center">
                            <h2 className="text-2xl font-bold">No products available</h2>
                            <p className="text-muted-foreground">Check back later for new products!</p>
                        </div>
                    </div>
                </div>
            </AppLayout>
        );
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Products" />
            <div className="flex h-full flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Products</h1>
                        <p className="text-muted-foreground">Browse our collection of eco-friendly products</p>
                    </div>
                </div>

                {products.data && products.data.length > 0 ? (
                    <>
                        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            {products.data.map((product) => (
                                <ProductCard
                                    key={product.id}
                                    product={product}
                                    isInCart={isProductInCart(product.id)}
                                    onAddToCart={(productId) => handleAddToCart(productId)}
                                    onGoToCart={handleGoToCart}
                                    isAddingToCart={addingToCart === product.id}
                                />
                            ))}
                        </div>

                        {products.last_page > 1 && (
                            <div className="flex items-center justify-center gap-2 mt-6 border-t pt-6">
                                <Button
                                    variant="outline"
                                    disabled={products.current_page === 1}
                                    onClick={() => {
                                        const params = new URLSearchParams();
                                        if (search) params.set('search', search);
                                        params.set('page', String(products.current_page - 1));
                                        router.get(`/products?${params.toString()}`);
                                    }}
                                >
                                    Previous
                                </Button>
                                <span className="text-sm text-muted-foreground px-4">
                                    Page {products.current_page} of {products.last_page}
                                </span>
                                <Button
                                    variant="outline"
                                    disabled={products.current_page === products.last_page}
                                    onClick={() => {
                                        const params = new URLSearchParams();
                                        if (search) params.set('search', search);
                                        params.set('page', String(products.current_page + 1));
                                        router.get(`/products?${params.toString()}`);
                                    }}
                                >
                                    Next
                                </Button>
                            </div>
                        )}
                    </>
                ) : (
                    <div className="flex flex-col items-center justify-center gap-4 py-12">
                        <ShoppingCart className="h-16 w-16 text-muted-foreground/50" />
                        <div className="text-center">
                            <h2 className="text-2xl font-bold">No products available</h2>
                            <p className="text-muted-foreground">Check back later for new products!</p>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

