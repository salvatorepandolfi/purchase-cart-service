# Purchase Cart Service

A RESTful API service for calculating order pricing with VAT, built with Symfony 7.3 and API Platform.

## Script Usage

### Build the Application

```bash
./scripts/build.sh
```

This script:
- Builds and starts Docker services using Docker Compose
- Installs Composer dependencies
- Clears application cache
- Runs database migrations
- Generates product fixtures with sample data


### Run the Application

```bash
./scripts/run.sh
```

This script:
- Starts the application and database services
- Makes the API available on http://localhost:9090

### Run Tests

```bash
 ./scripts/test.sh
```

This script:

- Runs PHPUnit tests for all components
- Displays test results and coverage

## Implementation Overview

### Architecture
- **Backend**: PHP 8.4 with Symfony 7.3 framework
- **Database**: PostgreSQL 16 with Doctrine ORM
- **API Documentation**: API Platform with Swagger/OpenAPI
- **Containerization**: Docker with Docker Compose
- **Testing**: PHPUnit with comprehensive test coverage

### Key Components

#### Entities
- **Product**: Stores product information with base price and VAT rate
- **Order**: Manages order data with total price and VAT calculations
- **Item**: Represents individual items in an order with quantity and pricing

#### Services
- **OrderService**: Core business logic for order creation and pricing calculations
- **ProductRepository**: Data access for product information

#### DTOs (Data Transfer Objects)
- **OrderRequest**: Validates incoming order data
- **OrderResponse**: Structures API response with pricing details
- **ItemResponse**: Individual item pricing information


## API Usage

### Calculate Order Pricing

**Endpoint:** `POST /api/orders/calculate`

**Request Body:**
```json
{
  "order": {
    "items": [
      {
        "product_id": 1,
        "quantity": 1
      },
      {
        "product_id": 2,
        "quantity": 5
      },
      {
        "product_id": 3,
        "quantity": 1
      }
    ]
  }
}
```

**Response:**
```json
{
  "order_id": 3412433,
  "order_price": 12.50,
  "order_vat": 1.25,
  "items": [
    {
      "product_id": 1,
      "quantity": 1,
      "price": 2.00,
      "vat": 0.20
    },
    {
      "product_id": 2,
      "quantity": 5,
      "price": 7.50,
      "vat": 0.75
    },
    {
      "product_id": 3,
      "quantity": 1,
      "price": 3.00,
      "vat": 0.30
    }
  ]
}
```

## Swagger Interface

The application includes API Platform with Swagger documentation:

### Access Swagger UI
1. Start the application: `./scripts/run.sh`
2. Open your browser to: `http://localhost:9090/api/docs`

### Using Swagger Interface
1. **Browse Endpoints**: View all available API endpoints
2. **Test API**: Click on any endpoint to expand it
3. **Try it out**: Click "Try it out" button for interactive testing
4. **Request Body**: Paste your JSON request in the provided text area
5. **Execute**: Click "Execute" to send the request
6. **View Response**: See the formatted response with pricing details

## Alternative: Direct Docker Compose Commands

### Build and Start Services
```bash
docker compose up -d --build
```

### Run Tests
```bash
docker compose exec app ./vendor/bin/phpunit
```

### View Logs
```bash
docker compose logs -f
```

### Stop Services
```bash
docker compose stop
```

## Technical Considerations

- **Docker Compliance**: Full directory mounted to `/mnt` in container
- **Port Binding**: Web service bound to port 9090, PSQL bound to port 5432
- **Script Structure**: All scripts executable within Docker container
- **Database Persistence**: PostgreSQL data persisted via Docker volumes
- **Error Handling**: Comprehensive validation and error responses
- **Testing**: Unit tests for entities, integration tests for API
- **Code Quality**: PHPStan static analysis and PHP CS Fixer formatting
