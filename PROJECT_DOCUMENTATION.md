# ERP System Project Documentation

## Project Overview

এটি একটি Laravel 8 (PHP 7.3+/8.0+) ভিত্তিক সম্পূর্ণ ERP (Enterprise Resource Planning) সিস্টেম। এই সিস্টেমে বিভিন্ন মডিউল রয়েছে যেমন HRM, Inventory, Sales, Project, Accounting, Assets ইত্যাদি।

---

## ১. প্রযুক্তিগত স্পেসিফিকেশন

| বিষয় | বিবরণ |
|-------|--------|
| Framework | Laravel 8.40 |
| PHP Version | 7.3 - 8.0 |
| Database | MySQL (config/.env এ সংযোগ সেটিংস) |
| Frontend | Blade Templates + Bootstrap |
| Additional Packages | intervention/image, rats/zkteco, riskihajar/terbilang |


---
## ২. মডিউল ভিত্তিক স্ট্রাকচার

### ২.১ HRM (Human Resource Management)

**Location:** `routes/hrm.php`

HRM মডিউলে নিম্নলিখিত ফিচারগুলো রয়েছে:

| Feature | Controller | Route Name |
|--------|------------|------------|
| Employee Management | EmployeeController | hrm.employee.* |
| Position/Designation | PositionController | hrm.position.* |
| Attendance | AttendanceController | hrm.attendance.* |
| Attendance Log | AttendanceLogController | hrm.attendancelog.* |
| Salary Sheet | SalarySheetController | hrm.salary.sheet.* |
| Pay Sheet | PaySheetController | hrm.paysheet.* |
| Leave Application | LeaveApplicationController | hrm.leave.* |
| Leave Approval | ApproveLeaveApplicationController | hrm.leaveapprove.* |
| Loan Application | LoneApplicationController | hrm.lone.* |
| Loan Approval | ApproveLoneApplicationController | hrm.loneapprove.* |
| Cash Request | CashApplicationController | hrm.cashapplicaon.* |
| Cash Request Approval | ApproveCashReqApplicationController | hrm.cash-req.* |
| Holiday | HolidayController | hrm.holiday.* |
| Award | AwardController | hrm.award.* |
| Recruitment | CandidateInformationController | candidate.* |
| Candidate Shortlist | CandidateShortlistController | candidate.shortlist.* |
| Candidate Selection | CandidateSelectionController | candidate.selection.* |

**Database Tables:**
- `employees` - কর্মচারী তথ্য
- `positions` - পদ/পদমর্যাদা
- `attendances` - উপস্থিতি
- `salary_sheets` - বেতন শীট
- `monthly_payable_salaries` - মাসিক বেতনযোগ্য
- `leave_applications` - ছুটি আবেদন
- `lones` - ঋণ আবেদন
- `cash_reqs` - নগদ অনুরোধ
- `holidays` - ছুটির দিন
- `awards` - পুরস্কার

**Models:**
- `Employee.php` - belongsTo Position, Branch
- `Position.php` - পদ
- `Attendance.php` - উপস্থিতি
- `SalarySheet.php` - বেতন শীট
- `MonthlyPayableSalary.php` - মাসিক বেতন
- `LeaveApplication.php` - ছুটি আবেদন
- `Lone.php` - ঋণ
- `Holiday.php` - ছুটি

---

### ২.২ Sales Module

**Location:** `routes/sale.php`

| Feature | Controller | Route Name |
|--------|------------|------------|
| Sale/Invoice | SaleController | sale.sale.* |
| Delivery Challan | DeliveryChalanController | sale.challan.* |

**Database Tables:**
- `sales` - বিক্রয়
- `sales__details` - বিক্রয় বিবরণ
- `inovices` - চালান
- `invoice_details` - চালান বিবরণ
- `delivery_chalans` - ডেলিভারি চালান
- `delivery_chalanDetails` - ডেলিভারি বিবরণ

---

### ২.৩ Inventory/Stock Module

**Location:** `routes/inventory.php`

