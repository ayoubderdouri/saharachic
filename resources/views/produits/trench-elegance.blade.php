@php
    $brand = 'Sahara Chic';
    $title = 'ترانش أناقة – معطف نسائي عصري بـ ' . $price . ' درهم والتوصيل مجاني | ' . $brand;
    $description =
        'ترانش أناقة: معطف نسائي بياقة مرتفعة، أزرار أمامية مزدوجة وحزام قابل للتعديل. 5 ألوان وعدة مقاسات، بثمن ' .
        $price .
        ' درهم فقط مع التوصيل المجاني والدفع عند الاستلام.';
    $url = route('produits.show', $product);
    $ogImage = $cover ?? asset('produits/manteau/beige.jpg');
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'alternateName' => $product->meta['name_fr'] ?? null,
        'description' => $description,
        'image' => $colors->pluck('image')->filter()->values()->all(),
        'brand' => ['@type' => 'Brand', 'name' => $brand],
        'sku' => 'TRE',
        'offers' => [
            '@type' => 'Offer',
            'url' => $url,
            'priceCurrency' => 'MAD',
            'price' => $price,
            'availability' => $inStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
            'shippingDetails' => [
                '@type' => 'OfferShippingDetails',
                'shippingRate' => ['@type' => 'MonetaryAmount', 'value' => 0, 'currency' => 'MAD'],
                'shippingDestination' => ['@type' => 'DefinedRegion', 'addressCountry' => 'MA'],
            ],
        ],
    ];
    $features = $product->meta['features'] ?? [];
    $faqs = [
        ['كيف يتم الدفع؟', 'الدفع عند الاستلام: تتوصلين بالمعطف وتؤدّين ثمنه لعامل التوصيل.'],
        ['هل التوصيل مجاني؟', 'نعم، ثمن ' . $price . ' درهم يشمل التوصيل إلى جميع مدن المغرب.'],
        ['ماذا لو لم يناسبني المقاس؟', 'تواصلي معنا بعد الاستلام وسنساعدك في تبديل المقاس حسب التوفر.'],
        ['كم تستغرق مدة التوصيل؟', 'يتم التوصيل عادة خلال 24 إلى 72 ساعة حسب المدينة.'],
    ];
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-L80YWZ4VK6"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-L80YWZ4VK6');
    </script>
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="theme-color" content="#5B3828">
    <link rel="canonical" href="{{ $url }}">

    <meta property="og:type" content="product">
    <meta property="og:site_name" content="{{ $brand }}">
    <meta property="og:locale" content="ar_MA">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $url }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="768">
    <meta property="og:image:height" content="1018">
    <meta property="og:image:alt" content="{{ $product->name }}">
    <meta property="product:price:amount" content="{{ $price }}">
    <meta property="product:price:currency" content="MAD">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($faqs)->map(fn ($faq) => [
            '@type' => 'Question',
            'name' => $faq[0],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq[1]],
        ])->all(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=tajawal:400,500,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Tajawal', ui-sans-serif, system-ui, sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-stone-50 text-stone-900 antialiased" x-data="landing(@js(['colors' => $colors, 'sizes' => $sizes, 'stock' => $stock, 'oldColor' => (int) old('color_id'), 'oldSize' => (int) old('size_id')]))">

    {{-- Barre supérieure --}}
    <div class="bg-[#5B3828] text-white text-center text-sm py-2 px-4">
        <svg class="inline-block h-5 w-5 align-text-bottom me-1" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
        </svg>
        التوصيل مجاني لجميع مدن المغرب · الدفع عند الاستلام
    </div>

    <header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-stone-200">
        <div class="max-w-6xl mx-auto flex items-center justify-between px-4 h-14">
            <span class="text-lg font-extrabold tracking-wide text-[#5B3828]">{{ $brand }}</span>
            <a href="#commander"
                class="rounded-full bg-[#5B3828] text-white text-sm font-bold px-5 py-2 hover:bg-[#46291c] transition">اطلبي
                الآن</a>
        </div>
    </header>

    <main>
        @if (session('ordered'))
            <div class="max-w-2xl mx-auto mt-6 px-4" role="status">
                <div class="rounded-2xl bg-green-50 border border-green-300 text-green-900 p-5 text-center">
                    <p class="text-xl font-extrabold inline-flex items-center gap-2">
                        <svg class="h-7 w-7 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        تم تسجيل طلبك بنجاح
                    </p>
                    <p class="mt-1">شكراً لثقتك! سنتصل بك قريباً على رقم هاتفك لتأكيد الطلب.</p>
                </div>
            </div>
        @endif

        {{-- Hero --}}
        <section class="max-w-6xl mx-auto px-4 py-8 lg:py-14 grid gap-8 lg:grid-cols-2 lg:gap-14 items-start">
            <div>
                <div class="relative overflow-hidden rounded-3xl bg-stone-100 shadow-lg aspect-[768/1018]">
                    @foreach ($colors as $color)
                        <img src="{{ $color['image'] }}" alt="{{ $product->name }} – {{ $color['name'] }}"
                            width="768" height="1018"
                            @if (!$loop->first) loading="lazy" x-cloak @endif
                            x-show="currentColor && currentColor.id === {{ $color['id'] }}"
                            class="absolute inset-0 h-full w-full object-cover">
                    @endforeach
                    <span
                        class="absolute top-4 start-4 rounded-full bg-white/95 text-[#5B3828] text-sm font-extrabold px-4 py-1.5 shadow">توصيل
                        مجاني</span>
                </div>

                <div class="mt-4 grid grid-cols-5 gap-2 sm:gap-3">
                    @foreach ($colors as $color)
                        <button type="button" @click="selectColor({{ $color['id'] }})"
                            :class="currentColor && currentColor.id === {{ $color['id'] }} ?
                                'ring-2 ring-[#5B3828] ring-offset-2' : 'opacity-80 hover:opacity-100'"
                            class="overflow-hidden rounded-xl aspect-square transition"
                            aria-label="{{ $color['name'] }}">
                            <img src="{{ $color['image'] }}" alt="" loading="lazy" width="150"
                                height="150" class="h-full w-full object-cover">
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="lg:sticky lg:top-24">
                <p class="text-sm font-bold text-[#5B3828]">مجموعة الخريف والشتاء</p>
                <h1 class="mt-1 text-3xl sm:text-4xl font-extrabold leading-tight">{{ $product->name }}</h1>
                <p class="mt-1 text-stone-500" dir="ltr" style="text-align: start;">
                    {{ $product->meta['name_fr'] ?? '' }}</p>

                <div class="mt-5 flex flex-wrap items-end gap-3">
                    <span class="text-5xl font-extrabold text-[#5B3828]">{{ $price }} <span
                            class="text-2xl">درهم</span></span>
                    <span class="mb-2 rounded-full bg-green-100 text-green-800 text-sm font-bold px-3 py-1">التوصيل
                        مشمول في الثمن</span>
                </div>

                <p class="mt-5 leading-8 text-stone-700">{{ $product->description }}</p>

                <ul class="mt-5 grid sm:grid-cols-2 gap-2">
                    @foreach ($features as $feature)
                        <li
                            class="flex items-center gap-2 rounded-xl bg-white border border-stone-200 px-3 py-2 text-sm font-medium">
                            <svg class="h-5 w-5 shrink-0 text-[#5B3828]" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>{{ $feature }}
                        </li>
                    @endforeach
                </ul>

                <a href="#commander"
                    class="mt-7 block text-center rounded-2xl bg-[#5B3828] text-white text-lg font-extrabold py-4 shadow-lg hover:bg-[#46291c] transition">
                    اطلبي الآن والدفع عند الاستلام
                </a>
            </div>
        </section>

        {{-- Avantages --}}
        <section class="bg-white border-y border-stone-200">
            <div class="max-w-6xl mx-auto px-4 py-10 grid grid-cols-2 lg:grid-cols-4 gap-4 text-center">
                @foreach ([
        ['M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12', 'توصيل مجاني', 'لجميع مدن المغرب'],
        ['M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z', 'الدفع عند الاستلام', 'تدفعين بعد المعاينة'],
        ['M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z', 'جودة عالية', 'قماش أنيق ومريح'],
        ['M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z', 'دعم سريع', 'نتصل بك لتأكيد الطلب'],
    ] as [$icon, $label, $text])
                    <div class="rounded-2xl bg-stone-50 p-5">
                        <svg class="mx-auto h-9 w-9 text-[#5B3828]" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                        </svg>
                        <p class="mt-2 font-extrabold">{{ $label }}</p>
                        <p class="text-sm text-stone-600 mt-1">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Formulaire de commande --}}
        <section id="commander" class="max-w-6xl mx-auto px-4 py-12 scroll-mt-20">
            <div class="rounded-3xl bg-white border border-stone-200 shadow-xl p-5 sm:p-8">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-center">اطلبي معطفك الآن</h2>
                <p class="mt-2 text-center text-stone-600">املئي المعلومات التالية وسنتصل بك لتأكيد الطلب</p>

                @if (!$inStock)
                    <p class="mt-6 rounded-xl bg-red-50 text-red-800 text-center p-4 font-bold">نعتذر، المنتج غير متوفر
                        حالياً.</p>
                @else
                    @if ($errors->any())
                        <div class="mt-6 rounded-xl bg-red-50 border border-red-200 text-red-800 p-4 text-sm"
                            role="alert">
                            <ul class="list-disc ps-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('produits.order', $product) }}" class="mt-6 space-y-6"
                        @submit="submitting = true">
                        @csrf
                        <input type="hidden" name="variant_id" :value="variantId">
                        <input type="hidden" name="color_id" :value="colorId">
                        <input type="hidden" name="size_id" :value="sizeId">

                        <fieldset>
                            <legend class="font-bold mb-3">اللون: <span class="text-[#5B3828]"
                                    x-text="currentColor ? currentColor.name : ''"></span></legend>
                            <div class="flex flex-wrap gap-3">
                                @foreach ($colors as $color)
                                    <button type="button" @click="selectColor({{ $color['id'] }})"
                                        :class="colorId === {{ $color['id'] }} ? 'ring-2 ring-offset-2 ring-[#5B3828]' :
                                            'ring-1 ring-stone-300'"
                                        class="h-11 w-11 rounded-full transition"
                                        style="background: {{ $color['hex'] }}"
                                        aria-label="{{ $color['name'] }}"></button>
                                @endforeach
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend class="font-bold mb-3">المقاس</legend>
                            <div class="flex flex-wrap gap-3">
                                @foreach ($sizes as $size)
                                    <button type="button" @click="sizeId = {{ $size['id'] }}"
                                        :disabled="!available({{ $size['id'] }})"
                                        :class="sizeId === {{ $size['id'] }} ? 'bg-[#5B3828] text-white border-[#5B3828]' :
                                            'bg-white border-stone-300'"
                                        class="min-w-14 rounded-xl border px-4 py-2.5 font-bold transition disabled:opacity-30 disabled:line-through disabled:cursor-not-allowed"
                                        dir="ltr">{{ $size['name'] }}</button>
                                @endforeach
                            </div>
                        </fieldset>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label for="customer_name" class="block font-bold mb-1.5">الاسم الكامل</label>
                                <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
                                    required autocomplete="name"
                                    class="w-full rounded-xl border-stone-300 focus:border-[#5B3828] focus:ring-[#5B3828] py-3">
                            </div>
                            <div>
                                <label for="phone" class="block font-bold mb-1.5">رقم الهاتف</label>
                                <input id="phone" name="phone" value="{{ old('phone') }}" required
                                    type="tel" inputmode="tel" dir="ltr" placeholder="06XXXXXXXX"
                                    autocomplete="tel"
                                    class="w-full rounded-xl border-stone-300 focus:border-[#5B3828] focus:ring-[#5B3828] py-3 text-end">
                            </div>
                            <div>
                                <label for="city" class="block font-bold mb-1.5">المدينة</label>
                                <input id="city" name="city" value="{{ old('city') }}" required
                                    autocomplete="address-level2"
                                    class="w-full rounded-xl border-stone-300 focus:border-[#5B3828] focus:ring-[#5B3828] py-3">
                            </div>
                            <div>
                                <label for="quantity" class="block font-bold mb-1.5">الكمية</label>
                                <select id="quantity" name="quantity" x-model.number="quantity"
                                    class="w-full rounded-xl border-stone-300 focus:border-[#5B3828] focus:ring-[#5B3828] py-3">
                                    @foreach (range(1, 5) as $qty)
                                        <option value="{{ $qty }}" @selected((int) old('quantity', 1) === $qty)>
                                            {{ $qty }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="address" class="block font-bold mb-1.5">العنوان الكامل</label>
                                <textarea id="address" name="address" rows="3" required autocomplete="street-address"
                                    class="w-full rounded-xl border-stone-300 focus:border-[#5B3828] focus:ring-[#5B3828]">{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-stone-50 border border-stone-200 p-4 space-y-1 text-sm">
                            <div class="flex justify-between"><span>ثمن المعطف</span><span
                                    class="font-bold">{{ $price }} درهم</span></div>
                            <div class="flex justify-between"><span>التوصيل</span><span
                                    class="font-bold text-green-700">مجاني</span></div>
                            <div
                                class="flex justify-between border-t border-stone-200 pt-2 mt-2 text-lg font-extrabold">
                                <span>المجموع</span>
                                <span><span x-text="{{ $price }} * quantity"></span> درهم</span>
                            </div>
                        </div>

                        <button type="submit" :disabled="!variantId || submitting"
                            class="w-full rounded-2xl bg-[#5B3828] text-white text-lg font-extrabold py-4 shadow-lg hover:bg-[#46291c] transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="variantId">تأكيد الطلب · الدفع عند الاستلام</span>
                            <span x-show="! variantId" x-cloak>اختاري اللون والمقاس المتوفرين</span>
                        </button>
                    </form>
                @endif
            </div>
        </section>

        {{-- FAQ --}}
        <section class="max-w-6xl mx-auto px-4 pb-16">
            <h2 class="text-2xl font-extrabold text-center mb-6">أسئلة شائعة</h2>
            <div class="space-y-3">
                @foreach ($faqs as [$question, $answer])
                    <details class="group rounded-2xl bg-white border border-stone-200 p-4">
                        <summary class="cursor-pointer font-bold flex items-center justify-between gap-3 list-none">
                            {{ $question }}
                            <span class="text-[#5B3828] transition group-open:rotate-45 text-xl">+</span>
                        </summary>
                        <p class="mt-3 text-stone-700 leading-7">{{ $answer }}</p>
                    </details>
                @endforeach
            </div>
        </section>
    </main>

    <footer class="bg-[#2b1a12] text-stone-300 text-center text-sm py-8 px-4 pb-24 sm:pb-8">
        <p class="font-extrabold text-white text-lg">{{ $brand }}</p>
        <p class="mt-1">© {{ date('Y') }} {{ $brand }} · جميع الحقوق محفوظة</p>
    </footer>

    @if ($product->whatsapp_url)
        <a href="{{ $product->whatsapp_url }}" target="_blank" rel="noopener noreferrer"
            aria-label="تواصلي معنا عبر واتساب"
            class="fixed bottom-24 left-4 sm:bottom-4 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-green-600 text-white shadow-lg transition hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-8 w-8" aria-hidden="true">
                <path d="M20.52 3.48A11.9 11.9 0 0 0 12.02 0C5.4 0 .02 5.38.02 12c0 2.12.55 4.19 1.6 6.01L0 24l6.13-1.6A11.95 11.95 0 0 0 12.02 24h.01C18.64 24 24 18.62 24 12c0-3.21-1.25-6.23-3.48-8.52ZM12.02 21.97a9.93 9.93 0 0 1-5.06-1.38l-.36-.21-3.64.95.97-3.55-.23-.37A9.93 9.93 0 0 1 2.05 12c0-5.5 4.47-9.97 9.98-9.97 2.66 0 5.16 1.04 7.04 2.92A9.9 9.9 0 0 1 21.97 12c0 5.5-4.46 9.97-9.95 9.97Zm5.47-7.45c-.3-.15-1.77-.87-2.04-.97-.28-.1-.48-.15-.68.15-.2.3-.77.97-.95 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.39-1.47-.88-.79-1.48-1.76-1.65-2.06-.18-.3-.02-.46.13-.61.13-.13.3-.35.45-.53.15-.18.2-.3.3-.5.1-.2.05-.38-.03-.53-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.68-.51h-.57c-.2 0-.53.07-.8.37-.27.3-1.05 1.02-1.05 2.49s1.08 2.9 1.23 3.1c.15.2 2.12 3.24 5.13 4.55.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.18-1.42-.08-.13-.28-.2-.58-.35Z" />
            </svg>
        </a>
    @endif

    {{-- CTA mobile fixe --}}
    <div
        class="sm:hidden fixed bottom-0 inset-x-0 z-30 bg-white border-t border-stone-200 p-3 flex items-center gap-3">
        <div class="font-extrabold text-[#5B3828] leading-tight">{{ $price }} درهم<br><span
                class="text-xs font-medium text-green-700">التوصيل مجاني</span></div>
        <a href="#commander" class="flex-1 text-center rounded-xl bg-[#5B3828] text-white font-extrabold py-3">اطلبي
            الآن</a>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('landing', (config) => ({
                colors: config.colors,
                stock: config.stock,
                colorId: config.oldColor || (config.colors[0] ? config.colors[0].id : null),
                sizeId: config.oldSize || null,
                quantity: {{ (int) old('quantity', 1) }},
                submitting: false,
                init() {
                    if (this.sizeId && !this.available(this.sizeId)) this.sizeId = null;
                },
                get currentColor() {
                    return this.colors.find((color) => color.id === this.colorId) || null;
                },
                get variantId() {
                    return this.stock[this.colorId + '-' + this.sizeId] || null;
                },
                available(sizeId) {
                    return !!this.stock[this.colorId + '-' + sizeId];
                },
                selectColor(id) {
                    this.colorId = id;
                    if (this.sizeId && !this.available(this.sizeId)) this.sizeId = null;
                },
            }));
        });
    </script>
</body>

</html>
