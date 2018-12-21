
set -e;

if [[ "$#" -ne 1 ]]; then
    echo "Usage: ${BASH_SOURCE[0]} <domain-name>"
    exit 1
fi

domainName=$1

# Numeric fields
aws cloudsearch define-index-field \
   --domain-name ${domainName} \
   --name vote_count \
   --type int \
   --sort-enabled true \
   --return-enabled true

aws cloudsearch define-index-field \
   --domain-name ${domainName} \
   --name vote_average \
   --type double \
   --sort-enabled true \
   --return-enabled true

# Literal fields
aws cloudsearch define-index-field \
   --domain-name ${domainName} \
   --name original_title \
   --type literal \
   --sort-enabled true \
   --return-enabled true

aws cloudsearch define-index-field \
   --domain-name ${domainName} \
   --name release_date \
   --type date \
   --sort-enabled true \
   --return-enabled true

# Text fields
aws cloudsearch define-index-field \
    --domain-name ${domainName} \
    --name *_ro \
    --type text \
    --return-enabled true \
    --highlight-enabled true \
    --analysis-scheme romanian

aws cloudsearch define-index-field \
    --domain-name ${domainName} \
    --name *_fuzzy_ro \
    --type text \
    --return-enabled true \
    --highlight-enabled true \
    --analysis-scheme fuzzy_romanian

aws cloudsearch define-index-field \
   --domain-name ${domainName} \
   --name *_multi_ro \
   --type text-array \
   --return-enabled true \
   --highlight-enabled true \
   --analysis-scheme romanian

aws cloudsearch define-index-field \
    --domain-name ${domainName} \
    --name *_en \
    --type text \
    --return-enabled true \
    --highlight-enabled true \
    --analysis-scheme english

aws cloudsearch define-index-field \
    --domain-name ${domainName} \
    --name *_fuzzy_en \
    --type text \
    --return-enabled true \
    --highlight-enabled true \
    --analysis-scheme fuzzy_english

aws cloudsearch define-index-field \
   --domain-name ${domainName} \
   --name *_multi_en \
   --type text-array \
   --return-enabled true \
   --highlight-enabled true \
   --analysis-scheme english