**Database Tables:**
- `products` - পণ্য
- `categories` - পণ্যের ক্যাটাগরি
- `brands` - ব্র্যান্ড
- `product_units` - ইউনিট
- `stocks` - স্টক
- `grns` - গুডস রিসিভ নোট
- `grn_details` - GRN বিবরণ
- `purchase_orders` - পারচেস অর্ডার
- `purchase_order_details` - অর্ডার বিবরণ

---

### ২.৪ Project Management

**Location:** `routes/project.php`

| Feature | Controller | Route Name |
|--------|------------|------------|
| Project | ProjectController | project.* |
| Project Expense | ProjectExpenseController | project.expense.* |
| Project Requisition | ProjectRequisitionController | project.req.* |
| Project Transfer | ProjectTransferController | project.transfer.* |
| Project Return | ProjectReturnController | project.return.* |
| Project Money | ProjectMoneyController | project.money.* |
| Invoice | ProjectInvoiceController | project.invoice.* |

**Database Tables:**
- `projects` - প্রকল্প
- `project_expenses` - প্রকল্প খরচ
- `project_requisitions` - প্রকল্প রিকোয়িজিশন
- `project_transfer_details` - প্রকল্প ট্রান্সফার
- `project_returns` - প্রকল্প রিটার্ন

---

### ২.৫ Accounting/Finance Module

**Location:** বিভিন্ন voucher routes

| Voucher Type | Table Name |
|-------------|------------|
| Journal Voucher | journal_vouchers |
| Debit Voucher | dabit_vouchers |
| Credit Voucher | credit_vouchers |
| Contra Voucher | contra_vouchers |
| Payment Voucher | (dabit_voucher_details) |
| Receipt Voucher | (credit_voucher_details) |

**Chart of Accounts:**
- `chart_of_accounts` - হিসাব প্ল্যান
- `accounts` (Model) - Accounts.php

**Key Models:**
- `ChartOfAccount.php` - চার্ট অফ অ্যাকাউন্টস
- `AccountTransaction.php` - লেনদেন
- `GeneralLedger.php` - জেনারেল লেজার

---

### ২.৬ Assets Management

**Location:** `routes/assets.php`

**Database Tables:**
- `assets_lists` - সম্পদের তালিকা
- `assets_categories` - সম্পদের ক্যাটাগরি
- `assets_warranties` - ওয়ারেন্টি

---

### ২.৭ Customer & Supplier Management

**Routes:**
- `routes/customer.php` - গ্রাহক ব্যবস্থাপনা
- `routes/supplier.php` - সরবরাহকারী ব্যবস্থাপনা

**Database Tables:**
- `customers` - গ্রাহক
- `customer_groups` - গ্রাহক গ্রুপ
- `suppliers` - সরবরাহকারী

---

### ২.৮ Report Module

**Location:** `routes/reports.php`

বিভিন্ন রিপোর্ট জেনারেশন।

---

## ৩. Database Schema (Key Tables)

### Core Tables (Base Setup)

```sql
-- Company Setup
generals, general_setups, companies

-- User Management
users, user_roles, role_accesses, admin_roles

-- Branch & Division
branches, divisions, districts

-- Currency & Language
currencies, languages
```

### Accounting Core

```sql
chart_of_accounts (accounts)
-- Fields: account_name, accountCode, parent_id, balance_type, unique_identifier

journal_vouchers
journal_voucher_details

dabit_vouchers  
dabit_voucher_details

credit_vouchers
credit_voucher_details

contra_vouchers
contra_voucher_details

all_vouchers
transections
general_ledgers
opening_balances
```

### HRM Core

```sql
employees
-- Fields: emp_name, emp_code, position_id, branch_id, join_date, status

positions
attendances
salary_sheets
monthly_payable_salaries
emp_pay_details
leave_applications
lones
cash_reqs
holidays
awards
```

### Inventory & Sales

```sql
products
categories
brands
product_units
stocks

purchases
purchases_details
sales
sales__details
inovices
invoice_details
delivery_chalans
delivery_chalan_details
```

---

## ৪. Application Flow

### ৪.১ Attendance Flow

```
1. Employee Login → attendance_sign_in (POST)
2. System records: employee_id, sign_in time, date
3. Employee Logout → attendance_sign_out (POST)
4. System calculates: working hours
```

