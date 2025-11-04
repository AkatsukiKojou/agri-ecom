<?php

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\UserReportController;
use App\Http\Controllers\SuperAdminReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UserServiceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProductController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderManagementController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AdminSettingsController;
use Illuminate\Support\Facades\Auth;
use  App\Http\Controllers\User\ServiceApplicationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileActionController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\VerifyController;
use App\Http\Controllers\Auth\VerificationCodeController;
use App\Http\Controllers\Admin\InventoryController;


use App\Models\Products;
use App\Models\Service;
Route::get('/', function () {
    $products = Products::all();
    $services = Service::all();
    $admins = \App\Models\User::where('role', 'admin')->get();
    return view('welcome', compact('products', 'services', 'admins'));
});
// Route::get('/', [UserController::class, 'landing'])->name('landing');
// Route::middleware('auth')->group(function () {
//     Route::get('/products/{id}', [ProductsController::class, 'shows'])->name('user.products.show');
//     Route::get('/services/{id}', [ServiceController::class, 'shows'])->name('user.services.show');
//     Route::get('/profiles/{id}', [ProfileController::class, 'shows'])->name('user.profiles.show');
// });

// Verification code routes (accessible to guests and logged-in users)
Route::get('/verify-code', [VerificationCodeController::class, 'showForm'])->name('verify.code.form');
Route::post('/verify-code', [VerificationCodeController::class, 'verify'])->name('verify.code');
// Route::get('/dashboard', function () {
//     $randomProducts = \App\Models\Products::whereHas('admin', function($q) {
//         $q->where('role', 'admin');
//     })->inRandomOrder()->take(3)->get();
//     return view('dashboard', compact('randomProducts'));
// })->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/superadmin/reportss', [SuperAdminReportController::class, 'index'])->middleware('auth')->name('admin.reports');
Route::post('/admin/users/report', [UserReportController::class, 'store'])->middleware('auth')->name('admin.users.report');

// User chat modal AJAX endpoints
Route::middleware(['auth'])->group(function() {
    Route::get('/user/messages/fetch/{userId}', [App\Http\Controllers\MessageController::class, 'userFetch']);
    Route::post('/user/messages/{userId}', [App\Http\Controllers\MessageController::class, 'userSend']);
});
Route::middleware(['auth'])->group(function() {
    Route::get('/chat/{userId}', [MessageController::class, 'chat'])->name('chat.with');
    Route::post('/chat/send', [MessageController::class, 'send'])->name('chat.send');
});
require __DIR__.'/auth.php';


// User
// Route::middleware(['auth','role:admin'])->group(function(){
//     Route::get('/user/dashboard', [UserController::class, 'index1'])->name('user.dashboard');
// });

