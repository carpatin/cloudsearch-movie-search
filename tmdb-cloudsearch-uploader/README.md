# What does this CLI tool do?
This CLI tool allows one to import movies data from The Movie Database (www.themoviedb.org) using their exposed API and the upload 
a a few fields for each of the movies into a Cloudsearch domain.

# Setup
Prerequisites:
- An AWS account (access key and secret key) and a domain created using one of the domain creation tools provided in this repo.
- An account on www.themoviedb.org with a functional API key. 

To properly setup the tool make sure you:
- have installed latest PHP 7.1.x
- run composer install to install 3rd party libs
- copy config/config.yaml.dist to config/config.yaml and fill in correct values for:
```yaml
parameters:
  tmdb:
    api_key: the api key here
  cloudsearch:
    accessKey: access key here
    secretKey: secret key here
```

# Usage examples
You can use this tool in two ways:
1. First import data into a plain text file then upload the file's contents to CS whenever you want and as many times you want. You'll have to run 2 commands for that.
2. Import data from TMDB and the send it to Cloudsearch without persisting it to disk in one tool execution.

## Import and upload in two steps

The following command will import for each of the given years (2017 and 2018) 5 pages of movies (20 movies per page) 
from TMDB and will get the language specific fields data in both English and Romanian. It will save the plain text data 
into a file located in the _data_ directory and will report the filename under which the data was saved.
```bash
php console.php tmdb:moviesImport en,ro 5 2017,2018
```

Example output:
```bash
Saved movie documents imported to file 2018Dec20174111
```

The following command will take movie data from the previously imported file and will push it to the Cloudsearch domain:
```bash
php console.php cloudsearch:moviesUpload 2018Dec20174111 movies-some_hash_here.eu-central-1.cloudsearch.amazonaws.com
```

Example output:
```bash
Uploaded 200 documents to Cloudsearch domain movies-some_hash_here.eu-central-1.cloudsearch.amazonaws.com
```

## Import and upload at once

```bash
php console.php full:moviesImportAndUpload en,ro 5 2017,2018 movies-some_hash_here.eu-central-1.cloudsearch.amazonaws.com
```

Example output:
```bash
Importing movies data from TMDB...
Uploading movies data to Cloudsearch...
```
