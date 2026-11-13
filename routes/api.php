<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PartyController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ReceivedPaymentController;

use App\Http\Controllers\Api\PurchaseController;

use App\Http\Controllers\Api\AutoPurchaseController;
use App\Http\Controllers\Api\ManagedUserController;
use App\Http\Controllers\Api\ReportEmailController;

use App\Http\Middleware\EnsureManagedUserToken;

//need//
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\PaymentOutController;
use App\Http\Controllers\Api\PurchaseReturnController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\DebitNoteController;
use App\Http\Controllers\Api\SalesReturnController;
use App\Http\Controllers\Api\CreditNoteController;
use App\Http\Controllers\Api\QuotationController;
use App\Http\Controllers\Api\DeliveryChallanController;
use App\Http\Controllers\Api\ProformaInvoiceController;
use App\Http\Controllers\Api\ReminderSettingController;
use App\Http\Controllers\Api\AutomatedBillController;
use App\Http\Controllers\Api\PartyActivityController;
use App\Http\Controllers\Api\CaSharingController;


Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

//need to add //
Route::post('/managed/send-otp',[AuthController::class, 'sendManagedOtp']);

Route::post('/managed/verify-otp',[AuthController::class, 'verifyManagedOtp']);

Route::middleware([
    'auth:managed-api',
    \App\Http\Middleware\EnsureManagedUserToken::class,
])->get('/managed/me', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'success' => true,
        'user' => $request->user(),
    ]);
});

Route::middleware([
    'auth:managed-api',
    EnsureManagedUserToken::class,
])->prefix('managed')->group(function () {
    Route::get('/parties', [PartyController::class, 'managedIndex']);
    Route::post('/parties', [PartyController::class, 'managedStore']);
    Route::get('/parties/{id}', [PartyController::class, 'managedShow']);
    Route::put('/parties/{id}', [PartyController::class, 'managedUpdate']);

    Route::get('/party-categories', [PartyController::class, 'managedCategories']);
    Route::post('/party-categories', [PartyController::class, 'managedStoreCategory']);
});

Route::middleware([
    'auth:managed-api',
    EnsureManagedUserToken::class,
])->group(function () {
    Route::post('/managed/invoices', [
        InvoiceController::class,
        'managedStore'
    ]);
});

