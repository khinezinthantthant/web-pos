# Web POS API Documentation

This document provides details about the Web POS API endpoints along with their request methods and descriptions.

## Database Design

[drawsql.app](https://drawsql.app/teams/hello-world-20/diagrams/copy-of-web-pos)

## Authentication

All endpoints require authentication using a **bearer token**. The token must be included in the request headers with the key `Authorization`.

```http
Authorization: Bearer 2|KgdoQujKkqSRb74j6NhelW8veouaE9hLnSMlbEVj
```

### 1.Admin Login

```
 https://h.mmsdev.site/api/v1/login
```

**Description**: This endpoint is used for admin user login. It requires an email and password as form-data and returns a bearer token upon successful authentication.

#### Request

-   Method: `POST`
-   form-data

| Arguments  | Type     | Description                  |
| :--------- | :------- | :--------------------------- |
| `email`    | `string` | **Required** admin@gmail.com |
| `password` | `string` | **Required** asdffdsa        |

##### Response

The response will contain the bearer token used for subsequent authenticated requests.
`2|KgdoQujKkqSRb74j6NhelW8veouaE9hLnSMlbEVj`

---

### 2.Staff Login

```
 https://h.mmsdev.site/api/v1/login
```

**Description**: This endpoint is used for staff user login. It requires an email and password as form-data and returns a bearer token upon successful authentication.

#### Request

-   Method: `POST`
-   form-data

| Arguments  | Type     | Description                   |
| :--------- | :------- | :---------------------------- |
| `email`    | `string` | **Required** ayeaye@gmail.com |
| `password` | `string` | **Required** asdffdsa         |

##### Response

The response will contain the bearer token used for subsequent authenticated requests.
`5|KgdoQujKkqSRb74j6NhelW8veouaE9hLnSMlbEVj`

---

### 3. Profile

#### Auth

-   Type: `Bearer Token`
-   Token: `{{token}}`

#### 3.1 Logout

```
https://h.mmsdev.site/api/v1/logout
```

**Description**: This endpoint is used to log out the currently authenticated user.

#### Request

-   Method: `POST`

#### 3.2 Logout All

```
https://h.mmsdev.site/api/v1/logout-all
```

**Description**: This endpoint is used to logout for all authenticated users.

#### Request

-   Method: `POST`

### 4. User

#### Auth

-   Type: `Bearer Token`
-   Token: `{{token}}`

#### 4.1 User List

```
https://h.mmsdev.site/api/v1/user
```

**Description**: This endpoint is used to retrieve a list of all users.

##### Response

The response will contain a list of users.

#### 4.2 Show User

```
https://h.mmsdev.site/api/v1/user/1
```

**Description**: This endpoint is used to retrieve information about a specific user.

##### Response

The response will contain information about the specified user.

#### 4.3 Create User

```
https://h.mmsdev.site/api/v1/user
```

**Description**: This endpoint is used to create a new user account.

#### Request

-   Method: `POST`

-   form-data

| Arguments       | Type     | Description                   |
| :-------------- | :------- | :---------------------------- |
| `name`          | `string` | **Required** Aye Aye          |
| `email`         | `string` | **Required** ayeaye@gmail.com |
| `password`      | `string` | **Required** asdffdsa         |
| `phone_number`  | `number` | **Required** 098888888        |
| `address`       | `string` | **Required** yangon           |
| `gender`        | `string` | **Required** female           |
| `date_of_birth` | `string` | **Required** 1/1/2000         |
| `role`          | `string` | **Required** staff            |
| `photo`         | `string` | (upload file)                 |

#### 4.4 Update User

```
https://h.mmsdev.site/api/v1/user/2
```

**Description**: This endpoint is used to update information about a specific user.

#### Request

-   Method: `PUT`

-   form-data

| Arguments       | Type     | Description                     |
| :-------------- | :------- | :------------------------------ |
| `name`          | `string` | **Required** Kyaw Kyaw          |
| `email`         | `string` | **Required** kyawkyaw@gmail.com |
| `password`      | `string` | **Required** 097777777          |
| `phone_number`  | `number` | **Required** mandalay           |
| `address`       | `string` | **Required** male               |
| `gender`        | `string` | **Required** 1/1/2000           |
| `date_of_birth` | `string` | **Required** staff              |
| `role`          | `string` | **Required** dozen              |
| `photo`         | `string` | (remove existing photo)         |

#### 4.5 Delete User

```
https://h.mmsdev.site/api/v1/user/2
```

#### Request

Method: `DELETE`

**Description**: This endpoint is used to delete a specific user.

#### 4.6 Modify Password

```
https://h.mmsdev.site/api/v1/change-staff-password
```

**Description**: This endpoint is used to modify user password.

#### Request

Method: `POST`

-   form-data

| Arguments      | Type      | Description             |
| :------------- | :-------- | :---------------------- |
| `user_id`      | `integer` | **Required** 2          |
| `new_password` | `string`  | **Required** hellohello |

#### 4.7 Ban User

```
https://h.mmsdev.site/api/v1/ban_user/
```

**Description**: This endpoint is used to ban user.

#### Request

Method: `POST`

---

### 5. Inventory Management

#### 5.1 Products

#### 5.1.1 Product List

```
https://h.mmsdev.site/api/v1/product
```

**Description**: This endpoint is used to retrieve a list of all products from the inventory.

##### Response

The response will contain a list of products.

#### 5.1.2 Show Product

```
https://h.mmsdev.site/api/v1/product/2
```

**Description**: This endpoint is used to retrieve information about a specific product.

##### Response

The response will contain information about the specified product.

#### 5.1.3 Store Product

```
https://h.mmsdev.site/api/v1/product
```

**Description**: This endpoint is used to add a new product to the inventory.

#### Request

-   Method: `POST`

-   form-data

| Arguments          | Type     | Description             |
| :----------------- | :------- | :---------------------- |
| `name`             | `string` | **Required** toothbrush |
| `brand_id`         | `number` | **Required** 3          |
| `actual_price`     | `number` | **Required** 100        |
| `sale_price`       | `number` | **Required** 1200       |
| `unit`             | `string` | **Required** dozen      |
| `photo`            | `string` | **Required**            |
| `more_information` | `string` | it's a toothbrush       |

##### Response

The response will contain information about the created product with successful message.

#### 5.1.4 Update Product

```
https://h.mmsdev.site/api/v1/product/4
```

**Description**: This endpoint is used to update information about a specific product.

#### Request

-   Method: `PUT`

-   form-data

| Arguments          | Type     | Description             |
| :----------------- | :------- | :---------------------- |
| `name`             | `string` | **Required** toothpaste |
| `brand_id`         | `number` | **Required** 3          |
| `user_id`          | `number` | **Required** 1          |
| `actual_price`     | `number` | **Required** 100        |
| `sale_price`       | `number` | **Required** 1200       |
| `unit`             | `string` | **Required** dozen      |
| `more_information` | `string` | it's a toothpaste       |

#### 5.1.5 Delete Product

```
https://h.mmsdev.site/api/v1/product/1
```

#### Request

Method: `DELETE`

**Description**: This endpoint is used to delete a specific product from the inventory.

---

#### 5.2 Brand

#### 5.2.1 Store

```http
 https://h.mmsdev.site/api/v1/brand
```

**Description**: This endpoint is used to add a new brand to the inventory.

#### Request

-   Method: `POST`

-   form-data

| Arguments     | Type      | Description              |
| :------------ | :-------- | :----------------------- |
| `name`        | `string`  | **Required** cocala      |
| `company`     | `string`  | **Required** max         |
| `description` | `text`    | **Required** lorem ispum |
| `user_id`     | `integer` | **Required** max         |
| `agent`       | `string`  | **Required** max         |
| `phone_no`    | `string`  | **Required** max         |

#### 5.2.2 Index

```
https://h.mmsdev.site/api/v1/brand
```

**Description**: This endpoint is used to retrieve a list of all brands in the inventory.

##### Response

The response will contain a list of brands.

#### 5.2.3 Show

```
 https://h.mmsdev.site/api/v1/brand/4
```

**Description**: This endpoint is used to retrieve information about a specific brand.

##### Response

The response will contain information about the specified brand.

#### 5.2.4 Update

```
https://h.mmsdev.site/api/v1/brand/4
```

**Description**: This endpoint is used to update information about a specific brand.

#### Request

-   Method: `PUT`

-   form-data

| Arguments     | Type     | Description              |
| :------------ | :------- | :----------------------- |
| `name`        | `string` | **Required** cocala      |
| `company`     | `string` | **Required** max         |
| `description` | `text`   | **Required** lorem ispum |
| `agent`       | `string` | **Required** max         |
| `phone_no`    | `string` | **Required** max         |

#### 5.2.5 Delete

```
https://h.mmsdev.site/api/v1/brand/1
```

Method: `DELETE`

**Description**: This endpoint is used to delete a specific brand from the inventory.

---

#### 5.3 Stock

#### 5.3.1 Store

```
https://h.mmsdev.site/api/v1/stock
```

**Description**: This endpoint is used to store stock information.

#### Request

-   Method: `POST`

-   form-data

| Arguments    | Type     | Description              |
| :----------- | :------- | :----------------------- |
| `user_id`    | `number` | **Required** 1           |
| `product_id` | `number` | **Required** 2           |
| `quantity`   | `number` | **Required** 50          |
| `more`       | `text`   | **Required** lorem ispum |

#### 5.3.2 Index

```
https://h.mmsdev.site/api/v1/stock
```

**Description**: This endpoint is used to retrieve a list of all stock items.

##### Response

The response will contain a list of stock items.

#### 5.3.3 Show

```
https://h.mmsdev.site/api/v1/stock/10
```

**Description**: This endpoint is used to retrieve information about a specific stock item.

##### Response

The response will contain information about the specified stock item.

#### 5.3.4 Update Stock

```
https://h.mmsdev.site/api/v1/stock/4
```

**Description**: This endpoint is used to update information about a specific brand.

#### Request

-   Method: `PUT`

-   form-data

| Arguments    | Type     | Description              |
| :----------- | :------- | :----------------------- |
| `user_id`    | `number` | **Required** 1           |
| `product_id` | `number` | **Required** 2           |
| `quantity`   | `number` | **Required** 50          |
| `more`       | `text`   | **Required** lorem ispum |

#### 5.3.5 Delete

```
https://h.mmsdev.site/api/v1/stock/1
```

Method: `DELETE`

**Description**: This endpoint is used to delete a specific stock item.

### 3. Sale Processing

#### 3.1 Voucher

#### 3.1.1 Store

```
http://127.0.0.1:8000/api/v1/voucher
```

**Description**: This endpoint is used to process a sale and generate a voucher for the purchased products.

#### Request

-   Method: `POST`

-   Body:
    ```json
    {
        "products": [
            {
                "product_id": 1,
                "quantity": 5
            },
            {
                "product_id": 2,
                "quantity": 2
            }
        ],
        "customer_name": "hnin si",
        "phone_number": "098888888"
    }
    ```

---
