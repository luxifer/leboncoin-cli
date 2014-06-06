# Leboncoin CLI

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/luxifer/leboncoin-cli/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/luxifer/leboncoin-cli/?branch=master)

Leboncoin CLI is a software to make request to Leboncoin.fr from the shell. Request are stored in a configuration file. You can have multiple request in thfis file. Every time a new bid is fetched it is stored on a database. Then you can send these bids by mail.

## Features

* Query Leboncoin based on a yaml config
* Notify by mail the fetched bids

## Setup

* Download the latest stable build from release
* Extract it somewhere

```
cd leboncoin-cli-<version>
cp config/database.yml.dist config/database.yml
cp config/mailer.yml.dist config/mailer.yml
cp config/leboncoin.yml.dist config/leboncoin.yml
bin/console setup
```

## Configuration

### Database

By default the database use Sqlite3, you need to have the php `sqlite` extension loaded. You don't have to touch the configuration file as the default values works.

### Mailer

The default values inside `config/mailer.yml` are set to use Sendmail. You may have to adjust the path according to your operating system.

### Alerts

Take a look at the `config/leboncoin.yml` file to know how to configure the queries. You can have multiple queries under the `criterias` node with different index name.

A new command to build this file is in the pipe.

## Commands

### Fetch bids

```
bin/console fetch
```

### Notify bids

```
bin/console notify
```

### Help

```
bin/console help
```

## Cron

Setup a cron task with the following command:

```
/path/to/installation/bin/console notify -q
```

## TODO

* Setup a proxy (port 80 closed for OVH and Online IPs) **[urgent]**
* Create a makefile to bundle the application

## TODO later

* Interactive configuration builder
