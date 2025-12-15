## EasyPad

A simple, responsive note-taking application with sharing capabilities.

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser (Chrome, Firefox, Safari, Edge)

## Setup & Installation

### 1. Enable PHP Extensions

Edit your `php.ini` file and uncomment these lines:

```ini
extension=mysqli
extension=pdo_mysql
```


Find your php.ini location:
```bash
php --ini
```

After editing, restart your PHP server.

### 2. Database Setup


##### Run the SQL schema to create the required tables:

```bash
cd server
mysql -u root -p your_password < schema/setup.sql
```

### 3. Configure Database Connection

Edit `server/utils/database.php` with your MySQL credentials.

## Running the Application

### Start Backend Server (Port 8000)

```bash
cd server
php -S localhost:8000 index.php
```

The API will be available at `http://localhost:8000`

### Start Frontend Server (Port 5173)


Option 1: Using PHP Built-in Server
```bash
cd client
php -S localhost:5173
```


Option 2: Using Python
```bash
cd client
python -m http.server 5173
```

Open `http://localhost:5173` in your browser.

## API Endpoints

- `POST /api/notes` - Create a new note
- `GET /api/notes/{id}` - Get a note by ID
- `PUT /api/notes/{id}` - Update a note
- `POST /api/share` - Create a share link
- `GET /api/share/{token}` - Get a shared note
- `PUT /api/share/{token}` - Update a shared note
