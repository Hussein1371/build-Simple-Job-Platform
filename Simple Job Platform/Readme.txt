# Job Platform Project

A job platform built with PHP, MySQL, and CSS for users to post and search jobs, upload resumes, and manage applications.

---

## How to Run

1. **Install XAMPP**  
   Download and install XAMPP (Apache + MySQL).

2. **Setup Project**  
   - Copy the project folder to `C:\xampp\htdocs\job_platform`.
   - Open XAMPP and start **Apache** and **MySQL**.

3. **Import Database**  
   - Go to `http://localhost/phpmyadmin`.
   - Create a new database called `job_platform`.
   - Import the `job_platform.sql` file from the project.

4. **Configure Database**  
   - Open `db.php` and verify the database credentials:
     ```php
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "job_platform";
     ```

5. **Run the Application**  
   - Visit `http://localhost/job_platform` in your browser.

---

## Key Files

- `db.php`: Database connection.
- `register.php`: User registration.
- `login.php`: User login.
- `post_job.php`: Post job listings.
- `search_jobs.php`: Search for jobs.
- `job_platform.sql`: Database setup.

---

## Troubleshooting

- Ensure Apache and MySQL are running in XAMPP.
- Verify the database credentials in `db.php`.
- Check the project folder name matches the URL path.

