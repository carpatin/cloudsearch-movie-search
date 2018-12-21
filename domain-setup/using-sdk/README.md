# CloudSearch domain setup using SDK client

## To setup 'movies' domain it should be run:
`php bin/console sdk:setup-movies-domain`
##By default the domain name is 'movies', but you can change it by adding the --domain-name option
`php bin/console sdk:setup-movies-domain --domain-name movies-1`

## CLI commands used for creating and configuring 'movies' domain
* `php bin/console sdk:create-domain` - creates a new domain
* `php bin/console sdk:configure-access-policies-domain` - sets the access policies
* `php bin/console sdk:configure-analysis-scheme-domain` - adds analysis schemes
* `php bin/console sdk:configure-fields-domain` - adds the index fields
* `php bin/console sdk:trigger-reindexing` - triggers indexing to apply all the changes
