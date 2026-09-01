# Change Log

## 2026-09-01

### Sale Create Page - Performance Optimization (N+1 Query Fix)

**Affected:** `SaleController::create()`

### Problem

The Sale Create page was taking approximately **7–8 seconds** to load.

Investigation identified that the shared `<x-account>` component was causing excessive database queries while rendering the account hierarchy.

The main issue was **N+1 queries caused by lazy-loading nested account relationships**.

### Root Cause

The controller was loading account records without sufficiently eager-loading the relationships required by the recursive `<x-account>` component.

As the component traversed:

- `parent`
- `subAccount`
- nested `subAccount`
- nested parent relationships

additional database queries were triggered during Blade rendering.

### Fix Applied

Added nested eager loading to the account queries in `SaleController::create()`.

```php
$accountEagerLoad = [
    'parent',
    'subAccount.parent',
    'subAccount.subAccount.parent',
    'subAccount.subAccount.subAccount.parent',
];
```

The eager-load configuration was applied to the relevant `ChartOfAccount` queries.

For example:

```php
$ledgers = ChartOfAccount::whereIn(
    'id',
    [getAccountByUniqueID(5)->id, getAccountByUniqueID(16)->id]
)
->with($accountEagerLoad)
->get();
```

The same eager-loading structure was also applied to the `$account` query used by the Sale Create page.

### Performance Verification

Before optimization, the page had significant delay during rendering due to repeated account relationship queries.

After adding eager loading and measuring the **full Blade render**, including the recursive `<x-account>` component:

- Total queries observed: approximately **119**
- Database query time: approximately **83ms**
- Full controller + Blade render: approximately **380ms**
- Repeated measurements remained approximately **370–400ms**

The query count is still higher than ideal, but the actual full render time is now well below one second and the previous multi-second delay was eliminated.

### Debug Code Removed

Temporary performance/debugging code was used during investigation, including:

```php
DB::enableQueryLog();
$start = microtime(true);
```

and manual Blade rendering:

```php
$html = view('backend.pages.sale.create', get_defined_vars())->render();
```

along with query/time logging.

These temporary measurements should **not remain in production code**.

The controller was restored to the normal Laravel response:

```php
return view('backend.pages.sale.create', get_defined_vars());
```

### Blade Component

No change was required in the shared `<x-account>` component.

The component itself remains unchanged. The optimization was performed at the controller/data-loading level so that the relationships required during rendering are already available.

### Existing Sale Functionality Preserved

The performance optimization does not intentionally change:

- Sale form behavior
- Customer/Ledger selection
- Branch selection
- Warehouse selection
- Product selection
- Product stock calculation
- Price calculation
- VAT calculation
- Discount calculation
- Carrying cost
- Labor bill
- Payment calculation
- Sale item management
- Form submission



### Current Status

**Sale Create performance optimization: COMPLETED**

- N+1 issue identified
- Nested eager loading added
- Full render measured
- Performance verified at approximately 370–400ms
- Temporary debug measurement code removed/recommended for removal
- Existing Blade/component business logic preserved
- Purchase Create optimization identified as the next task






# Change Log

## 2026-09-01

### Sales Create Page - Responsive & Modal Select2 Fix

**File:** Sales Create Blade view (`create.blade.php`)

### Changes Made

#### 1. Sales Item Table Responsive Fix
- Replaced the previous broken table-in-table layout with a Bootstrap responsive grid structure.
- Added `.sale-item-table-wrapper` with horizontal scrolling for smaller screens.
- Added minimum table width to keep all Sales Item columns usable without breaking the layout.
- Added mobile-specific table width handling.

#### 2. Form Responsive Improvements
- Added responsive spacing for form columns on mobile devices.
- Reduced `.card-body` padding on smaller screens.
- Enabled wrapping for payment type button groups.
- Set narration textarea width to `100%` with a maximum width of `100%`.

#### 3. Select2 UI / Responsive Fix
- Added global Select2 width control:
  - `.select2-container { width: 100% !important; }`
- Standardized Select2 single-selection height and line-height.
- Added `initSaleSelect2()` function to safely destroy existing Select2 instances before reinitializing them.
- Added `dropdownParent` support when Select2 fields are inside a Bootstrap modal.
- Added `width: '100%'` and `dropdownAutoWidth: false` to stabilize Select2 sizing.

#### 4. Modal Reopen Issue Fix
- Added `shown.bs.modal` event handling.
- When the Sale modal is opened again, existing Select2 instances are destroyed and initialized again.
- This prevents Select2 fields from becoming compressed or visually broken after closing and reopening the Sale modal.

#### 5. Branch → Warehouse Select2 Fix
- Updated `getWarehousesByBranch()` to reinitialize the Sub-Warehouse Select2 with consistent width.
- Added modal-aware `dropdownParent` handling.

#### 6. Product Select2 Fix
- Updated dynamic Product Select2 initialization inside `getProductList()`.
- Preserved the existing product search matcher for:
  - Product code
  - Product name
  - Numeric product code search
- Added consistent width and modal-aware dropdown configuration.

### Existing Business Logic Preserved

The following existing functionality was not changed:

- Branch selection
- Sub-Warehouse loading
- Customer/Ledger selection
- Sales Representative selection
- Product Category → Product loading
- Product stock checking
- Product sale price loading
- Quantity calculation
- VAT calculation
- Discount calculation
- Carrying Cost calculation
- Labor Bill calculation
- Payment calculation
- Customer balance checking
- Duplicate product validation
- Sales item add/remove
- Sale form submission

### Result

- Sales Create page is responsive on desktop and mobile.
- Sales Item table no longer breaks the page layout on smaller screens.
- Select2 fields maintain proper width and height.
- Select2 dropdowns work correctly inside Bootstrap modals.
- Closing and reopening the Sale modal no longer causes Select2/input design distortion.
