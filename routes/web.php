<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeOptionController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\VariantNameController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCodeTypeController;
use App\Http\Controllers\CustomFieldTypeController;
use App\Http\Controllers\Shop\HomeController as ShopHomeController;
use App\Http\Controllers\Shop\ProductController as ShopProductController;
use App\Http\Controllers\Shop\CartController as ShopCartController;
use App\Http\Controllers\Shop\EnquiryController as ShopEnquiryController;
use App\Http\Controllers\Shop\PageController as ShopPageController;
use App\Http\Controllers\Admin\EnquiryController as AdminEnquiryController;
use App\Http\Controllers\Admin\CmsPageController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\Admin\SmtpSettingController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\HomepageSectionController;

#test
#test222
$front_back_middleware = ['auth', 'permission:frontend_shop|admin'];
$back_end_middleware = ['auth', 'permission:admin'];

/*
|--------------------------------------------------------------------------
| Customer / Public Website (root)
|--------------------------------------------------------------------------
*/
Route::name('shop.')->group(function () {
    Route::get('/', [ShopHomeController::class, 'index'])->name('home');
    Route::get('/about', [ShopHomeController::class, 'about'])->name('about');
    Route::get('/contact', [ShopHomeController::class, 'contact'])->name('contact');
    Route::post('/contact', [ShopPageController::class, 'contactSubmit'])->middleware('throttle:10,1')->name('contact.submit');

    Route::get('/products', [ShopProductController::class, 'index'])->name('products');
    Route::get('/search', [ShopProductController::class, 'search'])->name('search');
    Route::get('/product/{slug}', [ShopProductController::class, 'show'])->name('product');

    Route::get('/cart', [ShopCartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [ShopCartController::class, 'add'])->middleware('throttle:60,1')->name('cart.add');
    Route::post('/cart/update', [ShopCartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [ShopCartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [ShopCartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/count', [ShopCartController::class, 'count'])->name('cart.count');

    Route::get('/enquiry', [ShopEnquiryController::class, 'create'])->name('enquiry.create');
    Route::post('/enquiry', [ShopEnquiryController::class, 'store'])->middleware('throttle:8,1')->name('enquiry.store');
    Route::get('/enquiry/thank-you/{enquiryNumber}', [ShopEnquiryController::class, 'thankYou'])->name('enquiry.thankyou');

    Route::get('/page/{slug}', [ShopPageController::class, 'show'])->name('page');
});

// Legacy /shop URLs → root customer website
Route::redirect('/shop', '/', 301);
Route::get('/shop/{any}', function (string $any) {
    return redirect('/'.$any, 301);
})->where('any', '.*');

/*
|--------------------------------------------------------------------------
| Salesman Frontend
|--------------------------------------------------------------------------
*/
Route::middleware($front_back_middleware)->prefix('salesman')->group(function () {
  Route::get('/', [FrontController::class, 'index'])->name('front.index');
  Route::get('/search', [FrontController::class, 'globalSearch'])->name('front.search');
  Route::get('/product/{slug}', [FrontController::class, 'product'])->name('front.product');
  Route::post('/add-to-cart', [FrontController::class, 'addToCart'])->name('front.add_to_cart');
  Route::get('/category/{id}', [FrontController::class, 'category'])->name('front.category');
  Route::get('/checkout', [FrontController::class, 'checkout'])->name('front.checkout');
  Route::post('place_order', [FrontController::class, 'place_order'])->name('front.place.order');
  Route::get('/order_placed', [FrontController::class, 'order_placed']);

  Route::get('/order', [FrontController::class, 'order'])->name('front.order');
  Route::get('/order-detail/{id}', [FrontController::class, 'order_detail'])->name('front.order_detail');

  Route::get('/cart', [FrontController::class, 'cart'])->name('front.view.cart');
  Route::post('/remove-cart-item', [FrontController::class, 'removeCartItem'])
    ->name('front.cart.remove');
  Route::post('/update-cart-qty', [FrontController::class, 'updateCartQty'])
    ->name('front.cart.updateQty');
  Route::get('/order-success/{order}', [FrontController::class, 'orderSuccess'])
    ->name('front.order.success');
});

Route::middleware($back_end_middleware)->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

  // Route::get('/admin/dashboard', function () {
  //   return view('admin.pages.dashboard');
  // })->name('admin.dashboard');
});


Route::get('/test', function () {
  return view('admin.test');
});





Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::get('/register', function () {
  return view('admin.pages.register');
});
// Route::get('/login', function () {
//   return view('admin.pages.login');
// });

//only those have manage_user permission will get access
Route::middleware($back_end_middleware)->group(function () {
  Route::get('/users', [UserController::class, 'index'])->name('users.index');
  Route::get('/user/get-list', [UserController::class, 'getUserList']);
  Route::get('/user/create', [UserController::class, 'create']);
  Route::post('/user/create', [UserController::class, 'store'])->name('create-user');
  Route::get('/user/{id}', [UserController::class, 'edit']);
  Route::post('/user/update', [UserController::class, 'update']);
  Route::get('/user/delete/{id}', [UserController::class, 'delete']);
  Route::get('/clear-cache', [UserController::class, 'clearCache']);
});

//only those have manage_permission permission will get access
Route::middleware($back_end_middleware)->group(function () {
  Route::get('/permission', [PermissionController::class, 'index']);
  Route::get('/permission/get-list', [PermissionController::class, 'getPermissionList']);
  Route::post('/permission/create', [PermissionController::class, 'create']);
  Route::get('/permission/update', [PermissionController::class, 'update']);
  Route::get('/permission/delete/{id}', [PermissionController::class, 'delete']);
});


Route::get('get-role-permissions-badge', [PermissionController::class, 'getPermissionBadgeByRole']);

//only those have manage_role permission will get access
Route::middleware($back_end_middleware)->group(function () {
  Route::get('/roles', [RolesController::class, 'index']);
  Route::get('/role/get-list', [RolesController::class, 'getRoleList']);
  Route::post('/role/create', [RolesController::class, 'create']);
  Route::get('/role/edit/{id}', [RolesController::class, 'edit']);
  Route::post('/role/update', [RolesController::class, 'update']);
  Route::get('/role/delete/{id}', [RolesController::class, 'delete']);
});


Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
  Route::get('/category', [CategoryController::class, 'index'])->name('admin.category');
  Route::post('/category/store', [CategoryController::class, 'store'])->name('admin.category.store');
  Route::get('/category/get-list', [CategoryController::class, 'getCategoryList'])->name('admin.category.get.list');
  Route::get('/category/edit/{id}', [CategoryController::class, 'edit'])->name('admin.category.edit');
  Route::patch('/category/update', [CategoryController::class, 'update'])->name('admin.category.update');
  Route::delete('/category/delete/{id}', [CategoryController::class, 'delete'])->name('admin.category.delete');
});




#product route
Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
  Route::get('/product', [ProductController::class, 'index'])->name('admin.product');
  Route::get('/product/create', [ProductController::class, 'create'])->name('admin.product.create');
  Route::get('/product/edit/{id}', [ProductController::class, 'edit'])->name('admin.product.edit');
  Route::patch('/product/update/{id}', [ProductController::class, 'update'])->name('admin.product.update');
  Route::delete('/product/delete/{id}', [ProductController::class, 'delete'])->name('admin.product.delete');
  Route::post('/product/store', [ProductController::class, 'store'])->name('admin.product.store');
  Route::get('/product/get-list', [ProductController::class, 'getProductList'])->name('admin.product.list');
});

    // product restore route
