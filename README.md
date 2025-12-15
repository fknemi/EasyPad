## EasyPad

A simple, responsive note-taking application with sharing capabilities.

## Setup & Installation

### 1. Database Setup

Run the SQL schema to create the required tables:

```bash
cd server/schema
mysql -u root -p your_pass < schema/setup.sql
```

### 2. Configure Database Connection

Edit `server/utils/database.php` with your MySQL credentials.

## Running the Application

### Start Backend Server (Port 8000)

Navigate to the server directory and start PHP's built-in server:

```bash
cd server
php -S localhost:8000 index.php
```

The API will be available at `http://localhost:8000`

### Start Frontend Server (Port 5173)

##### Option 1: Using PHP Built-in Server

Navigate to the client directory and start a separate PHP server:

```bash
cd client
php -S localhost:5173
```

##### Option 2: Using Python

```bash
cd client
python -m http.server 5173
```

## API Endpoints

- `POST /api/notes` - Create a new note
- `GET /api/notes/{id}` - Get a note by ID
- `PUT /api/notes/{id}` - Update a note
- `POST /api/share` - Create a share link
- `GET /api/share/{token}` - Get a shared note
- `PUT /api/share/{token}` - Update a shared note

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser (Chrome, Firefox, Safari, Edge)
