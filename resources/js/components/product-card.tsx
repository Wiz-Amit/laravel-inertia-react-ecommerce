import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { formatPrice } from '@/lib/format-price';
import { type Product } from '@/types';
import { Link } from '@inertiajs/react';
import { ShoppingCart } from 'lucide-react';

interface ProductCardProps {
    product: Product;
    isInCart: boolean;
    onAddToCart: (productId: number) => void;
    onGoToCart: () => void;
    isAddingToCart: boolean;
    badge?: {
        text: string;
        className: string;
    };
}

export default function ProductCard({
    product,
    isInCart,
    onAddToCart,
    onGoToCart,
    isAddingToCart,
    badge,
}: ProductCardProps) {
    const handleButtonClick = (e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();
    };

    return (
        <Link href={`/products/${product.id}`} className="block">
            <Card className="flex flex-col cursor-pointer transition-all duration-200 hover:shadow-md hover:scale-[1.02]">
                <CardHeader>
                    <div className="aspect-square w-full overflow-hidden rounded-lg bg-muted mb-4 flex items-center justify-center">
                        {product.image ? (
                            <img
                                src={product.image}
                                alt={product.name}
                                className="h-full w-full object-cover"
                                loading="lazy"
                            />
                        ) : (
                            <ShoppingCart className="h-16 w-16 text-muted-foreground/50" />
                        )}
                    </div>
                    {badge && (
                        <div className="mb-2">
                            <span className={badge.className}>{badge.text}</span>
                        </div>
                    )}
                    <CardTitle className="line-clamp-2">{product.name}</CardTitle>
                    <CardDescription className="line-clamp-2">
                        {product.description || 'Eco-friendly product'}
                    </CardDescription>
                </CardHeader>
                <CardContent className="flex-1"></CardContent>
                <CardFooter className="flex flex-col items-start gap-3">
                    <div className="flex w-full items-center justify-between">
                        <span className="text-2xl font-bold">{formatPrice(product.price)}</span>
                        {product.stock_quantity > 0 && product.stock_quantity < 10 && (
                            <span className="text-sm text-orange-600 dark:text-orange-400">
                                Only {product.stock_quantity} left
                            </span>
                        )}
                    </div>
                    {isInCart ? (
                        <Button
                            className="w-full"
                            onClick={(e) => {
                                handleButtonClick(e);
                                onGoToCart();
                            }}
                        >
                            <ShoppingCart className="mr-2 h-4 w-4" />
                            Go to Cart
                        </Button>
                    ) : (
                        <Button
                            className="w-full"
                            onClick={(e) => {
                                handleButtonClick(e);
                                onAddToCart(product.id);
                            }}
                            disabled={product.stock_quantity === 0 || isAddingToCart}
                        >
                            {isAddingToCart ? (
                                'Adding...'
                            ) : (
                                <>
                                    <ShoppingCart className="mr-2 h-4 w-4" />
                                    Add to Cart
                                </>
                            )}
                        </Button>
                    )}
                </CardFooter>
            </Card>
        </Link>
    );
}

