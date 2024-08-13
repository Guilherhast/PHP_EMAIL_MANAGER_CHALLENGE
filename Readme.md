# Email manager

## Endpoint

List of endpoints to manage an email database.

## How to use

### Set up the enviroment

First create the `.env` file:

```bash
cp .env.default .env
```

Set up the variables as you whish.

You may also set up the variable using the export:

```bash
export JWT_SECRET=$(uuidgen | tr -d -)
```

### Installing the packages

Go to the source folder if you are not already there.

```bash
cd  src/
```

Use composer to install the packages.

```bash
composer i
```

### Start the server

Go to the source folder if you are not already there.

```bash
cd  src/
```

Run the PHP builtin server:

```bash
php -S localhost:8000
```

Open the url in your local browser ( <localhost:8000> ).
