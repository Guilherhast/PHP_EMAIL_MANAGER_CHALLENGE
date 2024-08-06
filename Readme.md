# Email manager

## Endpoint

List of endpoints to manage an email database.

## Set up the enviroment

First create the `.env` file:

```bash
cp .env.default .env
```

Set up the variables as you whish.

You may also set up the variable using the export:


```bash
export JWT_SECRET=$(uuidgen | tr -d -)
```

## How to run

