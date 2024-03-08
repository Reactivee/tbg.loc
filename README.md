<p align="center">
    <h1 align="center">Theme test task</h1>
    <br>
</p>

Loyiha shundan iboratki: Ishxona xodimlarini tug'ilgan kunini eslatib turadigan bot.

2 ta jadval bo'ladi, Lavozim va Xodimlar degan. Lavozimda lavozim nomlari bo'ladi xolos. Xodimlarda ism , familiya,
rasmi va qachon tug'ilganligi haqida ma'lumotlari kirg'izilishi kerak

Bu yerda 2 ta sahifa

1. Xodimlar /employees/index
2. Lavozim  /position

Xodimlar bo'limidan barcha xodimlarni tahrirlash ishlari amalga oshiriladi va telegram xabarnoma kimga ketishi haqida
ham shu sahifadan boshqarsa bo'ladi. Test uchun telegram bot orqali sms jo'natish funksiyasi bor. Har kuni qachon sms ketishini server orqali hal qilish mumkin
Kerakli baza ham faylga biriktirilgan.
https://t.me/my_tbg_bot shu bot orqali start tugmasi bosiladi va har bir xodimlar chat idsi olinib bazaga yozilishi kerak

O'rnatish.

1. php init
2. composer
3. connect database
4. php yii migrate

DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for backend application    
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```
