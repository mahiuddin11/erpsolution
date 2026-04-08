<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Xml\Totals;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        ChartOfAccount::truncate();

        $insert = [
            [
                'account_name' => 'ASSETS',
                'accountCode' => "",
                'unique_identifier' => 1,
                'status' => 'Active',
                'parent_id' => 0,
                'submenu' => [
                    [
                        'account_name' => 'FIXED ASSET',
                        'accountCode' => "",
                        'unique_identifier' => 2,
                        'status' => 'Active',
                        'parent_id' => 0,
                        'submenu' => []
                    ],
                    [
                        'account_name' => 'CURRENT ASSETS',
                        'accountCode' => "",
                        'unique_identifier' => 3,
                        'status' => 'Active',
                        'parent_id' => 0,
                        'submenu' => [
                            [
                                'account_name' => 'ADVANCE, DEPOSITS AND PRE-PAYMENTS',
                                'accountCode' => "",
                                'unique_identifier' => 4,
                                'status' => 'Active',
                                'parent_id' => 0,
                                'submenu' => []
                            ],
                            [
                                'account_name' => 'ACCOUNTS RECEIVABLE',
                                'accountCode' => "",
                                'unique_identifier' => 5,
                                'status' => 'Active',
                                'parent_id' => 0,
                                'submenu' => []
                            ],
                            [
                                'account_name' => 'CASH AND CASH EQUIVALENTS',
                                'accountCode' => "",
                                'unique_identifier' => 6,
                                'status' => 'Active',
                                'parent_id' => 0,
                                'submenu' => [
                                    [
                                        'account_name' => 'Cash in Hand',
                                        'accountCode' => "",
                                        'unique_identifier' => 7,
                                        'status' => 'Active',
                                        'parent_id' => 0,
                                        'submenu' => []
                                    ],
                                    [
                                        'account_name' => 'Cash in Bank',
                                        'accountCode' => "",
                                        'unique_identifier' => 8,
                                        'status' => 'Active',
                                        'parent_id' => 0,
                                        'submenu' => []
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'account_name' => 'Equity & Liabilities',
                'accountCode' => "",
                'unique_identifier' => 9,
                'status' => 'Active',
                'parent_id' => 0,
                'submenu' => [
                    [
                        'account_name' => 'Equity',
                        'accountCode' => "",
                        'unique_identifier' => 10,
                        'status' => 'Active',
                        'parent_id' => 0,
                        'submenu' => [
                            [
                                'account_name' => 'Share capital',
                                'accountCode' => "",
                                'unique_identifier' => 11,
                                'status' => 'Active',
                                'parent_id' => 0,
                                'submenu' => []
                            ],
                            [
                                'account_name' => 'Reserve and Surplus',
                                'accountCode' => "",
                                'unique_identifier' => 12,
                                'status' => 'Active',
                                'parent_id' => 0,
                                'submenu' => [
                                    [
                                        'account_name' => 'Retained earnings',
                                        'accountCode' => "",
                                        'unique_identifier' => 13,
                                        'status' => 'Active',
                                        'parent_id' => 0,
                                        'submenu' => []
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'account_name' => 'Long Term Liabilities',
                        'accountCode' => "",
                        'unique_identifier' => 14,
                        'status' => 'Active',
                        'parent_id' => 0,
                        'submenu' => []
                    ],
                    [
                        'account_name' => 'Current Liabilities',
                        'accountCode' => "",
                        'unique_identifier' => 15,
                        'status' => 'Active',
                        'parent_id' => 0,
                        'submenu' => [
                            [
                                'account_name' => 'Accounts Payable',
                                'accountCode' => "",
                                'unique_identifier' => 16,
                                'status' => 'Active',
                                'parent_id' => 0,
                                'submenu' => []
                            ]
                        ]
                    ]
                ]
            ],
            [
                'account_name' => 'INCOME',
                'accountCode' => "",
                'unique_identifier' => 17,
                'status' => 'Active',
                'parent_id' => 0,
                'submenu' => [
                    [
                        'account_name' => 'Sales',
                        'accountCode' => "",
                        'unique_identifier' => 18,
                        'status' => 'Active',
                        'parent_id' => 0,
                        'submenu' => []
                    ],
                    [
                        'account_name' => 'Direct Income',
                        'accountCode' => "",
                        'unique_identifier' => 24,
                        'status' => 'Active',
                        'parent_id' => 0,
                        'submenu' => []
                    ],
                    [
                        'account_name' => 'Indirect Income',
                        'accountCode' => "",
                        'unique_identifier' => 25,
                        'status' => 'Active',
                        'parent_id' => 0,
                        'submenu' => []
                    ],
                ]
            ],
            [
                'account_name' => 'EXPENSES',
                'accountCode' => "",
                'unique_identifier' => 19,
                'status' => 'Active',
                'parent_id' => 0,
                'submenu' => [
                    [
                        'account_name' => 'Direct Expenses',
                        'accountCode' => "",
                        'unique_identifier' => 20,
                        'status' => 'Active',
                        'parent_id' => 0,
                        'submenu' => []
                    ],
                    [
                        'account_name' => 'Indirect Expenses',
                        'accountCode' => "",
                        'unique_identifier' => 21,
                        'status' => 'Active',
                        'parent_id' => 0,
                        'submenu' => []
                    ],
                    [
                        'account_name' => 'Purchase',
                        'accountCode' => "",
                        'unique_identifier' => 22,
                        'status' => 'Active',
                        'parent_id' => 0,
                        'submenu' => []
                    ],
                    [
                        'account_name' => 'Profit & Loss Accounting',
                        'accountCode' => "",
                        'unique_identifier' => 23,
                        'status' => 'Active',
                        'parent_id' => 0,
                        'submenu' => []
                    ]
                ]
            ]
        ];

        // Totals array 25

        function insertAccounts($accounts, $parent_id = 0)
        {
            foreach ($accounts as $account) {
                $submenu = $account['submenu'];
                unset($account['submenu']);
                $account['parent_id'] = $parent_id;
                $insertedAccount = ChartOfAccount::create($account);
                if (!empty($submenu)) {
                    insertAccounts($submenu, $insertedAccount->id);
                }
            }
        }

        insertAccounts($insert);
    }
}
