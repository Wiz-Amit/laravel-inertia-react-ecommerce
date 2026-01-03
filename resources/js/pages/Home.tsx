import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader } from '@/components/ui/card';
import ProductCard from '@/components/product-card';
import { useCart } from '@/hooks/use-cart';
import { useFlashMessages } from '@/hooks/use-flash-messages';
import AppLayout from '@/layouts/app-layout';
import { type Product } from '@/types';
import { Head, router } from '@inertiajs/react';
import { ArrowRight, Sparkles, Star } from 'lucide-react';

interface Props {
    bestsellers: Product[];
    newArrivals: Product[];
}

export default function Home({ bestsellers, newArrivals }: Props) {
    useFlashMessages();
    const { isProductInCart, handleAddToCart, handleGoToCart, addingToCart } = useCart();

    return (
        <AppLayout>
            <Head title="Home - Homedine" />
            <div className="flex min-h-screen flex-col overflow-x-hidden">
                {/* Hero Section */}
                <section className="relative w-full overflow-hidden -mx-4 md:-mx-8 lg:relative lg:left-1/2 lg:right-1/2 lg:ml-[-50vw] lg:mr-[-50vw] lg:w-screen lg:mx-0">
                    <div className="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950/20 dark:to-emerald-950/20 rounded-b-3xl">
                        <div className="relative mx-auto w-full max-w-7xl px-4 py-6 md:px-8 md:py-8 lg:py-12">
                            <div className="grid gap-8 lg:grid-cols-2 lg:gap-12">
                            <div className="flex flex-col justify-center space-y-6">
                                <h1 className="text-4xl font-bold tracking-tight text-foreground md:text-5xl lg:text-6xl">
                                    Eco-Friendly Kitchenware for a{' '}
                                    <span className="text-green-600 dark:text-green-400">greener</span> home.
                                </h1>
                                <p className="text-lg text-muted-foreground">
                                    Discover sustainable, planet-friendly kitchen essentials crafted with care for
                                    you and the environment.
                                </p>
                                <div className="flex gap-4">
                                    <Button
                                        size="lg"
                                        onClick={() => router.visit('/products')}
                                        className="group"
                                    >
                                        Shop now
                                        <ArrowRight className="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                                    </Button>
                                </div>
                                <div className="mt-4 inline-flex w-fit items-center gap-2 rounded-lg bg-green-100 px-4 py-3 dark:bg-green-900/30">
                                    <span className="text-sm font-medium text-green-800 dark:text-green-200">
                                        Natural. Sustainable. Eco-conscious.
                                    </span>
                                    <span className="text-lg font-bold text-green-600 dark:text-green-400">100%</span>
                                </div>
                            </div>
                            <div className="relative hidden lg:block">
                                <div className="aspect-square overflow-hidden rounded-2xl bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30">
                                    <img
                                        src="https://images.unsplash.com/photo-1556910103-1c02745aae4d?w=800&h=800&fit=crop&q=80"
                                        alt="Eco-friendly kitchenware products"
                                        className="h-full w-full object-cover"
                                        loading="lazy"
                                    />
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Bestsellers Section */}
                <section className="bg-background py-16">
                    <div className="mx-auto max-w-7xl px-4 md:px-8">
                        <div className="mb-8 flex items-center justify-between">
                            <div>
                                <h2 className="text-3xl font-bold tracking-tight">
                                    Eco Essentials Planet-Friendly Bestselling <Sparkles className="inline h-6 w-6" />{' '}
                                    Products
                                </h2>
                            </div>
                            <Button
                                variant="ghost"
                                onClick={() => router.visit('/products')}
                                className="group"
                            >
                                More products
                                <ArrowRight className="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                            </Button>
                        </div>
                        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                            {bestsellers.map((product) => (
                                <ProductCard
                                    key={product.id}
                                    product={product}
                                    isInCart={isProductInCart(product.id)}
                                    onAddToCart={(productId) => handleAddToCart(productId)}
                                    onGoToCart={handleGoToCart}
                                    isAddingToCart={addingToCart === product.id}
                                    badge={{
                                        text: 'Customer favorite',
                                        className: 'rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-200',
                                    }}
                                />
                            ))}
                        </div>
                    </div>
                </section>

                {/* New Arrivals Section */}
                <section className="bg-muted/50 py-16">
                    <div className="mx-auto max-w-7xl px-4 md:px-8">
                        <div className="mb-8">
                            <h2 className="text-3xl font-bold tracking-tight">New Arrival</h2>
                            <p className="mt-2 text-muted-foreground">
                                Discover our latest eco-friendly additions to transform your kitchen.
                            </p>
                        </div>
                        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                            {newArrivals.map((product) => (
                                <ProductCard
                                    key={product.id}
                                    product={product}
                                    isInCart={isProductInCart(product.id)}
                                    onAddToCart={(productId) => handleAddToCart(productId)}
                                    onGoToCart={handleGoToCart}
                                    isAddingToCart={addingToCart === product.id}
                                    badge={{
                                        text: 'New',
                                        className: 'rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-200',
                                    }}
                                />
                            ))}
                        </div>
                    </div>
                </section>

                {/* Customer Reviews Section */}
                <section className="bg-background py-16">
                    <div className="mx-auto max-w-7xl px-4 md:px-8">
                        <div className="mb-8 text-center">
                            <div className="mb-4 flex items-center justify-center gap-2">
                                <Star className="h-6 w-6 fill-yellow-400 text-yellow-400" />
                                <span className="text-3xl font-bold">4.9/5</span>
                            </div>
                            <p className="text-lg text-muted-foreground">
                                More than 25,000 5-Star Reviews for Our Award-Winning Eco Products
                            </p>
                        </div>
                        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                            {[
                                {
                                    quote: "HomeDine's glass jars are awesome for storage, and the bamboo utensils are perfect for daily use!",
                                    author: 'Jane Cooper',
                                    role: 'Nutritionist',
                                },
                                {
                                    quote: 'Fantastic products and fast delivery. My kitchen feels so much greener!',
                                    author: 'Darlene Robertson',
                                    role: 'Culinary Instructor',
                                },
                                {
                                    quote: "Love HomeDine's eco-style! Glass jars keep things fresh, and bamboo utensils are so chic.",
                                    author: 'Jacob Jones',
                                    role: 'Food Blogger',
                                },
                                {
                                    quote: 'The quality is outstanding. These products have transformed my cooking experience.',
                                    author: 'Esther Hosoor',
                                    role: 'Sous Chef',
                                },
                            ].map((review, index) => (
                                <Card key={index}>
                                    <CardHeader>
                                        <div className="mb-2 text-4xl text-muted-foreground">"</div>
                                        <CardDescription className="text-base">{review.quote}</CardDescription>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="font-semibold">{review.author}</div>
                                        <div className="text-sm text-muted-foreground">{review.role}</div>
                                    </CardContent>
                                </Card>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Commitment Statement */}
                <section className="relative w-full overflow-hidden bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-950/20 dark:to-emerald-950/20 py-16 -mx-4 md:-mx-8 lg:relative lg:left-1/2 lg:right-1/2 lg:ml-[-50vw] lg:mr-[-50vw] lg:w-screen lg:mx-0">
                    <div className="relative mx-auto w-full max-w-7xl px-4 md:px-8">
                        <div className="grid gap-8 lg:grid-cols-2">
                            <div className="relative overflow-hidden rounded-2xl bg-muted">
                                <img
                                    src="https://images.unsplash.com/photo-1556911220-bff31c812dba?w=800&h=600&fit=crop&q=80"
                                    alt="Eco-friendly kitchenware commitment"
                                    className="h-full w-full object-cover min-h-[300px]"
                                    loading="lazy"
                                />
                            </div>
                            <div className="flex flex-col justify-center space-y-4">
                                <h2 className="text-3xl font-bold tracking-tight">Our Commitment</h2>
                                <p className="text-lg text-muted-foreground">
                                    Discover our commitment to sustainable materials, low-impact production, and
                                    ethical sourcing partnerships – all crafted to support a healthier planet and a
                                    greener kitchen.
                                </p>
                                <Button
                                    variant="outline"
                                    size="lg"
                                    onClick={() => router.visit('/products')}
                                    className="w-fit"
                                >
                                    Explore Products
                                    <ArrowRight className="ml-2 h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </AppLayout>
    );
}

