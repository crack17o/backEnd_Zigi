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
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/api/(?'
                    .'|orders/(?'
                        .'|a(?'
                            .'|pproved/([^/]++)/([^/]++)(*:89)'
                            .'|dd(?'
                                .'|P(?'
                                    .'|hotoProduct/([^/]++)/([^/]++)(*:134)'
                                    .'|roduct/([^/]++)(*:157)'
                                .')'
                                .'|Category/([^/]++)(*:183)'
                            .')'
                        .')'
                        .'|rejected/([^/]++)/([^/]++)(*:219)'
                        .'|update(?'
                            .'|Product/([^/]++)/([^/]++)(*:261)'
                            .'|Category/([^/]++)/([^/]++)(*:295)'
                        .')'
                        .'|delete(?'
                            .'|Product/([^/]++)/([^/]++)(*:338)'
                            .'|Category/([^/]++)/\\{\\$categoryId\\}(*:380)'
                        .')'
                    .')'
                    .'|comment/(?'
                        .'|create/([^/]++)(*:416)'
                        .'|response/([^/]++)/([^/]++)(*:450)'
                    .')'
                    .'|products/([^/]++)/update\\-stock(*:490)'
                    .'|users/(?'
                        .'|user(?'
                            .'|Update/([^/]++)(*:529)'
                            .'|Delete/([^/]++)(*:552)'
                        .')'
                        .'|activeAccount/([^/]++)(*:583)'
                        .'|requestNewCode/([^/]++)(*:614)'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        89 => [[['_route' => 'approve_order', '_controller' => 'App\\Controller\\CommandController::approveOrder'], ['adminId', 'commandId'], ['POST' => 0], null, false, true, null]],
        134 => [[['_route' => 'app_newpho', '_controller' => 'App\\Controller\\PhotoProductController::executeAddPhoto'], ['idAdmin', 'idProduct'], ['POST' => 0], null, false, true, null]],
        157 => [[['_route' => 'app_newProduct', '_controller' => 'App\\Controller\\ProductController::executeAddProduct'], ['idAdmin'], ['POST' => 0], null, false, true, null]],
        183 => [[['_route' => 'app_newCategory', '_controller' => 'App\\Controller\\ProductController::executeAddCategory'], ['idAdmin'], ['POST' => 0], null, false, true, null]],
        219 => [[['_route' => 'rejected_order', '_controller' => 'App\\Controller\\CommandController::rejectOrder'], ['adminId', 'commandId'], ['POST' => 0], null, false, true, null]],
        261 => [[['_route' => 'app_updateProduct', '_controller' => 'App\\Controller\\ProductController::executeUpdateProduct'], ['idAdmin', 'productId'], ['PUT' => 0], null, false, true, null]],
        295 => [[['_route' => 'app_updateCategory', '_controller' => 'App\\Controller\\ProductController::executeUpdateCategory'], ['idAdmin', 'categoryId'], ['PUT' => 0], null, false, true, null]],
        338 => [[['_route' => 'app_deleteProduct', '_controller' => 'App\\Controller\\ProductController::executeDeleteProduct'], ['idAdmin', 'productId'], ['DELETE' => 0], null, false, true, null]],
        380 => [[['_route' => 'app_deleteCategory', '_controller' => 'App\\Controller\\ProductController::executeDeleteCategory'], ['idAdmin'], ['DELETE' => 0], null, false, false, null]],
        416 => [[['_route' => 'new_comment', '_controller' => 'App\\Controller\\CommentController::newComment'], ['idUser'], ['POST' => 0], null, false, true, null]],
        450 => [[['_route' => 'app_reply', '_controller' => 'App\\Controller\\ReplyController::addReply'], ['idAdmin', 'idComment'], ['POST' => 0], null, false, true, null]],
        490 => [[['_route' => 'update_product_stock', '_controller' => 'App\\Controller\\ProductController::updateProductStock'], ['productId'], ['POST' => 0], null, false, false, null]],
        529 => [[['_route' => 'app_update_user', '_controller' => 'App\\Controller\\UserController::updateUser'], ['id'], ['PUT' => 0], null, false, true, null]],
        552 => [[['_route' => 'app_delete_user', '_controller' => 'App\\Controller\\UserController::deleteUser'], ['id'], ['DELETE' => 0], null, false, true, null]],
        583 => [[['_route' => 'app_myactiveUser', '_controller' => 'App\\Controller\\UserController::activeAccount'], ['id'], ['POST' => 0], null, false, true, null]],
        614 => [
            [['_route' => 'app_newCustommeee', '_controller' => 'App\\Controller\\UserController::requestNewCode'], ['id'], ['GET' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
