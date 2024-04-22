<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/blogs' => [
            [['_route' => 'get-blogsCollection', '_controller' => 'App\\Controller\\BlogController::getCollections'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'post-blog', '_controller' => 'App\\Controller\\BlogController::post'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/interests' => [[['_route' => 'post-interest', '_controller' => 'App\\Controller\\InterestController::post'], null, ['POST' => 0], null, false, false, null]],
        '/api/messages/unread' => [[['_route' => 'count-unread', '_controller' => 'App\\Controller\\MessageController::unread'], null, ['GET' => 0], null, false, false, null]],
        '/api/inbox-messages' => [[['_route' => 'inbox-messages', '_controller' => 'App\\Controller\\MessageController::inbox'], null, ['GET' => 0], null, false, false, null]],
        '/api/outbox-messages' => [[['_route' => 'outbox-messages', '_controller' => 'App\\Controller\\MessageController::outbox'], null, ['GET' => 0], null, false, false, null]],
        '/api/token' => [[['_route' => 'get-by-token', '_controller' => 'App\\Controller\\ProfileController::getProfileByToken'], null, ['POST' => 0], null, false, false, null]],
        '/api/profiles' => [[['_route' => 'get-profiles', '_controller' => 'App\\Controller\\ProfileController::getProfiles'], null, ['GET' => 0], null, false, false, null]],
        '/register' => [[['_route' => 'registration', '_controller' => 'App\\Controller\\RegistrationController::register'], null, ['POST' => 0, 'GET' => 1], null, false, false, null]],
        '/login' => [[['_route' => 'login', '_controller' => 'App\\Controller\\RegistrationController::login'], null, ['POST' => 0, 'GET' => 1], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/api(?'
                    .'|/\\.well\\-known/genid/([^/]++)(*:43)'
                    .'|(?:/(index)(?:\\.([^/]++))?)?(*:78)'
                    .'|/(?'
                        .'|errors(?:/(\\d+))?(*:106)'
                        .'|validation_errors/([^/]++)(*:140)'
                        .'|docs(?:\\.([^/]++))?(*:167)'
                        .'|contexts/([^.]+)(?:\\.(jsonld))?(*:206)'
                        .'|tags(?'
                            .'|/([^/\\.]++)(?:\\.([^/]++))?(*:247)'
                            .'|(?:\\.([^/]++))?(*:270)'
                        .')'
                        .'|users/([^/\\.]++)(?:\\.([^/]++))?(*:310)'
                        .'|blogs/([^/]++)(?'
                            .'|(*:335)'
                            .'|/(?'
                                .'|c(?'
                                    .'|omments(*:358)'
                                    .'|reate\\-comment(*:380)'
                                .')'
                                .'|add\\-tag(*:397)'
                            .')'
                        .')'
                        .'|interests/([^/]++)(?'
                            .'|(*:428)'
                        .')'
                        .'|messages/([^/]++)(?'
                            .'|(*:457)'
                        .')'
                        .'|profiles/([^/]++)(?'
                            .'|(*:486)'
                        .')'
                    .')'
                .')'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:525)'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        43 => [[['_route' => 'api_genid', '_controller' => 'api_platform.action.not_exposed', '_api_respond' => 'true'], ['id'], null, null, false, true, null]],
        78 => [[['_route' => 'api_entrypoint', '_controller' => 'api_platform.action.entrypoint', '_format' => '', '_api_respond' => 'true', 'index' => 'index'], ['index', '_format'], null, null, false, true, null]],
        106 => [[['_route' => 'api_errors', '_controller' => 'api_platform.action.not_exposed', 'status' => '500'], ['status'], null, null, false, true, null]],
        140 => [[['_route' => 'api_validation_errors', '_controller' => 'api_platform.action.not_exposed'], ['id'], null, null, false, true, null]],
        167 => [[['_route' => 'api_doc', '_controller' => 'api_platform.action.documentation', '_format' => '', '_api_respond' => 'true'], ['_format'], null, null, false, true, null]],
        206 => [[['_route' => 'api_jsonld_context', '_controller' => 'api_platform.jsonld.action.context', '_format' => 'jsonld', '_api_respond' => 'true'], ['shortName', '_format'], null, null, false, true, null]],
        247 => [[['_route' => '_api_/tags/{id}{._format}_get', '_controller' => 'api_platform.symfony.main_controller', '_format' => null, '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags/{id}{._format}_get'], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        270 => [[['_route' => '_api_/tags{._format}_get_collection', '_controller' => 'api_platform.symfony.main_controller', '_format' => null, '_stateless' => true, '_api_resource_class' => 'App\\Entity\\Tag', '_api_operation_name' => '_api_/tags{._format}_get_collection'], ['_format'], ['GET' => 0], null, false, true, null]],
        310 => [[['_route' => '_api_/users/{id}{._format}_get', '_controller' => 'api_platform.action.not_exposed', '_format' => null, '_stateless' => true, '_api_resource_class' => 'App\\Entity\\User', '_api_operation_name' => '_api_/users/{id}{._format}_get'], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        335 => [
            [['_route' => 'edit-blog', '_controller' => 'App\\Controller\\BlogController::edit'], ['id'], ['PATCH' => 0], null, false, true, null],
            [['_route' => 'get-blog', '_controller' => 'App\\Controller\\BlogController::get'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'delete-blog', '_controller' => 'App\\Controller\\BlogController::delete'], ['id'], ['DELETE' => 0], null, false, true, null],
        ],
        358 => [[['_route' => 'get-comments', '_controller' => 'App\\Controller\\CommentController::blog'], ['id'], ['GET' => 0], null, false, false, null]],
        380 => [[['_route' => 'post-comment', '_controller' => 'App\\Controller\\CommentController::post'], ['id'], ['POST' => 0], null, false, false, null]],
        397 => [[['_route' => 'add-tag', '_controller' => 'App\\Controller\\TagController'], ['blogId'], ['POST' => 0], null, false, false, null]],
        428 => [
            [['_route' => 'get-interest', '_controller' => 'App\\Controller\\InterestController::get'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'delete-interest', '_controller' => 'App\\Controller\\InterestController::delete'], ['id'], ['DELETE' => 0], null, false, true, null],
            [['_route' => 'edit-interest', '_controller' => 'App\\Controller\\InterestController::edit'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        457 => [
            [['_route' => 'post-message', '_controller' => 'App\\Controller\\MessageController::post'], ['id'], ['POST' => 0], null, false, true, null],
            [['_route' => 'current-message', '_controller' => 'App\\Controller\\MessageController::message'], ['id'], ['GET' => 0], null, false, true, null],
        ],
        486 => [
            [['_route' => 'get-profile', '_controller' => 'App\\Controller\\ProfileController::getProfile'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'edit-profile', '_controller' => 'App\\Controller\\ProfileController::edit'], ['id'], ['PATCH' => 0], null, false, true, null],
        ],
        525 => [
            [['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
