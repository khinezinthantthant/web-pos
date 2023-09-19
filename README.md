# Web POS
## API Documentation
#### Admin Login (Post)

``` https://h.mmsdev.site/api/v1/login ```

| Arguments | Type      | Description                   |
| :-------- | :---------| :-----------------------------|
| email     | string    | **Required** admin@gmail.com  |
| password  | string    | **Required** asdffdsa         |


#### Staff Login (Post)

``` https://h.mmsdev.site/api/v1/login ```

| Arguments | Type      | Description                       |
| :-------- | :---------| :---------------------------------|
| email     | string    | **Required** staffone@gmail.com   |
| password  | string    | **Required** asdffdsa             |


## Profile
#### Logout (Post)

``` https://h.mmsdev.site/api/v1/logout ```

#### Logout All (Post)

``` https://h.mmsdev.site/api/v1/logout-all ```

#### Change Password (Post)

``` https://h.mmsdev.site/api/v1/change-password ```

| Arguments             | Type      | Description                       |
| :---------------------| :---------| :---------------------------------|
| current_password      | string    | **Required** asdfdsa              |
| password              | string    | **Required** hellohello           |
| password_confirmation | string    | **Required** hellohello           |

## User

#### User List

``` https://h.mmsdev.site/api/v1/user ```

#### Show User

``` https://h.mmsdev.site/api/v1/user/{id} ```

#### Create User (Post)

``` https://h.mmsdev.site/api/v1/user ```

| Arguments             | Type      | Description                       |
| :---------------------| :---------| :---------------------------------|
| name                  | string    | **Required** Mg Mg                |
| email                 | string    | **Required** mgmg@gmail.com       |
| password              | string    | **Required** asdffdsa             |
| phone_number          | string    | **Required** 098888888            |
| address               | string    | **Required** yangon               |
| gender                | enum      | **Required** male/female          |
| date_of_birth         | string    | **Required** 1/1/1999             |
| role                  | enum      | **Required** admin/staff          |
| photo                 | string    | **Required** url()                |
| password_confirmation | string    | **Required** asdfdsa              |

#### Edit User (Put)

``` https://h.mmsdev.site/api/v1/user/{id} ```

| Arguments             | Type      | Description                       |
| :---------------------| :---------| :---------------------------------|
| name                  | string    | **Required** Hnin Nu              |
| email                 | string    | **Required** hninnu@gmail.com     |
| phone_number          | string    | **Required** 097777777            |
| address               | string    | **Required** mandalay             |
| gender                | enum      | **Required** male/female          |
| date_of_birth         | string    | **Required** 1/1/1999             |
| photo                 | string    | **Required** url()                |


#### Modify Password (Post)

``` https://h.mmsdev.site/api/v1/change-staff-password ```

| Arguments             | Type      | Description                       |
| :---------------------| :---------| :---------------------------------|
| user_id               | integer   | **Required** 4                    |
| new_password          | string    | **Required** asdffdsa             |


## Media

#### Photo Upload (Post)

``` https://h.mmsdev.site/api/v1/photo ```

| Arguments             | Type      | Description                       |
| :---------------------| :---------| :---------------------------------|
| photos[]              | array     | **Required** []                   |

#### Photo List

``` https://h.mmsdev.site/api/v1/photo ```

#### Delete Photo (Delete)

``` https://h.mmsdev.site/api/v1/photo/{id} ```

## Inventory Management

### Brand

#### Brand List

``` https://h.mmsdev.site/api/v1/brand ```

#### Show Brand

``` https://h.mmsdev.site/api/v1/brand/{id} ```

#### Create Brand (Post)

``` https://h.mmsdev.site/api/v1/brand ```

| Arguments             | Type      | Description                       |
| :---------------------| :---------| :---------------------------------|
| name                  | string    | **Required** orange               |
| company               | string    | **Required** abcde                |
| description           | string    | **Required** hellohello           |
| user_id               | string    | **Required** 1                    |
| agent                 | string    | **Required** Micheal              |
| phone_no              | string    | **Required** 09888888             |
| photo                 | string    | **Required** url()                |


#### Update Brand (Put)

``` https://h.mmsdev.site/api/v1/brand/{id} ```

| Arguments             | Type      | Description                       |
| :---------------------| :---------| :---------------------------------|
| name                  | string    | **Required** orange               |
| company               | string    | **Required** abcde                |
| description           | string    | **Required** hellohello           |
| agent                 | string    | **Required** Pagac-Reinger        |
| phone_no              | string    | **Required** 09888888             |
| photo                 | string    | **Required** url()                |

#### Delete Brand (Delete)

``` https://h.mmsdev.site/api/v1/brand/{id} ```

#### Product List

