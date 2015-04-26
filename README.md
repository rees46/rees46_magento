# REES46 for Magento

Исходный код модуля REES46 для CMS Magento.

Инструкция по установке: [http://memo.mkechinov.ru/pages/viewpage.action?pageId=1835151](http://memo.mkechinov.ru/pages/viewpage.action?pageId=1835151)

## Подготовка к разработке

1. Скачать и установить [свежую версию Magento](https://www.magentocommerce.com/products/downloads/magento/).
2. Скачать содержимое репозитория и поместить в корневую директорию установки Magento.
3. В настройках почистить все виды кеша и сессий, чтобы модуль появился в меню и начал работать.

## Обновление репозитория

После изменения расширения в локальной установке Magento необходимо перенести измененные файлы, включая файлы пакетов (из /var/connect и /var/package) в репозиторий и закоммитить.

## Публикация в Magento Connect

Вся подготовка пакетов обновлений происходит через локальную установку CMS Magento. Предварительно стоит ознакомиться с [инструкцией по упаковке расширений](http://www.magentocommerce.com/wiki/7_-_magento_connect/creating_magento_connect_extension_package).

1. В локальной версии Magento открыть меню System => Magento Connect => Package Extensions.
2. Выбираем слева в меню "Load local extension" и кликаем на Rees46_Personalization в списке файлов.
3. Меняем описание расширения.
4. Нажимаем "Save data and create package". Получаем файл вида /var/connect/Rees46_Personalization-X.Y.Z.tgz
5. Далее заходим в [личный кабинет разработчика Magento Commerce](http://www.magentocommerce.com/magento-connect/extension/extension/list).
6. Загружаем полученный пакет файлов.

Используйте следующие значения полей при подготовке пакета расширения:

* Title: Rees46_Personalization
* Channel: community
* Short description: REES46 analyzes customers activity and recommend them best goods they are willing to buy for greater conversion and ARPPU.
* License: GNU
* Description:

```
Personal recommendations for e-commerce and e-mail notifications. Increases conversion rate and revenue without changing costs for advertisement. Automatically.

Behaviour analysis: analyzes activity of every your customer an stores it anonymously. REES46 collects these actions: product view, product added or removed from cart, order created.

Then REES46 recommends appropriate products to every individual customer using blocks of recommended products:

- Personalized Popular Products.
- Personalized Similar Products.
- Personalized Related Products.
- Personalized Interesting Products.
- Recently Viewed Products.
- People Buying It Right Now.

REES46 increases efficiency of e-commerce website up to 24% (in revenue).
```

## Версии

### 3.2.6

- Исправлена генерация URL'а на товар из карточки рекомендации – теперь recommended_by подставляется не просто в конце строки, а перед знаком "?", т.к. URL'ы иногда бывают с параметрами.
- Не всегда можно было получить полную картинку товара (если Magento работает в режиме уменьшения размеров картинок), поэтому сейчас при генерации блоков рекомендаций изображение каждого товара берется отдельным запросом к модели.