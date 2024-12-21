# SQL worksheet - multi-database SQL playground

SQL worksheet is a docker-based in-browser SQL playground that allows you to run SQL code against a number of backend database systems, including MySQL, PostgreSQL, and Oracle.

## Prerequisites

- Docker

## Getting started

### Build from source

Build the application container using docker compose:

```bash
docker compose build
```

Before running, generate database passwords using `generate-secrets.sh`:

```bash
mkdir secrets
./generate-secrets.sh
```

These are used by the backend to log in to each database.

You can then start all databases using docker compose:

```
docker compose up
```

Access the application in a browser at http://localhost:12345. You can bind to a different port by changing the ports key in `docker-compose.yml`:

```yaml
services:
    webapp:
        # ...
        ports:
            - 12345:8080
```

### Run using only specific databases

It is possible to save resources and disk space by only enabling the DBMSs you want to use.

You can use the `start.py` python script to only start specific databases. For example, to start only MySQL 8.4 and PostgreSQL 16, use:

```bash
python3 start.py --mysql8.4 --postgres16
```

Use `python3 start.py -h` for a list of available DBMSs.

If you don't want to use python, you can do the same by running docker compose directly and setting the `ENABLED_DBS` environment variable:

```bash
ENABLED_DBS=mysql8.4,postgres16 docker compose --profile mysql8.4 --profile postgres16 up
```

As of now, the python script just wraps this command.

## Worksheet isolation

While worksheets are not designed to process highly sensitive information, they are isolated from each other, so SQL queries in one cannot read data in tables created by another. Worksheets run on the same server instance but under different users as appropriate for the DBMS; for example, two worksheets ran against MySQL 8.4 will run on the same server but under different users and databases created by the `CREATE USER` and `CREATE DATABASE` statements. This means that all objects you create in a worksheet (tables, indexes, etc) are private, as is the worksheet itself.

This means it is acceptable to allow untrusted users to run worksheets on an instance. Any ability for a worksheet to read the contents of another worksheet is considered a security vulnerability and should be reported.

This also means that you cannot run SQL code that requires root/admin privileges on the backend database.

The user name and/or database name is randomly generated for each worksheet run. For example, running `SELECT USER(), DATABASE()` against MySQL will return a different result for every run.

## Worksheet ephemerality

Tables and other objects that are created during a worksheet run are ephemeral and are dropped as soon as the worksheet finishes executing. This means that if you query a random number generator or the current time, for example, the result will be different every time you run the worksheet.

## TODO/Planned features

- Worksheet import/export
- Authentication
- Server side worksheet storage
- Sharing with link
- CSV file import to table/SQL queries against CSV file
- .sql file import
- Add option to remove worksheet sandbox and execute statements that require root/admin privileges
