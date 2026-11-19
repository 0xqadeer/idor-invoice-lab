# IDOR Invoice Lab

> **WARNING: This application is intentionally vulnerable.** It exists for local, educational use only. Never deploy it on a public network or a production server.

A small PHP and MySQL invoice app, built to practice finding and fixing an **Insecure Direct Object Reference (IDOR)**, also known as Broken Access Control (OWASP Top 10 2021, A01, and CWE-639). The repo includes the vulnerable app, the fixed query, and a full penetration test report.

## What this project shows

- A logged-in user (alice) can read other users' invoices just by changing the `invoice` number in the URL.
- The flaw was exploited with Burp Suite (Repeater and Intruder), documented with evidence, then fixed.
- The fix was verified by repeating the same tests and showing that other users' invoices now return "Invoice not found".

## Tech stack

- PHP 8.3 with Apache
- MySQL 8.0
- Docker and Docker Compose
- Burp Suite Community Edition for testing

## Project structure

```
idor-invoice-lab/
  app/
    index.php
    db.php
  database/
    init.sql
  report/
    IDOR_Penetration_Test_Report.pdf
  Dockerfile
  docker-compose.yml
  README.md
```

## How to run

1. Install Docker Desktop.
2. Clone the repo and open the folder in a terminal.
3. Start the lab:

```
docker compose up -d --build
```

4. Wait about 20 seconds for MySQL to finish setting up, then open http://localhost:8080.

The web port is bound to `127.0.0.1` only, so the lab is reachable from your own machine and nowhere else.

To reset the database after changing `init.sql`, run `docker compose down -v` and start it again.

## Test accounts (fake lab data)

| Username | Password |
|----------|----------|
| alice | alicepass |
| bob | bobpass |
| admin | adminpass |

Invoices 1 and 2 belong to alice, 3 and 4 to bob, and 5 to the admin.

## The vulnerability

The invoice page looked up records by ID only:

```php
$stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ?");
$stmt->execute([$_GET['invoice']]);
```

The server never checked who owned the invoice, so alice could open `?invoice=3`, `?invoice=4`, or `?invoice=5` and read bob's and the admin's data.

## The fix

```php
$stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ? AND user_id = ?");
$stmt->execute([$_GET['invoice'], $_SESSION['user_id']]);
```

The ownership check now uses the server-side session, which the user cannot change.

## Full report

The complete write-up, with Burp screenshots, impact analysis, CVSS scoring, remediation, and fix verification, is in [`report/IDOR_Penetration_Test_Report.pdf`](report/IDOR_Penetration_Test_Report.pdf).

## Disclaimer

This project was built and tested only against a deliberately vulnerable local lab. No production systems were tested. Use it for learning only.

## Author

Qadeer Khan, application security researcher. Find me on X: [0xqadeer]