// Route::get('/admin/product', [ProductController::class, 'index'])->name('admin.product.index');

Route::get('/admin/product/recently-deleted-page', [ProductController::class, 'recentlyDeletedpage'])->name('admin.product.recentlyDeletedPage');

// Route::get('/admin/product/permanentlyDelete/{id}', [ProductController::class, 'permanentlyDelete'])->name('admin.product.permanentlyDelete');
Route::post('/admin/product/permanently-delete/{id}', [ProductController::class, 'permanentlyDelete'])
     ->name('admin.product.permanentlyDelete');



Route::post('/admin/product/restore/{id}', [ProductController::class, 'restore'])->name('admin.product.restore');





Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
  Route::get('/banner', [BannerController::class, 'index'])->name('admin.banner');
  Route::post('/banner/store', [BannerController::class, 'store'])->name('admin.banner.store');
  Route::get('/banner/get-list', [BannerController::class, 'getBannerList'])->name('admin.banner.list');
  Route::delete('/banner/delete/{id}', [BannerController::class, 'delete'])->name('admin.banner.delete');
});


Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
  Route::get('/brand', [BrandController::class, 'index'])->name('admin.brand');
  Route::post('/brand/store', [BrandController::class, 'store'])->name('admin.brand.store');
  Route::get('/brand/get-list', [BrandController::class, 'getBrandList'])->name('admin.brand.list');
  Route::delete('/brand/delete/{id}', [BrandController::class, 'delete'])->name('admin.brand.delete');
});

