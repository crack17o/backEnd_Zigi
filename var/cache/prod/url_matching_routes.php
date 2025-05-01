<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/orders/create' => [[['_route' => 'create_order', '_controller' => 'App\\Controller\\CommandController::createOrder'], null, ['POST' => 0], null, false, false, null]],
        '/api/orders/list' => [[['_route' => 'get_all_orders', '_controller' => 'App\\Controller\\CommandController::getAllOrders'], null, ['GET' => 0], null, false, false, null]],
        '/api/comment/list' => [[['_route' => 'list_comment', '_controller' => 'App\\Controller\\CommentController::listComment'], null, ['GET' => 0], null, false, false, null]],
        '/payment/init' => [[['_route' => 'payment_init', '_controller' => 'App\\Controller\\PaymentController::initPayment'], null, ['POST' => 0], null, false, false, null]],
        '/payment/notify' => [[['_route' => 'payment_notify', '_controller' => 'App\\Controller\\PaymentController::paymentNotify'], null, ['POST' => 0], null, false, false, null]],
        '/payment/success' => [[['_route' => 'payment_success', '_controller' => 'App\\Controller\\PaymentController::paymentSuccess'], null, ['GET' => 0], null, false, false, null]],
        '/api/orders/listProducts' => [[['_route' => 'app_listProducts', '_controller' => 'App\\Controller\\ProductController::executeListProducts'], null, ['GET' => 0], null, false, false, null]],
        '/api/orders/listCategories' => [[['_route' => 'app_listCategories', '_controller' => 'App\\Controller\\ProductController::executeListCategories'], null, ['GET' => 0], null, false, false, null]],
        '/api/stats' => [[['_route' => 'get_stats', '_controller' => 'App\\Controller\\StatsController::getStats'], null, ['GET' => 0], null, false, false, null]],
        '/api/login' => [[['_route' => 'app_login', '_controller' => 'App\\Controller\\UserController::login'], null, ['POST' => 0], null, false, false, null]],
        '/api/users/newCustomer' => [[['_route' => 'app_newCustomer', '_controller' => 'App\\Controller\\UserController::createCustomer'], null, ['POST' => 0], null, false, false, null]],
        '/api/users/userList' => [[['_route' => 'app_list_users', '_controller' => 'App\\Controller\\UserController::listUsers'], null, ['GET' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/api/(?'
                    .'|orders/(?'
                        .'|a(?'
                            .'|pproved/([^/]++)/([^/]++)(*:54)'
                            .'|dd(?'
                                .'|P(?'
                                    .'|hotoProduct/([^/]++)/([^/]++)(*:99)'
                                    .'|roduct/([^/]++)(*:121)'
                                .')'
                                .'|Category/([^/]++)(*:147)'
                            .')'
                        .')'
                        .'|rejected/([^/]++)/([^/]++)(*:183)'
                        .'|update(?'
                            .'|Product/([^/]++)/([^/]++)(*:225)'
                            .'|Category/([^/]++)/([^/]++)(*:259)'
                        .')'
                        .'|delete(?'
                            .'|Product/([^/]++)/([^/]++)(*:302)'
                            .'|Category/([^/]++)/\\{\\$categoryId\\}(*:344)'
                        .')'
                    .')'
                    .'|comment/(?'
                        .'|create/([^/]++)(*:380)'
                        .'|response/([^/]++)/([^/]++)(*:414)'
                    .')'
                    .'|products/([^/]++)/update\\-stock(*:454)'
                    .'|users/(?'
                        .'|user(?'
                            .'|Update/([^/]++)(*:493)'
                            .'|Delete/([^/]++)(*:516)'
                        .')'
                        .'|activeAccount/([^/]++)(*:547)'
                        .'|requestNewCode/([^/]++)(*:578)'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        54 => [[['_route' => 'approve_order', '_controller' => 'App\\Controller\\CommandController::approveOrder'], ['adminId', 'commandId'], ['POST' => 0], null, false, true, null]],
        99 => [[['_route' => 'app_newpho', '_controller' => 'App\\Controller\\PhotoProductController::executeAddPhoto'], ['idAdmin', 'idProduct'], ['POST' => 0], null, false, true, null]],
        121 => [[['_route' => 'app_newProduct', '_controller' => 'App\\Controller\\ProductController::executeAddProduct'], ['idAdmin'], ['POST' => 0], null, false, true, null]],
        147 => [[['_route' => 'app_newCategory', '_controller' => 'App\\Controller\\ProductController::executeAddCategory'], ['idAdmin'], ['POST' => 0], null, false, true, null]],
        183 => [[['_route' => 'rejected_order', '_controller' => 'App\\Controller\\CommandController::rejectOrder'], ['adminId', 'commandId'], ['POST' => 0], null, false, true, null]],
        225 => [[['_route' => 'app_updateProduct', '_controller' => 'App\\Controller\\ProductController::executeUpdateProduct'], ['idAdmin', 'productId'], ['PUT' => 0], null, false, true, null]],
        259 => [[['_route' => 'app_updateCategory', '_controller' => 'App\\Controller\\ProductController::executeUpdateCategory'], ['idAdmin', 'categoryId'], ['PUT' => 0], null, false, true, null]],
        302 => [[['_route' => 'app_deleteProduct', '_controller' => 'App\\Controller\\ProductController::executeDeleteProduct'], ['idAdmin', 'productId'], ['DELETE' => 0], null, false, true, null]],
        344 => [[['_route' => 'app_deleteCategory', '_controller' => 'App\\Controller\\ProductController::executeDeleteCategory'], ['idAdmin'], ['DELETE' => 0], null, false, false, null]],
        380 => [[['_route' => 'new_comment', '_controller' => 'App\\Controller\\CommentController::newComment'], ['idUser'], ['POST' => 0], null, false, true, null]],
        414 => [[['_route' => 'app_reply', '_controller' => 'App\\Controller\\ReplyController::addReply'], ['idAdmin', 'idComment'], ['POST' => 0], null, false, true, null]],
        454 => [[['_route' => 'update_product_stock', '_controller' => 'App\\Controller\\ProductController::updateProductStock'], ['productId'], ['POST' => 0], null, false, false, null]],
        493 => [[['_route' => 'app_update_user', '_controller' => 'App\\Controller\\UserController::updateUser'], ['id'], ['PUT' => 0], null, false, true, null]],
        516 => [[['_route' => 'app_delete_user', '_controller' => 'App\\Controller\\UserController::deleteUser'], ['id'], ['DELETE' => 0], null, false, true, null]],
        547 => [[['_route' => 'app_myactiveUser', '_controller' => 'App\\Controller\\UserController::activeAccount'], ['id'], ['POST' => 0], null, false, true, null]],
        578 => [
            [['_route' => 'app_newCustommeee', '_controller' => 'App\\Controller\\UserController::requestNewCode'], ['id'], ['GET' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
