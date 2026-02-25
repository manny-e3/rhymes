# Running Laravel Queues on cPanel

cPanel environments (especially shared hosting) often have limitations on long-running processes. Here are the two best methods to run your Laravel queues.

## Method 1: Cron Job (Recommended for Shared Hosting)

Start a worker every minute that processes jobs and then stops when the queue is empty. This prevents the process from being killed for running too long and avoids memory leaks.

1.  Log in to **cPanel**.
2.  Go to **Cron Jobs**.
3.  Add a **New Cron Job**.
4.  **Common Settings**: select `Once Per Minute` (`* * * * *`).
5.  **Command**:
    ```bash
    /usr/local/bin/ea-php99 /home/rhbooksc/author.rhbooks.com.ng/artisan queue:work --stop-when-empty
    ```
    *   **Important**: `ea-php99` is likely not a real version (PHP 9.9 doesn't exist yet). You should use your actual PHP version, like `ea-php81`, `ea-php82`, or `/usr/local/bin/php` for the default.
    *   Ensure `/home/rhbooksc/author.rhbooks.com.ng` is the folder where your `artisan` file lives.


## Method 2: Process Manager (If you have SSH/Terminal Access)

If you can keep a terminal window open or use `nohup`, you can run a daemon. However, on shared hosting, these are often killed periodically.

```bash
nohup php artisan queue:work --daemon &
```

## Method 3: Supervisor (VPS / Validated cPanel)

If your host supports **Supervisor** (usually under "Laravel Options" or "Supervisor" in cPanel), this is the most robust method.

1.  Go to **Supervisor** in cPanel.
2.  Create a new worker.
3.  **Command**: `php artisan queue:work`
4.  **Directory**: `/home/YOUR_USERNAME/public_html`
5.  **Autostart**: Yes
6.  **Autorestart**: Yes

## Important: The `.env` File

Make sure your `.env` file on the server has:

```env
QUEUE_CONNECTION=database
```
(Or `redis` / `beanstalkd` if you are using those).

## Clearing the Cache

After deploying or changing queue code, always run:

```bash
php artisan queue:restart
```

## Sync Sales Cron Job

To run the sales sync automatically (e.g., every hour), add another Cron Job:

1.  **Time**: Once Per Hour (`0 * * * *`) or as needed.
2.  **Command**:
    ```bash
    /usr/bin/curl -X POST "https://author.rhbooks.com.ng/api/erprev/sync-sales" > /dev/null 2>&1
    ```
    *   Replace `https://author.rhbooks.com.ng` with your actual domain if different.
    *   This command silently hits the route to trigger the sync.

**Note on Timeouts**: HTTP requests have timeouts. If your sync takes a long time (minutes), it might time out when run via `curl`. A better long-term solution is to create a custom Artisan command for this, but `curl` works for typical use cases.