Route::middleware('auth:api')->group(function () {
    // Purchase orders: do not affect inventory until converted to purchases.
    Route::get('/purchase-orders/next-number', [PurchaseOrderController::class, 'nextNumber']);
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index']);
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store']);
    Route::get('/purchase-orders/{id}', [PurchaseOrderController::class, 'show']);
    Route::delete('/purchase-orders/{id}', [PurchaseOrderController::class, 'destroy']);


    // =======================
    // Business Details
    // =======================
    Route::post('/business-details', [AuthController::class, 'updateBusinessDetails']);
    Route::get('/business-info', [AuthController::class, 'businessInfo']);
    Route::post('/verify-gstin', [AuthController::class, 'verifyGSTIN']);

    Route::post('/logout', [AuthController::class, 'logout']);   // ✅
    Route::post('/refresh', [AuthController::class, 'refresh']); // ✅ NEW

    //need to add//

    Route::post('/business-industry',[AuthController::class, 'updateIndustry']);
    Route::get('/business-details',[AuthController::class, 'businessDetails']);

    Route::post('/business-type',[AuthController::class, 'updateBusinessType']);

    Route::post('/business-tax-identifiers',[AuthController::class, 'updateBusinessTaxIdentifiers']);
    Route::post('/business-address',[AuthController::class, 'updateBusinessAddress']);

    Route::post('/business-email',[AuthController::class, 'updateBusinessEmail']);
    Route::post('/business-custom-fields',[AuthController::class, 'updateBusinessCustomFields']);


    Route::get('/business-phone', [AuthController::class,'businessPhone',]);

    Route::post('/business-phone', [AuthController::class,'updateBusinessPhone',]);
    Route::post('/business-name',[AuthController::class, 'updateBusinessName']);
    // Terms and Conditions
    Route::get('/business-terms',[AuthController::class, 'businessTerms']);

    Route::post('/business-terms',[AuthController::class, 'updateBusinessTerms']);

// Signature Settings
    Route::get('/business-signature',[AuthController::class, 'businessSignature']);
    Route::post('/business-signature',[AuthController::class, 'updateBusinessSignature']);

    // Discount Type Settings
    Route::get('/discount-type',[AuthController::class, 'discountType']);
    Route::post('/discount-type',[AuthController::class, 'updateDiscountType']);

    // Invoice Theme & Color
    Route::get('/invoice-theme', [AuthController::class, 'invoiceTheme']);
    Route::post('/invoice-theme', [AuthController::class, 'updateInvoiceTheme']);

    // Invoice Number Prefix
    Route::get('/invoice-number-prefix',[AuthController::class, 'invoiceNumberPrefix']);
    Route::post('/invoice-number-prefix',[AuthController::class, 'updateInvoiceNumberPrefix']);

    Route::get('/bill-wise-profit', [InvoiceController::class, 'billWiseProfit']);
    Route::get('/profit-loss',[InvoiceController::class, 'profitLoss']);

    Route::get('/balance-sheet',[InvoiceController::class, 'balanceSheet']);
    Route::get('/daybook', [InvoiceController::class, 'daybook']);

    // Email generated reports to the registered CA
   Route::post('/reports/email-to-ca',[ReportEmailController::class, 'emailReportToCA']);

    // =======================
// Managed Users API
// =======================
    Route::get('/managed-users', [ManagedUserController::class,'index',]);

    Route::post('/managed-users', [ManagedUserController::class,'store',]);





    Route::get('/me', function () {
        return auth()->user();
    });

    // =======================
    // Parties API
    // =======================
    Route::get('/parties', [PartyController::class, 'index']);
    Route::post('/parties', [PartyController::class, 'store']);

    // =======================
    // Party Invoices (Amount Screen)
    // =======================
    Route::get('/parties/{id}/invoices', [PartyController::class, 'invoices']);

    // 🟢 NEW: All invoices (paid + partial + unpaid)
    Route::get('/parties/{id}/all-invoices', [PartyController::class, 'allInvoices']);

    // 🔄 Party Transactions (Invoices + Payments)
    Route::get('/parties/{id}/transactions', [PartyController::class, 'transactions']);

    Route::get('/parties/{id}', [PartyController::class, 'show']);

    Route::put('/parties/{id}', [PartyController::class, 'update']);

    Route::delete('/parties/{id}', [PartyController::class, 'destroy']);

     Route::get('/dashboard/totals', [PartyController::class, 'dashboardTotals']);

     Route::get('/dashboard/transactions', [PartyController::class, 'allTransactions']);

     //need to add//

    Route::get('/party-categories', [PartyController::class,'categories']);

    Route::post('/party-categories', [PartyController::class,'storeCategory']);



    // =======================
    // Items API
    // =======================
    Route::get('/items', [ItemController::class, 'index']);
    Route::post('/items', [ItemController::class, 'store']);

    //need//
    // NEW: Party Report by Item
    Route::get('/party-report-by-item',[ItemController::class, 'partyReportByItem']);
    Route::get('/party-statement/{id}',[PartyController::class, 'partyStatement']);
    Route::get('/party-wise-outstanding',[PartyController::class, 'partyWiseOutstanding']);
    Route::get('/item-report-by-party',[ItemController::class, 'itemReportByParty']);
    Route::get('/items-sale-summary',[ItemController::class, 'itemsSaleSummary']);
    Route::get('/items/{id}/timeline', [ItemController::class, 'itemTimeline']);
    Route::get('/gst-purchase-report',[PurchaseController::class, 'gstPurchaseReport']);
    Route::get('/hsn-sales-summary',[InvoiceController::class, 'hsnSalesSummary']);

    // =======================
    // Expense APIs
    // =======================
    //need//
    Route::get('/expense-categories', [ExpenseController::class,'categories']);
    Route::post('/expense-categories', [ExpenseController::class,'storeCategory']);
    Route::get('/expenses/next-number', [ExpenseController::class,'nextNumber']);
    Route::get('/expenses', [ExpenseController::class,'index']);
    Route::post('/expenses', [ExpenseController::class,'store']);
    Route::get('/expenses/{id}', [ExpenseController::class,'show']);
    Route::put('/expense-categories/{id}', [ExpenseController::class, 'updateCategory']);
    Route::delete('/expense-categories/{id}', [ExpenseController::class, 'destroyCategory']);
    Route::get('/expense-items', [ExpenseController::class, 'items']);
    Route::post('/expense-items', [ExpenseController::class, 'storeItem']);
    Route::get('/expense-category-report',[ExpenseController::class, 'categoryReport']);

   // ==========================================
   // PAYMENT OUT APIs
   // ==========================================
   //need//
   Route::get('/payment-outs/next-number',[PaymentOutController::class, 'nextNumber']);
   Route::get('/payment-outs/unpaid-purchases',[PaymentOutController::class, 'unpaidPurchases']);
   Route::get('/payment-outs/party-balance',[PaymentOutController::class, 'partyBalance']);
   Route::post('/payment-outs',[PaymentOutController::class, 'store']);
   Route::get('/payment-outs',[PaymentOutController::class, 'index']);
   Route::get('/payment-outs/{id}',[PaymentOutController::class, 'show']);



  Route::get('/purchase-returns/last-number', [PurchaseReturnController::class, 'lastNumber']);
  Route::get('/purchase-returns/source-purchases/{id}', [PurchaseReturnController::class, 'sourcePurchase']);
  Route::post('/purchase-returns', [PurchaseReturnController::class, 'store']);
  Route::get('/purchase-returns/{id}', [PurchaseReturnController::class, 'show']);

  // Debit Note APIs
  Route::get('/debit-notes/next-number',[DebitNoteController::class, 'nextNumber']);
  Route::get('/debit-notes/purchases',[DebitNoteController::class, 'purchases']);
  Route::get('/debit-notes/purchases/{id}',[DebitNoteController::class, 'purchaseDetails']);
  Route::get('/debit-notes',[DebitNoteController::class, 'index']);
  Route::post('/debit-notes',[DebitNoteController::class, 'store']);
  Route::get('/debit-notes/{id}',[DebitNoteController::class, 'show']);
  Route::delete('/debit-notes/{id}',[DebitNoteController::class, 'destroy']);


  Route::get('/sales-returns/next-number',[SalesReturnController::class, 'nextNumber']);
  Route::get('/sales-returns/invoices',[SalesReturnController::class, 'invoices']);
  Route::get('/sales-returns/invoices/{id}',[SalesReturnController::class, 'invoiceDetails'])->whereNumber('id');
  Route::get('/sales-returns',[SalesReturnController::class, 'index']);
  Route::post('/sales-returns',[SalesReturnController::class, 'store']);
  Route::get('/sales-returns/{id}',[SalesReturnController::class, 'show'])->whereNumber('id');



  Route::get('/credit-notes/next-number',[CreditNoteController::class, 'nextNumber']);
  Route::get('/credit-notes/invoices',[CreditNoteController::class, 'invoices']);
  Route::get('/credit-notes/invoices/{id}',[CreditNoteController::class, 'invoiceDetails'])->whereNumber('id');
  Route::get('/credit-notes',[CreditNoteController::class, 'index']);
  Route::post('/credit-notes',[CreditNoteController::class, 'store']);
  Route::get('/credit-notes/{id}',[CreditNoteController::class, 'show'])->whereNumber('id');


  Route::get('/quotations/next-number',[QuotationController::class, 'nextNumber']);
  Route::get('/quotations',[QuotationController::class, 'index']);
  Route::post('/quotations',[QuotationController::class, 'store']);
  Route::get('/quotations/{id}',[QuotationController::class, 'show'])->whereNumber('id');
  Route::put('/quotations/{id}',[QuotationController::class, 'update'])->whereNumber('id');
  Route::delete('/quotations/{id}',[QuotationController::class, 'destroy'])->whereNumber('id');


  Route::get('/delivery-challans/next-number',[DeliveryChallanController::class, 'nextNumber']);
  Route::get('/delivery-challans',[DeliveryChallanController::class, 'index']);
  Route::post('/delivery-challans',[DeliveryChallanController::class, 'store']);
  Route::get('/delivery-challans/{id}',[DeliveryChallanController::class, 'show'])->whereNumber('id');
  Route::put('/delivery-challans/{id}',[DeliveryChallanController::class, 'update'])->whereNumber('id');
  Route::delete('/delivery-challans/{id}',[DeliveryChallanController::class, 'destroy'])->whereNumber('id');


  Route::get('/proforma-invoices/next-number',[ProformaInvoiceController::class, 'nextNumber']);
  Route::get('/proforma-invoices',[ProformaInvoiceController::class, 'index']);
  Route::post('/proforma-invoices',[ProformaInvoiceController::class, 'store']);
  Route::get('/proforma-invoices/{id}',[ProformaInvoiceController::class, 'show'])->whereNumber('id');
  Route::put('/proforma-invoices/{id}',[ProformaInvoiceController::class, 'update'])->whereNumber('id');
  Route::delete('/proforma-invoices/{id}',[ProformaInvoiceController::class, 'destroy'])->whereNumber('id');


  Route::get('/reminder-settings',[ReminderSettingController::class, 'index']);
  Route::put('/reminder-settings',[ReminderSettingController::class, 'update']);

  Route::get('/automated-bills', [AutomatedBillController::class, 'index']);
  Route::post('/automated-bills', [AutomatedBillController::class, 'store']);
  Route::get('/automated-bills/{id}', [AutomatedBillController::class, 'show']);
  Route::put('/automated-bills/{id}', [AutomatedBillController::class, 'update']);
  Route::post('/automated-bills/{id}/pause', [AutomatedBillController::class, 'pause']);
  Route::post('/automated-bills/{id}/resume', [AutomatedBillController::class, 'resume']);
  Route::post('/automated-bills/{id}/stop', [AutomatedBillController::class, 'stop']);

  Route::post('items/{id}/adjust-stock',[ItemController::class, 'adjustStock']);
  Route::get('/items/{id}/party-prices',[ItemController::class, 'partyPrices']);
  Route::post('/items/{id}/party-prices',[ItemController::class, 'savePartyPrice']);

  Route::get('parties/{partyId}/activities', [PartyActivityController::class, 'index']);
  Route::post('parties/{partyId}/activities', [PartyActivityController::class, 'store']);
  Route::put('parties/{partyId}/activities/{activityId}', [PartyActivityController::class, 'update']);
  Route::delete('parties/{partyId}/activities/{activityId}', [PartyActivityController::class, 'destroy']);

  Route::get('/ca-sharing', [CaSharingController::class, 'show']);
  Route::put('/ca-sharing', [CaSharingController::class, 'update']);

Route::get('/activities', [PartyActivityController::class, 'generalIndex']);
Route::post('/activities', [PartyActivityController::class, 'generalStore']);
Route::put('/activities/{activityId}', [PartyActivityController::class, 'generalUpdate']);
Route::delete('/activities/{activityId}', [PartyActivityController::class, 'generalDestroy']);






    Route::get('/items/{id}/timeline', [ItemController::class, 'timeline']);
    Route::put('/items/{id}', [ItemController::class, 'update']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);
    Route::get('/stock-summary', [ItemController::class, 'stockSummary']);
    //need to add in server
    Route::get('/stock-summary-pdf', [ItemController::class, 'stockSummaryPdf']);

    Route::get('/items/{id}', [ItemController::class, 'show']);






    // =======================
    // Invoices API
    // =======================
    Route::post('/invoices', [InvoiceController::class, 'store']);

    Route::get('/invoices/last-number', [InvoiceController::class, 'lastNumber']);

    Route::get('/sales-summary', [InvoiceController::class, 'salesSummary']);

    //need to add in server
    Route::get('/sales-summary-pdf', [InvoiceController::class, 'salesSummaryPdf']);

    Route::get('/cash-bank-summary', [InvoiceController::class, 'cashBankSummary']);

    Route::get('/cash-bank-details', [InvoiceController::class, 'cashBankDetails']);

    Route::get('/invoices/{id}', [InvoiceController::class, 'show']);




    // ✅ RECEIVED PAYMENT

    // need to add
    Route::get('/payments', [ReceivedPaymentController::class, 'index']);

    Route::post('/payments', [ReceivedPaymentController::class, 'store']);

    Route::get('/payments/next-number', [ReceivedPaymentController::class, 'nextNumber']);

    Route::get('/payments/{id}', [ReceivedPaymentController::class, 'show']);


    ///add to server

    Route::get('/gst-report', [InvoiceController::class, 'gstReport']);

    Route::get('/gst-report-pdf', [InvoiceController::class, 'gstReportPdf']);

    Route::post('/purchases', [PurchaseController::class, 'store']);

    Route::get('/purchases', [PurchaseController::class, 'index']);

    Route::post('/scan-bill', [PurchaseController::class, 'scanBill']);

    Route::post('/auto-purchase', [AutoPurchaseController::class, 'storeAuto']);

    Route::get('/purchases/{id}', [PurchaseController::class, 'show']);




});

///add to server

Route::get('/invoice-payment-link/{id}', function ($id) {
    $invoice = \App\Models\Invoice::findOrFail($id);

    return response()->json([
        'invoice' => [
            'id' => $invoice->id,
            'payment_link' => $invoice->payment_link ?? null,
        ]
    ]);
});