``` https://h.mmsdev.site/api/v1/product ```

#### Show Product

``` https://h.mmsdev.site/api/v1/product/{id} ```

#### Create Product (Post)

``` https://h.mmsdev.site/api/v1/product ```

| Arguments             | Type      | Description                       |
| :---------------------| :---------| :---------------------------------|
| name                  | string    | **Required** apple                |
| actual_price          | string    | **Required** 100                  |
| sale_price            | string    | **Required** 150                  |
| unit                  | string    | **Required** single               |
| more_information      | string    | **Required** text                 |
| brand_id              | string    | **Required** 1                    |
| photo                 | string    | **Required** url()                |
| total_stock           | string    | **Required** 10                   |


#### Update Product (Put)

``` https://h.mmsdev.site/api/v1/product/{id} ```

| Arguments             | Type      | Description                       |
| :---------------------| :---------| :---------------------------------|
| name                  | string    | **Required** apple                |
| actual_price          | string    | **Required** 100                  |
| sale_price            | string    | **Required** 150                  |
| unit                  | string    | **Required** single               |
| more_information      | string    | **Required** text                 |
| brand_id              | string    | **Required** 1                    |
| photo                 | string    | **Required** url()                |
| total_stock           | string    | **Required** 10                   |


#### Delete Product (Put)

``` https://h.mmsdev.site/api/v1/product/{id} ```

#### Stock List

``` https://h.mmsdev.site/api/v1/stock ```

#### Show Stock

``` https://h.mmsdev.site/api/v1/stock/{id} ```

#### Add Stock

``` https://h.mmsdev.site/api/v1/product ```

| Arguments             | Type      | Description                       |
| :---------------------| :---------| :---------------------------------|
| user_id               | integer   | **Required** 1                    |
| product_id            | integer   | **Required** 20                   |
| quantity              | integer   | **Required** 10                   |
| more_information      | string    | **Required** text                 |

#### Delete Stock

``` https://h.mmsdev.site/api/v1/stock/{id} ```

## Sale

### Voucher

#### Checkout (Post)

``` https://h.mmsdev.site/api/v1/voucher ```

| Arguments             | Type      | Description                       |
| :---------------------| :---------| :---------------------------------|
| customer_name         | string    | **Nullable** John Doe             |
| phone_number          | string    | **Nullable** 09888888             |
| product_id            | integer   | **Required** 1                    |
| quantity              | integer   | **Required** 20                   |

#### Voucher List

``` https://h.mmsdev.site/api/v1/voucher ```

#### Show Voucher

``` https://h.mmsdev.site/api/v1/voucher/{id} ```

### Finance

#### Sale Close (Post)

``` https://h.mmsdev.site/api/v1/sale_close ```

#### Sale Open (Post)

``` https://h.mmsdev.site/api/v1/sale_open ```

#### Daily Sale Records (Post)

``` https://h.mmsdev.site/api/v1/daily_sale_records?date=2023-09-11 ```

#### Monthly Sale Records (Post)

``` https://h.mmsdev.site/api/v1/monthly_sale_record?month=7&year=2023 ```


| Id  | Month    | Id  | Month     |
| :-- | :------- | :-- | :-------- |
| 1   | January  | 7   | July      |
| 2   | February | 8   | August    |
| 3   | March    | 9   | September |
| 4   | April    | 10  | October   |
| 5   | May      | 11  | November  |
| 6   | June     | 12  | December  |

#### Yearly Sale Records (Post)

``` https://h.mmsdev.site/api/v1/yearly_sale_record?year=2023 ```

#### Custom Sale Records (Post)

``` https://h.mmsdev.site/api/v1/custom_sale_records?start_date=2023-06-13&end_date=2023-06-25 ```

| Arguments         | Type | Description           |
| :-----------------| :--- | :---------------------|
| start_date        | date | **Search** 2023-06-13 |
| end_date          | date | **Search** 2023-06-25 |

#### Get Year

``` https://h.mmsdev.site/api/v1/year ```

### Report

#### Stock Overview

``` https://h.mmsdev.site/api/v1/stock_report?keyword=voluptatem&stock_level=instock ```

| Arguments         | Type   | Description                                  |
| :-----------------| :------| :--------------------------------------------|
| stock_level       | string | **required** instock/low stock/ out of stock |
| keyword           | string | **Nullable** product_name                    |


#### Brand Report

``` https://h.mmsdev.site/api/v1/brand_report ```

#### Today Sale Report

``` https://h.mmsdev.site/api/v1/today-sale-report ```

#### Product Sale Reportk

``` https://h.mmsdev.site/api/v1/product-sale-report ```

#### Weekely Sale Report

``` https://h.mmsdev.site/api/v1/weekely-sale-report ```