// SuperAdminRoute
Route::middleware(['auth','role:super_admin'])->group(function(){
    Route::get('/admin/services/{id}/restore', [SuperAdminController::class, 'restoreService'])->name('superadmin.services.restore');
    Route::get('/admin/services/blocklist', [SuperAdminController::class, 'blocklistServices'])->name('superadmin.services.blocklist');
    Route::get('/admin/services/{id}/block', [SuperAdminController::class, 'blockService'])->name('superadmin.services.block');
    Route::get('/admin/services', [SuperAdminController::class, 'services'])->name('superadmin.services');
    Route::get('/users/blocklist', [SuperAdminController::class, 'userBlocklistView'])->name('superadmin.users.blocklist');
    Route::post('/users/{id}/blocklist', [SuperAdminController::class, 'blocklistUser'])->name('superadmin.users.blocklistUser');
    Route::post('/users/{id}/unblocklist', [SuperAdminController::class, 'unblocklistUser'])->name('superadmin.users.unblocklist');
    Route::get('/admins/blocklist', [SuperAdminController::class, 'blocklistView'])->name('manageadmins.blocklist');
    Route::post('/admins/{id}/blocklist', [SuperAdminController::class, 'blocklistAdmin'])->name('manageadmins.blocklistAdmin');
    Route::post('/admins/{id}/unblocklist', [SuperAdminController::class, 'unblocklistAdmin'])->name('manageadmins.unblocklist');
    Route::get('admin/dashboard', [SuperAdminController::class, 'SuperAdminDashboard'])->name('superadmin.dashboard');
    Route::get('admin/logout', [SuperAdminController::class, 'SuperAdminLogout'])->name('superadmin.logout');

//ManageUsers
    Route::get('/admin/users', [SuperAdminController::class, 'users'])->name('superadmin.users');
    Route::get('/admin/users/{id}', [App\Http\Controllers\SuperAdminController::class, 'showUser'])->name('superadmin.users.show');
Route::delete('/admin/users/{id}', [App\Http\Controllers\SuperAdminController::class, 'destroyUser'])->name('superadmin.users.destroy');

// ManageuAdmin
        Route::get('/admins', [SuperAdminController::class, 'index'])->name('manageadmins.index');
    Route::get('/admins/create', [SuperAdminController::class, 'create'])->name('manageadmins.create');
    Route::post('/admins', [SuperAdminController::class, 'store'])->name('manageadmins.store');
    Route::get('/admins/{id}', [SuperAdminController::class, 'show'])->name('manageadmins.show');
    Route::get('/admins/{id}/edit', [SuperAdminController::class, 'edit'])->name('manageadmins.edit');
    Route::put('/admins/{id}', [SuperAdminController::class, 'update'])->name('manageadmins.update');
    Route::put('/admins/{id}/password', [SuperAdminController::class, 'updateAdminPassword'])->name('manageadmins.password.update');
    Route::delete('/admins/{id}', [SuperAdminController::class, 'destroy'])->name('manageadmins.destroy');

    // MAanageProducts
    Route::get('/admin/products', [SuperAdminController::class, 'products'])->name('superadmin.products');
    Route::get('/admin/products/{id}', [SuperAdminController::class, 'showProduct'])->name('superadmin.products.show');
    Route::delete('/admin/products/{id}', [SuperAdminController::class, 'destroyProduct'])->name('superadmin.products.destroy');
    Route::get('/admin/products/analytics', [SuperAdminController::class, 'productsAnalytics'])->name('superadmin.products.analytics');
    Route::get('/admin/products/{id}/edit', [SuperAdminController::class, 'editProduct'])->name('superadmin.products.edit');
    Route::put('/admin/products/{id}', [SuperAdminController::class, 'updateProduct'])->name('superadmin.products.update');
      // ManageProducts
    Route::get('/admin/products', [\App\Http\Controllers\Superadmin\ProductsController::class, 'index'])->name('superadmin.products');
    Route::post('/admin/products/{id}/blocklist', [\App\Http\Controllers\Superadmin\ProductsController::class, 'blocklist'])->name('superadmin.products.blocklist');
    Route::post('/admin/products/{id}/unblocklist', [\App\Http\Controllers\Superadmin\ProductsController::class, 'unblocklist'])->name('superadmin.products.unblocklist');
    
    // Reports and Analytics
    Route::get('/admin/reports', [SuperAdminController::class, 'reports'])->name('superadmin.reports');
    // System Settings
    Route::get('/admin/settings', [App\Http\Controllers\SuperAdmin\SettingsController::class, 'index'])->name('superadmin.settings');
    Route::get('/admin/support', [App\Http\Controllers\SuperAdmin\SupportController::class, 'index'])->name('superadmin.support');
    // Activity Log
    Route::get('/admin/activitylog', [App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'index'])->name('superadmin.activitylog');
    // Redirect /auth/login to /login for compatibility
});



