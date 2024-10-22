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
}
