# Database Configuration Guide

This guide covers database setup and optimization for the Stock Analytics application in different environments.

## Environment Configurations

### Development (SQLite)

SQLite is configured for development with performance optimizations:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
DB_FOREIGN_KEYS=true

# SQLite Performance Optimizations
DB_JOURNAL_MODE=WAL          # Write-Ahead Logging for better concurrency
DB_SYNCHRONOUS=NORMAL        # Balance between safety and speed
DB_CACHE_SIZE=-64000         # 64MB cache (negative = KB)
DB_MMAP_SIZE=268435456       # 256MB memory mapping
DB_TEMP_STORE=MEMORY         # Store temp tables in memory
DB_BUSY_TIMEOUT=30000        # 30 second timeout for locks
```

### Production (MySQL)

MySQL is recommended for production with the following configuration:

```env
DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=stock_analytics
DB_USERNAME=your-username
DB_PASSWORD=your-secure-password

# MySQL Performance Options
DB_ENGINE=InnoDB
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
DB_TIMEOUT=60

# SSL Configuration (if required)
MYSQL_ATTR_SSL_CA=/path/to/ca-cert.pem
MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=false
MYSQL_ATTR_SSL_CIPHER=AES256-SHA
```

### Alternative: PostgreSQL

PostgreSQL can be used as an alternative:

```env
DB_CONNECTION=pgsql
DB_HOST=your-postgres-host
DB_PORT=5432
DB_DATABASE=stock_analytics
DB_USERNAME=postgres
DB_PASSWORD=your-secure-password
DB_CHARSET=utf8
```

## Database Optimization

### Indexes Added

The optimization migration (`2025_08_10_000000_optimize_database_indexes.php`) adds:

#### Users Table Indexes:
- `users_email_role_index` - Composite index for email and role lookups
- `users_mobile_number_index` - Mobile number lookups for duplicate checking
- `users_created_at_index` - Admin sorting by creation date

#### Requests Table Indexes:
- `requests_user_created_index` - User requests with date sorting
- `requests_stock_code_index` - Stock code searches
- `requests_email_index` - Email lookups for existing user checks
- `requests_timeframe_index` - Timeframe filtering
- `requests_created_stock_index` - Admin filtering by date and stock
- `requests_advice_fulltext` - Full-text search on advice (MySQL only)

#### System Table Indexes:
- `jobs_queue_reserved_index` - Queue processing optimization
- `cache_expiration_index` - Cache cleanup optimization
- `sessions_last_activity_index` - Session cleanup optimization

### Performance Benefits

| Operation | Before | After | Improvement |
|-----------|--------|-------|-------------|
| User login lookup | Full table scan | Index seek | 10-100x faster |
| Stock code search | Table scan | Index seek | 10-50x faster |
| Admin request filtering | Multiple scans | Composite index | 5-20x faster |
| Duplicate email check | Full scan | Index seek | 10-100x faster |
| Queue job processing | Table scan | Index seek | 5-10x faster |

## Migration Commands

### Run Migrations

```bash
# Fresh installation
php artisan migrate

# Add optimization indexes to existing database
php artisan migrate --path=database/migrations/2025_08_10_000000_optimize_database_indexes.php

# Check migration status
php artisan migrate:status
```

### Rollback (if needed)

```bash
# Rollback last batch
php artisan migrate:rollback

# Rollback specific migration
php artisan migrate:rollback --path=database/migrations/2025_08_10_000000_optimize_database_indexes.php
```

## Production Setup

### MySQL Production Configuration

1. **Create Database:**
```sql
CREATE DATABASE stock_analytics CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'stock_user'@'%' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON stock_analytics.* TO 'stock_user'@'%';
FLUSH PRIVILEGES;
```

2. **MySQL Configuration (my.cnf):**
```ini
[mysqld]
# Performance optimizations
innodb_buffer_pool_size = 2G          # 70-80% of available RAM
innodb_log_file_size = 512M
innodb_log_buffer_size = 64M
innodb_flush_log_at_trx_commit = 1     # ACID compliance
innodb_file_per_table = 1

# Connection settings
max_connections = 200
wait_timeout = 300
interactive_timeout = 300

# Query cache (if using MySQL 5.7 or earlier)
query_cache_type = 1
query_cache_size = 256M
```

3. **Run Setup Commands:**
```bash
# Set environment variables
php artisan config:cache

# Run migrations
php artisan migrate --force

# Seed admin user
php artisan db:seed --class=AdminUserSeeder