### ৪.২ Salary Processing Flow

```
1. Create Salary Sheet (month/year)
2. Generate Monthly Payable Salary
3. Manual adjustments (empPayDetailsStore)
4. Review salary (salaryreview)
5. Approve/Update pay
```

### ৪.৩ Leave/Loan Approval Flow

```
1. Employee Submit Application (store)
2. Manager Review (show)
3. Approve/Reject (approve/cancel)
4. Update status in database
```

### ৪.৪ Sales Flow

```
1. Create Sale (store)
2. Generate Invoice
3. Create Delivery Challan (challan)
4. Update stock
5. Customer Payment
```

### ৪.৫ Accounting Flow

```
1. Create Voucher (Journal/Credit/Debit/Contra)
2. Post to Ledger
3. Update Account Balance
4. Generate Reports
```

---

## ৫. URL/Routes Structure

সব রুট `/admin` প্রিফিক্সের অধীন:

```
admin/
├── hrm/
│   ├── hrm-employe-list
│   ├── hrm-attendance-index
│   ├── hrm-salary-pay-sheet-list
│   ├── hrm-leave-applicaitn-list
│   ├── hrm-lone-applicaitn-list
│   ├── hrm-cash-req-applicaitn-list
│   └── hrm-award-list
├── sale/
│   ├── sale-sale-list
│   └── sale-challan-list
├── project/
│   ├── project-list
│   └── project-invoice-list
├── inventory/
├── customer/
├── supplier/
├── assets/
└── chart/
```

---

## ৬. Folder Structure

```
app/
├── Http/
│   └── Controllers/
│       └── Backend/
│           ├── Hrm/
│           ├── Sale/
│           ├── Inventory/
│           ├── Project/
│           ├── Chart/
│           ├── Assets/
│           └── Recruitment/
├── Models/
│   ├── Employee.php
│   ├── Position.php
│   ├── Attendance.php
│   ├── Product.php
│   ├── Accounts.php
│   ├── Project.php
│   └── ... (100+ models)
├── Helpers/
│   └── Helper.php
└── Providers/

database/
├── migrations/
│   ├── 2021_07_10_* (Initial)
│   ├── 2023_* (Features)
│   └── 2026_* (Recent)
└── seeders/

routes/
├── hrm.php
├── sale.php
├── project.php
├── inventory.php
├── customer.php
├── supplier.php
├── assets.php
├── reports.php
└── web.php

resources/
└── views/
    └── backend/
        └── pages/
            ├── hrm/
            ├── sale/
            ├── project/
            ├── inventory/
            └── ...
```

---

## ৭. Key Dependencies (composer.json)

```json
"require": {
    "php": "^7.3|^8.0",
    "laravel/framework": "^8.40",
    "laravel/ui": "^3.3",
    "intervention/image": "^2.5",
    "fruitcake/laravel-cors": "^2.0",
    "guzzlehttp/guzzle": "^7.0.1",
    "rats/zkteco": "^002.0",
    "riskihajar/terbilang": "^1.2"
}
```

---

## ৮. Key Features Summary

| Module | Features |
|--------|-----------|
| HRM | Employee, Attendance, Salary, Leave, Loan, Cash Request, Holiday, Award, Recruitment |
| Sales | Sale, Invoice, Delivery Challan, Customer Balance |
| Inventory | Product, Category, Brand, GRN, Purchase Order |
| Project | Project, Expense, Requisition, Transfer, Return, Invoice |
| Accounting | Chart of Accounts, Journal, Debit, Credit, Contra Voucher, Ledger |
| Assets | Asset List, Category, Warranty |
| Customer | Customer, Customer Group, Payment |
| Supplier | Supplier, Purchase |

---

## ৯. Recent Changes (2026)

1. **Loan Approval Enhancement** - Added `approved_by` and `note` fields to lones table
2. **Monthly Payable Salary** - Added missing columns
3. **Attendance Auto Checkout** - Added auto checkout feature
4. **Purchase Order Account** - Added `account_id` to purchase_orders

---

*Documentation generated: 2026-04-16*