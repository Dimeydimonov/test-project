#!/bin/bash

# Create a temporary directory for migrations
mkdir -p database/migrations/temp

# Move all migrations to the temp directory
mv database/migrations/*.php database/migrations/temp/

# Move migrations back in the correct order
mv database/migrations/temp/0001_01_01_000000_create_users_table.php database/migrations/
mv database/migrations/temp/0001_01_01_000001_create_cache_table.php database/migrations/
mv database/migrations/temp/0001_01_01_000002_create_jobs_table.php database/migrations/
mv database/migrations/temp/2025_09_14_184439_add_role_to_users_table.php database/migrations/

# Categories must come before artworks due to foreign key constraint
mv database/migrations/temp/2025_09_14_185904_create_categories_table.php database/migrations/
mv database/migrations/temp/2025_09_14_185834_create_artworks_table.php database/migrations/

# Other migrations
mv database/migrations/temp/2025_09_14_190448_create_comments_table.php database/migrations/
mv database/migrations/temp/2025_09_14_190648_create_likes_table.php database/migrations/
mv database/migrations/temp/2025_09_14_191615_create_personal_access_tokens_table.php database/migrations/

# Remove the temp directory
rmdir database/migrations/temp

echo "Migrations have been reordered successfully."
