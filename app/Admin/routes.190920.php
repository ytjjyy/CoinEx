<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    Route::group(['middleware' => ['admin.token']], function (Router $router) {
//        $router->get('/', 'HomeController@index');
        $router->get('/', function () {
            return redirect('admin/auth/setting');
        });
        $router->resource('articles', ArticleController::class);
        $router->post('articles/release', 'ArticleController@release');
        $router->post('article/ChangeStatus', 'ArticleController@ChangeStatus');
        $router->post('article/upload', 'ArticleController@upload');
        $router->post('article/destroy', 'ArticleController@destroy');
        $router->resource('systemUser', SystemUserController::class);
        $router->resource('userGroup', UserGroupController::class);
        $router->post('systemUser/ChangeStatus', 'SystemUserController@ChangeStatus');
        $router->post('systemUser/ChangeCoinVip', 'SystemUserController@ChangeCoinVip');
        $router->post('systemUser/release', 'SystemUserController@release');
        $router->post('systemUser/resetPassword', 'SystemUserController@resetPassword');
        $router->get('systemUser-user-level', 'SystemUserController@userLevels');
        $router->post('systemUser/set-user-level', 'SystemUserController@updateUserLevel');
        $router->get('user/record/{id}', 'SystemUserController@user_record')->where(['id' => '[0-9]+']);
        $router->resource('coinConfig', CoinController::class);
        $router->post('coinConfig/ChangeStatus', 'CoinController@ChangeStatus');
        $router->resource('tradeMarket', TradeMarketController::class);
        $router->post('tradeMarket/changeStatus', 'TradeMarketController@changeStatus');
        $router->post('tradeMarket/deleteRows', 'TradeMarketController@deleteRows');
        $router->get('config/system', "TradeMarketController@config");
        $router->post('config/system', "TradeMarketController@system_config");
        $router->resource('advertise', AdvertiseController::class);
        $router->post('advertise/destroy', 'AdvertiseController@destroy');
        $router->post('advertise/ChangeStatus', 'AdvertiseController@ChangeStatus');
        $router->post('advertise/release', 'AdvertiseController@release');
        $router->get('tradeManager', "TradeMangerController@index");
        $router->post('cancel/tradeManager', 'TradeMangerController@cancelTradeManager'); //取消交易订单
        $router->get('tradeLog', "TradeMangerController@tradeLog");
        $router->get('trade', "TradeMangerController@tradeAll");
        $router->get('tradeManagerGrid', 'TradeMangerController@grid_html');
        $router->get('trade/detail/{id}', 'TradeMangerController@trade_detail')->where(['id' => '[0-9]+']);
        $router->get('finance/transfer', 'FinanceTransferController@index');
        $router->get('finance/secondTransfer', 'FinanceTransferController@second_index');
        $router->get('transfer/history', 'FinanceTransferController@transfer_history');
        $router->get('reward/register', 'UserRewardController@reward_register');
        $router->get('reward/referral', 'UserRewardController@reward_referral');
        $router->post('finance/changeStatus', 'FinanceTransferController@changeStatus');
//        $router->get('mining/reward', 'UserRewardController@mining_reward'); 挖矿奖励
//        $router->get('share/reward', 'UserRewardController@share_reward'); 分红奖励
        $router->get('coin/usdtBalance', "CoinController@usdtBalance");
        $router->post('coin/collectUsdt', "CoinController@collectUsdtBalance");


        $router->resource('userApi', UserApiController::class);

        $router->post('userApi/release', 'UserApiController@release');
        $router->post('userApi/destroy', 'UserApiController@destroy');


        $router->get('release/report', 'ReleaseController@release_report');
        $router->get('dispatch/coin', 'ReleaseController@dispatch_coin');

        $router->get('dispatch', "ReleaseController@dispatch_view");

        $router->get('changeReceiveFreeze', "ReleaseController@changeReceiveFreeze");
        $router->post('dispatch/change_rf_coin', 'ReleaseController@changeReceiveFreeze_post');
        $router->get('dispatch/rf_bill', "ReleaseController@rf_bill");

        $router->resource('dispatch/config', ReleaseController::class);

        $router->post('dispatch/coin', 'ReleaseController@dispatch_post');
        $router->get('ctc/applications', 'C2CController@otc_applications');
        $router->post('cancel/application', 'C2CController@cancel_application');

        $router->get('c2c/order', 'C2CController@c2c_order');
        $router->get('order/detail/{id}', 'C2CController@order_detail')->where(['id' => '[0-9]+']);
        $router->post('finish/order', 'C2CController@finish_order');
        $router->post('cancel/order', 'C2CController@cancel_order');
        $router->get('c2c/privileges', 'C2CController@otc_privileges');
        $router->post('c2c/approve-privilege', 'C2CController@approve_privilege');

        $router->resource('c2c/config', C2CConfigController::class);

        $router->get('user/coin', 'SystemUserController@user_coin');  //用户资产信息
        $router->get('user/coin_saving', 'CoinSavingController@user_saving_coin');  //用户锁定资产/分润宝信息

        $router->get('finance/bill', 'FinanceTransferController@finance_bill'); // 获取全栈流水信息


        $router->resource('merchants', MerchantController::class);//兑换商管理
        $router->post('merchant/ChangeStatus', 'MerchantController@ChangeStatus');

        $router->get('merchant/history/{id}', 'MerchantController@transfer_history')->where(['id' => '[0-9]+']); //兑换商流水明细

        $router->get('merchant/application', 'MerchantController@transfer_application'); //兑换商交易明细

        $router->get('merchant/complaint/{id}', 'MerchantController@complaint')->where(['id' => '[0-9]+']);  // 获取申诉详情


//        $router->post('merchant/release', 'MerchantController@release');  //操作btc订单列表

        $router->post('dispatch/balance', 'MerchantController@dispatch_balance');
//        $router->get('merchant/balance/{id}', 'MerchantController@merchant_balance')->where(['id' => '[0-9]+']);

//        $router->resource('merchantCoin', MerchantConfigController::class);
//        $router->post('merchantCoin/destroy', 'MerchantConfigController@destroy');
        $router->resource('appVersion', AppVersionController::class);

        $router->resource('coinGroupConfig', CoinGroupConfigController::class); //用户分组币种配置

        $router->resource('tradeWarnConfig', TrandeWarningController::class); // 交易对警告配置
        $router->post('tradeWarnConfig/destroy', 'TrandeWarningController@destroy'); //删除交易对警告配置

        $router->resource('marketFreeConfig', MarketFreeConfigController::class); // 交易对警告配置

        $router->resource('specialAccountHandler', SpecialAccountHandlerController::class); //特殊账号处理

        $router->post('specialAccountHandler/release', 'SpecialAccountHandlerController@release');

        $router->post('marketFreeConfig/destroy', 'MarketFreeConfigController@destroy');

        $router->post('setUserGroup', 'SystemUserController@setUserGroup'); //用户设置分组

        $router->post('userGroup/destroy', 'UserGroupController@destroy');  //删除用户分组

        $router->resource('monitoringConfig', MonitoringConfigController::class); //警告配置

        $router->post('monitoringConfig/destroy', 'MonitoringConfigController@destroy');
        $router->get('serviceStatus', 'MonitoringConfigController@serviceStatus');

        $router->resource('timeMonitoringConfig', TimeMonitoringConfigController::class);
        $router->post('timeMonitoringConfig/destroy', 'TimeMonitoringConfigController@destroy');
        $router->resource('timeSpecialAccountHandler', TimeSpecialAccountHandlerController::class); //特殊账号处理

        $router->post('timeSpecialAccountHandler/release', 'TimeSpecialAccountHandlerController@release');

        $router->get('transactions', 'TimeMonitoringConfigController@highTransaction');

        $router->get('transactionsGrid', 'TimeMonitoringConfigController@grid_html');
        $router->get('transactions', 'TimeMonitoringConfigController@highTransaction');

        $router->get('transactionsGrid', 'TimeMonitoringConfigController@grid_html');
        $router->get('global/monitor', 'ReleaseController@global_monitor');
        $router->resource('cashConfig', CashMonitoringConfigController::class);
        $router->post('cashConfig/destroy', 'CashMonitoringConfigController@destroy');

        $router->get('cashMonitor', 'CashMonitoringConfigController@monitor');
        $router->get('cashMonitorGrid', 'CashMonitoringConfigController@grid_html');

        $router->get('userTradeStatus', 'UserRewardController@user_tradeStatus');

        $router->get('lastTrade', 'UserRewardController@last_trade');
        $router->resource('leverage',LeverageController::class);
        $router->post('leverage/destroy','LeverageController@destroy');
        $router->get('leverage-loan','LeverageController@loan');
        $router->get('leverage-lend','LeverageController@lend');
        $router->get('leverage-user','LeverageController@user');
        $router->get('leverage-user-balance','LeverageController@user_balance');

        $router->get('lockUserCoin', 'LockUserCoinController@setLock');
        $router->post('lockCoin','LockUserCoinController@lockCoin');
        $router->get('lockHistory','LockUserCoinController@lockHistory');
        $router->get('lockDetail','LockUserCoinController@lockDetail');

        $router->resource('fundraising',FundraisingController::class);
        $router->post('fundraising/cancel-fundraising','FundraisingController@cancelFundraising');
        $router->post('fundraising/profit-fundraising','FundraisingController@profitFundraising');
        $router->get('fundraising-operate','FundraisingController@operate');
        $router->get('fundraising-invest-list','FundraisingController@investList');
        $router->post('fundraising/create-trade','FundraisingController@createTrade');
        $router->get('fundraising-open-trade-list', 'FundraisingController@openTradeList');
        $router->post('fundraising/cancel-trade', 'FundraisingController@cancelTrade');
        $router->get('fundraising-trade-log-list', 'FundraisingController@tradeLogList');
    });

});
