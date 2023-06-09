<?php

use Joshua\Core\Router;

Router::add("",                                                 ["controller" => App\Controllers\DefaultController::class,  "activity" => "index"                   ]);
Router::add("browse/{category:[a-z\-]+}",                       ["controller" => App\Controllers\DefaultController::class,  "activity" => "browse"                  ]);
Router::add("product/{upc:[A-Z0-9]+}",                          ["controller" => App\Controllers\DefaultController::class,  "activity" => "product"                 ]);
Router::add("cart",                                             ["controller" => App\Controllers\CartController::class,     "activity" => "cart"                    ]);
Router::add("cart/json",                                        ["controller" => App\Controllers\CartController::class,     "activity" => "json"                    ]);
Router::add("cart/add",                                         ["controller" => App\Controllers\CartController::class,     "activity" => "addToCart"               ]);
Router::add("cart/remove",                                      ["controller" => App\Controllers\CartController::class,     "activity" => "removeFromCart"          ]);
Router::add("checkout",                                         ["controller" => App\Controllers\CartController::class,     "activity" => "checkout"                ]);
Router::add("register",                                         ["controller" => App\Controllers\UserController::class,     "activity" => "register"                ]);
Router::add("login",                                            ["controller" => App\Controllers\UserController::class,     "activity" => "login"                   ]);
Router::add("logout",                                           ["controller" => App\Controllers\UserController::class,     "activity" => "logout"                  ]);
Router::add("account",                                          ["controller" => App\Controllers\UserController::class,     "activity" => "account"                 ]);
Router::add("orders",                                           ["controller" => App\Controllers\OrderController::class,    "activity" => "orders"                  ]);
Router::add("order/details/{ordernum:[\-a-zA-Z0-9]+}",          ["controller" => App\Controllers\OrderController::class,    "activity" => "order"                   ]);
Router::add("order/create/paypal-order",                        ["controller" => App\Controllers\OrderController::class,    "activity" => "paypalCreate"            ]);
Router::add("order/process/paypal-order/{id:.+}",               ["controller" => App\Controllers\OrderController::class,    "activity" => "paypalCapture"           ]);
Router::add("order/confirmation",                               ["controller" => App\Controllers\OrderController::class,    "activity" => "confirmation"            ]);
Router::add("admin/login",                                      ["controller" => App\Controllers\AdminController::class,    "activity" => "login"                   ]);
Router::add("admin/logout",                                     ["controller" => App\Controllers\AdminController::class,    "activity" => "logout"                  ]);
Router::add("admin/dashboard",                                  ["controller" => App\Controllers\AdminController::class,    "activity" => "dashboard"               ]);
Router::add("admin/carouselupdate",                             ["controller" => App\Controllers\AdminController::class,    "activity" => "carouselUpdate"          ]);
Router::add("admin/accounts",                                   ["controller" => App\Controllers\AdminController::class,    "activity" => "accounts"                ]);
Router::add("admin/categories",                                 ["controller" => App\Controllers\AdminController::class,    "activity" => "categories"              ]);
Router::add("admin/category/create",                            ["controller" => App\Controllers\AdminController::class,    "activity" => "categoryCreate"          ]);
Router::add("admin/category/details/{category:[a-z\-]+}",       ["controller" => App\Controllers\AdminController::class,    "activity" => "categoryDetails"         ]);
Router::add("admin/category/updatedetails",                     ["controller" => App\Controllers\AdminController::class,    "activity" => "categoryUpdateDetails"   ]);
Router::add("admin/category/delete",                            ["controller" => App\Controllers\AdminController::class,    "activity" => "categoryDelete"          ]);
Router::add("admin/products",                                   ["controller" => App\Controllers\AdminController::class,    "activity" => "products"                ]);
Router::add("admin/products/{page:[0-9]*}",                     ["controller" => App\Controllers\AdminController::class,    "activity" => "products"                ]);
Router::add("admin/product/create",                             ["controller" => App\Controllers\AdminController::class,    "activity" => "productCreate"           ]);
Router::add("admin/product/details/{upc:[A-Z0-9]+}",            ["controller" => App\Controllers\AdminController::class,    "activity" => "productDetails"          ]);
Router::add("admin/product/updatedetails",                      ["controller" => App\Controllers\AdminController::class,    "activity" => "productUpdateDetails"    ]);
Router::add("admin/product/updatestock",                        ["controller" => App\Controllers\AdminController::class,    "activity" => "productUpdateStock"      ]);
Router::add("admin/orders",                                     ["controller" => App\Controllers\AdminController::class,    "activity" => "orders"                  ]);
Router::add("admin/orders/pending",                             ["controller" => App\Controllers\AdminController::class,    "activity" => "pendingOrders"           ]);
Router::add("admin/orders/processing",                          ["controller" => App\Controllers\AdminController::class,    "activity" => "processingOrders"        ]);
Router::add("admin/orders/shipped",                             ["controller" => App\Controllers\AdminController::class,    "activity" => "shippedOrders"           ]);
Router::add("admin/orderstatusupdate",                          ["controller" => App\Controllers\AdminController::class,    "activity" => "orderStatusUpdate"       ]);
Router::add("admin/order/completed/{ordernum:[\-a-zA-Z0-9]+}",  ["controller" => App\Controllers\AdminController::class,    "activity" => "orderCompleted"          ]);
Router::add("admin/order/pending/{ordernum:[\-a-zA-Z0-9]+}",    ["controller" => App\Controllers\AdminController::class,    "activity" => "orderPending"            ]);
Router::add("admin/order/details/{ordernum:[\-a-zA-Z0-9]+}",    ["controller" => App\Controllers\AdminController::class,    "activity" => "orderDetails"            ]);
