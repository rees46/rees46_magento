# REES46 Extension for CMS Magento

## Compatibility

* Magento 1.9.x

## Documentation

English documentation: [https://docs.rees46.com/display/en/Magento+1.9.x+Extension](https://docs.rees46.com/display/en/Magento+1.9.x+Extension)

Русская документация: [https://docs.rees46.com/display/R46D/Magento+1.9.x](https://docs.rees46.com/display/R46D/Magento+1.9.x)

## Support

[support@rees46.com](mailto:support@rees46.com)

## License

MIT

![magento](https://api.rees46.com/marker/magento)



### Подготовка к разработке

1. Скачать и установить Magento 1.9.x.
2. Скачать содержимое репозитория и поместить в корневую директорию установки Magento.
3. В настройках почистить все виды кеша и сессий, чтобы модуль появился в меню и начал работать.
4. Все комментарии и вообще все писать только на английском языке - установщик Magento иногда глючит с кириллицей.

### Обновление репозитория

После изменения расширения в локальной установке Magento необходимо перенести измененные файлы, включая файлы пакетов (из /var/connect) в репозиторий и закоммитить.

### Публикация в Magento Connect

Вся подготовка пакетов обновлений происходит через локальную установку CMS Magento. Предварительно стоит ознакомиться с [инструкцией по упаковке расширений](http://www.magentocommerce.com/wiki/7_-_magento_connect/creating_magento_connect_extension_package).

1. В файле app/code/community/Rees46/Personalization/etc/config.xml изменить версию на новую.
2. В локальной версии Magento открыть меню System => Magento Connect => Package Extensions.
3. Выбираем слева в меню "Load local extension" и кликаем на Rees46_Personalization в списке файлов.
4. Меняем описание расширения.
5. Не забываем увеличить номер версии.
6. Нажимаем "Save data and create package". Получаем файл вида /var/connect/Rees46_Personalization-X.Y.Z.tgz
7. Далее заходим в [личный кабинет разработчика Magento Connect](https://www.magentocommerce.com/magento-connect/extension/extension/list/).
8. Загружаем полученный пакет файлов.
