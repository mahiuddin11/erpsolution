<?php

$parent_menu = array(

    (object) array(
        'label' => 'Inventory Setup',
        'route' => null,
        'uniqueName' => "Inventory",
        'icon' => 'fa fa-sitemap',
        'parent_id' => 0,
        'submenu' => (object) array(
            // (object) array(
            //     'label' => 'Product Category',
            //     'route' => null,
            //     'uniqueName' => "Product",
            //     'icon' => 'fa fa-th-large',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Category', 'route' => 'inventorySetup.maincategory.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Category', 'route' => 'inventorySetup.maincategory.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Category', 'route' => 'inventorySetup.maincategory.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Category', 'route' => 'inventorySetup.maincategory.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Category', 'route' => 'inventorySetup.maincategory.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),

            (object) array(
                'label' => 'Category',
                'route' => null,
                'uniqueName' => "Category",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Sub Category', 'route' => 'inventorySetup.category.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Sub Category', 'route' => 'inventorySetup.category.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Sub Category', 'route' => 'inventorySetup.category.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Sub Category', 'route' => 'inventorySetup.category.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Sub Category', 'route' => 'inventorySetup.category.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Product',
                'route' => null,
                'uniqueName' => "Product2",
                'icon' => 'fa fa-home',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Product', 'route' => 'inventorySetup.product.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Product', 'route' => 'inventorySetup.product.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Product', 'route' => 'inventorySetup.product.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Product', 'route' => 'inventorySetup.product.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Product', 'route' => 'inventorySetup.product.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
            (object) array(
                'label' => 'Unit',
                'route' => null,
                'uniqueName' => "Unit",
                'icon' => 'fa fa-home',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All  Unit', 'route' => 'inventorySetup.unit.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New  Unit', 'route' => 'inventorySetup.unit.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit  Unit', 'route' => 'inventorySetup.unit.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show  Unit', 'route' => 'inventorySetup.unit.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy  Unit', 'route' => 'inventorySetup.unit.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
            (object) array(
                'label' => 'Brand',
                'route' => null,
                'uniqueName' => "Brand",
                'icon' => 'fa fa-home',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Brand', 'route' => 'inventorySetup.brand.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Brand', 'route' => 'inventorySetup.brand.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Brand', 'route' => 'inventorySetup.brand.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Brand', 'route' => 'inventorySetup.brand.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Brand', 'route' => 'inventorySetup.brand.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Current Stock',
                'route' => null,
                'uniqueName' => "Current",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Current Stock', 'route' => 'inventorySetup.currentStock.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),

            (object) array(
                'label' => 'Transfer',
                'route' => null,
                'uniqueName' => "Transfer",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Stock Transfer', 'route' => 'inventorySetup.transfer.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add Stock Transfer', 'route' => 'inventorySetup.transfer.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Stock Transfer', 'route' => 'inventorySetup.transfer.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Approval Stock Transfer', 'route' => 'inventorySetup.transfer.approval', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Approval Stock Transfer', 'route' => 'inventorySetup.transfer.editapproval ', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Stock Transfer', 'route' => 'inventorySetup.transfer.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Stock Transfer', 'route' => 'inventorySetup.transfer.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
            (object) array(
                'label' => 'Stock Adjustment',
                'route' => null,
                'uniqueName' => "Stock",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Stock Adjustment', 'route' => 'inventorySetup.stockAdjustment.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add Stock Adjustment', 'route' => 'inventorySetup.stockAdjustment.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Stock Adjustment', 'route' => 'inventorySetup.stockAdjustment.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Approval Stock Adjustment', 'route' => 'inventorySetup.stockAdjustment.approval', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Approval Stock Adjustment', 'route' => 'inventorySetup.stockAdjustment.editapproval ', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Stock Adjustment', 'route' => 'inventorySetup.stockAdjustment.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Stock Adjustment', 'route' => 'inventorySetup.stockAdjustment.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Product Opening Stock',
                'route' => null,
                'uniqueName' => "productopeningstokc",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Product Opening Stock', 'route' => 'inventorySetup.productOS.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add Product Opening Stock', 'route' => 'inventorySetup.productOS.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Product Opening Stock', 'route' => 'inventorySetup.productOS.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Product Opening Stock', 'route' => 'inventorySetup.productOS.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Product Opening Stock', 'route' => 'inventorySetup.productOS.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
        )
    ),

    (object) array(
        'label' => 'Purchase Manage',
        'route' => null,
        'uniqueName' => "Purchase",
        'icon' => 'fa fa-shopping-cart',
        'parent_id' => 0,
        'submenu' => (object) array(
            (object) array(
                'label' => 'Direct Purchase',
                'route' => null,
                'uniqueName' => "Direct",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Purchase', 'route' => 'inventorySetup.purchase.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Purchase', 'route' => 'inventorySetup.purchase.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Purchase', 'route' => 'inventorySetup.purchase.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Purchase', 'route' => 'inventorySetup.purchase.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Purchase', 'route' => 'inventorySetup.purchase.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),


        )
    ),


    // (object) array(
    //     'label' => 'Production',
    //     'route' => null,
    //     'uniqueName' => "Production",
    //     'icon' => 'fa fa-object-group',
    //     'parent_id' => 0,
    //     'submenu' => (object) array(
    //         (object) array(
    //             'label' => 'Conversion',
    //             'route' => null,
    //             'uniqueName' => "Conversion",
    //             'icon' => 'fa fa-home',
    //             'parent_id' => null,
    //             'childMenu' => (object) array(
    //                 (object) array('label' => 'All Conversions', 'route' => 'inventorySetup.conversion.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
    //                 (object) array('label' => 'Add New Conversions', 'route' => 'inventorySetup.conversion.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Edit Conversions', 'route' => 'inventorySetup.conversion.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Show Conversions', 'route' => 'inventorySetup.conversion.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Destroy Conversions', 'route' => 'inventorySetup.conversion.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //             )
    //         ),
    //         (object) array(
    //             'label' => 'New Production',
    //             'route' => null,
    //             'uniqueName' => "New",
    //             'icon' => 'fa fa-th-large',
    //             'parent_id' => null,
    //             'childMenu' => (object) array(
    //                 (object) array('label' => 'All Production', 'route' => 'production.production.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
    //                 (object) array('label' => 'Add New Production', 'route' => 'production.production.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Edit Production', 'route' => 'production.production.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Show Production', 'route' => 'production.production.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Destroy Production', 'route' => 'production.production.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //             )
    //         ),


    //     )
    // ),


    (object) array(
        'label' => 'Sale',
        'route' => null,
        'uniqueName' => "Sale",
        'icon' => 'fa fa-shopping-bag',
        'parent_id' => 0,
        'submenu' => (object) array(
            (object) array(
                'label' => 'Manage Sale',
                'route' => null,
                'uniqueName' => "Managesale",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Sale', 'route' => 'sale.sale.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Sale', 'route' => 'sale.sale.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Sale', 'route' => 'sale.sale.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Sale', 'route' => 'sale.sale.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show challan', 'route' => 'sale.sale.challan', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Sale', 'route' => 'sale.sale.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
            (object) array(
                'label' => 'Add New Sale ',
                'route' => null,
                'uniqueName' => "Addnewsale",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Add New Sale', 'route' => 'sale.sale.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),

                )
            ),
            // (object) array(
            //     'label' => 'Delivery Challan',
            //     'route' => null,
            //     'uniqueName' => "Deliverychalan",
            //     'icon' => 'fa fa-th-large',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Sale', 'route' => 'sale.challan.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Sale', 'route' => 'sale.challan.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Sale', 'route' => 'sale.challan.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Sale', 'route' => 'sale.challan.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Sale', 'route' => 'sale.challan.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),
        )
    ),





    // (object) array(
    //     'label' => 'Stock Manage',
    //     'route' => null,
    //     'uniqueName' => "Stock3",
    //     'icon' => 'fas fa-chart-line',
    //     'parent_id' => 0,
    //     'submenu' => (object) array(

    //     )
    // ),

    (object) array(
        'label' => 'HRM',
        'route' => null,
        'uniqueName' => "HRM",
        'icon' => 'fas fa-chart-line',
        'parent_id' => 0,
        'submenu' => (object) array(
            (object) array(
                'label' => 'Position',
                'route' => null,
                'uniqueName' => "Positionhr",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Position', 'route' => 'hrm.position.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Position', 'route' => 'hrm.position.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Position', 'route' => 'hrm.position.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Position', 'route' => 'hrm.position.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Position', 'route' => 'hrm.position.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
            (object) array(
                'label' => 'Employe',
                'route' => null,
                'uniqueName' => "Employehr",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All employee', 'route' => 'hrm.employee.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New employee', 'route' => 'hrm.employee.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit employee', 'route' => 'hrm.employee.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show employee', 'route' => 'hrm.employee.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy employee', 'route' => 'hrm.employee.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Award',
                'route' => null,
                'uniqueName' => "Awardhr",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Award', 'route' => 'hrm.award.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New award', 'route' => 'hrm.award.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Award', 'route' => 'hrm.award.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Award', 'route' => 'hrm.award.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Award', 'route' => 'hrm.award.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            // (object) array(
            //     'label' => 'Salary Pay',
            //     'route' => null,
            //     'uniqueName' => "Salaryhr",
            //     'icon' => 'fa fa-th-large',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Salary Pay', 'route' => 'hrm.salary.sheet.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Salary Pay', 'route' => 'hrm.salary.sheet.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Salary Pay', 'route' => 'hrm.salary.sheet.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Salary Pay', 'route' => 'hrm.salary.sheet.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Salary Pay', 'route' => 'hrm.salary.sheet.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),

            (object) array(
                'label' => 'Attendance',
                'route' => null,
                'uniqueName' => "Attendancehr",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Attendance Sheet', 'route' => 'hrm.attendance.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Attendance Sheet', 'route' => 'hrm.attendance.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Attendance Sheet', 'route' => 'hrm.attendance.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Attendance Sheet', 'route' => 'hrm.attendance.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    // (object) array('label' => 'Show Attendance Sheet', 'route' => 'hrm.attendance.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),


            (object) array(
                'label' => 'Attendance Log',
                'route' => null,
                'uniqueName' => "Attendancehrlog",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Attendance Log', 'route' => 'hrm.attendancelog.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),

            (object) array(
                'label' => 'Holiday Setup',
                'route' => null,
                'uniqueName' => "Holidayhr",
                'icon' => 'fa fa-calendar-alt',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Holiday List', 'route' => 'hrm.holiday.index', 'icon' => 'fa fa-list', 'navigate_status' => 1),
                    (object) array('label' => 'Holiday Create', 'route' => 'hrm.holiday.create', 'icon' => 'fa fa-plus', 'navigate_status' => null),
                    (object) array('label' => 'Holiday Edit', 'route' => 'hrm.holiday.edit', 'icon' => 'fa fa-edit', 'navigate_status' => null),
                    (object) array('label' => 'Holiday Show', 'route' => 'hrm.holiday.show', 'icon' => 'fa fa-eye', 'navigate_status' => null),
                    (object) array('label' => 'Holiday Destroy', 'route' => 'hrm.holiday.destroy', 'icon' => 'fa fa-trash', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Salary Sheet',
                'route' => null,
                'uniqueName' => "Salaryhr",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Salary Sheet ', 'route' => 'hrm.paysheet.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),




            (object) array(
                'label' => 'Leave Application',
                'route' => null,
                'uniqueName' => "Leavehr",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Leave List ', 'route' => 'hrm.leave.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Leave Create ', 'route' => 'hrm.leave.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Leave Edit ', 'route' => 'hrm.leave.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Leave Show ', 'route' => 'hrm.leave.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Leave Destroy ', 'route' => 'hrm.leave.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Leave Approve ', 'route' => 'hrm.leave.approve', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Cash Requisition',
                'route' => null,
                'uniqueName' => "cashreq",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Cash Requisition List ', 'route' => 'hrm.cashapplicaon.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Cash Requisition Create ', 'route' => 'hrm.cashapplicaon.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Cash Requisition Edit ', 'route' => 'hrm.cashapplicaon.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Cash Requisition Show ', 'route' => 'hrm.cashapplicaon.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Cash Requisition Destroy ', 'route' => 'hrm.cashapplicaon.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Cash Requisition Approve ', 'route' => 'hrm.cashapplicaon.approve', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Cash Application Approve',
                'route' => null,
                'uniqueName' => "cashapplicationapprove",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Cash Application Approve List ', 'route' => 'hrm.cash-req.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Cash Application Approve Edit', 'route' => 'hrm.cash-req.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Cash Application Approve show', 'route' => 'hrm.cash-req.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Cash Application Approve Approve', 'route' => 'hrm.cash-req.cancel', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
            (object) array(
                'label' => 'Leave Application Approve',
                'route' => null,
                'uniqueName' => "Leavehrappr",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Leave Application List ', 'route' => 'hrm.leaveapprove.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Leave Application Edit', 'route' => 'hrm.leaveapprove.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Leave Application show', 'route' => 'hrm.leaveapprove.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Leave Application Approve', 'route' => 'hrm.leaveapprove.cancel', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Loan Application',
                'route' => null,
                'uniqueName' => "Loanhr",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Loan List ', 'route' => 'hrm.lone.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Loan Create ', 'route' => 'hrm.lone.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'loan Edit ', 'route' => 'hrm.lone.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'loan Show ', 'route' => 'hrm.lone.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'loan Destroy ', 'route' => 'hrm.lone.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'loan Approve ', 'route' => 'hrm.lone.approve', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Loan Application Approve',
                'route' => null,
                'uniqueName' => "Loanhrappr",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Loan Application List ', 'route' => 'hrm.loneapprove.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Loan Application Edit', 'route' => 'hrm.loneapprove.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Loan Application Show', 'route' => 'hrm.loneapprove.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Loan Application Approve', 'route' => 'hrm.loneapprove.cancel', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

        )
    ),

    (object) array(
        'label' => 'Recruitment',
        'route' => null,
        'uniqueName' => "Recruitment",
        'icon' => 'fas fa-chart-line',
        'parent_id' => 0,
        'submenu' => (object) array(

            (object) array(
                'label' => 'Add New Candidate',
                'route' => null,
                'uniqueName' => "AddRecruitment",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Add New candidate', 'route' => 'candidate.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1)
                )
            ),
            (object) array(
                'label' => 'Manage Candidate',
                'route' => null,
                'uniqueName' => "ManageRecruitment",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Candidate Information', 'route' => 'candidate.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Candidate', 'route' => 'candidate.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Candidate', 'route' => 'candidate.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Candidate', 'route' => 'candidate.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Candidate', 'route' => 'candidate.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),


            (object) array(
                'label' => 'Add New Shortlist',
                'route' => null,
                'uniqueName' => "AddRecruitmentshortli",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Add New Shortlist', 'route' => 'candidate.shortlist.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1)
                )
            ),

            (object) array(
                'label' => 'Manage Candidate Shortlist',
                'route' => null,
                'uniqueName' => "ManageRecruitmentshor",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Candidate Shortlists', 'route' => 'candidate.shortlist.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Shortlist', 'route' => 'candidate.shortlist.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Shortlist', 'route' => 'candidate.shortlist.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Shortlist', 'route' => 'candidate.shortlist.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Shortlist', 'route' => 'candidate.shortlist.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),


            (object) array(
                'label' => 'Add New Selection',
                'route' => null,
                'uniqueName' => "AddRecruitmentselect",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Add New Selection', 'route' => 'candidate.selection.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1)
                )
            ),

            (object) array(
                'label' => 'Manage Candidate Selection',
                'route' => null,
                'uniqueName' => "ManageRecruitmentselect",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Candidate Selection', 'route' => 'candidate.selection.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Selection', 'route' => 'candidate.selection.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Selection', 'route' => 'candidate.selection.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Selection', 'route' => 'candidate.selection.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Selection', 'route' => 'candidate.selection.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
        )
    ),


    (object) array(
        'label' => 'Account',
        'route' => null,
        'uniqueName' => "Account",
        'icon' => 'fas fa-coins',
        'parent_id' => 0,
        'submenu' => (object) array(

            // (object) array(
            //     'label' => 'Paid-Up Capital',
            //     'route' => null,
            //     'uniqueName' => "PaidAccount",
            //     'icon' => 'fa fa-cubes',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'Paid Up Capital', 'route' => 'paidup.capital.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),

            //     )
            // ),
            // (object) array(
            //     'label' => 'Authorized Capital',
            //     'route' => null,
            //     'uniqueName' => "AuthorizedAccount",
            //     'icon' => 'fa fa-cubes',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'Authorized Capital', 'route' => 'authorized_capital.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),

            //     )
            // ),

            // (object) array(
            //     'label' => 'Financial Year',
            //     'route' => null,
            //     'uniqueName' => "FinancialAccount",
            //     'icon' => 'fa fa-cubes',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Financial Year', 'route' => 'financial.year.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Financial Year', 'route' => 'financial.year.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Financial Year', 'route' => 'financial.year.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Financial Year', 'route' => 'financial.year.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Financial Year', 'route' => 'financial.year.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),

            (object) array(
                'label' => 'Chart of Accounts',
                'route' => null,
                'uniqueName' => "ChartAccount",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Account', 'route' => 'settings.account.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Account', 'route' => 'settings.account.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Account', 'route' => 'settings.account.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Account', 'route' => 'settings.account.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Account', 'route' => 'settings.account.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
            (object) array(
                'label' => 'Account Opening Balance',
                'route' => null,
                'uniqueName' => "AccountAccount",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Account Balance', 'route' => 'settings.openingbalance.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Add New Account Balance', 'route' => 'settings.openingbalance.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Edit Account Balance', 'route' => 'settings.openingbalance.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Account Balance', 'route' => 'settings.openingbalance.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Account Balance', 'route' => 'settings.openingbalance.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Payment Voucher',
                'route' => null,
                'uniqueName' => "DebitAccount",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All  Payment Voucher', 'route' => 'settings.dabit.voucher.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Add New  Payment Voucher', 'route' => 'settings.dabit.voucher.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Edit  Payment Voucher', 'route' => 'settings.dabit.voucher.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show  Payment Voucher', 'route' => 'settings.dabit.voucher.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy  Payment Voucher', 'route' => 'settings.dabit.voucher.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Approve  Payment Voucher', 'route' => 'settings.dabit.voucher.approve', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Receive Voucher',
                'route' => null,
                'uniqueName' => "CreditAccount",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All  Receive Voucher', 'route' => 'settings.credit.voucher.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Add New  Receive Voucher', 'route' => 'settings.credit.voucher.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Edit  Receive Voucher', 'route' => 'settings.credit.voucher.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show  Receive Voucher', 'route' => 'settings.credit.voucher.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy  Receive Voucher', 'route' => 'settings.credit.voucher.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Approve  Receive Voucher', 'route' => 'settings.credit.voucher.approve', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
            (object) array(
                'label' => 'Contra Voucher',
                'route' => null,
                'uniqueName' => "ContraAccount",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All  Contra Voucher', 'route' => 'settings.contra.voucher.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Add New  Contra Voucher', 'route' => 'settings.contra.voucher.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Edit  Contra Voucher', 'route' => 'settings.contra.voucher.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show  Contra Voucher', 'route' => 'settings.contra.voucher.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy  Contra Voucher', 'route' => 'settings.contra.voucher.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
            (object) array(
                'label' => 'Journal Voucher',
                'route' => null,
                'uniqueName' => "JournalAccount",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All  Journal Voucher', 'route' => 'settings.journal.voucher.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Add New  Journal Voucher', 'route' => 'settings.journal.voucher.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Edit  Journal Voucher', 'route' => 'settings.journal.voucher.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show  Journal Voucher', 'route' => 'settings.journal.voucher.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy  Journal Voucher', 'route' => 'settings.journal.voucher.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            // (object) array(
            //     'label' => 'Balance Transfer',
            //     'route' => null,
            //     'uniqueName' => "BalanceAccount",
            //     'icon' => 'fa fa-cubes',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Transfer Balance', 'route' => 'settings.transfer.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Transfer Balance', 'route' => 'settings.transfer.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Transfer Balance', 'route' => 'settings.transfer.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Transfer Balance', 'route' => 'settings.transfer.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Transfer Balance', 'route' => 'settings.transfer.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),
            // (object) array(
            //     'label' => 'Supplier Payment',
            //     'route' => null,
            //     'uniqueName' => "SupplierAccount",
            //     'icon' => 'fa-credit-card',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Supplier Payment', 'route' => 'payment.supplier.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Supplier Payment', 'route' => 'payment.supplier.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Supplier Payment', 'route' => 'payment.supplier.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Supplier Payment', 'route' => 'payment.supplier.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Supplier Payment', 'route' => 'payment.supplier.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),
            // (object) array(
            //     'label' => 'Customer Payment',
            //     'route' => null,
            //     'uniqueName' => "CustomerAccount",
            //     'icon' => 'fa-credit-card',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Customer Payment', 'route' => 'payment.customer.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Customer Payment', 'route' => 'payment.customer.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Customer Payment', 'route' => 'payment.customer.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Customer Payment', 'route' => 'payment.customer.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Customer Payment', 'route' => 'payment.customer.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),
        )
    ),

    (object) array(
        'label' => 'Assets Management',
        'route' => null,
        'uniqueName' => "Assets",
        'icon' => 'fas fa-coins',
        'parent_id' => 0,
        'submenu' => (object) array(

            (object) array(
                'label' => 'Add Category',
                'route' => null,
                'uniqueName' => "AddAssetscategory",
                'icon' => 'fa-credit-card',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Add New Asset Category', 'route' => 'assets.category.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1)

                )
            ),
            (object) array(
                'label' => 'Manage Category',
                'route' => null,
                'uniqueName' => "ManageAssetscategory",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Category', 'route' => 'assets.category.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Asset Category', 'route' => 'assets.category.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => Null),
                    (object) array('label' => 'Edit  Unit', 'route' => 'assets.category.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show  Unit', 'route' => 'assets.category.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy  Unit', 'route' => 'assets.category.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),


            (object) array(
                'label' => 'Add New Asset',
                'route' => null,
                'uniqueName' => "AddAssets",
                'icon' => 'fa-credit-card',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Add New Asset ', 'route' => 'assets.list.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1)

                )
            ),

            (object) array(
                'label' => 'Manage Asset List',
                'route' => null,
                'uniqueName' => "ManageAssets",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Asset List', 'route' => 'assets.list.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Asset List', 'route' => 'assets.list.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => Null),
                    (object) array('label' => 'Edit  Asset List', 'route' => 'assets.list.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show  Asset List', 'route' => 'assets.list.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy  Asset List', 'route' => 'assets.list.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Add New Asset Warranty',
                'route' => null,
                'uniqueName' => "AddAssetswarranty",
                'icon' => 'fa-credit-card',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Add New Asset Warranty', 'route' => 'assets.warranty.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1)

                )
            ),
            (object) array(
                'label' => 'Manage Asset Warranty',
                'route' => null,
                'uniqueName' => "ManageAssetswarranty",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Asset Warranty', 'route' => 'assets.warranty.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Asset Warranty', 'route' => 'assets.warranty.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => Null),
                    (object) array('label' => 'Edit  Asset Warranty', 'route' => 'assets.warranty.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show  Asset Warranty', 'route' => 'assets.warranty.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy  Asset Warranty', 'route' => 'assets.warranty.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),


            (object) array(
                'label' => 'Add New Destroy',
                'route' => null,
                'uniqueName' => "Adddestroy",
                'icon' => 'fa-credit-card',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Add New Destroy ', 'route' => 'assets.destroy.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1)

                )
            ),
            (object) array(
                'label' => 'Manage Destroy Items',
                'route' => null,
                'uniqueName' => "ManageAssetsdestroy",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Destroy List', 'route' => 'assets.destroy.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Asset List', 'route' => 'assets.destroy.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => Null),
                    (object) array('label' => 'Edit  Asset List', 'route' => 'assets.destroy.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show  Asset List', 'route' => 'assets.destroy.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy  Asset List', 'route' => 'assets.destroy.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

        )
    ),


    (object) array(
        'label' => 'Supplier',
        'route' => null,
        'uniqueName' => "Supplier",
        'icon' => 'fas fa-coins',
        'parent_id' => 0,
        'submenu' => (object) array(
            (object) array(
                'label' => 'Manage Supplier',
                'route' => null,
                'uniqueName' => "ManageSupplier",
                'icon' => 'fa fa-home',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Supplier', 'route' => 'inventorySetup.supplier.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Supplier', 'route' => 'inventorySetup.supplier.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Supplier', 'route' => 'inventorySetup.supplier.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Supplier', 'route' => 'inventorySetup.supplier.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Supplier', 'route' => 'inventorySetup.supplier.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Add  Supplier',
                'route' => null,
                'uniqueName' => "AddSupplier",
                'icon' => 'fa fa-home',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Add New Supplier', 'route' => 'inventorySetup.supplier.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),




        )
    ),

    (object) array(
        'label' => 'Customer ',
        'route' => null,
        'uniqueName' => "Customer",
        'icon' => 'fa fa-users',
        'parent_id' => 0,
        'submenu' => (object) array(

            (object) array(
                'label' => 'Company Group',
                'route' => null,
                'uniqueName' => "CompanyCustomer",
                'icon' => 'fa fa-home',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Company Group', 'route' => 'inventorySetup.customer.group.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Company Group', 'route' => 'inventorySetup.customer.group.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Company Group', 'route' => 'inventorySetup.customer.group.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Company Group', 'route' => 'inventorySetup.customer.group.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Company Group', 'route' => 'inventorySetup.customer.group.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Company',
                'route' => null,
                'uniqueName' => "CompanyCustomer",
                'icon' => 'fa fa-home',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Company', 'route' => 'inventorySetup.customer.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Company', 'route' => 'inventorySetup.customer.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Company', 'route' => 'inventorySetup.customer.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Company', 'route' => 'inventorySetup.customer.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Company', 'route' => 'inventorySetup.customer.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
            // (object) array(
            //     'label' => 'Balance Adjust',
            //     'route' => null,
            //     'uniqueName' => "BalanceCustomer",
            //     'icon' => 'fa fa-home',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Adjust', 'route' => 'inventorySetup.adjust.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Adjust', 'route' => 'inventorySetup.adjust.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Adjust', 'route' => 'inventorySetup.adjust.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Adjust', 'route' => 'inventorySetup.adjust.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Adjust', 'route' => 'inventorySetup.adjust.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),

            // (object) array(
            //     'label' => 'Credit Adjust ',
            //     'route' => null,
            //     'uniqueName' => "CreditCustomer",
            //     'icon' => 'fa fa-home',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Credit Adjust', 'route' => 'inventorySetup.adjustCredit.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Adjust', 'route' => 'inventorySetup.adjustCredit.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Adjust', 'route' => 'inventorySetup.adjustCredit.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Adjust', 'route' => 'inventorySetup.adjustCredit.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Adjust', 'route' => 'inventorySetup.adjustCredit.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),
            // (object) array(
            //     'label' => 'Deposit Adjust',
            //     'route' => null,
            //     'uniqueName' => "DepositCustomer",
            //     'icon' => 'fa fa-home',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Deposit Adjust', 'route' => 'inventorySetup.adjustDeposit.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Adjust', 'route' => 'inventorySetup.adjustDeposit.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Adjust', 'route' => 'inventorySetup.adjustDeposit.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Adjust', 'route' => 'inventorySetup.adjustDeposit.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Adjust', 'route' => 'inventorySetup.adjustDeposit.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),

            // (object) array(
            //     'label' => 'Return Deposit',
            //     'route' => null,
            //     'uniqueName' => "ReturnCustomer",
            //     'icon' => 'fa fa-home',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Return Deposit ', 'route' => 'inventorySetup.returnDeposit.returnindex', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Return Deposit', 'route' => 'inventorySetup.returnDeposit.returncreate', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Return Deposit ', 'route' => 'inventorySetup.returnDeposit.returnedit', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Return Deposit ', 'route' => 'inventorySetup.returnDeposit.returnmodel', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Show Return Deposit ', 'route' => 'inventorySetup.returnDeposit.returnshow', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //     )
            // ),


        )
    ),

    // (object) array(
    //     'label' => 'Cash Book',
    //     'route' => null,
    //     'uniqueName' => "Cash",
    //     'icon' => 'fa fa-suitcase',
    //     'parent_id' => 0,
    //     'submenu' => (object) array(
    //         (object) array(
    //             'label' => 'Account Opn Balance',
    //             'route' => null,
    //             'uniqueName' => "AccountCash",
    //             'icon' => 'fa fa-cubes',
    //             'parent_id' => null,
    //             'childMenu' => (object) array(
    //                 (object) array('label' => 'All Account Balance', 'route' => 'settings.openingbalance.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
    //                 (object) array('label' => 'Add New Account Balance', 'route' => 'settings.openingbalance.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Edit Account Balance', 'route' => 'settings.openingbalance.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Show Account Balance', 'route' => 'settings.openingbalance.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Destroy Account Balance', 'route' => 'settings.openingbalance.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //             )
    //         ),

    //                    (object) array(
    //                        'label' => 'Customer Opn Balance',
    //                        'route' => null,
    //                        'uniqueName' => "CustomerCash",
    //                        'icon' => 'fa fa-cubes',
    //                        'parent_id' => null,
    //                        'childMenu' => (object) array(
    //                            (object) array('label' => 'All Customer Balance', 'route' => 'settings.customerOpening.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
    //                            (object) array('label' => 'Add New Customer Balance', 'route' => 'settings.customerOpening.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                            (object) array('label' => 'Edit Customer Balance', 'route' => 'settings.customerOpening.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                            (object) array('label' => 'Show Customer Balance', 'route' => 'settings.customerOpening.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                            (object) array('label' => 'Destroy Customer Balance', 'route' => 'settings.customerOpening.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                        )
    //                    ),
    //         (object) array(
    //             'label' => 'Balance Transfer',
    //             'route' => null,
    //             'uniqueName' => "BalanceCash",
    //             'icon' => 'fa fa-cubes',
    //             'parent_id' => null,
    //             'childMenu' => (object) array(
    //                 (object) array('label' => 'All Transfer Balance', 'route' => 'settings.transfer.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
    //                 (object) array('label' => 'Add New Transfer Balance', 'route' => 'settings.transfer.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Edit Transfer Balance', 'route' => 'settings.transfer.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Show Transfer Balance', 'route' => 'settings.transfer.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Destroy Transfer Balance', 'route' => 'settings.transfer.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //             )
    //         ),
    //     )
    // ),

    // (object) array(
    //     'label' => 'Expense',
    //     'route' => null,
    //     'uniqueName' => "Expense",
    //     'icon' => 'fa fa-paper-plane',
    //     'parent_id' => 0,
    //     'submenu' => (object) array(
    //         (object) array(
    //             'label' => 'Branch Expense',
    //             'route' => null,
    //             'uniqueName' => "Branch",
    //             'icon' => 'fa fa-cubes',
    //             'parent_id' => null,
    //             'childMenu' => (object) array(
    //                 (object) array('label' => 'All Expense ', 'route' => 'settings.expense.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
    //                 (object) array('label' => 'Add New Expense ', 'route' => 'settings.expense.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Edit Expense ', 'route' => 'settings.expense.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Show Expense ', 'route' => 'settings.expense.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Destroy Expense ', 'route' => 'settings.expense.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //             )
    //         ),

    //         (object) array(
    //             'label' => 'Category',
    //             'route' => null,
    //             'uniqueName' => "Category1",
    //             'icon' => 'fa fa-cubes',
    //             'parent_id' => null,
    //             'childMenu' => (object) array(
    //                 (object) array('label' => 'All Category ', 'route' => 'settings.category.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
    //                 (object) array('label' => 'Add New Category ', 'route' => 'settings.category.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Edit Category ', 'route' => 'settings.category.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Show Category ', 'route' => 'settings.category.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Destroy Category ', 'route' => 'settings.category.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //             )
    //         ),

    //     )
    // ),
    (object) array(
        'label' => 'Project',
        'route' => null,
        'uniqueName' => "Project",
        'icon' => 'fa fa-tree',
        'parent_id' => 0,
        'submenu' => (object) array(
            (object) array(
                'label' => 'Project Setup',
                'route' => null,
                'uniqueName' => "Projectname",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Project ', 'route' => 'project.project.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Project ', 'route' => 'project.project.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Project ', 'route' => 'project.project.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Project ', 'route' => 'project.project.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Project ', 'route' => 'project.project.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Purchase Requisition (PR)',
                'route' => null,
                'uniqueName' => "Purchase4Project",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Purchase Requisition', 'route' => 'inventorySetup.purchaserequisition.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Purchase Requisition', 'route' => 'inventorySetup.purchaserequisition.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Purchase Requisition Approve', 'route' => 'inventorySetup.purchaserequisition.approve', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Purchase Requisition', 'route' => 'inventorySetup.purchaserequisition.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Purchase Requisition', 'route' => 'inventorySetup.purchaserequisition.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Purchase Requisition Invoice', 'route' => 'inventorySetup.purchaserequisition.invoice', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),

                )
            ),


            (object) array(
                'label' => 'Purchase Order (PO)',
                'route' => null,
                'uniqueName' => "Purchase3Project",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Purchase Order', 'route' => 'inventorySetup.purchaseorder.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Purchase Order', 'route' => 'inventorySetup.purchaseorder.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Purchase Order', 'route' => 'inventorySetup.purchaseorder.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Purchase Order', 'route' => 'inventorySetup.purchaseorder.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Approve Purchase Order', 'route' => 'inventorySetup.purchaseorder.approve', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Purchase Order Invoice', 'route' => 'inventorySetup.purchaseorder.invoice', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
            (object) array(
                'label' => 'Purchase (PV)',
                'route' => null,
                'uniqueName' => "Purchase2Project",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Purchase (PV)', 'route' => 'inventorySetup.purchase.pvindex', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Purchase (PV)', 'route' => 'inventorySetup.purchase.pvcreate', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Purchase (PV)', 'route' => 'inventorySetup.purchase.pvedit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Purchase (PV)', 'route' => 'inventorySetup.purchase.pvdestroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Purchase Invoice (PV)', 'route' => 'inventorySetup.purchase.pvinvoice', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'Goods Rcv Note (GRN)',
                'route' => null,
                'uniqueName' => "GoodsProject",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Good Receiver', 'route' => 'inventorySetup.goodrcvnote.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Good Receiver', 'route' => 'inventorySetup.goodrcvnote.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Good Receiver', 'route' => 'inventorySetup.goodrcvnote.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Good Receiver', 'route' => 'inventorySetup.goodrcvnote.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Good Receiver Invoice', 'route' => 'inventorySetup.goodrcvnote.invoice', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            // (object) array(
            //     'label' => 'Project Balance',
            //     'route' => null,
            //     'uniqueName' => "ProjectProject",
            //     'icon' => 'fa fa-cubes',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Balance ', 'route' => 'project.balance.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Balance ', 'route' => 'project.balance.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Balance ', 'route' => 'project.balance.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Balance ', 'route' => 'project.balance.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Balance ', 'route' => 'project.balance.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),

            // (object) array(
            //     'label' => 'Transfer',
            //     'route' => null,
            //     'uniqueName' => "Transfer4Project",
            //     'icon' => 'fa fa-cubes',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Product requisition ', 'route' => 'project.Productrequisition.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Product requisition ', 'route' => 'project.Productrequisition.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Product requisition ', 'route' => 'project.Productrequisition.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Product requisition ', 'route' => 'project.Productrequisition.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Product requisition ', 'route' => 'project.Productrequisition.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),

            // (object) array(
            //     'label' => 'Transfer',
            //     'route' => null,
            //     'uniqueName' => "Transfer3Project",
            //     'icon' => 'fa fa-cubes',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Product requisition ', 'route' => 'project.RequisitionAction.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Approve Product requisition ', 'route' => 'project.RequisitionAction.approve', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Approve Product requisition ', 'route' => 'project.RequisitionAction.approve', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Product requisition ', 'route' => 'project.RequisitionAction.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),

            (object) array(
                'label' => 'Transfer',
                'route' => null,
                'uniqueName' => "Transfer2Project",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Use Project Transfer ', 'route' => 'project.transferproject.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Project Transfer ', 'route' => 'project.transferproject.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Project Transfer ', 'route' => 'project.transferproject.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Project Transfer ', 'route' => 'project.transferproject.invoice', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Project Transfer ', 'route' => 'project.transferproject.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            // (object) array(
            //     'label' => 'Use Product',
            //     'route' => null,
            //     'uniqueName' => "UseProject",
            //     'icon' => 'fa fa-cubes',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Use Product ', 'route' => 'project.productuse.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Use Product ', 'route' => 'project.productuse.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Use Product ', 'route' => 'project.productuse.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Use Product ', 'route' => 'project.productuse.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Use Product ', 'route' => 'project.productuse.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),

            // (object) array(
            //     'label' => 'Return Product',
            //     'route' => null,
            //     'uniqueName' => "ReturnProject",
            //     'icon' => 'fa fa-cubes',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Return Product ', 'route' => 'project.projectreturn.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Return Product ', 'route' => 'project.projectreturn.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Return Product ', 'route' => 'project.projectreturn.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Return Product ', 'route' => 'project.projectreturn.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Return Product ', 'route' => 'project.projectreturn.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),
            // (object) array(
            //     'label' => 'Project Expense',
            //     'route' => null,
            //     'uniqueName' => "ProjectProject",
            //     'icon' => 'fa fa-cubes',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Project Expense ', 'route' => 'project.projectexpense.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Project Expense ', 'route' => 'project.projectexpense.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Project Expense ', 'route' => 'project.projectexpense.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Project Expense ', 'route' => 'project.projectexpense.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Project Expense ', 'route' => 'project.projectexpense.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),
            // (object) array(
            //     'label' => 'Invoice',
            //     'route' => null,
            //     'uniqueName' => "InvoiceProject",
            //     'icon' => 'fa fa-cubes',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Invoice ', 'route' => 'project.invoiceCreate.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add Invoice ', 'route' => 'project.invoiceCreate.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Invoice', 'route' => 'project.invoiceCreate.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Invoice ', 'route' => 'project.invoiceCreate.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Invoice ', 'route' => 'project.invoiceCreate.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),

        )
    ),
    // (object) array(
    //     'label' => 'Service Center',
    //     'route' => null,
    //     'uniqueName' => "Service",
    //     'icon' => 'fa fa-tree',
    //     'parent_id' => 0,
    //     'submenu' => (object) array(
    //         (object) array(
    //             'label' => 'Service',
    //             'route' => null,
    //             'uniqueName' => "Servicecol",
    //             'icon' => 'fa fa-cubes',
    //             'parent_id' => null,
    //             'childMenu' => (object) array(
    //                 (object) array('label' => 'All Service ', 'route' => 'service.service.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
    //                 (object) array('label' => 'Add New Service ', 'route' => 'service.service.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Edit Service ', 'route' => 'service.service.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Show Service ', 'route' => 'service.service.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //                 (object) array('label' => 'Destroy Service ', 'route' => 'service.service.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
    //             )
    //         ),

    //     )
    // ),

    (object) array(
        'label' => 'Report',
        'route' => null,
        'uniqueName' => "Report",
        'icon' => 'far fa-address-book',
        'parent_id' => 0,
        'submenu' => (object) array(
            (object) array(
                'label' => 'Purchase',
                'route' => null,
                'uniqueName' => "Purchase1Report",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => ' Purchase Report', 'route' => 'report.purchase.purchase', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),
            // (object) array(
            //     'label' => 'Production',
            //     'route' => null,
            //     'uniqueName' => "ProductionReport",
            //     'icon' => 'fa fa-th-large',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'Production Report', 'route' => 'report.production.production', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //     )
            // ),
            (object) array(
                'label' => 'Sale',
                'route' => null,
                'uniqueName' => "Sale1Report",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => ' Sales Report', 'route' => 'report.sale.sale', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),
            // (object) array(
            //     'label' => 'Transfer',
            //     'route' => null,
            //     'uniqueName' => "Transfer1Report",
            //     'icon' => 'fa fa-th-large',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'Transfer Report', 'route' => 'report.transfer.transfer', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //     )
            // ),
            (object) array(
                'label' => 'Project',
                'route' => null,
                'uniqueName' => "ProjectReport",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Full Project Report', 'route' => 'report.project.project', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),
            // (object) array(
            //     'label' => 'Employee Ledger',
            //     'route' => null,
            //     'uniqueName' => "EmployeeReport",
            //     'icon' => 'fa fa-th-large',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'Employee Ledger report', 'route' => 'report.employee.salary', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //     )
            // ),
            // (object) array(
            //     'label' => 'Project Expence',
            //     'route' => null,
            //     'uniqueName' => "ProjectReport",
            //     'icon' => 'fa fa-th-large',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => ' Project Expence Report', 'route' => 'report.projectexpence.projectex', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //     )
            // ),
            // (object) array(
            //     'label' => 'Expense',
            //     'route' => null,
            //     'uniqueName' => "ExpenseReport",
            //     'icon' => 'fa fa-th-large',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'Branch Expense Report', 'route' => 'report.expense.expense', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //     )
            // ),
            // (object) array(
            //     'label' => 'Supplier Ledger',
            //     'route' => null,
            //     'uniqueName' => "SupplierLedgerReport",
            //     'icon' => 'fa fa-th-large',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'Supplier Ledger Report', 'route' => 'report.supledger.supledger', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //     )
            // ),
            // (object) array(
            //     'label' => 'Customer Ledger',
            //     'route' => null,
            //     'uniqueName' => "CustomerLedgerReport",
            //     'icon' => 'fa fa-th-large',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'Customer Ledger Report', 'route' => 'report.custledger.custledger', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //     )
            // ),

            // (object) array(
            //     'label' => 'Account Balance',
            //     'route' => null,
            //     'uniqueName' => "AccountReport",
            //     'icon' => 'fa fa-th-large',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'Account Balance Report', 'route' => 'report.account.account', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //     )
            // ),

            (object) array(
                'label' => 'Group Ledger',
                'route' => null,
                'uniqueName' => "Groupledger",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Group Ledger', 'route' => 'report.group-ledger-list', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),

            (object) array(
                'label' => 'Ledger Group Amount',
                'route' => null,
                'uniqueName' => "ledgerGroupReport",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Ledger Group Amount', 'route' => 'report.group-ledger', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),
            (object) array(
                'label' => 'Cash Flow',
                'route' => null,
                'uniqueName' => "CashReport",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Day Book Report', 'route' => 'report.cashflow', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),

            (object) array(
                'label' => 'Retained Earning',
                'route' => null,
                'uniqueName' => "RetainedReport",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Day Book Report', 'route' => 'report.retained_earning', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),

            (object) array(
                'label' => 'Bank Book',
                'route' => null,
                'uniqueName' => "BankReport",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Day Book Report', 'route' => 'report.bank_book', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),

            (object) array(
                'label' => 'Cash Book',
                'route' => null,
                'uniqueName' => "cashbook",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Cash Requisition Report', 'route' => 'report.cashbook.cashbook', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),

            (object) array(
                'label' => 'Leave Report',
                'route' => null,
                'uniqueName' => "leavereport",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Leave Report', 'route' => 'report.leave', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),
            (object) array(
                'label' => 'Cash Requisition',
                'route' => null,
                'uniqueName' => "CashRequisitionreport",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Cash Requisition Report', 'route' => 'report.cashbook.reqcashbook', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),

            (object) array(
                'label' => 'Day Book',
                'route' => null,
                'uniqueName' => "DayReport",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Day Book Report', 'route' => 'report.day.book', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),
            (object) array(
                'label' => 'Ledger',
                'route' => null,
                'uniqueName' => "LedgerReport",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Ledger Report', 'route' => 'report.ledger.ledger', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),
            (object) array(
                'label' => 'Trial Balance',
                'route' => null,
                'uniqueName' => "TrialReport",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Trial Balance Report', 'route' => 'report.trialbalance.trialbalance', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),
            (object) array(
                'label' => 'Income Statement',
                'route' => null,
                'uniqueName' => "IncomeReport",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Income Statement Report', 'route' => 'report.incomestatement.incomestatement', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),
            (object) array(
                'label' => 'Balance Sheet',
                'route' => null,
                'uniqueName' => "BalanceReport",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Balance Sheet Report', 'route' => 'report.balancesheet.balancesheet', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),

            (object) array(
                'label' => 'Stock Detail',
                'route' => null,
                'uniqueName' => "Stock1Report",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Stock Detail', 'route' => 'report.stock.stock', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),
            (object) array(
                'label' => 'Stock Summery',
                'route' => null,
                'uniqueName' => "Stock2Report",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Stock Detail', 'route' => 'report.stock.stocksummery', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),

            // (object) array(
            //     'label' => 'Low stocks',
            //     'route' => null,
            //     'uniqueName' => "LowReport",
            //     'icon' => 'fa fa-th-large',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'Low stocks', 'route' => 'report.stock.lowstocks', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //     )
            // ),
            (object) array(
                'label' => 'Product Ledger',
                'route' => null,
                'uniqueName' => "Product1Report",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'Product Ledger', 'route' => 'report.stock.productledger', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),
            (object) array(
                'label' => 'PR Report',
                'route' => null,
                'uniqueName' => "ReportReport",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'PR Report', 'route' => 'report.purchase.pr', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),
            (object) array(
                'label' => 'PO Report',
                'route' => null,
                'uniqueName' => "ReportReportpo",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'PO Report', 'route' => 'report.purchase.po', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),
            (object) array(
                'label' => 'GRN Report',
                'route' => null,
                'uniqueName' => "GRNReport",
                'icon' => 'fa fa-th-large',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'GRN Report', 'route' => 'report.purchase.grn', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                )
            ),

        )
    ),

    (object) array(
        'label' => 'System Configuration',
        'route' => null,
        'uniqueName' => "System",
        'icon' => 'fa fa-cogs',
        'parent_id' => 0,
        'submenu' => (object) array(
            (object) array(
                'label' => 'Commission Rules',
                'route' => null,
                'uniqueName' => "commissionRules",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Branch', 'route' => 'settings.commissionRules.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Branch', 'route' => 'settings.commissionRules.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Branch', 'route' => 'settings.commissionRules.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Branch', 'route' => 'settings.commissionRules.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Branch', 'route' => 'settings.commissionRules.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
            (object) array(
                'label' => 'Branch',
                'route' => null,
                'uniqueName' => "BranchSystem",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Branch', 'route' => 'settings.branch.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Branch', 'route' => 'settings.branch.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Branch', 'route' => 'settings.branch.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Branch', 'route' => 'settings.branch.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Branch', 'route' => 'settings.branch.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
            (object) array(
                'label' => 'Warehouses',
                'route' => null,
                'uniqueName' => "WarehousesSystem",
                'icon' => 'fa fa-cubes',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Warehouses', 'route' => 'settings.warehouses.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Warehouses', 'route' => 'settings.warehouses.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Warehouses', 'route' => 'settings.warehouses.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Warehouses', 'route' => 'settings.warehouses.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Warehouses', 'route' => 'settings.warehouses.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            // (object) array(
            //     'label' => 'HRM Setup',
            //     'route' => null,
            //     'uniqueName' => "HRMsetupSystem",
            //     'icon' => 'fa fa-cubes',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Hrm', 'route' => 'settings.hrm.setup.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Setup', 'route' => 'settings.hrm.setup.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Branch', 'route' => 'settings.hrm.setup.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Branch', 'route' => 'settings.hrm.setup.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Branch', 'route' => 'settings.hrm.setup.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),

            // (object) array(
            //     'label' => 'Store',
            //     'route' => null,
            //     'uniqueName' => "StoreSystem",
            //     'icon' => 'fa fa-home',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Store', 'route' => 'settings.store.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Store', 'route' => 'settings.store.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Store', 'route' => 'settings.store.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Store', 'route' => 'settings.store.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Store', 'route' => 'settings.store.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),

            (object) array(
                'label' => 'Company Setup ',
                'route' => null,
                'uniqueName' => "CompanySystem",
                'icon' => 'fa fa-eur',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Company', 'route' => 'settings.company.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Company', 'route' => 'settings.company.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Company', 'route' => 'settings.company.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Company', 'route' => 'settings.company.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Company', 'route' => 'settings.company.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            // (object) array(
            //     'label' => 'Fiscal Year ',
            //     'route' => null,
            //     'uniqueName' => "FiscalSystem",
            //     'icon' => 'fa fa-eur',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Fiscal Year', 'route' => 'settings.fiscal_year.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Fiscal Year', 'route' => 'settings.fiscal_year.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Fiscal Year', 'route' => 'settings.fiscal_year.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Fiscal Year', 'route' => 'settings.fiscal_year.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Fiscal Year', 'route' => 'settings.fiscal_year.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),

            // (object) array(
            //     'label' => 'Currency ',
            //     'route' => null,
            //     'uniqueName' => "CurrencySystem",
            //     'icon' => 'fa fa-eur',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Currency', 'route' => 'settings.currency.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Currency', 'route' => 'settings.currency.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Currency', 'route' => 'settings.currency.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Currency', 'route' => 'settings.currency.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Currency', 'route' => 'settings.currency.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),
            // (object) array(
            //     'label' => 'Language ',
            //     'route' => null,
            //     'uniqueName' => "LanguageSystem",
            //     'icon' => 'fa fa-eur',
            //     'parent_id' => null,
            //     'childMenu' => (object) array(
            //         (object) array('label' => 'All Language', 'route' => 'settings.language.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
            //         (object) array('label' => 'Add New Language', 'route' => 'settings.language.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Edit Language', 'route' => 'settings.language.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Show Language', 'route' => 'settings.language.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //         (object) array('label' => 'Destroy Language', 'route' => 'settings.Language.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
            //     )
            // ),

            (object) array(
                'label' => 'Admin Role',
                'route' => null,
                'uniqueName' => "AdminSystem",
                'icon' => 'fa fa-lock',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All Role', 'route' => 'usermanage.userRole.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New Role', 'route' => 'usermanage.userRole.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit Role', 'route' => 'usermanage.userRole.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show Role', 'route' => 'usermanage.userRole.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy Role', 'route' => 'usermanage.userRole.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),

            (object) array(
                'label' => 'User',
                'route' => null,
                'uniqueName' => "UserSystem",
                'icon' => 'fa fa-lock',
                'parent_id' => null,
                'childMenu' => (object) array(
                    (object) array('label' => 'All User', 'route' => 'usermanage.user.index', 'icon' => 'fa fa-dashboard', 'navigate_status' => 1),
                    (object) array('label' => 'Add New User', 'route' => 'usermanage.user.create', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Edit User', 'route' => 'usermanage.user.edit', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Show User', 'route' => 'usermanage.user.show', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                    (object) array('label' => 'Destroy User', 'route' => 'usermanage.user.destroy', 'icon' => 'fa fa-dashboard', 'navigate_status' => null),
                )
            ),
        )
    ),
);

return $parent_menu;
