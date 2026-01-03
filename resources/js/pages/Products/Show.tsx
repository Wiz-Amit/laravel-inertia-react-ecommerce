import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import ProductCard from '@/components/product-card';
import { useCart } from '@/hooks/use-cart';
import { useFlashMessages } from '@/hooks/use-flash-messages';
import AppLayout from '@/layouts/app-layout';
import { formatPrice } from '@/lib/format-price';
import products from '@/routes/products';
import { type BreadcrumbItem, type Product } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { ArrowLeft, ShoppingCart } from 'lucide-react';

interface Props {
    product: Product;
    relatedProducts: Product[];
}

export default function ProductsShow({ product, relatedProducts }: Props) {
    useFlashMessages();
    const { isProductInCart, handleAddToCart, handleGoToCart, addingToCart } = useCart();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Products',
            href: '/products',
        },
        {
            title: product.name,
            href: `/products/${product.id}`,
        },
    ];

    const isInCart = isProductInCart(product.id);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={product.name} />
            <div className="flex h-full flex-1 flex-col gap-6 p-6">
                <Button
                    variant="ghost"
                    onClick={() => router.visit('/products')}
                    className="w-fit"
                >
                    <ArrowLeft className="mr-2 h-4 w-4" />
                    Back to Products
                </Button>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <div className="aspect-square w-full overflow-hidden rounded-lg bg-muted mb-4 flex items-center justify-center">
                                {product.image ? (
                                    <img
                                        src={product.image}
                                        alt={product.name}
                                        className="h-full w-full object-cover"
                                    />
                                ) : (
                                    <ShoppingCart className="h-32 w-32 text-muted-foreground/50" />
                                )}
                            </div>
                        </CardHeader>
                    </Card>

                    <div className="space-y-6">
                        <div>
                            <CardTitle className="text-3xl mb-2">{product.name}</CardTitle>
                            <CardDescription className="text-lg">
                                {product.description || 'Eco-friendly product'}
                            </CardDescription>
                        </div>

                        <Card>
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <span className="text-3xl font-bold">{formatPrice(product.price)}</span>
                                    <span
                                        className={`text-sm font-medium ${
                                            product.stock_quantity > 0
                                                ? 'text-green-600 dark:text-green-400'
                                                : 'text-red-600 dark:text-red-400'
                                        }`}
                                    >
                                        {product.stock_quantity > 0
                                            ? `${product.stock_quantity} in stock`
                                            : 'Out of stock'}
                                    </span>
                                </div>
                            </CardHeader>
                            <CardContent>
                                {isInCart ? (
                                    <Button
                                        className="w-full"
                                        size="lg"
                                        onClick={handleGoToCart}
                                    >
                                        <ShoppingCart className="mr-2 h-4 w-4" />
                                        Go to Cart
                                    </Button>
                                ) : (
                                    <Button
                                        className="w-full"
                                        size="lg"
                                        onClick={() => handleAddToCart(product.id)}
                                        disabled={product.stock_quantity === 0 || addingToCart === product.id}
                                    >
                                        {addingToCart === product.id ? (
                                            'Adding...'
                                        ) : (
                                            <>
                                                <ShoppingCart className="mr-2 h-4 w-4" />
                                                Add to Cart
                                            </>
                                        )}
                                    </Button>
                                )}
                            </CardContent>
                        </Card>

                        {product.description && (
                            <Card>
                                <CardHeader>
                                    <CardTitle>Description</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-muted-foreground">{product.description}</p>
                                </CardContent>
                            </Card>
                        )}
                    </div>
                </div>

                {/* Related Products Section */}
                {relatedProducts && relatedProducts.length > 0 && (
                    <div className="mt-12">
                        <h2 className="text-2xl font-bold tracking-tight mb-6">Related Products</h2>
                        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                            {relatedProducts.map((relatedProduct) => (
                                <ProductCard
                                    key={relatedProduct.id}
                                    product={relatedProduct}
                                    isInCart={isProductInCart(relatedProduct.id)}
                                    onAddToCart={(productId) => handleAddToCart(productId)}
                                    onGoToCart={handleGoToCart}
                                    isAddingToCart={addingToCart === relatedProduct.id}
                                />
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

