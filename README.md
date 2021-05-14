# Database ski manufacturer project

## Running the Web server

First make a file called `dbCredentials.php`, the template for this file is called `dbCredentialsTemplate.php`.

When the dbCredentials file is made with the correct information, run the setup script called `Setup.php`.

To start the web server, use the command: `php -S localhost:8080 api.php`.

## Endpoints

There are 6 endpoint roots in this API:
```
localhost:8080/public/
localhost:8080/customer/
localhost:8080/shipper/
localhost:8080/planner/
localhost:8080/storekeeper/
localhost:8080/customer-rep/
```
`{:value}` indicates a mandatory input paramater

`{value}` indicates optional input

## Public Endpoint

There is only one public endpoint

```
localhost:8080/public/ski-types
```

This endpoint will give you a list of all ski-types, you can also filter these based on the grip type or the model.

#### - Request
```
Method: GET
localhost:8080/public/ski-types{?grip={:grip_type}}{?model={:model}}
```
`{?grip={:string}}` and `{?model={:string}}` are optional queries.


## Customer Endpoints

There are two customer endpoints:
```
localhost:8080/customer/orders/
localhost:8080/customer/production-plan/
```

### Orders
This endpoint allows the customer to get their orders, create new orders, cancel orders and split orders.

#### - Requests

##### Get specific order:
```
Method: GET
localhost:8080/customer/orders/{:order_number}{?customer_id={:id}}
```
`{?customer_id={:id}}` is a mandatory query to make sure a customer cant see other customers orders.

Example request: `localhost:8080/customer/orders/10?customer_id=500`


##### Get all orders for a customer, with optional since filter:
```
Method: GET
localhost:8080/customer/orders{?customer_id={:id}}{?since={:date}}
```
`{?customer_id={:id}}` is a mandatory query to make sure a customer cant see other customers orders.
`{?since={:date}}` is an optional query to only show orders made after the queried date.

Example request: `localhost:8080/customer/orders?customer_id=500&since=2020-01-01`

##### Creates a new order:
```
Method: POST
localhost:8080/customer/orders{?customer_id={:id}}
Body:
{
    "skis": {
        "ski_type_id": quantity,
        "ski_type_id": quantity
    }
}
```
`{?customer_id={:id}}` is a mandatory query to know what customer is making the order.

Example request: `localhost:8080/customer/orders?customer_id=10`

Example body:
```
{
    "skis": {
        "1": 50,
        "2": 100,
        "3": 150
    }
}
```
This example request would create an order with 50 skis with ski type id 1, 100 skis with ski type id 2, and 150 skis with ski type id 3.

##### Delete an order:
```
Method: DELETE
localhost:8080/customer/orders/{:order_number}{?customer_id={:id}}
```
`{:order_number}` is a mandatory parameter
`{?customer_id={:id}}` is a mandatory query

Example request: `localhost:8080/customer/orders/10?customer_id=5`

##### Split an order:
```
Method: POST
localhost:8080/customer/orders/{:order_number}{?customer_id={:id}}
```
`{:order_number}` is a mandatory parameter
`{?customer_id={:id}}` is a mandatory query

Example request: `localhost:8080/customer/orders/1?customer_id=10`


### Production Plan

This endpoint allows the customer to see a production plan summary of a chosen month

#### - Request

```
Method: GET
localhost:8080/customer/production-plan/{month}
```
`{month}` is a mandatory parameter

Example request: `localhost:8080/customer/production-plan/5`


## Shipper endpoint
The shipper has one endpoint:
```
localhost:8080/shipper/orders
```

This endpoint allows a shipper to update the status of an order from ready-for-shipping to shipped.
It also allows the shipper to see information about orders ready for shipment

#### - Requests

##### Get all orders ready for shipment:
```
Method: GET
localhost:8080/shipper/orders
```

##### Update the state of an order:
```
Method: PATCH
localhost:8080/shipper/orders/{order_number}
Body:
{
    "state": "shipped"
    "shipment_number": 100
}
```
`{order_number}` is a mandatory parameter


## Planner endpoint

There is only one planner endpoint:

```
localhost:8080/planner/production-plan
```
This endpoint allows a production planner to add a production plan.

#### - Request

```
Method: POST
localhost:8080/planner/production-plan
Body: 
{
    "month": month_number,
    "skis": {
        "ski_type_id": quantity,
        "ski_type_id": quantity
    }
}
```
Example request: `localhost:8080/planner/production-plan`

Example body:
```
{   
    "month": 5
    "skis": {
        "1": 50,
        "2": 100,
        "3": 150
    }
}
```

## Storekeeper endpoints

There are two storekeeper endpoints:
```
localhost:8080/storekeeper/skis
localhost:8080/storekeeper/orders
```

### Skis
This endpoint allows a storekeeper to add a new ski

##### - Request

##### Add a new ski with given ski type id:
```
Method: POST
localhost:8080/storekeeper/skis
Body:
{
    "ski_type_id": id
}
```
Example request: `localhost:8080/storekeeper/skis`

Example body:
```
Body:
{
    "ski_type_id": 2
}
```

### Orders
This endpoint allows a storekeeper to update the state of the order

#### - Requests

#### Get orders with status skis-available:
```
Method: GET
localhost:8080/storekeeper/orders/
```

#### Update the state of an order:
```
Method: PATCH
localhost:8080/storekeeper/orders/{order_number}
Body:
{
    "state": "ready-for-shipping"
}
```
`{order_number}` is a mandatory parameter

## Customer rep endpoints
There is one customer rep endpoint:
```
localhost:8080/customer-rep/orders
```
This endpoint allows the customer rep to retrieve orders and update the state of an order.

### - Requests

##### Gets all orders with a state filter:
```
Method: GET
localhost:8080/customer-rep/orders{?state={:state}}
```
`{?state={:state}}` is an optional query used to get only orders of a certain state


##### Change the state of an order to either open or skis-available:
```
Method: PATCH
localhost:8080/storekeeper/orders/{order_number}
Body:
{
    "state": "open/skis-available"
}
```
`{order_number}` is a mandatory parameter




