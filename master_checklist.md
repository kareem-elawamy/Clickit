# ClickIt Audit — Master Fix Checklist

---

## 🔒 Security

- [x] **SEC-1:** Replace file-upload blacklist with whitelist validation in `ProductAddRequest.php` — re-enable the commented-out `mimes` rule and remove the manual `in_array($extension, …)` check.
- [x] **SEC-2:** Replace file-upload blacklist with whitelist validation in `ProductUpdateRequest.php` (same pattern likely duplicated).
- [x] **SEC-3:** Add proper `authorize()` logic in `ProductAddRequest` (currently returns `true` unconditionally).
- [x] **SEC-4:** Add proper `authorize()` logic in `ProductUpdateRequest`.
- [x] **SEC-5:** Audit all remaining FormRequest classes for the same `authorize() → true` anti-pattern.

---

## ⚡ Performance

- [x] **PERF-1:** Fix N+1 queries in `ProductController::getProductGalleryView()` — color lookup inside `$products->map()` loop queries DB per iteration.
- [x] **PERF-2:** Fix N+1 / in-memory sorting in `ProductRepository::getTopSellList()` — fetches ALL products then sorts in PHP with `sortByDesc`.
- [x] **PERF-3:** Fix N+1 / in-memory pagination in `ProductRepository::getTopRatedList()` — fetches ALL products then slices in PHP.
- [x] **PERF-4:** Review `ProductService` SKU combination generation (`getCombinations`, [getVariations](file:///d:/Freelance/Clickit/Clickit/app/Http/Controllers/Admin/Product/ProductController.php#625-635)) for memory efficiency on large catalogs.
- [x] **PERF-5:** Audit View templates for lazy-loaded relationship access that would cause N+1 — `_side-bar.blade.php` was firing 20+ individual count queries on every page load. Colleague cached all sidebar badge counters with 60s `Cache::remember` and a single `GROUP BY` query.
- [x] **PERF-6:** `DashboardController::index()` — Severe memory leak: fetches entire tables into memory just to count them (e.g., `customerRepo->...->count()`). Must use DB-level count().
- [x] **PERF-7:** `DashboardController::index()` — In-memory limits: fetches entire tables to get top records (e.g., `getTopCustomerList(dataLimit: 'all')->take()`). Must use DB `limit()`.
- [x] **PERF-8:** `DashboardController::getOrderStatusData()` — Replaced 12 redundant per-status queries with a single `GROUP BY order_status` query.
- [x] **PERF-9:** `ProductRepository::getListWhere()` — Fetches full models into memory via `->get()` just to array map IDs (`->get()?->pluck('id')`). Must use DB-level `pluck()`.
- [x] **PERF-10:** `ProductRepository::getListWhereNotIn()` — Ignores `dataLimit` argument entirely and always calls `->get()`, returning potentially un-paginated massive datasets.
- [x] **PERF-11:** `BrandController::index()` & Admin list view — Memory leak on brand list. Loaded all 10,000+ products into memory to sum order details and count products instead of pushing to DB.
- [x] **PERF-12:** `product.php` helper — `getVendorProductsCount()` / `getAdminProductsCount()` replaced `->get()->count()` with `DB::table()->count()` for sidebar product badge counts.
- [x] **PERF-13:** `DashboardController::getRealTimeActivities()` — Replaced full-collection fetch with DB-level count() for new orders.
- [x] **PERF-14:** Fixed 500 error on Dashboard caused by referencing non-existent `avoid_walking_customer` column.
- [x] **PERF-15:** `CustomerController::getView()` — Fetches ALL orders for a customer via getListWhere(dataLimit: 'all') into memory just to count statuses with `->map()`. Should use a single `GROUP BY order_status` query.
- [x] **PERF-16:** `CustomerController::index()` — Calls getListWhereBetween(dataLimit: 'all')->count() to get `$totalCustomers`, loading every customer row into memory just for a count. Must use DB-level `->count()`.
- [x] **PERF-17:** `CustomerController::getSubscriberListView()` — Same fetch-all-then-count anti-pattern: getListWhere(dataLimit: 'all')->count() for `$totalSubscribers`.
- [x] **PERF-18:** `CustomerRepository::getListWhereBetween()` — When `$takeItem` is set, fetches the **entire** result set via `->get()` then slices in PHP. Should use DB-level `->take()` / `->limit()` instead.
- [x] **PERF-19:** `CustomerRepository::getListWhereNotIn()` — Ignores `$dataLimit` parameter entirely and always calls `->get()`, returning potentially massive unfiltered collections.
- [x] **PERF-20:** `DeliveryManController::getRatingView()` — Fetches ALL reviews for a delivery man into memory, then does in-PHP calculations. Should use DB-level `COUNT`, `AVG`, `GROUP BY rating`.
- [x] **PERF-21:** `CustomRoleController::index()` — Fetches all employee roles with `dataLimit: 'all'` without pagination.
- [x] **PERF-22:** `OrderController::exportList()` — Fetches ALL orders with `dataLimit: 'all'`, causing a massive N+1 query issue for large exports.
- [x] **PERF-23:** `OrderController::getView()` — Calculates `$totalDelivered` by fetching ALL delivered orders into PHP memory just to count them! Should use DB-level `->count()`.
- [x] **PERF-24:** `ReviewController::index()` & exportList() — Fetches all products and customers matching search into memory with `dataLimit: 'all'`, causing severe memory leaks. Must use DB-level `pluck()`.
- [x] **PERF-25:** ProductController — exportList, getStockLimitStatus, and exportRestockList use `dataLimit: 'all'` without chunking.
- [x] **PERF-26:** `SiteMapController::processGenerateAndDownload()` — Fetches ALL active products and shops into memory with `dataLimit: 'all'` just to `->pluck()`. Must use DB-level `pluck()`.
- [x] **PERF-27:** `InhouseShopController::index()` — Fetches ALL in-house products into memory with `dataLimit: 'all'` just to pluck IDs. Must use DB-level pluck or subquery.
- [x] **PERF-28:** `ShippingMethodController::index()` — N+1 query issue. Should use `insert()` or bulk operations.
- [x] **PERF-29:** `VendorController::exportOrderList()` — Fetches ALL orders into memory then uses PHP map() to count order statuses instead of using a DB-level `GROUP BY order_status` query.
- [x] **PERF-30:** `VendorController::getView()` — Calculates complex review/rating data by iterating over all products and their reviews in PHP memory. Must use DB-level aggregates.
- [x] **PERF-31:** ReportController — Severe memory leaks. Calculates earnings, metrics, and aggregates by fetching all orders and looping over collections in PHP memory. Must use DB-level aggregates (`SUM`, `GROUP BY`).
- [x] **PERF-32:** TransactionReportController — Severe memory leaks. Handles transaction aggregations by fetching large datasets into PHP memory (`->get()`) and summing via loops.
- [x] **PERF-33:** ChattingController — Uses `->getListWhereNotNull(..., dataLimit: 'all')` resulting in loading all matching chat records into memory.
- [x] **PERF-34:** OrderReportController — Memory leaks in chart/monthly aggregations due to `->get()` followed by in-PHP loops to sum metrics.
- [x] **PERF-35:** VendorProductSaleReportController — Memory leaks for vendor chart aggregations due to `->get()` followed by PHP array loops.
- [x] **PERF-36:** ExpenseTransactionReportController — Memory leaks in expense chart data processing (`->get()` and loops).
- [x] **PERF-37:** ProductReportController — Memory leaks in product chart data processing (`->get()` and loops).
- [x] **PERF-38:** `TransactionController::export()` — Fetches all transactions into memory using `->get()` and iterates over them. Large exports will exhaust memory.
- [x] **PERF-39:** `[API v1] BannerController::getBannerList()` — Severe N+1 query loop. Calls `Product::find()` inside a `foreach` loop.
- [x] **PERF-40:** `[API v1] BrandController::get_brands()` — Memory leak in priority sort. Loads all brands and their products into memory via `->get()->map()` just to sort them.
- [x] **PERF-41:** `[API v1] CartController::getCartList()` — N+1 query loops. Calls `Product::find()` and `Cart::where()->get()` inside a map loop.
- [x] **PERF-42:** `[API v1] CategoryController::find_what_you_need()` — Fetches all categories into memory and uses PHP array slicing and filtering loops instead of SQL limits.
- [x] **PERF-43:** `[API v1] ChatController::search()` — Severe N+1 loop. Queries the DB twice inside a `foreach` loop.
- [x] **PERF-44:** `[API v1] CustomerController::get_order_details()` — N+1 query loop. Inside the Collection map loop for order details, it runs a DB query: `Review::where(...)`.
- [x] **PERF-45:** `[API v1] CustomerRestockRequestController::deleteRestockRequests()` — Memory leak. Fetches ALL restock products into memory with `dataLimit: 'all'` deleting via map loop instead of a DB-level query.
- [x] **PERF-46:** `[API v1] ProductController::getProductsFilter()` — Severe memory leak. Fetches ALL Publishing Houses and Authors with their related products into memory via `->get()`.
- [x] **PERF-47:** `[API v1] ProductController::just_for_you()` — Severe memory leak. Fetches ALL user orders with details into memory via `->get()`.
- [x] **PERF-48:** `[API v1] SellerController::getSellerList()` — Memory leak. Iterates through all in-house product reviews in PHP memory (`pluck('rating')`) to count positive ratings instead of using a DB-level query.
- [x] **PERF-49:** `[API v1 Seller] SellerController::more_sellers()` — Memory leak. Fetches ALL active shops with their seller's order counts into memory via `->get()`, then sorts them in PHP using `sortByDesc()` instead of `orderBy()`.
- [x] **PERF-50:** `[API v1 Auth] CustomerAPIAuthController::login()` — Redundant query. Queries the user twice for failed login attempts.
- [x] **PERF-51:** `[API v1 Auth] EmailVerificationController` and `PhoneVerificationController` — Security/Performance. OTP cleanup logic uses expensive `->first()` loops and manual deletions instead of bulk expiration queries.
- [x] **PERF-52:** `[API v1 Auth] ForgotPasswordController::reset_password_request()` — Memory Leak risk. Uses `->get()` and manual logic instead of relying on `->updateOrCreate()`.
- [x] **PERF-53:** `[API v1 Auth] SocialAuthController::social_login()` — N+1 query. Fetches business settings (`apple_login`) via a direct DB query rather than utilizing cached config helpers.
- [x] **PERF-54:** `[API v2 Delivery] DeliveryManController::info()` — Severe memory leak. Fetches ALL orders into memory via `->get()` just to count their statuses. Must use DB-level `COUNT` with `GROUP BY`.
- [x] **PERF-55:** `[API v2 Delivery] ChatController::search()` — Severe N+1 loop. Plucks user IDs, queries unique chat IDs, then loops over them running `Chatting::with(...)->where(...)->latest()->first()` for every single chat ID.
- [x] **PERF-56:** `[API v2 Seller] ProductController::list()` — Memory leak. Fetches ALL seller products via `->get()` without pagination.
- [x] **PERF-57:** `[API v2 Seller] OrderController::list()` — Severe memory leak. Fetches ALL orders for a seller into memory via `->get()` without pagination.
- [x] **PERF-58:** `[API v2 Seller] SellerController::shop_info()` — Inefficient query. Plucks ALL a seller's product IDs into a massive PHP array just to pass it into a `Review::whereIn()` average rating query.
- [x] **PERF-59:** `[API v2 Seller] SellerController::monthly_earning()` and `monthly_commission_given()` — Memory leak. Fetches all order transactions via `->get()->toArray()` then loops 1 to 12 in PHP.
- [x] **PERF-60:** `[API v2 Seller] ChatController::search()` — Severe N+1 loop. Identical to the Delivery Man ChatController search flaw.
- [x] **PERF-61:** `[API v2 Seller] RefundController::list()` — Memory leak liability. Fetches the entire refund request list for a seller into memory via `->get()` (without pagination).
- [x] **PERF-62:** `[API v3 Seller] ProductController::getVendorAllProducts()` — Memory leak. Fetches ALL PublishingHouse and Author records along with their nested products into memory just to map IDs.
- [x] **PERF-63:** `[API v3 Seller] POSController::place_order()` — Severe N+1 loop. Queries the DB via `Product::where()->first()` inside a `foreach` loop over cart items.
- [x] **PERF-64:** `[API v3 Seller] SellerController::shop_info()` — Inefficient query array blob. Plucks ALL a seller's product IDs into a PHP array just to pass it into a `Review::whereIn()` aggregation query.
- [x] **PERF-65:** `[API v3 Seller] SellerController::monthly_earning()` and `monthly_commission_given()` — Memory leak. Fetches all order transactions via `->get()->toArray()` then iterates 1 to 12 in a PHP loop instead of using GROUP BY.
- [x] **PERF-66:** `[API v3 Seller] ChatController::search()` — Severe N+1 loop. Plucks user IDs, queries unique chat IDs, then loops over them running DB queries.
- [x] **PERF-67:** `[API v3 Seller] RefundController::list()` — Memory leak. Fetches the entire refund request list into memory via `->get()` without pagination.
- [x] **PERF-68:** `[Web Vendor] TransactionReportController` — Severe memory leak in order_transaction_summary_pdf loading all transactions to memory and calculating totals via PHP loops.
- [x] **PERF-69:** `[Web Vendor] OrderReportController` — Memory leak in exportOrderReportInPDF doing `$orders->sum('order_amount')` after fetching all orders.
- [x] **PERF-70:** `[Web Vendor] DashboardController` — Heavy in-memory date mapping in index() after `$orderTransactionRepo->getListWhereBetween()->get()`.
- [x] **PERF-71:** `Web Vendor/ProductReportController` — all_product() loads eager `orderDetails` models mapping large logic chains to count totals.
- [x] **PERF-72:** `Web Vendor/WithdrawController` — exportList method uses `dataLimit: 'all'`., fetching all withdraw requests into memory (`dataLimit: 'all'`) instead of using chunking.
- [x] **PERF-73:** `Web/WebController` — getAllVendorsView() maps over each vendor's products in PHP to pull review arrays and calculate ratings.
- [x] **PERF-74:** `Web/ProductListController` — theme_fashion() loads products into PHP memory, maps them to append the average rating, and then attempts to filter (`where()`) and paginate IN MEMORY instead of via DB.
- [x] **PERF-75:** `Web/ShopViewController` — getShopInfoArray() executes `Review::active()->whereIn()->avg()` inside a loop or isolated mapped process.
- [x] **PERF-76:** `[Web Front] UserProfileController` — Memory leaks fetching large collections of order history/addresses before processing.
- [x] **PERF-77:** `[Web Front] ProductDetailsController` — Extensive data aggregation in getThemeFashion which might be unoptimized.
- [x] **PERF-78:** `[Web Front] HomeController` — Heavy dataset fetching and processing in default_theme and theme_fashion.
- [x] **PERF-79:** `[Web Front] CartController` — N+1 queries in getVariantPrice where `Product::find($request->id)` is called inside a loop over choice options.

---

## 🏗️ Code Quality & Refactoring

### God Classes — Break down oversized files
- [x] **REF-1:** Extract image-processing logic from `ProductService` into a dedicated `ProductImageService`.
- [x] **REF-2:** Extract variation/SKU logic from `ProductService` into a dedicated `ProductVariationService`.
- [x] **REF-3:** Extract bulk-import logic from `ProductService` into a dedicated `ProductImportService`.
- [ ] **REF-4:** Extract digital-product variation logic from ProductController into its Service.

### Monolithic Repository Queries — Use Pipelines / Filter classes
- [x] **REF-5:** Refactor `ProductRepository::getListWhere()` — replace 100+ line `when()` chain with a Pipeline or Filter pattern.
- [x] **REF-6:** Refactor `ProductRepository::getListWithScope()` — same treatment.
- [x] **REF-7:** Refactor `ProductRepository::getWebListWithScope()` — same treatment.

### Missing DTOs
- [ ] **REF-8:** Create a `ProductData` DTO to replace the massive array in `ProductService::getAddProductData()`.
- [ ] **REF-9:** Create / reuse DTO for `ProductService::getUpdateProductData()`.
- [ ] **REF-10:** Create / reuse DTO for `ProductService::getImportBulkProductData()`.

### Tight Coupling to Global State
- [ ] **REF-11:** Remove direct `auth('admin')->id()` / `auth('seller')->id()` calls from `ProductService`; pass the user ID from the Controller instead.
- [ ] **REF-12:** Remove direct `currencyConverter()` calls from `ProductService`; inject a CurrencyConverter service or pass converted values.
- [ ] **REF-13:** Remove direct `getWebConfig()` calls from `ProductService`; pass config values from the Controller.

### Naming & Miscellaneous
- [x] **REF-14:** Fix typo model file `ReferrlaCustomer.php` → `ReferralCustomer.php` and update all references.
- [ ] **REF-15:** Implement the empty `ProductRepository::getList()` stub method (currently has `// TODO`).
- [x] **REF-16:** Remove unused imports in `ProductService`.
- [ ] **REF-17:** ProductController constructor has **25 injected dependencies** — reduce by extracting sub-controllers or action classes.
- [ ] **REF-18:** `SubscriptionRepository::getListWhereBetween()` — Duplicates the same in-memory `->get()->slice()` pagination anti-pattern found in CustomerRepository. Should share a base class or trait with DB-level limit/offset.
- [ ] **REF-19:** ReviewRepository — Has 3 large `getListWhere*` methods with heavily duplicated `->when()` filter chains. Should consolidate shared filters into a private `applyFilters()` method or use a Pipeline/Filter pattern.
- [ ] **REF-20:** OrderController — Massive God Class (552 lines) with **16 injected dependencies** in its constructor. Extract responsibilities into dedicated Action classes or Services.
- [ ] **REF-21:** ProductController — Massive God Class (~1000 lines) with **25 injected dependencies**. Needs SRP breakdown.
- [ ] **REF-22:** VendorController — God Class (580 lines) managing vendor lists, individual vendor profiles, registration, and withdrawals. It injects **15 repositories/services**. It should be broken down into sub-controllers.
- [ ] **REF-23:** ReportController — God Class (over 1500 lines). Handles multiple disparate reports. Should be broken down into discrete Controllers or Services.
- [ ] **REF-24:** TransactionReportController — God Class (over 1300 lines). Similar issues handling order transactions, expense reports, wallet reports, etc. Needs breakdown.
- [ ] **REF-25:** `[API v1] OrderController` — Massive API God Class (almost 900 lines). Contains complex nested logic for checkout, OTP sending, refunds, and file downloads. Needs Action classes.
- [ ] **REF-26:** `[API v1] ProductController` — Massive API God Class (800+ lines). Handles complex filtering, recommendations, reviews, and related logic. Highly tangled business logic should move to Services.
- [ ] **REF-27:** `[API v2 Seller] ProductController` — God Class (672 lines). Extremely similar tightly-coupled logic to the web ProductController for adding/updating products. Requires extraction into a shared `ProductService`.
- [ ] **REF-28:** `[API v3 Seller] ProductController` — God Class (1600+ lines). Exact massive duplication of logic from the web and v2 ProductControllers handles filtering, uploading, saving, and variations. Must be refactored into the shared `ProductService`.
- [ ] **REF-29:** `[Web Front] WebController` — God Class (~800 lines). Handles various frontend aspects like product searches, category listings, brand listings, and checkout processes. Needs breakdown.
- [ ] **REF-30:** `[Web Front] UserProfileController` — God Class (~800 lines). Too many responsibilities (account details, addresses, orders, support tickets). Should be extracted into discrete controllers.
- [ ] **REF-31:** `[Web Vendor] TransactionReportController` — God Class (~800 lines). Handles all complex transaction and expense reporting logic calculation inside loops. Needs Action classes.
- [ ] **REF-32:** `[System] InstallController / UpdateController` — Messy Logic. Uses raw shell commands (`shell_exec`, `ln -s`) and raw DB queries (`DB::unprepared`) directly in the controller instead of utilizing proper services or native Laravel storage links securely.
- [ ] **REF-33:** `[Payment] Payment Controllers` — Code Duplication. Controllers like `PaytmController`, `LiqPayController`, `PaymobController` contain duplicated cURL implementations, encryptions, and hash generations. Should be abstracted into a unified `PaymentGatewayService` or utilize Laravel's Http client.
- [ ] **REF-34:** `[Cart & Order] Deep CartManager/OrderManager Coupling` — The Cart calculations (like `OrderManager::verifyCartListMinimumOrderAmount`) contain highly redundant queries mixed with heavy business-logic conditions affecting the global state. Fixing N+1 at the root of `CartManager` needs architectural decouple. 
- [ ] **REF-35:** `[API v2/v3 Seller] Unpaginated List Endpoints — Mobile App Pagination Support` — ProductController::list(), OrderController::list(), and RefundController::list() currently return raw JSON arrays capped at 100 via `take(100)`. The mobile frontend must be updated to support proper paginated responses (`total_size/limit/offset` wrapper) before the API can switch to `->paginate()`.

---

## 🧪 Testing

- [ ] **TEST-1:** Set up a testing framework (Pest PHP or PHPUnit) with a proper test database.
- [ ] **TEST-2:** Write Feature test: Admin can add a product (physical).
- [ ] **TEST-3:** Write Feature test: Admin can add a product (digital).
- [ ] **TEST-4:** Write Feature test: Seller cannot view/modify another Seller's product (multi-vendor isolation).
- [ ] **TEST-5:** Write Feature test: Cart + Checkout with stock-race-condition edge case.
- [ ] **TEST-6:** Write Unit test: SKU combination generation produces unique, correct results.
- [ ] **TEST-7:** Write Unit test: Discount cannot exceed or equal unit/variation price.

---

**Total items: 121**