#attribute
Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
  Route::get('/attribute', [AttributeController::class, 'index'])->name('admin.attribute');
  Route::post('/attribute/store', [AttributeController::class, 'store'])->name('admin.attribute.store');
  Route::get('/attribute/get-list', [AttributeController::class, 'getList'])->name('admin.attribute.list');
  Route::delete('/attribute/delete/{id}', [AttributeController::class, 'delete'])->name('admin.attribute.delete');
  Route::get('/attribute/edit/{id}', [AttributeController::class, 'edit'])->name('admin.attribute.edit');
  Route::patch('/attribute/update', [AttributeController::class, 'update'])->name('admin.attribute.update');
});


Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
  Route::get('/attribute_option', [AttributeOptionController::class, 'index'])->name('admin.attribute_option');
  Route::post('/attribute_option/store', [AttributeOptionController::class, 'store'])->name('admin.attribute_option.store');
  Route::get('/attribute_option/get-list', [AttributeOptionController::class, 'getList'])->name('admin.attribute_option.list');
  Route::delete('/attribute_option/delete/{id}', [AttributeOptionController::class, 'delete'])->name('admin.attribute_option.delete');
  #edition route
  Route::get('/attribute_option/edit/{id}', [AttributeOptionController::class, 'edit'])->name('admin.attribute_option.edit');
  Route::patch('/attribute_option/update', [AttributeOptionController::class, 'update'])->name('admin.attribute_option.update');
  Route::post('attribute-option/check-unique', [AttributeOptionController::class, 'checkUnique'])
       ->name('admin.attribute_option.check-unique');
});


Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
  Route::get('/color', [ColorController::class, 'index'])->name('admin.color');
  Route::post('/color/store', [ColorController::class, 'store'])->name('admin.color.store');
  Route::get('/color/get-list', [ColorController::class, 'getList'])->name('admin.color.list');
  Route::delete('/color/delete/{id}', [ColorController::class, 'delete'])->name('admin.color.delete');
  #edition route
  Route::get('/color/edit/{id}', [ColorController::class, 'edit'])->name('admin.color.edit');
  Route::patch('/color/update', [ColorController::class, 'update'])->name('admin.color.update');
});

#order section
Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
  Route::get('/order', [OrderController::class, 'index'])->name('admin.order');
  Route::get('/order/order-detail/{id}', [OrderController::class, 'order_detail'])->name('admin.order.detail');
  Route::post('/order/store', [OrderController::class, 'store'])->name('admin.order.store');
  Route::get('/order/get-list', [OrderController::class, 'getList'])->name('admin.order.list');
  Route::post('/order/make-paid', [OrderController::class, 'makePaid'])->name('admin.order.make_paid');
  Route::post('/order/cancel', [OrderController::class, 'cancelOrder'])->name('admin.order.cancel');
  Route::get('/order/salesmen', [OrderController::class, 'salesmenIndex'])->name('admin.order.salesmen');
  Route::get('/order/salesman/{user}/orders', [OrderController::class, 'salesmanOrders'])->name('admin.order.salesman');
  Route::delete('/order/delete/{id}', [OrderController::class, 'delete'])->name('admin.order.delete');
  #edition route
  Route::get('/order/edit/{id}', [OrderController::class, 'edit'])->name('admin.order.edit');
  Route::patch('/order/update', [OrderController::class, 'update'])->name('admin.order.update');

  Route::get('/order-detail/{id}', [OrderController::class, 'order_detail'])->name('admin.order_detail');
  Route::get('/order/{id}/invoice', [OrderController::class, 'invoicePreview'])
    ->name('admin.order.invoice.preview');
  Route::get('/order/{id}/invoice/pdf', [OrderController::class, 'invoicePdf'])
    ->name('admin.order.invoice.pdf');

});

#Unit section
Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
  Route::get('unit', [UnitController::class, 'index'])->name('admin.unit.index');
    Route::post('unit/store', [UnitController::class, 'store'])->name('admin.unit.store');

    Route::get('unit/list', [UnitController::class, 'list'])->name('admin.unit.list');

    Route::get('unit/edit/{id}', [UnitController::class, 'edit'])->name('admin.unit.edit');
    Route::patch('unit/update', [UnitController::class, 'update'])->name('admin.unit.update');

    Route::delete('unit/delete/{id}', [UnitController::class, 'destroy'])->name('admin.unit.delete');
});


#Variant section
Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
   Route::get('variant-name', [VariantNameController::class, 'index'])
        ->name('admin.variant-name.index');

    Route::post('variant-name/store', [VariantNameController::class, 'store'])
        ->name('admin.variant-name.store');

    Route::get('variant-name/list', [VariantNameController::class, 'list'])
        ->name('admin.variant-name.list');

    Route::get('variant-name/edit/{id}', [VariantNameController::class, 'edit'])
        ->name('admin.variant-name.edit');

    Route::patch('variant-name/update', [VariantNameController::class, 'update'])
        ->name('admin.variant-name.update');

    Route::delete('variant-name/delete/{id}', [VariantNameController::class, 'destroy'])
        ->name('admin.variant-name.delete');
});


// Custom Field Types Management (Admin Panel)

