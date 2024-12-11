<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function showLedgerAccounts()
    {
        $chartOfAccounts = ChartOfAccount::select('id', 'account_head', 'account_name','status')->where('status', 1)
        ->get();
        $suppliers = Supplier::getAllsuppliersWithBalance();
        return view('admin.accounts.ledger.accountname', compact('chartOfAccounts','suppliers'));
    }

    public function asset($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Purchase', 'Payment'])->sum('at_amount');
        $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Sold', 'Deprication'])->sum('at_amount');
        $totalBalance = $totalDrAmount - $totalCrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        return view('admin.accounts.ledger.asset', compact('data', 'totalBalance','accountName'));
    }

    public function expense($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Current', 'Prepaid', 'Due Adjust'])->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Current', 'Prepaid', 'Due Adjust'])->sum('at_amount');
        $totalBalance = $totalDrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        return view('admin.accounts.ledger.expense', compact('data', 'totalBalance','accountName'));
    }

    public function income($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Current', 'Advance Adjust', 'Refund'])->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Refund'])->sum('at_amount');
        $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Current', 'Advance Adjust'])->sum('at_amount');
        $totalBalance =  $totalCrAmount - $totalDrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        return view('admin.accounts.ledger.income', compact('data', 'totalBalance','accountName'));
    }

    public function liability($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Received'])->sum('at_amount');
        $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Payment'])->sum('at_amount');
        $totalBalance = $totalDrAmount - $totalCrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        return view('admin.accounts.ledger.liability', compact('data', 'totalBalance','accountName'));
    }

    public function equity($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Payment'])->sum('at_amount');
        $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('transaction_type', ['Received'])->sum('at_amount');
        $totalBalance =  $totalCrAmount - $totalDrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        return view('admin.accounts.ledger.equity', compact('data', 'totalBalance','accountName'));
    }

    public function shipmentCostLedger($slug)
    {
        if ($slug == 'cnf-cost') {
            $costColumn = 'cnf_cost';
            $paymentTypeColumn = 'cnf_payment_type';
            $accountName = 'CNF Cost';
        } elseif ($slug == 'import-duties-taxes') {
            $costColumn = 'import_duties_tax';
            $paymentTypeColumn = 'import_payment_type';
            $accountName = 'Import Duties & Taxes';
        } elseif ($slug == 'warehouse-handling-costs') {
            $costColumn = 'warehouse_and_handling_cost';
            $paymentTypeColumn = 'warehouse_payment_type';
            $accountName = 'Warehouse & Handling Costs';
        } elseif ($slug == 'other-costs') {
            $costColumn = 'other_cost';
            $paymentTypeColumn = 'other_payment_type';
            $accountName = 'Other Costs';
        } else {
            abort(404, 'Invalid cost type.');
        }
    
        $transactions = Transaction::with('shipment')
        ->where('table_type', 'Shipment')
        ->latest()
        ->get();

        $transactions->each(function ($transaction) use ($costColumn, $paymentTypeColumn) {
            $transaction->expense_cost = $transaction->shipment->$costColumn;
            $transaction->paymentType = $transaction->shipment->$paymentTypeColumn;
        });
    
        $totalBalance = $transactions->sum('expense_cost');

        return view('admin.accounts.ledger.shipment_expense', compact('transactions', 'totalBalance', 'accountName'));
    }
    
    public function purchaseLedger()
    {
        
        $data = Transaction::orderBy('id', 'desc')
                                ->whereIn('table_type', ['Purchase'])->whereIn('payment_type', ['Credit'])->select('id', 'amount', 'date', 'description','ref','transaction_type','table_type', 'discount','at_amount','document','payment_type')
                                ->get();

        $totalDrAmount = Transaction::whereIn('table_type', ['Purchase'])->whereIn('payment_type', ['Credit'])->sum('at_amount');

        $totalCrAmount = 0;

        // dd($totalCrAmount);
        $totalBalance = $totalDrAmount - $totalCrAmount;
        return view('admin.accounts.ledger.purchase', compact('data','totalBalance'));
    }

    public function salesLedger()
    {
        
        $data = Transaction::orderBy('id', 'desc')
                                ->whereIn('table_type', ['Sales'])->select('id', 'amount', 'date', 'description','ref','transaction_type','table_type', 'discount','at_amount','document','payment_type')->whereIn('transaction_type', ['Current'])
                                ->get();

        $totalDrAmount = Transaction::whereIn('table_type', ['Sales'])->whereIn('payment_type', ['Credit'])->sum('at_amount');

        $totalCrAmount = 0;

        // dd($totalCrAmount);
        $totalBalance = $totalDrAmount - $totalCrAmount;
        return view('admin.accounts.ledger.sales', compact('data','totalBalance'));
    }
}
