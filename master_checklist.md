# ClickIt Audit — Master Fix Checklist

---

## 🔒 Security

- [x] **SEC-1:** Replace file-upload blacklist with whitelist validation in [ProductAddRequest.php](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductAddRequest.php) — re-enable the commented-out `mimes` rule and remove the manual `in_array($extension, …)` check.
- [x] **SEC-2:** Replace file-upload blacklist with whitelist validation in [ProductUpdateRequest.php](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductUpdateRequest.php) (same pattern likely duplicated).
- [x] **SEC-3:** Add proper [authorize()](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductAddRequest.php#16-27) logic in [ProductAddRequest](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductAddRequest.php#10-244) (currently returns `true` unconditionally).
- [x] **SEC-4:** Add proper [authorize()](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductAddRequest.php#16-27) logic in [ProductUpdateRequest](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductUpdateRequest.php#13-335).
- [ ] **SEC-5:** Audit all remaining FormRequest classes for the same [authorize() → true](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductAddRequest.php#16-27) anti-pattern.

---

## ⚡ Performance

- [x] **PERF-1:** Fix N+1 queries in `ProductController::getProductGalleryView()` — color lookup inside `$products->map()` loop queries DB per iteration.
- [x] **PERF-2:** Fix N+1 / in-memory sorting in `ProductRepository::getTopSellList()` — fetches ALL products then sorts in PHP with `sortByDesc`.
- [x] **PERF-3:** Fix N+1 / in-memory pagination in `ProductRepository::getTopRatedList()` — fetches ALL products then slices in PHP.
- [x] **PERF-4:** Review [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#13-393) SKU combination generation ([getCombinations](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductVariationService.php#48-65), [getVariations](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductVariationService.php#82-111)) for memory efficiency on large catalogs.
- [x] **PERF-5:** Audit View templates for lazy-loaded relationship access that would cause N+1 — [_side-bar.blade.php](file:///d:/Freelance/Clickit/Clickit/resources/views/layouts/admin/partials/_side-bar.blade.php) was firing 20+ individual count queries on every page load. Colleague cached all sidebar badge counters with 60s `Cache::remember` and a single `GROUP BY` query.
- [x] **PERF-6:** `DashboardController::index()` — Severe memory leak: fetches entire tables into memory just to count them (e.g., `customerRepo->...->count()`). Must use DB-level [count()](file:///d:/Freelance/Clickit/Clickit/app/Utils/product.php#58-74).
- [x] **PERF-7:** `DashboardController::index()` — In-memory limits: fetches entire tables to get top records (e.g., `getTopCustomerList(dataLimit: 'all')->take()`). Must use DB `limit()`.
- [x] **PERF-8:** `DashboardController::getOrderStatusData()` — Replaced 12 redundant per-status queries with a single `GROUP BY order_status` query.
- [x] **PERF-9:** `ProductRepository::getListWhere()` — Fetches full models into memory via `->get()` just to array map IDs (`->get()?->pluck('id')`). Must use DB-level `pluck()`.
- [x] **PERF-10:** `ProductRepository::getListWhereNotIn()` — Ignores `dataLimit` argument entirely and always calls `->get()`, returning potentially un-paginated massive datasets.
- [x] **PERF-11:** `BrandController::index()` & Admin list view — Memory leak on brand list. Loaded all 10,000+ products into memory to sum order details and count products instead of pushing to DB.
- [x] **PERF-12:** [product.php](file:///d:/Freelance/Clickit/Clickit/app/Utils/product.php) helper — [getVendorProductsCount()](file:///d:/Freelance/Clickit/Clickit/app/Utils/product.php#133-143) / [getAdminProductsCount()](file:///d:/Freelance/Clickit/Clickit/app/Utils/product.php#146-157) replaced `->get()->count()` with `DB::table()->count()` for sidebar product badge counts.
- [x] **PERF-13:** `DashboardController::getRealTimeActivities()` — Replaced full-collection fetch with DB-level [count()](file:///d:/Freelance/Clickit/Clickit/app/Utils/product.php#58-74) for new orders.
- [x] **PERF-14:** Fixed 500 error on Dashboard caused by referencing non-existent `avoid_walking_customer` column.
- [ ] **PERF-15:** `CustomerController::getView()` — Fetches ALL orders for a customer via [getListWhere(dataLimit: 'all')](file:///d:/Freelance/Clickit/Clickit/app/Repositories/CustomerRepository.php#61-87) into memory just to count statuses with `->map()`. Should use a single `GROUP BY order_status` query (same pattern we fixed in `DashboardController::getOrderStatusData`).
- [ ] **PERF-16:** `CustomerController::index()` — Calls [getListWhereBetween(dataLimit: 'all')->count()](file:///d:/Freelance/Clickit/Clickit/app/Repositories/CustomerRepository.php#88-145) to get `$totalCustomers`, loading every customer row into memory just for a count. Must use DB-level `->count()`.
- [ ] **PERF-17:** `CustomerController::getSubscriberListView()` — Same fetch-all-then-count anti-pattern: [getListWhere(dataLimit: 'all')->count()](file:///d:/Freelance/Clickit/Clickit/app/Repositories/CustomerRepository.php#61-87) for `$totalSubscribers`.
- [ ] **PERF-18:** `CustomerRepository::getListWhereBetween()` — When `$takeItem` is set, fetches the **entire** result set via `->get()` then slices in PHP. Should use DB-level `->take()` / `->limit()` instead.
- [ ] **PERF-19:** `CustomerRepository::getListWhereNotIn()` — Ignores `$dataLimit` parameter entirely and always calls `->get()`, returning potentially massive unfiltered collections. Same bug pattern as the already-fixed `ProductRepository::getListWhereNotIn()`.
- [ ] **PERF-20:** `DeliveryManController::getRatingView()` — Fetches ALL reviews for a delivery man via [getListWhere(dataLimit: 'all')](file:///d:/Freelance/Clickit/Clickit/app/Repositories/CustomerRepository.php#61-87) into memory, then does in-PHP `->paginate()`, `->count()`, `->avg('rating')`, and per-star `->where('rating', N)->count()`. Should use DB-level `COUNT`, `AVG`, `GROUP BY rating`, and proper paginated query.
- [ ] **PERF-21:** `CustomRoleController::index()` — Fetches all employee roles with `dataLimit: 'all'` without pagination. Low risk now (few roles), but should use paginated query for consistency.

---

## 🏗️ Code Quality & Refactoring

### God Classes — Break down oversized files
- [x] **REF-1:** Extract image-processing logic from [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#13-393) into a dedicated [ProductImageService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductImageService.php#8-294).
- [x] **REF-2:** Extract variation/SKU logic from [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#13-393) into a dedicated [ProductVariationService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductVariationService.php#8-309).
- [x] **REF-3:** Extract bulk-import logic from [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#13-393) into a dedicated [ProductImportService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductImportService.php#8-121).
- [ ] **REF-4:** Extract digital-product variation logic from [ProductController](file:///d:/Freelance/Clickit/Clickit/app/Http/Controllers/Vendor/Product/ProductController.php#50-839) ([getDigitalProductUpdateProcess](file:///d:/Freelance/Clickit/Clickit/app/Http/Controllers/Vendor/Product/ProductController.php#350-426)) into its Service.

### Monolithic Repository Queries — Use Pipelines / Filter classes
- [ ] **REF-5:** Refactor `ProductRepository::getListWhere()` — replace 100+ line `when()` chain with a Pipeline or Filter pattern.
- [ ] **REF-6:** Refactor `ProductRepository::getListWithScope()` — same treatment.
- [ ] **REF-7:** Refactor `ProductRepository::getWebListWithScope()` — same treatment.

### Missing DTOs
- [ ] **REF-8:** Create a [ProductData](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#84-154) DTO to replace the massive array in `ProductService::getAddProductData()`.
- [ ] **REF-9:** Create / reuse DTO for `ProductService::getUpdateProductData()`.
- [ ] **REF-10:** Create / reuse DTO for `ProductService::getImportBulkProductData()`.

### Tight Coupling to Global State
- [ ] **REF-11:** Remove direct [auth('admin')->id()](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductAddRequest.php#16-27) / [auth('seller')->id()](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductAddRequest.php#16-27) calls from [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#13-393); pass the user ID from the Controller instead.
- [ ] **REF-12:** Remove direct `currencyConverter()` calls from [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#13-393); inject a CurrencyConverter service or pass converted values.
- [ ] **REF-13:** Remove direct `getWebConfig()` calls from [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#13-393); pass config values from the Controller.

### Naming & Miscellaneous
- [x] **REF-14:** Fix typo model file [ReferrlaCustomer.php](file:///d:/Freelance/Clickit/Clickit/app/Models/ReferrlaCustomer.php) → [ReferralCustomer.php](file:///d:/Freelance/Clickit/Clickit/app/Models/ReferralCustomer.php) and update all references.
- [ ] **REF-15:** Implement the empty `ProductRepository::getList()` stub method (currently has `// TODO`).
- [x] **REF-16:** Remove unused imports in [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#13-393) (`use function React\Promise\all;`, `use function Aws\map;`, `phpDocumentor\Reflection\Types\Boolean`).
- [ ] **REF-17:** [ProductController](file:///d:/Freelance/Clickit/Clickit/app/Http/Controllers/Vendor/Product/ProductController.php#50-839) constructor has **25 injected dependencies** — reduce by extracting sub-controllers or action classes.
- [ ] **REF-18:** `SubscriptionRepository::getListWhereBetween()` — Duplicates the same in-memory `->get()->slice()` pagination anti-pattern found in [CustomerRepository](file:///d:/Freelance/Clickit/Clickit/app/Repositories/CustomerRepository.php#13-196). Should share a base class or trait with DB-level limit/offset.
- [ ] **REF-19:** [ReviewRepository](file:///d:/Freelance/Clickit/Clickit/app/Repositories/ReviewRepository.php#11-192) — Has 3 large `getListWhere*` methods (lines 37–158) with heavily duplicated `->when()` filter chains. Should consolidate shared filters into a private `applyFilters()` method or use a Pipeline/Filter pattern.

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

**Total items: 47**