Route::middleware($back_end_middleware)->prefix('admin')->group(function () {

// Custom Field Types CRUD
    Route::get('/custom-field-type', [CustomFieldTypeController::class, 'index'])
        ->name('admin.custom-field-type.index');
    
    Route::get('/custom-field-type/create', [CustomFieldTypeController::class, 'create'])
        ->name('admin.custom-field-type.create');
    
    Route::post('/custom-field-type', [CustomFieldTypeController::class, 'store'])
        ->name('admin.custom-field-type.store');
    
    Route::get('/custom-field-type/{id}/edit', [CustomFieldTypeController::class, 'edit'])
        ->name('admin.custom-field-type.edit');
    
    Route::put('/custom-field-type/{id}', [CustomFieldTypeController::class, 'update'])
        ->name('admin.custom-field-type.update');
    
    Route::delete('/custom-field-type/{id}', [CustomFieldTypeController::class, 'destroy'])
        ->name('admin.custom-field-type.destroy');
    
    Route::patch('/custom-field-type/{id}/toggle-status', [CustomFieldTypeController::class, 'toggleStatus'])
        ->name('admin.custom-field-type.toggle-status');

});



#product code  type  section
Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
    Route::get('/product-code-type', [ProductCodeTypeController::class, 'index'])->name('admin.product-code-type.index');
    Route::get('/list', [ProductCodeTypeController::class, 'list'])->name('admin.product-code-type.list');
    Route::post('/store', [ProductCodeTypeController::class, 'store'])->name('admin.product-code-type.store');

    Route::get('product-code-type/edit/{id}', [ProductCodeTypeController::class, 'edit'])
        ->name('admin.product-code-type.edit');
    Route::patch('/update', [ProductCodeTypeController::class, 'update'])->name('admin.product-code-type.update');
    Route::delete('/delete/{id}', [ProductCodeTypeController::class, 'delete'])->name('admin.product-code-type.delete');
    Route::post('/status', [ProductCodeTypeController::class, 'status'])->name('admin.product-code-type.status');
});


Route::get('/pos', function () {
  return view('admin.inventory.pos');
});

Route::middleware($back_end_middleware)->prefix('admin')->group(function () {
  Route::get('/enquiries', [AdminEnquiryController::class, 'index'])->name('admin.enquiries.index');
  Route::get('/enquiries/list', [AdminEnquiryController::class, 'list'])->name('admin.enquiries.list');
  Route::get('/enquiries/{id}', [AdminEnquiryController::class, 'show'])->name('admin.enquiries.show');
  Route::post('/enquiries/{id}/status', [AdminEnquiryController::class, 'updateStatus'])->name('admin.enquiries.status');
  Route::post('/enquiries/{id}/assign', [AdminEnquiryController::class, 'assign'])->name('admin.enquiries.assign');
  Route::post('/enquiries/{id}/note', [AdminEnquiryController::class, 'addNote'])->name('admin.enquiries.note');

  Route::get('/cms-pages', [CmsPageController::class, 'index'])->name('admin.cms.index');
  Route::get('/cms-pages/create', [CmsPageController::class, 'create'])->name('admin.cms.create');
  Route::post('/cms-pages', [CmsPageController::class, 'store'])->name('admin.cms.store');
  Route::get('/cms-pages/{id}/edit', [CmsPageController::class, 'edit'])->name('admin.cms.edit');
  Route::put('/cms-pages/{id}', [CmsPageController::class, 'update'])->name('admin.cms.update');
  Route::delete('/cms-pages/{id}', [CmsPageController::class, 'destroy'])->name('admin.cms.destroy');

  Route::get('/website-settings', [WebsiteSettingController::class, 'edit'])->name('admin.website-settings.edit');
  Route::post('/website-settings', [WebsiteSettingController::class, 'update'])->name('admin.website-settings.update');

  Route::get('/smtp-settings', [SmtpSettingController::class, 'edit'])->name('admin.smtp.edit');
  Route::post('/smtp-settings', [SmtpSettingController::class, 'update'])->name('admin.smtp.update');
  Route::post('/smtp-settings/test', [SmtpSettingController::class, 'test'])->name('admin.smtp.test');

  Route::get('/email-templates', [EmailTemplateController::class, 'index'])->name('admin.email-templates.index');
  Route::get('/email-templates/{id}/edit', [EmailTemplateController::class, 'edit'])->name('admin.email-templates.edit');
  Route::put('/email-templates/{id}', [EmailTemplateController::class, 'update'])->name('admin.email-templates.update');

  Route::get('/homepage-sections', [HomepageSectionController::class, 'index'])->name('admin.homepage.index');
  Route::get('/homepage-sections/{id}/edit', [HomepageSectionController::class, 'edit'])->name('admin.homepage.edit');
  Route::put('/homepage-sections/{id}', [HomepageSectionController::class, 'update'])->name('admin.homepage.update');
});


// Route::view('product','admin.inventory.product.list');
// Route::view('product/create','admin.inventory.product.create');

require __DIR__ . '/auth.php';