# Optimize for production
php artisan optimize
```

### PostgreSQL Production Setup

1. **Create Database:**
```sql
CREATE DATABASE stock_analytics;
CREATE USER stock_user WITH PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE stock_analytics TO stock_user;
```

2. **PostgreSQL Configuration (postgresql.conf):**
```ini
# Memory settings
shared_buffers = 2GB                   # 25% of RAM
effective_cache_size = 6GB             # 75% of RAM
work_mem = 64MB
maintenance_work_mem = 512MB

# Connection settings
max_connections = 200
```

## Monitoring & Maintenance

### Query Performance Monitoring

1. **Enable MySQL Query Log:**
```sql
SET GLOBAL general_log = 'ON';
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;
```

2. **PostgreSQL Monitoring:**
```sql
-- Enable query stats
SELECT * FROM pg_stat_statements;
```

### Regular Maintenance Tasks

1. **Optimize Tables (MySQL):**
```sql
OPTIMIZE TABLE users, requests, jobs, failed_jobs;
```

2. **Update Statistics (PostgreSQL):**
```sql
ANALYZE users, requests, jobs, failed_jobs;
```

3. **Clean Up Old Data:**
```bash
# Clean old jobs
php artisan queue:prune-batches --hours=48

# Clean old logs (if stored in database)
php artisan log:clear --days=30
```

### Backup Strategy

1. **MySQL Backup:**
```bash
mysqldump -u stock_user -p stock_analytics > backup_$(date +%Y%m%d_%H%M%S).sql
```

2. **PostgreSQL Backup:**
```bash
pg_dump -U stock_user -h localhost stock_analytics > backup_$(date +%Y%m%d_%H%M%S).sql
```

3. **Automated Backup Script:**
```bash
#!/bin/bash
# Create backup
BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
mysqldump -u stock_user -p stock_analytics > $BACKUP_FILE

# Upload to cloud storage (example with AWS S3)
aws s3 cp $BACKUP_FILE s3://your-backup-bucket/database/

# Keep only last 7 days of local backups
find . -name "backup_*.sql" -mtime +7 -delete
```

## Security Considerations

### Database Security Checklist

- [ ] Use strong passwords for database users
- [ ] Enable SSL/TLS connections
- [ ] Restrict database user permissions
- [ ] Regular security updates
- [ ] Monitor for suspicious queries
- [ ] Enable audit logging
- [ ] Regular backup verification
- [ ] Network-level access controls

### Connection Security

```env
# Enable SSL for MySQL
MYSQL_ATTR_SSL_CA=/path/to/ca-cert.pem
MYSQL_ATTR_SSL_CERT=/path/to/client-cert.pem
MYSQL_ATTR_SSL_KEY=/path/to/client-key.pem
MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=true

# PostgreSQL SSL
PGSSLMODE=require
PGSSLCERT=/path/to/client-cert.pem
PGSSLKEY=/path/to/client-key.pem
PGSSLROOTCERT=/path/to/ca-cert.pem
```

## Troubleshooting

### Common Issues

1. **SQLite Locked Database:**
```bash
# Check for hung processes
lsof database/database.sqlite

# Enable WAL mode
sqlite3 database/database.sqlite "PRAGMA journal_mode=WAL;"
```

2. **MySQL Connection Timeout:**
```env
# Increase timeout
DB_TIMEOUT=120
```

3. **High Memory Usage:**
```sql
-- Check MySQL buffer pool usage
SHOW STATUS LIKE 'Innodb_buffer_pool%';

-- Optimize queries
EXPLAIN SELECT * FROM requests WHERE user_id = 1;
```

### Performance Analysis

1. **Identify Slow Queries:**
```sql
-- MySQL
SELECT * FROM mysql.slow_log WHERE query_time > 2;

-- PostgreSQL
SELECT query, mean_time, calls FROM pg_stat_statements ORDER BY mean_time DESC LIMIT 10;
```

2. **Check Index Usage:**
```sql
-- MySQL
SHOW INDEX FROM requests;

-- PostgreSQL
SELECT * FROM pg_stat_user_indexes WHERE relname = 'requests';
```

## Scaling Considerations

### Read Replicas

For high-traffic scenarios, consider:

1. **MySQL Read Replicas:**
```env
DB_READ_HOST=read-replica-host
DB_WRITE_HOST=master-host
```

2. **Connection Pooling:**
- Use PgBouncer for PostgreSQL
- Use MySQL Proxy or ProxySQL for MySQL

### Sharding Strategy

For very large datasets:
- Shard by user_id
- Separate historical data
- Archive old requests
- Use database partitioning

This configuration provides a solid foundation for both development and production environments with proper performance optimization and security measures.