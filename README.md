# Purchase Cart Service

A RESTful API service for calculating order pricing with VAT.

## Requirements

- Docker
- Docker Compose
- Git

## Running the Application

### Build the Application

```bash
docker run -v $(pwd):/mnt -p 9090:9090 -w /mnt mytest ./scripts/build.sh
```

### Run Tests

```bash
docker run -v $(pwd):/mnt -p 9090:9090 -w /mnt mytest ./scripts/test.sh
```

### Run the Application

```bash
docker run -v $(pwd):/mnt -p 9090:9090 -w /mnt mytest ./scripts/run.sh
```

## Alternative: Direct Docker Compose Commands

### Build and Start Services

```bash
docker-compose up -d --build
```

### Run Tests

```bash
docker-compose exec app ./vendor/bin/phpunit
```

### View Logs

```bash
docker-compose logs -f
```

### Stop Services

```bash
docker-compose down
```

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

## Architecture

- **Database**: PostgreSQL 16 with persistent volume
- **Application**: PHP 8.2 with Symfony 7.3
- **Networking**: Both containers communicate via Docker network
- **Ports**: 
  - Application: 9090
  - Database: 5432 (internal only)

## Considerations

- **Docker Compose**: Manages both database and application containers
- **Database Health Check**: Ensures database is ready before starting app
- **Volume Mounting**: Project directory mounted to `/mnt` in app container
- **Environment Variables**: Database connection configured via environment
- **Networking**: Containers communicate via internal Docker network
- **Testing**: PHPUnit tests run in the application container 