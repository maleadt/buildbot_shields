Buildbot Shield Status
======================

Quick and dirty HTTP status endpoint for generating Buildbot shields.


## Installation

1. Get a hold of composer, and install the [Poser](https://github.com/badges/poser) library
   using `composer install` (based on the `composer.lock` in this repo).

2. Create a MySQL user and database, and create a `private.php` with those details (see the
   `private.php.sample` template). There should be a single table:

   ```sql
   CREATE TABLE IF NOT EXISTS `builds` (
     `builder` varchar(256) NOT NULL,
     `branch` varchar(256) NOT NULL,
     `url` varchar(256) NOT NULL,
     `complete` tinyint(1) NOT NULL,
     `results` int(11) NOT NULL,
     `time` timestamp NOT NULL
   )
   ```

3. Make your buildbot master push its statuses to the `submit.php` endpoint:

   ```
   sp = reporters.HttpStatusPush(serverUrl="http://$WHEREVER/shields/submit.php")
   c['services'].append(sp)
   ```

4. (optional) Edit `.htaccess` to lock-down access; see the `.htaccess.sample` template.


## Usage

Use `build.php` to render badges, specifying the following GET parameters:

- `builder` (required)
- `branch` (optional, defaulting to `master`)


You can use `redirect.php` to get a hold of the build result page, which you can use to make
the badges clickable. The parameters are the same as for `build.php`.

For example, you could embed the use the following code in your `README.md`:

```

[builder-img]: http://$WHEREVER/shields/build.php?builder=foo&branch=bar
[builder-url]: http://$WHEREVER/shields/url.php?builder=foo&branch=bar

Build status: [![][builder-img]][builder-url]
```


## Hacking

Valid JSON data is available in `example.json`, which you can submit using `curl`: `curl -v
-X POST http://WHEREVER/shields/submit.php --data @example.json`.
