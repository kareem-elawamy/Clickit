# ClickIt Audit — Master Fix Checklist

---

## 🔒 Security

- [x] **SEC-1:** Replace file-upload blacklist with whitelist validation in [ProductAddRequest.php](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductAddRequest.php) — re-enable the commented-out `mimes` rule and remove the manual `in_array($extension, …)` check.
- [x] **SEC-2:** Replace file-upload blacklist with whitelist validation in [ProductUpdateRequest.php](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductUpdateRequest.php) (same pattern likely duplicated).
- [x] **SEC-3:** Add proper [authorize()](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductUpdateRequest.php#25-36) logic in [ProductAddRequest](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductAddRequest.php#10-244) (currently returns `true` unconditionally).
- [x] **SEC-4:** Add proper [authorize()](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductUpdateRequest.php#25-36) logic in [ProductUpdateRequest](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductUpdateRequest.php#13-335).
- [ ] **SEC-5:** Audit all remaining FormRequest classes for the same [authorize() → true](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductUpdateRequest.php#25-36) anti-pattern.

---

## ⚡ Performance

- [x] **PERF-1:** Fix N+1 queries in `ProductController::getProductGalleryView()` — color lookup inside `$products->map()` loop queries DB per iteration.
- [x] **PERF-2:** Fix N+1 / in-memory sorting in `ProductRepository::getTopSellList()` — fetches ALL products then sorts in PHP with `sortByDesc`.
- [x] **PERF-3:** Fix N+1 / in-memory pagination in `ProductRepository::getTopRatedList()` — fetches ALL products then slices in PHP.
- [ ] **PERF-4:** Review [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#17-1053) SKU combination generation ([getCombinations](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#318-332), [getVariations](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#349-378)) for memory efficiency on large catalogs.
- [ ] **PERF-5:** Audit View templates for lazy-loaded relationship access that would cause N+1 (requires template review).
- [x] **PERF-6:** `DashboardController::index()` — Severe memory leak: fetches entire tables into memory just to count them (e.g., `customerRepo->...->count()`). Must use DB-level [count()](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#1036-1052).
- [x] **PERF-7:** `DashboardController::index()` — In-memory limits: fetches entire tables to get top records (e.g., `getTopCustomerList(dataLimit: 'all')->take()`). Must use DB `limit()`.
- [x] **PERF-8:** `DashboardController::getOrderStatusData()` — Severe memory leak: loads ALL orders, products, and stores into Collections returning `->count()`.
- [x] **PERF-9:** `ProductRepository::getListWhere()` — Fetches full models into memory via `->get()` just to array map IDs (`->get()?->pluck('id')`). Must use DB-level `pluck()`.
- [x] **PERF-10:** `ProductRepository::getListWhereNotIn()` — Ignores `dataLimit` argument entirely and always calls `->get()`, returning potentially un-paginated massive datasets.

---

## 🏗️ Code Quality & Refactoring

### God Classes — Break down oversized files
- [ ] **REF-1:** Extract image-processing logic from [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#17-1053) into a dedicated `ProductImageService`.
- [ ] **REF-2:** Extract variation/SKU logic from [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#17-1053) into a dedicated `ProductVariationService`.
- [ ] **REF-3:** Extract bulk-import logic from [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#17-1053) into a dedicated `ProductImportService`.
- [ ] **REF-4:** Extract digital-product variation logic from [ProductController](file:///d:/Freelance/Clickit/Clickit/app/Http/Controllers/Admin/Product/ProductController.php#50-966) ([getDigitalProductUpdateProcess](file:///d:/Freelance/Clickit/Clickit/app/Http/Controllers/Admin/Product/ProductController.php#302-376)) into its Service.

### Monolithic Repository Queries — Use Pipelines / Filter classes
- [ ] **REF-5:** Refactor `ProductRepository::getListWhere()` — replace 100+ line `when()` chain with a Pipeline or Filter pattern.
- [ ] **REF-6:** Refactor `ProductRepository::getListWithScope()` — same treatment.
- [ ] **REF-7:** Refactor `ProductRepository::getWebListWithScope()` — same treatment.

### Missing DTOs
- [ ] **REF-8:** Create a [ProductData](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#466-536) DTO to replace the massive array in `ProductService::getAddProductData()`.
- [ ] **REF-9:** Create / reuse DTO for `ProductService::getUpdateProductData()`.
- [ ] **REF-10:** Create / reuse DTO for `ProductService::getImportBulkProductData()`.

### Tight Coupling to Global State
- [ ] **REF-11:** Remove direct [auth('admin')->id()](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductUpdateRequest.php#25-36) / [auth('seller')->id()](file:///d:/Freelance/Clickit/Clickit/app/Http/Requests/ProductUpdateRequest.php#25-36) calls from [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#17-1053); pass the user ID from the Controller instead.
- [ ] **REF-12:** Remove direct `currencyConverter()` calls from [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#17-1053); inject a CurrencyConverter service or pass converted values.
- [ ] **REF-13:** Remove direct `getWebConfig()` calls from [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#17-1053); pass config values from the Controller.

### Naming & Miscellaneous
- [ ] **REF-14:** Fix typo model file [ReferrlaCustomer.php](file:///d:/Freelance/Clickit/Clickit/app/Models/ReferrlaCustomer.php) → [ReferralCustomer.php](file:///d:/Freelance/Clickit/Clickit/app/Models/ReferralCustomer.php) and update all references.
- [ ] **REF-15:** Implement the empty `ProductRepository::getList()` stub method (currently has `// TODO`).
- [ ] **REF-16:** Remove unused imports in [ProductService](file:///d:/Freelance/Clickit/Clickit/app/Services/ProductService.php#17-1053) (`use function React\Promise\all;`, `use function Aws\map;`, `phpDocumentor\Reflection\Types\Boolean`).
- [ ] **REF-17:** [ProductController](file:///d:/Freelance/Clickit/Clickit/app/Http/Controllers/Admin/Product/ProductController.php#50-966) constructor has **25 injected dependencies** — reduce by extracting sub-controllers or action classes.

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

**Total items: 35**
