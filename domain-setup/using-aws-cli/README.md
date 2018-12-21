# CloudSearch domain setup using AWS CLI tool

## To setup or to update a domain called 'movies', simply run:
`bash setup.bash movies`

## Contents
* `1-create-domain.bash` creates a new domain, if a domain with the same name already exists then it does nothing
* `2-configure-access-policies.bash` applies the access policies located in `config/access-policies.json`
* `3-configure-analysis-scheme.bash` adds the all analysis schemes located in `config/analysis-scheme/` to the domain definition
* `4-configure-fields.bash` defines the index fields for the domain
* `5-trigger-indexing.bash` the final step for applying all the schema changes

## How to
### Adding a text field
* because we did the setup using dynamic fields, there is no additional work necessary for adding a new text field as long as it ends with one of the defined wildcards:
    * *_ro / _en* for single value fields without stemming 
    * *_multi_ro / _multi_en* for multi value fields without stemming 
    * *_fuzzy_ro / _fuzzy_en* for single value fields with full stemming, stopwords and synonyms
    
### Adding a new non-text field
* fields configuration is done using the script `4-configure-fields.bash`
* adding a new field is just a matter of appending a command to the script: 
```bash
aws cloudsearch define-index-field \
            --domain-name ${domainName} \
            --name <name> \
            --type <type> \
            --return-enabled true
```
* for more details on the available options and their possible values, use `aws cloudsearch define-index-field help`

### Adding support for a new language
* let's suppose you want to add support for french content
* follow the existing examples and create the analysis schemes:
    * `config/analysis-scheme/french.json` for non stemmed text fields
    * `config/analysis-scheme/fuzzy_french.json` for text fields that will make use of stopwords, synonyms and full stemming
* add the dynamic fields for the new language
```bash
aws cloudsearch define-index-field \
    --domain-name ${domainName} \
    --name *_fr \
    --type text \
    --return-enabled true \
    --highlight-enabled true \
    --analysis-scheme french
```
```bash
aws cloudsearch define-index-field \
    --domain-name ${domainName} \
    --name *_fuzzy_fr \
    --type text \
    --return-enabled true \
    --highlight-enabled true \
    --analysis-scheme fuzzy_french
```
```bash
aws cloudsearch define-index-field \
    --domain-name ${domainName} \
    --name *_multi_fr \
    --type text-array \
    --return-enabled true \
    --highlight-enabled true \
    --analysis-scheme french
```

After changing the configuration, running `bash setup.sh <domain-name>` will ensure that your changes are applied.