# (WIP) Sylius Borica payment gateway plugin  

##Motivation 
Adding Borica Bulgarian payment provider. 
Bassed on [mirovit/borica-api](https://github.com/mirovit/borica-api)

## Installation

```bash
composer require vanssa/borica-sylius-plugin
```


Add plugin dependencies to your config/bundles.php file:

```php
return [
    ...
    Vanssa\BoricaSyliusPlugin\VanssaBoricaSyliusPlugin::class => ['all'=>true]
];
```

Add routing to your config/routes/sylius_shop.yaml

```yml
payum_borica_notification_url:
    resource: "@VanssaBoricaSyliusPlugin/Resources/config/routes.yaml"
```

Add config to your config/packages/_sylius.yaml

```yml
imports:
 ...
    - { resource: "@VanssaBoricaSyliusPlugin/Resources/config/config.yml" }
```

Return url is :
https://{domain_name}/payment/borica/capture/

Signatures and keys is need to add in administration. 

#TODO 
1. Test configuration per channel channel 
2. Add detailed documentation. 
3. Cleanup typo errors.
