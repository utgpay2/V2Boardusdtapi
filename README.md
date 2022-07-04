# V2Boardusdtapi
V2Board usdt支付插件 点对点个人对个人 没有中间商 无手续费 实时到账

### 设置
```
1. 下载 SDK
下载token188.php，并上传到面板app/Payments目录中
2. 面板管理后台 > 系统配置 > 站点 
    添加你的网站域名，不然在后面添加支付会提示失败
3. 添加 Token188 支付方式
    面板管理后台 > 支付配置 > + 添加支付方式
    显示名称	Token188支付
    接口文件	Token188
    接口地址	https://api.token188.com/utg/pay/address
    商户ID	
    商户密钥	
3. 启用该支付方式
```
- 商户ID, 商户密钥  请到[TOKEN188](https://www.token188.com/) 官网注册获取.


### 产品介绍

 - [TOKEN188 USDT支付平台主页](https://www.token188.com)
 - [TOKEN188钱包](https://www.token188.com)（即将推出）
 - [商户平台](https://www.token188.com/manager)
### 特点
 - 使用您自己的USDT地址收款没有中间商
 - 五分钟完成对接
 - 没有任何支付手续费

## 安装流程
1. 注册[TOKEN188商户中心](https://mar.token188.com/)
2. 在商户中心添加需要监听的地址
3. 根据使用的不同面板进行回调设置(回调地址填写自己网站域名即可)


## 有问题和合作可以小飞机联系我们
 - telegram：@token188
