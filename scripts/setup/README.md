# Setup Scripts

This directory contains database setup and migration scripts for the Hardware Store Management System.

## Available Scripts

### setup_cycle_counts.php

- **Purpose**: Creates database tables for cycle counting functionality
- **Usage**: Access via web browser at `/scripts/setup/setup_cycle_counts.php`
- **Requirements**: Database connection configured in `app/config.php`
- **Tables Created**: Cycle count sessions, items, and related tables
- **Run Once**: Only run when first setting up cycle counts feature

## Usage Instructions

1. Ensure your database connection is properly configured
2. Access the script via web browser
3. Follow the on-screen instructions
4. Check for any error messages and resolve if needed
5. Return to the main application once setup is complete

## Notes

- These scripts are designed to be run once during initial setup
- Always backup your database before running setup scripts
- Check the `database/migrations/` directory for the actual SQL files being executed
