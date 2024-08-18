# Email manager

## Endpoint

List of endpoints to manage an email database.

## How to use

### Configure PHP

This project requires the (mailparse)[https://www.php.net/manual/en/book.mailparse.php] extension.

You will also need to enable the (iconv)[https://www.php.net/manual/en/function.iconv.php] extension.

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

Open the url in your local browser ( <http://localhost:8000> ).

### Log in

When you to to  <http://localhost:8000> you will be redirected
to <http://localhost:8000/public/>.

**Check if you are loged in:**

If the you see the emoje `❔` or `❌` you are not logged in.
You may click in the check button if you have logged in in
another tab.

To log in you shoud click the `login` button. If the emoji
`✅` appears you are logged in.

### Manage database

#### Get manny

Click in the `Manny` button to show the many interface.
Choose the limit and offset and then click in te `Get all` button to get some emails.

If the content is very long it will fail ( too much characters in the buffer ).
A value for the limit is `10`.

#### Get one

Now choose one `id` then click in the `Get` button.
If everything is ok you will see the content of the email with that `id`.

#### Create

If you want to create an email Fill an information fields ( except by `raw_text` ) then clik in `create`.
If it worked you will see the `id` field change for the id of the new email created.

#### Update

To update an email choose one `id` then click in the `get` button.
After change the fields you want to change then click on the `update` button.
If it went well you will see an alert saying "Success true".

#### Delete

To delete an email choose one `id` then click in the `delete` button.
It will show success whether the object exists or not.

To test if the object was deleted click on the `get` button.
One alert with the message `Non existent` will be shown.

## Save database

To save the command go to `src/CLI` and run:

```bash
php GenericRetriever.php  --raw --save=raw_text --all
```