// AdminRoute
Route::middleware(['auth','role:admin'])->group(function(){
Route::get('lsa/dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
Route::get('/lsa/sales', [App\Http\Controllers\AdminController::class, 'sales'])->name('admin.sales.index');
// notif
Route::post('/notifications/mark-read', function () {
    Auth::user()->unreadNotifications->markAsRead();
    return back();
})->name('notifications.markAllRead');

Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
    ->name('notifications.markAllRead');

Route::post('/profiles/{profile}/follow', [ProfileActionController::class, 'follow'])->name('user.profiles.follow');
Route::post('/profiles/{profile}/like', [ProfileActionController::class, 'like'])->name('user.profiles.like');

//Products
Route::resource('lsa/products', ProductsController::class);
Route::get('/inventory', [ProductsController::class, 'inventory'])->name('products.inventory');
Route::get('/products/{product}/edit', [ProductsController::class, 'edit'])->name('products.edit');
Route::put('admin/products/{id}', [ProductsController::class, 'update'])->name('products.update');
Route::post('/products/{product}/add-stock', [ProductsController::class, 'addStock'])->name('products.addStock');
// Route for archiving a product
Route::delete('{id}/archives', [ArchiveController::class, 'archive'])->name('products.archive');
Route::get('archiveds', [ArchiveController::class, 'index'])->name('products.archived.index');
Route::patch('product/archived/{id}/restore', [ArchiveController::class, 'restore'])->name('products.restore');
Route::delete('/archived-products/{product}/force-delete', [ArchiveController::class, 'forceDelete'])->name('archived-products.forceDelete');
// Route::delete('/archived-products/{product}/force-delete', [ArchiveController::class, 'forceDelete'])->name('archived-products.forceDelete');
Route::post('/lsa/products/bulk-archive', [\App\Http\Controllers\ProductsController::class, 'bulkArchive'])->name('products.bulkArchive');
Route::delete('/lsa/products/{product}/force-delete', [ArchiveController::class, 'forceDelete'])->name('products.forceDelete');
//Order
Route::get('/lsa/orders', [OrderManagementController::class, 'index'])->name('admin.orders.index');
Route::put('/lsa/orders/{order}/update-status', [OrderManagementController::class, 'updateStatus'])->name('admin.orders.updateStatus');

// Inventory (admin) routes
Route::get('/lsa/inventory', [InventoryController::class, 'index'])->name('admin.inventory.index');
Route::post('/lsa/inventory', [InventoryController::class, 'store'])->name('admin.inventory.store');
Route::post('/lsa/inventory/add-stock', [InventoryController::class, 'storeStock'])->name('admin.inventory.storeStock');
// Route::get('/lsa/inventory/{id}/edit', [InventoryController::class, 'edit'])->name('admin.inventory.edit');
// Route::put('/lsa/inventory/{id}', [InventoryController::class, 'update'])->name('admin.inventory.update');
// Route::post('/lsa/inventory/{id}/add-stock', [InventoryController::class, 'addStock'])->name('admin.inventory.addStock');
// Route::post('/lsa/inventory/{id}/reduce-stock', [InventoryController::class, 'reduceStock'])->name('admin.inventory.reduceStock');



// PRofilling
    Route::get('/lsa/profile', [ProfileController::class, 'show'])->name('profiles.show');
    Route::get('/lsa/profile/create', [ProfileController::class, 'create'])->name('profiles.create');
    Route::post('/lsa/profile', [ProfileController::class, 'store'])->name('profiles.store');
     Route::get('lsa/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    // Admin password management (dedicated controller/view)
    Route::get('/lsa/profile/password', [App\Http\Controllers\AdminPasswordController::class, 'edit'])->name('admin.profile.password.edit');
    Route::post('/lsa/profile/password', [App\Http\Controllers\AdminPasswordController::class, 'update'])->name('admin.profile.password.update');
     
    // Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
Route::post('/lsa/profiles/{profile}/gallery', [\App\Http\Controllers\ProfileController::class, 'addGalleryPhoto'])->name('profiles.gallery.add');
Route::patch('/profiles/{profile}/update-photo', [ProfileController::class, 'updatePhoto'])->name('profiles.updatePhoto');
Route::patch('/profiles/{profile}/update-address', [ProfileController::class, 'updateAddress'])->name('profiles.updateAddress');
    // Inline phone/email update routes
    Route::patch('/profiles/{profile}/update-phone', [ProfileController::class, 'updatePhone'])->name('profiles.updatePhone');
    Route::patch('/profiles/{profile}/update-email', [ProfileController::class, 'updateEmail'])->name('profiles.updateEmail');
Route::post('/profiles/{profile}/upload-gcash-qr', [ProfileController::class, 'uploadGcashQr'])->name('profiles.uploadGcashQr');
//Services
Route::resource('lsa/services', ServiceController::class);
Route::post('/lsa/services/bulk-archive', [App\Http\Controllers\ServiceController::class, 'bulkArchive'])->name('services.bulkArchive');
Route::put('/services/{id}', [ServiceController::class, 'update'])->name('services.update');
Route::get('/lsa/services/{id}', [App\Http\Controllers\ServiceController::class, 'show'])->name('admin.services.show');

// or
Route::patch('/services/{id}', [ServiceController::class, 'update']);
// Route::patch('services/archived/{id}/restore', [ArchiveController::class, 'restore'])->name('services.restore');
Route::delete('{id}/archive', [ArchiveController::class, 'archives'])->name('services.archive');
Route::get('archived', [ArchiveController::class, 'indexs'])->name('services.archived.index');
Route::patch('archived/{id}/restores', [ArchiveController::class, 'restores'])->name('services.restore');
Route::delete('/archived-services/{service}/force-delete', [ArchiveController::class, 'forceDeletes'])->name('archived-services.forceDelete');

//zbokking
    Route::get('/lsa/bookings', [AdminBookingController::class, 'index'])->name('admin.bookings.index');
    Route::post('/lsa/bookings/{booking}/approve', [AdminBookingController::class, 'approve'])->name('admin.bookings.approve');
    Route::post('/lsa/bookings/{booking}/reject', [AdminBookingController::class, 'reject'])->name('admin.bookings.reject');

/// Edit and update routes for admin bookings
    Route::get('/lsa/bookings/{booking}/edit', [AdminBookingController::class, 'edit'])->name('admin.bookings.edit');
    Route::put('/lsa/bookings/{booking}', [AdminBookingController::class, 'update'])->name('admin.bookings.update');
    Route::put('/lsa/bookings/{booking}/update-status', [AdminBookingController::class, 'updateStatus'])->name('admin.bookings.updateStatus');
// Admin Reports
    Route::get('/lsa/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/lsa/productsreport', [ReportController::class, 'productsreport'])->name('admin.reports.productsreport');
    Route::get('/lsa/servicesreport', [ReportController::class, 'servicesreport'])->name('admin.reports.servicesreport');



///MEssage
Route::get('/admin/messages/fetch/{userId}', [MessageController::class, 'adminFetch'])->name('admin.messages.fetch');
Route::get('/admin/messages/{user}', [MessageController::class, 'adminChat'])->name('admin.messages.chat');
Route::post('/admin/messages/{userId}', [MessageController::class, 'adminSend'])->name('admin.messages.send');
Route::get('/admin/messages', [MessageController::class, 'adminIndex'])->name('admin.messages');
Route::delete('/admin/messages/{userId}/delete', [MessageController::class, 'adminDeleteConversation'])->name('admin.messages.delete');
Route::get('/admin/messages/unread-count', [MessageController::class, 'unreadCount']);
    // ...other routes
//AdminSetting
 Route::get('/lsa/settings', [AdminSettingsController::class, 'edit'])->name('admin.settings.edit');
    Route::post('/lsa/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');

Route::get('services/trashed', [ServiceController::class, 'trashed'])->name('services.trashed');
Route::post('services/{id}/restore', [ServiceController::class, 'restore'])->name('services.restore');
Route::delete('services/{id}/force-delete', [ServiceController::class, 'forceDelete'])->name('services.forceDelete');

Route::get('/lsa/bookings/search', [App\Http\Controllers\AdminBookingController::class, 'search'])->name('admin.bookings.search');
    // Resource routes for events
    Route::resource('events', App\Http\Controllers\EventController::class);
    Route::post('/lsa/products/{id}/reduce-stock', [ProductsController::class, 'reduceStock'])->name('products.reduceStock');

    // Booking
    // Route::put('/admin/bookings/{booking}/update-status', [AdminBookingController::class, 'updateStatus'])->name('admin.bookings.updateStatus');

});




// USER Group
Route::middleware(['auth','role:user'])->group(function () {
    // Service Booking OTP routes
    Route::get('/user/servicess/{id}/otp', function($id) {
        $bookingData = session('booking_data');
        $email = $bookingData['email'] ?? null;
        if (!$bookingData) {
            return redirect()->route('user.services.show', $id)->with('error', 'No booking data found.');
        }
        return view('user.services.otp', [
            'email' => $email,
            'service_id' => $id,
            'booking_data' => $bookingData,
        ]);
    })->name('user.services.otp.get');
    Route::post('/user/servicess/{id}/otp', [App\Http\Controllers\UserServiceController::class, 'bookingOtp'])->name('user.services.otp');
    Route::post('/user/servicess/{id}/verify-otp', [App\Http\Controllers\UserServiceController::class, 'verifyBookingOtp'])->name('user.services.verifyBookingOtp');
    Route::get('/', [UserController::class, 'index'])->name('dashboard');
    Route::get('/user/dashboard', [UserController::class, 'index'])->name('user.dashboard');


//     // ProfileViews
Route::get('/profiless', [ProfileController::class, 'indexForUsers'])->name('user.profiles.index');
Route::get('/user/profiles/{id}', [ProfileController::class, 'show1'])->name('user.profiles.show');
Route::get('/my-profile', [UserController::class, 'myProfile'])->name('user.myprofile')->middleware('auth');

//MESSAGE
// Route::get('/message/create/{admin_id}', [MessageController::class, 'create'])->name('user.message.create');
// Route::post('/message/store', [MessageController::class, 'store'])->name('user.message.store');

// PRofileLikeandFollow  
Route::post('/profiles/{profile}/follow', [ProfileActionController::class, 'follow'])->name('user.profiles.follow');
Route::delete('/profiles/{profile}/unfollow', [ProfileActionController::class, 'unfollow'])->name('user.profiles.unfollow');
Route::post('/profiles/{profile}/like', [ProfileActionController::class, 'like'])->name('user.profiles.like');
Route::delete('/profiles/{profile}/unlike', [ProfileActionController::class, 'unlike'])->name('user.profiles.unlike');
//     Route::get('/profiless', [UserProfileViewController::class, 'index'])->name('user.profiles.index');
//     // Route::get('/profiless/{id}', [UserProfileViewController::class, 'show'])->name('user.profiles.show');

// Logout
Route::get('user/logout', [UserController::class, 'UserLogout'])->name('user.dashboard');

// //Services
Route::get('/servicess', [ServiceController::class, 'index1'])->name('user.services.index');
// Route::get('/services/{service_id}/apply', [ServiceApplicationController::class, 'apply'])->name('services.apply');
Route::get('/user/services/{id}', [ServiceController::class, 'show1'])->name('user.services.show');
// Bulk archive services
Route::post('/user/servicess/{id}', [ServiceController::class, 'show1'])->name('user.services.show1');

//Products
Route::get('/productss', [ProductsController::class, 'customerproducts'])->name('user.products.index');
Route::get('/products/{product}', [UserProductController::class, 'shows'])->name('product.show');
Route::get('/products/{product}', [UserProductController::class, 'show'])->name('product.show');
// Route::get('/shop/{admin}', [UserProductController::class, 'sellerShop'])->name('user.seller.shop');
Route::put('/user/update-shipping', [UserController::class, 'updateShipping'])->name('user.update.shipping');

//Cart
Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');

// View cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
// Add item to cart
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
// Update quantity
Route::put('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
// Remove item from cart
Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/shop/search', [ProductsController::class, 'search'])->name('shop.search');
Route::get('/user/products/search/{adminId}', [ProductsController::class, 'searchOtherProducts'])->name('user.products.search');// Process selected items for checkout
// Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

// Route::post('/checkout/review', [CartController::class, 'review'])->name('checkout.review');
Route::match(['get', 'post'], '/checkout/review', [CartController::class, 'review'])->name('checkout.review');
Route::post('/checkout/submit', [CartController::class, 'submit'])->name('checkout.submit');
// Route::get('/cart/update', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::match(['get', 'post'], '/checkout/otp', [OrderController::class, 'checkoutOtp'])->name('checkout.otp');
Route::get('/orders', [OrderController::class, 'index1'])->name('user.orders');
Route::post('/checkout/verify-otp', [OrderController::class, 'verifyOtp'])->name('checkout.verifyOtp');

// Buy Now
// Route::post('/orders/buy-now/{product}', [OrderController::class, 'buyNow'])->name('orders.buyNow');
// Route::post('/buy-now/{product}', [App\Http\Controllers\OrderController::class, 'buyNow'])->name('buy.now');
// Route::get('/orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');

// Route::post('/buy-now/{product}', [BuyNowController::class, 'buyNow'])->name('buy.now');
// Route::get('/checkout', [BuyNowController::class, 'checkoutView'])->name('checkout.view');
// Route::post('/checkout/place-order', [BuyNowController::class, 'placeOrder'])->name('checkout.place');
Route::post('/buy-now/{id}', [CartController::class, 'buyNow'])->name('buy.now');
// Route::post('/buy-now/{id}', [\App\Http\Controllers\OrderController::class, 'buyNow'])->name('buy.now');


Route::get('/my-orders', [OrderController::class, 'index'])->name('user.orders');
Route::get('/my-orders/{id}', [OrderController::class, 'show'])->name('user.orders.show');
Route::post('/orders/cancel/{id}', [OrderController::class, 'cancel'])->name('orders.cancel');
Route::get('/orders/history', [OrderController::class, 'history'])->name('user.orders.history');
//USer Profile
Route::get('/profile/edit', [UserController::class, 'edit'])->name('user.profile.edit');
Route::post('/profile/update', [UserController::class, 'update'])->name('user.profile.update');
Route::get('/myprofile', [App\Http\Controllers\UserController::class, 'profile'])->name('profile.show');
Route::post('/profile/update', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('profile.update');
Route::post('/profile/upload-photo', [UserController::class, 'uploadPhoto'])->name('user.profile.uploadPhoto');
Route::get('/user/profile', [UserController::class, 'show'])->name('userprofile.show');
Route::post('/user/profile', [UserController::class, 'update'])->name('userprofile.update');
//Shipping 
Route::get('/user/shipping', [ShippingController::class, 'index'])->name('user.shipping');
// web.php or routes file
Route::get('/user/shipping/{id}/edit', [ShippingController::class, 'edit'])->name('user.shipping.edit');    Route::get('/destroy/{id}', [ShippingController::class, 'destroy'])->name('user.shipping.destroy'); // If you want inline modal, you can use JS instead
Route::post('/user/shipping/{id}/default', [ShippingController::class, 'setDefault'])->name('user.shipping.setDefault');
Route::post('/user/update-shipping', [ShippingController::class, 'update'])->name('user.update-shipping');
Route::delete('user/shipping/{id}', [ShippingController::class, 'destroy'])->name('user.shipping.destroy');
Route::post('/user/shipping', [ShippingController::class, 'store'])->name('user.shipping.store');
Route::put('/user/shipping/{id}', [ShippingController::class, 'update'])->name('user.shipping.update');
Route::put('/user/shipping/{id}/default', [ShippingController::class, 'setDefault'])->name('user.shipping.set-default');
    Route::get('/shipping-addresses', [ShippingController::class, 'index1'])->name('user.shipping.index');
    


  Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
Route::get('/settings/password', [SettingController::class, 'editPassword'])->name('settings.password.edit');
Route::post('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password.update');
//BOoking 
Route::post('/user/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/user/bookings', [BookingController::class, 'index'])->name('bookings.index')->middleware('auth');
Route::post('/user/services/{id}/book', [UserServiceController::class, 'book'])->name('user.services.book');
Route::post('/user/services/{service}/book', [UserServiceController::class, 'store'])->name('user.services.book');
Route::post('/services/{service}/review-booking', [UserServiceController::class, 'reviewBooking'])->name('user.services.reviewBooking');
Route::post('/services/{service}/finalize-booking', [UserServiceController::class, 'finalizeBooking'])->name('user.services.finalizeBooking');
Route::get('/user/bookings', [BookingController::class, 'index'])->name('user.bookings.index');
Route::get('/user/bookings/{booking}/print', [BookingController::class, 'print'])->name('user.bookings.print');
Route::get('/user/bookings/{booking}/pay', [BookingController::class, 'pay'])->name('user.bookings.pay');
Route::get('/user/bookings/{id}/download-receipt', [BookingController::class, 'downloadReceipt'])->name('user.bookings.downloadReceipt');
Route::get('/user/bookings/{booking}', [BookingController::class, 'show'])->name('user.bookings.show')->middleware('auth');

Route::delete('/user/bookings/{booking}', [BookingController::class, 'cancel'])->name('user.bookings.cancel');



// ChatBot
Route::post('/chatbot/message', [ChatbotController::class, 'message'])->name('chatbot.message');
// MEssage
    Route::get('/messages/{adminId}', [MessageController::class, 'fetch'])->name('messages.fetch');
    Route::post('/messages/{adminId}', [MessageController::class, 'send'])->name('messages.send');
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('user.messages');

// routes/web.php
Route::post('/notifications/read', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return response()->json(['success' => true]);
})->name('notifications.read');
    // verify email
// Route::post('/verify-email', [VerifyController::class, 'verifyEmail'])->name('verify.email');
// // Custom registration for modal (address, verification)
});

use App\Http\Controllers\Auth\RegisteredUserController;
Route::post('/register', [RegisteredUserController::class, 'store'])->name('custom.register');

